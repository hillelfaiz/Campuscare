<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class AdminFacilityController extends Controller
{
    private $apiUrl = 'http://127.0.0.1:8001/api';

    public function index()
    {
        if (!Auth::user()->isSatpam() && Auth::user()->role !== 'admin') {
            return redirect()->route('facility.index')->withErrors('Anda tidak memiliki hak akses ke halaman Admin.');
        }

        try {
            $response = Http::timeout(5)->acceptJson()->get("{$this->apiUrl}/rooms");
            $rooms = $response->successful() ? ($response->json('data') ?? []) : [];
            return view('facility.admin_rooms', compact('rooms'));
        } catch (\Exception $e) {
            return view('facility.admin_rooms', ['rooms' => []])->withErrors('Gagal ambil data ruangan: ' . $e->getMessage());
        }
    }

    public function storeRoom(Request $request)
    {
        if (!Auth::user()->isSatpam() && Auth::user()->role !== 'admin') {
            return redirect()->route('facility.index')->withErrors('Aksi ditolak.');
        }

        $request->validate(['nama_ruangan' => 'required|string|max:255', 'gedung' => 'required|string|max:255', 'lantai' => 'required|string|max:255']);
        try {
            $response = Http::timeout(5)->acceptJson()->post("{$this->apiUrl}/eai/rooms", $request->only(['nama_ruangan', 'gedung', 'lantai']));
            if ($response->successful()) return redirect()->back()->with('success', 'Ruangan berhasil disimpan!');
            return redirect()->back()->withInput()->withErrors('Gagal: ' . $response->json('message'));
        } catch (\Exception $e) { return redirect()->back()->withInput()->withErrors('Error API: ' . $e->getMessage()); }
    }

    public function indexReports(Request $request)
    {
        if (!Auth::user()->isSatpam() && Auth::user()->role !== 'admin') {
            return redirect()->route('facility.index')->withErrors('Anda tidak memiliki hak akses ke halaman Admin.');
        }

        try {
            $response = Http::timeout(5)->acceptJson()->get("{$this->apiUrl}/eai/facility-reports");
            $allReports = $response->successful() ? ($response->json('data') ?? []) : [];

            // Kunci Kategori Admin Dashboard: Hitung total berdasarkan semua status laporan murni dari API
            $stats = [
                'total'       => count($allReports),
                'pending'     => count(array_filter($allReports, fn($r) => $r['status'] === 'pending')),
                'in_progress' => count(array_filter($allReports, fn($r) => $r['status'] === 'in_progress')),
                'completed'   => count(array_filter($allReports, fn($r) => $r['status'] === 'completed')),
            ];

            if ($request->filled('status')) {
                $allReports = array_filter($allReports, function ($report) use ($request) {
                    return $report['status'] === $request->status;
                });
            }

            return view('facility.admin_index', ['reports' => $allReports, 'stats' => $stats]);
        } catch (\Exception $e) {
            return view('facility.admin_index', [
                'reports' => [], 
                'stats' => ['total' => 0, 'pending' => 0, 'in_progress' => 0, 'completed' => 0]
            ])->withErrors('Gagal terhubung ke API Fasilitas.');
        }
    }

    public function updateReport(Request $request, $id)
    {
        if (!Auth::user()->isSatpam() && Auth::user()->role !== 'admin') {
            return redirect()->route('facility.index')->withErrors('Aksi ditolak.');
        }

        $request->validate([
            'status'          => 'required|in:pending,in_progress,completed',
            'catatan_teknisi' => 'nullable|string',
            'assigned_to'     => 'nullable|string',
        ]);

        try {
            $response = Http::timeout(5)->acceptJson()->put("{$this->apiUrl}/eai/facility-reports/{$id}", $request->only(['status', 'catatan_teknisi', 'assigned_to']));

            if ($response->successful()) {
                return redirect()->back()->with('success', 'Status laporan fasilitas berhasil diperbarui via EAI!');
            }
            return redirect()->back()->withErrors('Gagal update: ' . $response->json('message'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Gagal menghubungi API: ' . $e->getMessage());
        }
    }
}