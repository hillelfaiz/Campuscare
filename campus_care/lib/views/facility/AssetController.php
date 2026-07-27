<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset; // Pastikan model Asset sudah Anda buat
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function show($kode)
    {
        $asset = Asset::where('kode', $kode)->first();
        
        if (!$asset) {
            return response()->json(['message' => 'Aset tidak ditemukan'], 404);
        }
        
        return response()->json(['data' => $asset], 200);
    }
}