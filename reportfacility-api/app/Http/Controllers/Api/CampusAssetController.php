<?php

namespace App\Http\Controllers\Api;

use App\Models\CampusAsset;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CampusAssetController extends Controller
{
    private function checkAdmin(Request $request)
    {
        if (!$request->user() || $request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya untuk admin.'
            ], 403);
        }
        return null;
    }

    public function index(Request $request)
    {
        $denied = $this->checkAdmin($request);
        if ($denied) return $denied;

        $assets = CampusAsset::latest()->get();
        return response()->json(['success' => true, 'data' => $assets], 200);
    }

    public function store(Request $request)
    {
        $denied = $this->checkAdmin($request);
        if ($denied) return $denied;

        $validated = $request->validate([
            'kode'     => 'required|string|max:255|unique:campus_assets,kode',
            'nama'     => 'required|string|max:255',
            'lokasi'   => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'deskripsi'=> 'nullable|string',
        ]);

        $asset = CampusAsset::create($validated);
        return response()->json([
            'success' => true,
            'message' => 'Aset berhasil ditambahkan',
            'data'    => $asset
        ], 201);
    }

    public function show(Request $request, $kode)
    {
        $denied = $this->checkAdmin($request);
        if ($denied) return $denied;

        $asset = CampusAsset::where('kode', $kode)->first();
        if (!$asset) {
            return response()->json(['success' => false, 'message' => 'Aset tidak ditemukan'], 404);
        }
        return response()->json(['success' => true, 'data' => $asset], 200);
    }

    public function update(Request $request, $id)
    {
        $denied = $this->checkAdmin($request);
        if ($denied) return $denied;

        $asset = CampusAsset::find($id);
        if (!$asset) {
            return response()->json(['success' => false, 'message' => 'Aset tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'kode'     => 'required|string|max:255|unique:campus_assets,kode,' . $asset->id,
            'nama'     => 'required|string|max:255',
            'lokasi'   => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'deskripsi'=> 'nullable|string',
        ]);

        $asset->update($validated);
        return response()->json([
            'success' => true,
            'message' => 'Aset berhasil diupdate',
            'data'    => $asset
        ], 200);
    }

    public function destroy(Request $request, $id)
    {
        $denied = $this->checkAdmin($request);
        if ($denied) return $denied;

        $asset = CampusAsset::find($id);
        if (!$asset) {
            return response()->json(['success' => false, 'message' => 'Aset tidak ditemukan'], 404);
        }

        $asset->delete();
        return response()->json(['success' => true, 'message' => 'Aset berhasil dihapus'], 200);
    }
}   