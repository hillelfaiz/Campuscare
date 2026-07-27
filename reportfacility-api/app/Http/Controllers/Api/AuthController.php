<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\EmailVerificationCode;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

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
            return response()->json(['success' => false, 'message' => 'Email atau password salah'], 401);
        }

        $token = $user->createToken('mobile_app_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'user' => $user,
            'token' => $token
        ], 200);
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
            'nim' => 'nullable|string',
            'kelas' => 'nullable|string',
        ]);

        $email = strtolower($request->email);

        $existingUser = User::where('email', $email)->first();

        if ($existingUser && $existingUser->email_verified_at !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Email sudah terdaftar. Silakan login.',
            ], 409);
        }

        if ($existingUser && $existingUser->email_verified_at === null) {
            $user = $existingUser;

            $user->update([
                'nama' => $request->nama,
                'password' => Hash::make($request->password),
                'nim' => $request->nim,
                'kelas' => $request->kelas,
                'role' => 'mahasiswa',
            ]);
        } else {
            $user = User::create([
                'nama' => $request->nama,
                'email' => $email,
                'password' => Hash::make($request->password),
                'nim' => $request->nim,
                'kelas' => $request->kelas,
                'role' => 'mahasiswa',
                'email_verified_at' => null,
            ]);
        }

        EmailVerificationCode::where('email', $email)
            ->where('is_used', false)
            ->update([
                'is_used' => true,
            ]);

        $code = random_int(100000, 999999);

        EmailVerificationCode::create([
            'user_id' => $user->id,
            'email' => $email,
            'code' => (string) $code,
            'expires_at' => Carbon::now()->addMinutes(10),
            'is_used' => false,
        ]);

        Mail::raw(
            "Kode verifikasi akun Campus Care kamu adalah: {$code}\n\nKode ini berlaku selama 10 menit.\n\nJika kamu tidak merasa membuat akun, abaikan email ini.",
            function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Kode Verifikasi Akun Campus Care');
            }
        );

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP telah dikirim ke email. Silakan verifikasi akun kamu.',
            'email' => $user->email,
        ], 201);
    }

    public function googleAuth(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'nama' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            $user = User::create([
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make(uniqid()), // Password acak karena login via Google
                'role' => 'mahasiswa',
            ]);
        }

        $token = $user->createToken('mobile_app_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login Google berhasil',
            'user' => $user,
            'token' => $token
        ], 200);
    }

    public function completeProfile(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nim' => 'required|string|max:50',
            'kelas' => 'nullable|string|max:50',
        ]);

        $user = $request->user();

        $user->update([
            'nama' => $validated['nama'],
            'nim' => $validated['nim'],
            'kelas' => $validated['kelas'] ?? null,
            'role' => 'mahasiswa',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil dilengkapi',
            'data' => $user,
        ]);
    }
    
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['success' => true, 'message' => 'Berhasil logout'], 200);
    }

    public function getUser(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $request->user()
        ], 200);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'nama' => 'sometimes|required|string|max:255',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->has('nama')) {
            $user->nama = $request->nama;
        }

        if ($request->hasFile('foto_profil')) {
            // Hapus foto lama jika ada (dan bukan bawaan)
            if ($user->foto_profil) {
                $oldPath = str_replace(url('storage') . '/', '', $user->foto_profil);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('foto_profil')->store('profiles', 'public');
            $user->foto_profil = url('storage/' . $path);
        }

        $user->save();

        return response()->json(['success' => true, 'message' => 'Profil diupdate', 'user' => $user], 200);
    }

    public function syncUserFromWeb(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'nama' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'password_hash' => 'nullable|string',
            'password' => 'nullable|string',
            'nim' => 'nullable|string',
            'kelas' => 'nullable|string',
            'role' => 'nullable|string',
        ]);

        $email = strtolower($validated['email']);

        $allowedRoles = ['mahasiswa', 'satpam', 'teknisi_it', 'ob', 'admin'];
        $role = $validated['role'] ?? 'mahasiswa';

        if (!in_array($role, $allowedRoles)) {
            $role = 'mahasiswa';
        }

        $password = $validated['password_hash'] ?? $validated['password'] ?? null;

        $data = [
            'nama' => $validated['nama'] ?? $validated['name'] ?? 'User',
            'nim' => $validated['nim'] ?? null,
            'kelas' => $validated['kelas'] ?? null,
            'role' => $role,
        ];

        if ($password) {
            // Password dari web sudah berbentuk hash, jadi jangan Hash::make lagi.
            $data['password'] = $password;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            $data
        );

        return response()->json([
            'success' => true,
            'message' => 'User berhasil disinkronkan dari campus-web',
            'user' => $user,
        ], 200);
    }

    public function getUserByEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', strtolower($request->email))->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user,
        ], 200);
    }
}