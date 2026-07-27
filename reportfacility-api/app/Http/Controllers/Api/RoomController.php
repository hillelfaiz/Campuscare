<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    // Hapus __construct() — tidak perlu di Laravel 12
    // Pengecekan role dilakukan langsung di tiap method

    public function index()
    {
        $rooms = Room::orderBy('gedung')->orderBy('lantai')->orderBy('nama_ruangan')->get();
        return response()->json(['success' => true, 'data' => $rooms], 200);
    }

    public function search(Request $request)
    {
        $query = $request->input('q');
        if (!$query) {
            return response()->json(['success' => true, 'data' => []], 200);
        }

        $rooms = Room::where('nama_ruangan', 'LIKE', "%{$query}%")
                    ->orWhere('gedung', 'LIKE', "%{$query}%")
                    ->orWhere('lantai', 'LIKE', "%{$query}%")
                    ->orderBy('gedung')
                    ->orderBy('nama_ruangan')
                    ->limit(10)
                    ->get();

        return response()->json(['success' => true, 'data' => $rooms], 200);
    }

    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $validated = $request->validate([
            'nama_ruangan' => 'required|string|max:255',
            'gedung'       => 'required|string|max:255',
            'lantai'       => 'required|string|max:255',
        ]);

        $room = Room::create($validated);
        return response()->json(['success' => true, 'message' => 'Ruangan berhasil ditambahkan', 'data' => $room], 201);
    }

    public function show($id)
    {
        $room = Room::find($id);
        if (!$room) {
            return response()->json(['success' => false, 'message' => 'Ruangan tidak ditemukan'], 404);
        }
        return response()->json(['success' => true, 'data' => $room], 200);
    }

    public function update(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $room = Room::find($id);
        if (!$room) {
            return response()->json(['success' => false, 'message' => 'Ruangan tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'nama_ruangan' => 'required|string|max:255',
            'gedung'       => 'required|string|max:255',
            'lantai'       => 'required|string|max:255',
        ]);

        $room->update($validated);
        return response()->json(['success' => true, 'message' => 'Ruangan berhasil diupdate', 'data' => $room], 200);
    }

    public function destroy(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $room = Room::find($id);
        if (!$room) {
            return response()->json(['success' => false, 'message' => 'Ruangan tidak ditemukan'], 404);
        }

        $room->delete();
        return response()->json(['success' => true, 'message' => 'Ruangan berhasil dihapus'], 200);
    }
}