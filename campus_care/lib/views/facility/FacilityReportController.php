<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FacilityReport; // Pastikan model FacilityReport sudah Anda buat
use Illuminate\Http\Request;

class FacilityReportController extends Controller
{
    public function index()
    {
        $reports = FacilityReport::orderBy('created_at', 'desc')->get();
        return response()->json(['data' => $reports], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_kode' => 'required|string|exists:assets,kode',
            'reporter_id' => 'required|integer',
            'deskripsi_kerusakan' => 'required|string',
            'assigned_to' => 'required|string',
            'foto' => 'nullable|image',
        ]);

        $fotoUrl = null;
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('reports', 'public');
            $fotoUrl = url('storage/' . $path);
        }

        $report = FacilityReport::create([
            'asset_kode' => $request->asset_kode,
            'reporter_id' => $request->reporter_id,
            'deskripsi_kerusakan' => $request->deskripsi_kerusakan,
            'assigned_to' => $request->assigned_to,
            'foto' => $fotoUrl,
            'status' => 'Menunggu',
        ]);

        return response()->json([
            'message' => 'Laporan berhasil dikirim',
            'data' => $report
        ], 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:Menunggu,Sedang Diperbaiki,Sudah Diperbaiki'
        ]);

        $report = FacilityReport::find($id);
        if (!$report) {
            return response()->json(['message' => 'Laporan tidak ditemukan'], 404);
        }

        $report->status = $request->status;
        $report->save();

        return response()->json(['message' => 'Status berhasil diperbarui', 'data' => $report], 200);
    }
}