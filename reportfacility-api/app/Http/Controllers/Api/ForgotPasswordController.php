<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PasswordResetCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    public function sendCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak valid',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = strtolower($request->email);

        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak terdaftar',
            ], 404);
        }

        // Kalau user Google-only, optional: tolak reset password lokal
        // Kalau di tabel users kamu punya kolom google_id, ini bisa dipakai.
        if (isset($user->google_id) && $user->google_id && !$user->password) {
            return response()->json([
                'success' => false,
                'message' => 'Akun ini menggunakan Google Login. Silakan masuk dengan Google.',
            ], 400);
        }

        // Nonaktifkan kode lama
        PasswordResetCode::where('email', $email)
            ->where('is_used', false)
            ->update(['is_used' => true]);

        $code = random_int(100000, 999999);

        PasswordResetCode::create([
            'email' => $email,
            'code' => (string) $code,
            'expires_at' => Carbon::now()->addMinutes(10),
            'is_used' => false,
        ]);

        Mail::raw(
            "Kode verifikasi reset password Campus Care kamu adalah: {$code}\n\nKode ini berlaku selama 10 menit.\n\nJika kamu tidak meminta reset password, abaikan email ini.",
            function ($message) use ($email) {
                $message->to($email)
                    ->subject('Kode Reset Password Campus Care');
            }
        );

        return response()->json([
            'success' => true,
            'message' => 'Kode verifikasi berhasil dikirim ke email',
        ], 200);
    }

    public function verifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = strtolower($request->email);

        $resetCode = PasswordResetCode::where('email', $email)
            ->where('code', $request->code)
            ->where('is_used', false)
            ->latest()
            ->first();

        if (!$resetCode) {
            return response()->json([
                'success' => false,
                'message' => 'Kode verifikasi salah',
            ], 400);
        }

        if (Carbon::now()->greaterThan($resetCode->expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Kode verifikasi sudah kedaluwarsa',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kode verifikasi valid',
        ], 200);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code' => 'required|string|size:6',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = strtolower($request->email);

        $resetCode = PasswordResetCode::where('email', $email)
            ->where('code', $request->code)
            ->where('is_used', false)
            ->latest()
            ->first();

        if (!$resetCode) {
            return response()->json([
                'success' => false,
                'message' => 'Kode verifikasi salah',
            ], 400);
        }

        if (Carbon::now()->greaterThan($resetCode->expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Kode verifikasi sudah kedaluwarsa',
            ], 400);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan',
            ], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        $resetCode->update([
            'is_used' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil direset',
        ], 200);
    }
}