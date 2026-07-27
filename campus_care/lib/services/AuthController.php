<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email atau password salah'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'user' => $user,
            'token' => $token
        ], 200);
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'nim' => 'nullable|string',
            'kelas' => 'nullable|string',
        ]);

        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nim' => $request->nim,
            'kelas' => $request->kelas,
            'role' => $request->role ?? 'mahasiswa',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registrasi berhasil',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function googleAuth(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'nama' => 'required|string',
        ]);

        // Cari user berdasarkan email dari Google
        $user = User::where('email', $request->email)->first();

        // Jika belum terdaftar sama sekali, buatkan akun secara otomatis
        if (!$user) {
            $user = User::create([
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make(uniqid()), // Berikan password acak karena loginnya lewat Google
                'role' => 'mahasiswa',
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login Google berhasil',
            'user' => $user,
            'token' => $token
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Berhasil logout'], 200);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'foto_profil' => 'nullable|image|max:2048' // Maksimal 2MB
        ]);

        $user = $request->user();
        $user->nama = $request->nama;

        if ($request->hasFile('foto_profil')) {
            $path = $request->file('foto_profil')->store('profiles', 'public');
            $user->foto_profil = url('storage/' . $path);
        }

        $user->save();

        return response()->json(['message' => 'Profil berhasil diperbarui', 'user' => $user], 200);
    }
}