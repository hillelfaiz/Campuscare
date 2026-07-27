<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class FacilityConsumerController extends Controller
{
    private $apiUrl = 'http://127.0.0.1:8001/api';

    public function index()
    {
        try {
            $response = Http::timeout(10)
                ->acceptJson()
                ->get("{$this->apiUrl}/eai/facility-reports");

            if (!$response->successful()) {
                return view('facility.index', [
                    'reports' => [],
                    'stats' => [
                        'total' => 0,
                        'diproses' => 0,
                        'selesai' => 0,
                    ],
                ])->withErrors('API Fasilitas bermasalah. Status HTTP: ' . $response->status());
            }

            /*
            |--------------------------------------------------------------------------
            | Ambil Data Laporan dari API
            |--------------------------------------------------------------------------
            | Beberapa API biasanya response-nya bisa:
            | 1. { data: [...] }
            | 2. [...]
            | 3. { reports: [...] }
            |
            | Jadi dibuat fleksibel supaya data dari Flutter tetap kebaca di web.
            */
            $json = $response->json();

            if (isset($json['data']) && is_array($json['data'])) {
                $allReports = $json['data'];
            } elseif (isset($json['reports']) && is_array($json['reports'])) {
                $allReports = $json['reports'];
            } elseif (is_array($json)) {
                $allReports = $json;
            } else {
                $allReports = [];
            }

            /*
            |--------------------------------------------------------------------------
            | Mode Katalog Publik
            |--------------------------------------------------------------------------
            | Karena report dari Flutter tampil sebagai "Laporan Publik",
            | maka di web juga ditampilkan semua laporan.
            |
            | Kalau nanti mau dibuat "Laporan Saya", baru filter by email.
            */
            $reports = array_values($allReports);

            /*
            |--------------------------------------------------------------------------
            | Statistik Laporan
            |--------------------------------------------------------------------------
            | Disesuaikan dengan status dari backend:
            | pending / waiting / menunggu
            | in_progress / diproses
            | completed / selesai
            */
            $stats = [
                'total' => count($reports),

                'diproses' => count(array_filter($reports, function ($report) {
                    $status = strtolower((string) data_get($report, 'status', ''));
                    return in_array($status, ['in_progress', 'diproses', 'processing']);
                })),

                'selesai' => count(array_filter($reports, function ($report) {
                    $status = strtolower((string) data_get($report, 'status', ''));
                    return in_array($status, ['completed', 'selesai', 'done']);
                })),
            ];

            return view('facility.index', compact('reports', 'stats'));

        } catch (\Exception $e) {
            return view('facility.index', [
                'reports' => [],
                'stats' => [
                    'total' => 0,
                    'diproses' => 0,
                    'selesai' => 0,
                ],
            ])->withErrors('Gagal terhubung ke Layanan Fasilitas. Pastikan API di Port 8001 sedang menyala. Detail: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $response = Http::timeout(10)
                ->acceptJson()
                ->get("{$this->apiUrl}/eai/rooms");

            $json = $response->json();

            if ($response->successful()) {
                if (isset($json['data']) && is_array($json['data'])) {
                    $rooms = $json['data'];
                } elseif (isset($json['rooms']) && is_array($json['rooms'])) {
                    $rooms = $json['rooms'];
                } elseif (is_array($json)) {
                    $rooms = $json;
                } else {
                    $rooms = [];
                }
            } else {
                $rooms = [];
            }

            return view('facility.create', compact('rooms'));

        } catch (\Exception $e) {
            return redirect()
                ->route('facility.index')
                ->withErrors('Gagal mengambil daftar ruangan dari API Fasilitas. Detail: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_id'             => 'required',
            'nama_fasilitas'      => 'required|string|max:255',
            'deskripsi_kerusakan' => 'required|string',
            'foto'                => 'nullable|image|max:2048',
        ]);

        try {
            $userWeb = Auth::user();

            $payload = [
                'room_id'             => $request->room_id,
                'nama_fasilitas'      => $request->nama_fasilitas,
                'deskripsi_kerusakan' => $request->deskripsi_kerusakan,

                // Identitas reporter dari akun web
                'reporter_email'      => $userWeb->email,
                'reporter_name'       => $userWeb->name,
                'reporter_nim'        => $userWeb->nim ?? null,
            ];

            $apiCall = Http::timeout(15)
                ->acceptJson();

            if ($request->hasFile('foto')) {
                $file = $request->file('foto');

                $apiCall = $apiCall->attach(
                    'foto',
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                );
            }

            $response = $apiCall->post("{$this->apiUrl}/eai/facility-reports", $payload);

            if ($response->successful()) {
                return redirect()
                    ->route('facility.index')
                    ->with('success', 'Laporan kerusakan fasilitas berhasil dikirim!');
            }

            $errorMsg = $response->json('message')
                ?? $response->body()
                ?? 'Terjadi kesalahan internal pada server API.';

            return redirect()
                ->back()
                ->withInput()
                ->withErrors('Gagal kirim laporan: ' . $errorMsg);

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors('Gagal menghubungi API Fasilitas: ' . $e->getMessage());
        }
    }
}