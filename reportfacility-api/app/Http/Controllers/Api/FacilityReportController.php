<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\NewFacilityReportMail;
use App\Mail\ReportStatusUpdatedMail;
use App\Models\FacilityReport;
use App\Models\ReportGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class FacilityReportController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | MOBILE / FLUTTER INDEX
    |--------------------------------------------------------------------------
    | Route ini dipakai oleh Flutter dan membutuhkan auth:sanctum.
    */
    public function index(Request $request)
    {
        $user = $request->user();

        $reports = FacilityReport::with(['user', 'room', 'reportGroup'])
            ->latest()
            ->get();

        $data = $reports->map(function ($report) use ($user) {
            return $this->formatReportResponse($report, $user);
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar laporan berhasil diambil',
            'data' => $data,
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | EAI INDEX UNTUK CAMPUS-WEB
    |--------------------------------------------------------------------------
    | Route ini dipakai oleh web, jadi tidak boleh bergantung ke auth:sanctum.
    */
    public function eaiIndex()
    {
        $reports = FacilityReport::with(['user', 'room', 'reportGroup'])
            ->latest()
            ->get();

        $data = $reports->map(function ($report) {
            return $this->formatReportResponse($report, null, true);
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar laporan EAI berhasil diambil',
            'data' => $data,
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | MOBILE / FLUTTER STORE
    |--------------------------------------------------------------------------
    | Route ini dipakai Flutter, jadi reporter diambil dari token Sanctum.
    */
    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'nama_fasilitas' => 'required|string|max:255',
            'deskripsi_kerusakan' => 'required|string',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'assigned_to' => 'nullable|string',
        ]);

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terautentikasi.',
            ], 401);
        }

        $fotoPath = null;

        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('facility_reports', 'public');
        }

        $matchedGroup = $this->findSimilarReportGroup(
            roomId: (int) $request->room_id,
            namaFasilitas: $request->nama_fasilitas,
            deskripsi: $request->deskripsi_kerusakan
        );

        if ($matchedGroup['group']) {
            $group = $matchedGroup['group'];
            $similarityScore = $matchedGroup['score'];
        } else {
            $group = ReportGroup::create([
                'room_id' => $request->room_id,
                'nama_fasilitas' => $request->nama_fasilitas,
                'main_title' => $request->nama_fasilitas,
                'priority_level' => 'low',
                'report_count' => 0,
                'unique_reporter_count' => 0,
                'status' => 'pending',
                'first_reported_at' => now(),
                'last_reported_at' => now(),
            ]);

            $similarityScore = 100;
        }

        $report = FacilityReport::create([
            'report_group_id' => $group->id,
            'room_id' => $request->room_id,
            'reporter_id' => $user->id,
            'nama_fasilitas' => $request->nama_fasilitas,
            'deskripsi_kerusakan' => $request->deskripsi_kerusakan,
            'foto' => $fotoPath,
            'status' => 'pending',
            'assigned_to' => $request->assigned_to,
            'similarity_score' => $similarityScore,
        ]);

        $this->refreshReportGroupPriority($group);
        $this->syncReportGroupStatus($group->id);

        $report->load(['user', 'room', 'reportGroup']);

        $this->sendNewReportNotification($report);

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil dibuat',
            'data' => $this->formatReportResponse($report, $user),
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | EAI STORE UNTUK CAMPUS-WEB
    |--------------------------------------------------------------------------
    | Route ini dipakai web, jadi reporter dikirim lewat reporter_email.
    */
    public function storeFromWeb(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'nama_fasilitas' => 'required|string|max:255',
            'deskripsi_kerusakan' => 'required|string',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'reporter_email' => 'required|email',
            'reporter_name' => 'nullable|string|max:255',
            'reporter_nim' => 'nullable|string|max:100',
        ]);

        $email = strtolower($request->reporter_email);

        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'nama' => $request->reporter_name ?? 'Mahasiswa',
                'email' => $email,
                'password' => bcrypt(uniqid('eai_user_', true)),
                'nim' => $request->reporter_nim,
                'role' => 'mahasiswa',
            ]);
        } else {
            $user->update([
                'nama' => $request->reporter_name ?? $user->nama,
                'nim' => $request->reporter_nim ?? $user->nim,
            ]);
        }

        $fotoPath = null;

        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('facility_reports', 'public');
        }

        $matchedGroup = $this->findSimilarReportGroup(
            roomId: (int) $request->room_id,
            namaFasilitas: $request->nama_fasilitas,
            deskripsi: $request->deskripsi_kerusakan
        );

        if ($matchedGroup['group']) {
            $group = $matchedGroup['group'];
            $similarityScore = $matchedGroup['score'];
        } else {
            $group = ReportGroup::create([
                'room_id' => $request->room_id,
                'nama_fasilitas' => $request->nama_fasilitas,
                'main_title' => $request->nama_fasilitas,
                'priority_level' => 'low',
                'report_count' => 0,
                'unique_reporter_count' => 0,
                'status' => 'pending',
                'first_reported_at' => now(),
                'last_reported_at' => now(),
            ]);

            $similarityScore = 100;
        }

        $report = FacilityReport::create([
            'report_group_id' => $group->id,
            'room_id' => $request->room_id,
            'reporter_id' => $user->id,
            'nama_fasilitas' => $request->nama_fasilitas,
            'deskripsi_kerusakan' => $request->deskripsi_kerusakan,
            'foto' => $fotoPath,
            'status' => 'pending',
            'assigned_to' => null,
            'similarity_score' => $similarityScore,
        ]);

        $this->refreshReportGroupPriority($group);
        $this->syncReportGroupStatus($group->id);

        $report->load(['user', 'room', 'reportGroup']);

        $this->sendNewReportNotification($report);

        return response()->json([
            'success' => true,
            'message' => 'Laporan dari web berhasil dibuat',
            'data' => $this->formatReportResponse($report, null, true),
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();

        $report = FacilityReport::with([
            'user:id,nama,email,role,nim',
            'room:id,nama_ruangan,gedung,lantai',
            'reportGroup',
        ])->find($id);

        if (!$report) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatReportResponse($report, $user),
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | MOBILE / FLUTTER UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, $id)
    {
        $report = FacilityReport::find($id);

        if (!$report) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan tidak ditemukan',
            ], 404);
        }

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terautentikasi.',
            ], 401);
        }

        if (
            !in_array($user->role, ['admin', 'satpam']) &&
            $user->id !== $report->reporter_id
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk melakukan aksi ini.',
            ], 403);
        }

        $validated = $request->validate([
            'room_id' => 'sometimes|required|exists:rooms,id',
            'nama_fasilitas' => 'sometimes|required|string|max:255',
            'deskripsi_kerusakan' => 'sometimes|required|string',
            'status' => 'sometimes|required|in:pending,in_progress,completed',
            'catatan_teknisi' => 'nullable|string',
            'assigned_to' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        if ($user->role === 'mahasiswa') {
            unset($validated['status']);
            unset($validated['catatan_teknisi']);
            unset($validated['assigned_to']);
        }

        $this->applyReportUpdate($request, $report, $validated);

        if (
            $report->report_group_id &&
            in_array($user->role, ['admin', 'satpam'])
        ) {
            $this->applyGroupUpdate($report, $validated);
        }

        if ($report->report_group_id) {
            $group = ReportGroup::find($report->report_group_id);

            if ($group) {
                $this->refreshReportGroupPriority($group);
                $this->syncReportGroupStatus($group->id);
            }
        }

        $report = FacilityReport::with([
            'user:id,nama,email,role,nim',
            'room:id,nama_ruangan,gedung,lantai',
            'reportGroup',
        ])->find($id);

        if (
            in_array($user->role, ['admin', 'satpam']) &&
            isset($validated['status']) &&
            $report &&
            $report->report_group_id
        ) {
            $this->sendStatusUpdatedNotification($report);
        }

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil diupdate',
            'data' => $this->formatReportResponse($report, $user),
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | EAI UPDATE UNTUK CAMPUS-WEB ADMIN
    |--------------------------------------------------------------------------
    */
    public function updateFromWeb(Request $request, $id)
    {
        $report = FacilityReport::find($id);

        if (!$report) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan tidak ditemukan',
            ], 404);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
            'catatan_teknisi' => 'nullable|string',
            'assigned_to' => 'nullable|string',
        ]);

        $this->applyReportUpdate($request, $report, $validated);
        $this->applyGroupUpdate($report, $validated);

        if ($report->report_group_id) {
            $group = ReportGroup::find($report->report_group_id);

            if ($group) {
                $this->refreshReportGroupPriority($group);
                $this->syncReportGroupStatus($group->id);
            }
        }

        $report = FacilityReport::with([
            'user:id,nama,email,role,nim',
            'room:id,nama_ruangan,gedung,lantai',
            'reportGroup',
        ])->find($id);

        if ($report && $report->report_group_id && isset($validated['status'])) {
            $this->sendStatusUpdatedNotification($report);
        }

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil diupdate dari web',
            'data' => $this->formatReportResponse($report, null, true),
        ], 200);
    }

    public function destroy(Request $request, $id)
    {
        $report = FacilityReport::find($id);

        if (!$report) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan tidak ditemukan',
            ], 404);
        }

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terautentikasi.',
            ], 401);
        }

        if ($user->role !== 'admin' && $user->id !== $report->reporter_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk melakukan aksi ini.',
            ], 403);
        }

        if ($report->foto) {
            Storage::disk('public')->delete($report->foto);
        }

        $groupId = $report->report_group_id;

        $report->delete();

        if ($groupId) {
            $group = ReportGroup::find($groupId);

            if ($group) {
                $remainingReports = FacilityReport::where('report_group_id', $groupId)->count();

                if ($remainingReports === 0) {
                    $group->delete();
                } else {
                    $this->refreshReportGroupPriority($group);
                    $this->syncReportGroupStatus($groupId);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil dihapus',
        ], 200);
    }

    private function formatReportResponse(FacilityReport $report, $user = null, bool $forceShowPriority = false): array
    {
        $item = [
            'id' => $report->id,
            'reporter_id' => $report->reporter_id,
            'room_id' => $report->room_id,
            'nama_fasilitas' => $report->nama_fasilitas,
            'deskripsi_kerusakan' => $report->deskripsi_kerusakan,
            'foto' => $report->foto ? asset('storage/' . $report->foto) : null,
            'status' => $report->status,
            'catatan_teknisi' => $report->catatan_teknisi,
            'assigned_to' => $report->assigned_to,
            'created_at' => $report->created_at,
            'updated_at' => $report->updated_at,

            'user' => $report->user ? [
                'id' => $report->user->id,
                'nama' => $report->user->nama,
                'name' => $report->user->nama,
                'email' => $report->user->email,
                'nim' => $report->user->nim,
                'role' => $report->user->role,
            ] : null,

            'room' => $report->room ? [
                'id' => $report->room->id,
                'nama_ruangan' => $report->room->nama_ruangan,
                'gedung' => $report->room->gedung,
                'lantai' => $report->room->lantai,
            ] : null,
        ];

        $canSeePriority = $forceShowPriority ||
            ($user && in_array($user->role, ['admin', 'satpam']));

        if ($canSeePriority) {
            $item['priority_level'] = $report->reportGroup?->priority_level ?? 'low';
            $item['report_count'] = $report->reportGroup?->report_count ?? 1;
            $item['unique_reporter_count'] = $report->reportGroup?->unique_reporter_count ?? 1;
            $item['similarity_score'] = $report->similarity_score;
        }

        return $item;
    }

    private function applyReportUpdate(Request $request, FacilityReport $report, array $validated): void
    {
        $data = [];

        if (isset($validated['room_id'])) {
            $data['room_id'] = $validated['room_id'];
        }

        if (isset($validated['nama_fasilitas'])) {
            $data['nama_fasilitas'] = $validated['nama_fasilitas'];
        }

        if (isset($validated['deskripsi_kerusakan'])) {
            $data['deskripsi_kerusakan'] = $validated['deskripsi_kerusakan'];
        }

        if (isset($validated['status'])) {
            $data['status'] = $validated['status'];
        }

        if (array_key_exists('catatan_teknisi', $validated)) {
            $data['catatan_teknisi'] = $validated['catatan_teknisi'];
        }

        if (array_key_exists('assigned_to', $validated)) {
            $data['assigned_to'] = $validated['assigned_to'];
        }

        if ($request->hasFile('foto')) {
            if ($report->foto) {
                Storage::disk('public')->delete($report->foto);
            }

            $path = $request->file('foto')->store('facility_reports', 'public');
            $data['foto'] = $path;
        }

        $report->update($data);
    }

    private function applyGroupUpdate(FacilityReport $report, array $validated): void
    {
        if (!$report->report_group_id) {
            return;
        }

        $groupUpdateData = [];

        if (isset($validated['status'])) {
            $groupUpdateData['status'] = $validated['status'];
        }

        if (array_key_exists('catatan_teknisi', $validated)) {
            $groupUpdateData['catatan_teknisi'] = $validated['catatan_teknisi'];
        }

        if (array_key_exists('assigned_to', $validated)) {
            $groupUpdateData['assigned_to'] = $validated['assigned_to'];
        }

        if (!empty($groupUpdateData)) {
            $groupUpdateData['updated_at'] = now();

            FacilityReport::where('report_group_id', $report->report_group_id)
                ->update($groupUpdateData);
        }
    }

    private function sendNewReportNotification(FacilityReport $report): void
    {
        try {
            $internalUsers = User::whereIn('role', ['admin', 'satpam'])
                ->whereNotNull('email')
                ->get();

            foreach ($internalUsers as $internalUser) {
                Mail::to($internalUser->email)->send(
                    new NewFacilityReportMail($report)
                );
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function sendStatusUpdatedNotification(FacilityReport $report): void
    {
        try {
            $groupReports = FacilityReport::with(['user', 'room'])
                ->where('report_group_id', $report->report_group_id)
                ->get();

            $sentEmails = [];

            foreach ($groupReports as $groupReport) {
                $email = $groupReport->user?->email;

                if ($email && !in_array($email, $sentEmails)) {
                    Mail::to($email)->send(
                        new ReportStatusUpdatedMail($groupReport)
                    );

                    $sentEmails[] = $email;
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function findSimilarReportGroup(int $roomId, string $namaFasilitas, string $deskripsi): array
    {
        $candidateGroups = ReportGroup::where('room_id', $roomId)
            ->whereIn('status', ['pending', 'in_progress'])
            ->get();

        $newFacility = $this->normalizeText($namaFasilitas);

        $bestGroup = null;
        $bestScore = 0;

        foreach ($candidateGroups as $group) {
            $existingFacility = $this->normalizeText($group->nama_fasilitas ?? '');
            $existingTitle = $this->normalizeText($group->main_title ?? '');

            similar_text($newFacility, $existingFacility, $facilityPercent);
            similar_text($newFacility, $existingTitle, $titlePercent);

            $facilityScore = max($facilityPercent, $titlePercent);

            if ($facilityScore >= 80) {
                $score = (int) round($facilityScore);

                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestGroup = $group;
                }

                continue;
            }

            $newCombined = $this->normalizeText($namaFasilitas . ' ' . $deskripsi);
            $existingCombined = $this->normalizeText(
                ($group->nama_fasilitas ?? '') . ' ' . ($group->main_title ?? '')
            );

            similar_text($newCombined, $existingCombined, $combinedPercent);

            if ($combinedPercent > $bestScore) {
                $bestScore = (int) round($combinedPercent);
                $bestGroup = $group;
            }
        }

        $threshold = 65;

        if ($bestGroup && $bestScore >= $threshold) {
            return [
                'group' => $bestGroup,
                'score' => $bestScore,
            ];
        }

        return [
            'group' => null,
            'score' => $bestScore,
        ];
    }

    private function normalizeText(string $text): string
    {
        $text = strtolower($text);

        $replacements = [
            'ruangan' => 'ruang',
            'kelas' => 'ruang',

            'ac' => 'air conditioner',
            'a c' => 'air conditioner',

            'tdk' => 'tidak',
            'gk' => 'tidak',
            'ga' => 'tidak',
            'gak' => 'tidak',
            'nggak' => 'tidak',
            'ngga' => 'tidak',

            'mati' => 'rusak',
            'error' => 'rusak',
            'bermasalah' => 'rusak',
            'tidak berfungsi' => 'rusak',
            'ga berfungsi' => 'rusak',
            'gak berfungsi' => 'rusak',

            'copot' => 'lepas',
            'kelepas' => 'lepas',
            'lepas bang' => 'lepas',
            'rusak bang' => 'rusak',
        ];

        foreach ($replacements as $from => $to) {
            $text = str_replace($from, $to, $text);
        }

        $text = preg_replace('/[^a-z0-9\s]/', ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }

    private function refreshReportGroupPriority(ReportGroup $group): void
    {
        $reports = FacilityReport::where('report_group_id', $group->id)->get();

        $reportCount = $reports->count();
        $uniqueReporterCount = $reports->pluck('reporter_id')->unique()->count();

        if ($uniqueReporterCount >= 5) {
            $priority = 'high';
        } elseif ($uniqueReporterCount >= 2) {
            $priority = 'medium';
        } else {
            $priority = 'low';
        }

        $group->update([
            'report_count' => $reportCount,
            'unique_reporter_count' => $uniqueReporterCount,
            'priority_level' => $priority,
            'last_reported_at' => now(),
        ]);
    }

    private function syncReportGroupStatus(int $groupId): void
    {
        $reports = FacilityReport::where('report_group_id', $groupId)->get();

        if ($reports->isEmpty()) {
            return;
        }

        if ($reports->every(fn ($report) => $report->status === 'completed')) {
            $status = 'completed';
        } elseif ($reports->contains(fn ($report) => $report->status === 'in_progress')) {
            $status = 'in_progress';
        } else {
            $status = 'pending';
        }

        ReportGroup::where('id', $groupId)->update([
            'status' => $status,
        ]);
    }
}