<?php

namespace App\Http\Controllers;

use App\Models\LostFoundItem;
use App\Models\User;
use App\Services\CampusMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LostFoundController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($this->isStaff($user)) {
            $query = LostFoundItem::latest();

            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }

            $items = $query->paginate(12)->appends($request->query());

            $stats = [
                'total'         => LostFoundItem::count(),
                'pending_verif' => LostFoundItem::whereIn('status', ['pending_publish', 'claim_pending'])->count(),
                'available'     => LostFoundItem::where('status', 'available')->count(),
                'scheduled'     => LostFoundItem::where('status', 'claim_scheduled')->count(),
                'claimed'       => LostFoundItem::where('status', 'claimed')->count(),
            ];

            return view('lost-found.admin-index', compact('items', 'stats'));
        }

        $query = LostFoundItem::where('type', 'found');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'available');
        }

        $items = $query->latest()->paginate(12)->appends($request->query());

        $stats = [
            'total_found' => LostFoundItem::where('type', 'found')->where('status', 'available')->count(),
            'total_lost'  => LostFoundItem::where('type', 'lost')->where('status', 'available')->count(),
            'my_reports'  => LostFoundItem::where('reported_by', $user->id)->count(),
        ];

        return view('lost-found.index', compact('items', 'stats'));
    }

    public function lostIndex(Request $request)
    {
        $user = Auth::user();

        if ($this->isStaff($user)) {
            return redirect()->route('lost-found.index');
        }

        $query = LostFoundItem::where('type', 'lost');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $items = $query->where('status', 'available')
            ->latest()
            ->paginate(12)
            ->appends($request->query());

        $stats = [
            'total_found' => LostFoundItem::where('type', 'found')->where('status', 'available')->count(),
            'total_lost'  => LostFoundItem::where('type', 'lost')->where('status', 'available')->count(),
            'my_reports'  => LostFoundItem::where('reported_by', $user->id)->count(),
        ];

        return view('lost-found.lost-index', compact('items', 'stats'));
    }

    public function history(Request $request)
    {
        $user = Auth::user();

        if ($this->isStaff($user)) {
            return redirect()->route('lost-found.index');
        }

        $stats = [
            'total_found' => LostFoundItem::where('type', 'found')->where('reported_by', $user->id)->count(),
            'total_lost'  => LostFoundItem::where('type', 'lost')->where('reported_by', $user->id)->count(),
            'total_claim' => LostFoundItem::where('type', 'found')->where('claim_id_number', $user->nim)->count(),
        ];

        $tab = $request->query('tab', 'all');
        $query = LostFoundItem::latest();

        if ($tab === 'temuan') {
            $query->where('type', 'found')->where('reported_by', $user->id);
        } elseif ($tab === 'kehilangan') {
            $query->where('type', 'lost')->where('reported_by', $user->id);
        } elseif ($tab === 'klaim') {
            $query->where('type', 'found')->where('claim_id_number', $user->nim);
        } else {
            $query->where(function ($q) use ($user) {
                $q->where('reported_by', $user->id)
                    ->orWhere('claim_id_number', $user->nim);
            });
        }

        $items = $query->get();

        return view('lost-found.history', compact('items', 'stats', 'tab'));
    }

    public function historyLost()
    {
        return redirect()->route('lost-found.history', ['tab' => 'kehilangan']);
    }

    public function historyFound()
    {
        return redirect()->route('lost-found.history', ['tab' => 'temuan']);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $isStaff = $this->isStaff($user);

        $rules = [
            'type'        => 'required|in:found,lost',
            'name'        => 'required|string|max:255',
            'category'    => 'required|string|max:100',
            'description' => 'nullable|string',
            'photo'       => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];

        if ($request->type === 'found') {
            $rules['location'] = 'required|string|max:255';
            $rules['reporter_id_photo'] = 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048';
        }

        if ($request->type === 'lost') {
            $rules['location'] = 'nullable|string|max:255';
            $rules['reporter_id_photo'] = $isStaff
                ? 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
                : 'required|image|mimes:jpg,jpeg,png,webp|max:2048';
        }

        $validated = $request->validate($rules);

        $data = [
            'type'        => $validated['type'],
            'name'        => $validated['name'],
            'category'    => $validated['category'],
            'description' => $validated['description'] ?? null,
            'reported_by' => $user->id,
            'status'      => $isStaff ? 'available' : 'pending_publish',
        ];

        if ($request->type === 'found') {
            $data['location'] = $validated['location'];
        } else {
            $data['location'] = null;
        }

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('lost-found/items', 'public');
        }

        if ($request->hasFile('reporter_id_photo')) {
            $data['reporter_id_photo'] = $request->file('reporter_id_photo')->store('lost-found/ids', 'public');
        }

        $item = LostFoundItem::create($data);

        if (!$isStaff) {
            $jenisLaporan = $item->type === 'found' ? 'barang temuan' : 'barang kehilangan';

            CampusMailService::send(
                CampusMailService::adminEmail(),
                'Laporan Lost & Found Baru - Campus Care',
                'Ada Laporan Lost & Found Baru',
                'User ' . ($user->name ?? 'Mahasiswa') . ' baru saja membuat laporan ' . $jenisLaporan . ' dengan nama barang "' . $item->name . '". Silakan cek dan verifikasi laporan tersebut.',
                $this->itemUrl($item),
                'Lihat Laporan'
            );
        }

        if ($isStaff) {
            return redirect()
                ->route('lost-found.index')
                ->with('success', 'Data berhasil ditambahkan ke katalog.');
        }

        $routeDest = $request->type === 'lost'
            ? 'lost-found.history.lost'
            : 'lost-found.history.found';

        return redirect()
            ->route($routeDest)
            ->with('success', 'Laporan berhasil terkirim! Pantau statusnya di halaman riwayat.');
    }

    public function show($id)
    {
        $item = LostFoundItem::findOrFail($id);

        return view('lost-found.show', compact('item'));
    }

    public function requestClaim(Request $request, $id)
    {
        $user = Auth::user();

        if ($this->isStaff($user)) {
            abort(403, 'Petugas tidak perlu mengajukan klaim barang.');
        }

        $request->validate([
            'claim_name'        => 'required|string|max:255',
            'claim_id_type'     => 'required|string|max:50',
            'claim_id_number'   => 'required|string|max:100',
            'claim_proof_photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $item = LostFoundItem::findOrFail($id);

        if ($item->type !== 'found') {
            return redirect()
                ->back()
                ->withErrors('Pengajuan pengambilan hanya tersedia untuk barang temuan.');
        }

        if ($item->status !== 'available') {
            return redirect()
                ->back()
                ->withErrors('Barang ini sedang diproses atau sudah diambil.');
        }

        $item->update([
            'claim_name'        => $request->claim_name,
            'claim_id_type'     => $request->claim_id_type,
            'claim_id_number'   => $request->claim_id_number,
            'claim_proof_photo' => $request->file('claim_proof_photo')->store('lost-found/proofs', 'public'),
            'status'            => 'claim_pending',
        ]);

        CampusMailService::send(
            CampusMailService::adminEmail(),
            'Pengajuan Klaim Barang - Campus Care',
            'Ada Pengajuan Klaim Barang',
            'User ' . ($user->name ?? 'Mahasiswa') . ' mengajukan klaim untuk barang "' . $item->name . '". Silakan cek bukti identitas dan lakukan verifikasi.',
            $this->itemUrl($item),
            'Lihat Pengajuan'
        );

        return redirect()
            ->route('lost-found.history', ['tab' => 'klaim'])
            ->with('success', 'Pengajuan pengambilan berhasil dikirim. Tunggu verifikasi dari petugas.');
    }

    public function verifyReport($id)
    {
        $this->authorizeStaff();

        $item = LostFoundItem::findOrFail($id);
        $item->update(['status' => 'available']);

        $reporterEmail = $this->emailByUserId($item->reported_by);

        CampusMailService::send(
            $reporterEmail,
            'Laporan Kamu Sudah Diverifikasi - Campus Care',
            'Laporan Kamu Sudah Diverifikasi',
            'Laporan kamu untuk barang "' . $item->name . '" sudah diverifikasi oleh petugas dan sekarang sudah tampil di sistem Campus Care.',
            $this->itemUrl($item),
            'Lihat Detail'
        );

        return redirect()
            ->back()
            ->with('success', 'Laporan berhasil diverifikasi dan ditayangkan.');
    }

    public function scheduleClaim(Request $request, $id)
    {
        $this->authorizeStaff();

        $request->validate([
            'scheduled_at' => 'required|date',
        ]);

        $item = LostFoundItem::findOrFail($id);

        $item->update([
            'scheduled_at' => $request->scheduled_at,
            'status'       => 'claim_scheduled',
        ]);

        $claimUserEmail = $this->emailByNim($item->claim_id_number);

        CampusMailService::send(
            $claimUserEmail,
            'Jadwal Pengambilan Barang - Campus Care',
            'Jadwal Pengambilan Barang Sudah Ditentukan',
            'Pengajuan klaim untuk barang "' . $item->name . '" sudah disetujui. Silakan datang sesuai jadwal yang ditentukan oleh petugas Campus Care.',
            $this->itemUrl($item),
            'Lihat Jadwal'
        );

        return redirect()
            ->back()
            ->with('success', 'Jadwal pengambilan berhasil diatur.');
    }

    public function markClaimed(Request $request, $id)
    {
        $this->authorizeStaff();

        $request->validate([
            'claim_handover_photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'claim_note'           => 'nullable|string',
            'claim_name'           => 'nullable|string|max:255',
            'claim_id_type'        => 'nullable|string|max:50',
            'claim_id_number'      => 'nullable|string|max:100',
            'claim_proof_photo'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $item = LostFoundItem::findOrFail($id);

        if ($item->status === 'claimed' && $request->filled('claim_note')) {
            $history = $item->claim_history ?? [];
            $history[] = [
                'claim_name'      => $item->claim_name,
                'claim_id_number' => $item->claim_id_number,
                'note'            => $request->claim_note,
                'logged_at'       => now()->toDateTimeString(),
            ];

            $item->claim_history = $history;

            if ($request->filled('claim_name')) {
                $item->claim_name = $request->claim_name;
            }

            if ($request->filled('claim_id_type')) {
                $item->claim_id_type = $request->claim_id_type;
            }

            if ($request->filled('claim_id_number')) {
                $item->claim_id_number = $request->claim_id_number;
            }

            if ($request->hasFile('claim_proof_photo')) {
                $item->claim_proof_photo = $request->file('claim_proof_photo')->store('lost-found/proofs', 'public');
            }
        }

        $item->claim_handover_photo = $request->file('claim_handover_photo')->store('lost-found/handovers', 'public');
        $item->status = 'claimed';
        $item->claimed_by = Auth::id();
        $item->claimed_at = now();
        $item->save();

        $claimUserEmail = $this->emailByNim($item->claim_id_number);

        CampusMailService::send(
            $claimUserEmail,
            'Barang Sudah Diambil - Campus Care',
            'Barang Sudah Berhasil Diambil',
            'Proses serah terima untuk barang "' . $item->name . '" sudah dicatat selesai oleh petugas Campus Care.',
            $this->itemUrl($item),
            'Lihat Riwayat'
        );

        return redirect()
            ->route('lost-found.index')
            ->with('success', 'Serah terima berhasil dicatat!');
    }

    public function showProof($id)
    {
        $this->authorizeStaff();

        $item = LostFoundItem::findOrFail($id);

        return view('lost-found.proof', compact('item'));
    }

    private function itemUrl(LostFoundItem $item): string
    {
        return CampusMailService::webUrl('/lost-found/' . $item->id);
    }

    private function emailByUserId($userId): ?string
    {
        if (empty($userId)) {
            return null;
        }

        return User::where('id', $userId)->value('email');
    }

    private function emailByNim($nim): ?string
    {
        if (empty($nim)) {
            return null;
        }

        return User::where('nim', $nim)->value('email');
    }

    private function isStaff($user = null): bool
    {
        $user = $user ?: Auth::user();

        return $user && (
            (method_exists($user, 'isSatpam') && $user->isSatpam()) ||
            ($user->role ?? null) === 'admin'
        );
    }

    private function authorizeStaff(): void
    {
        if (!$this->isStaff(Auth::user())) {
            abort(403, 'Akses ditolak.');
        }
    }

    private function authorizeSatpam(): void
    {
        $this->authorizeStaff();
    }
}
