<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmailVerificationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class EmailVerificationController extends Controller
{
    public function verifyRegisterOtp(Request $request)
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

        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak terdaftar',
            ], 404);
        }

        if ($user->email_verified_at !== null) {
            return response()->json([
                'success' => true,
                'message' => 'Email sudah diverifikasi',
            ], 200);
        }

        $verificationCode = EmailVerificationCode::where('email', $email)
            ->where('code', $request->code)
            ->where('is_used', false)
            ->latest()
            ->first();

        if (!$verificationCode) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP salah',
            ], 400);
        }

        if (Carbon::now()->greaterThan($verificationCode->expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP sudah kedaluwarsa',
            ], 400);
        }

        $user->email_verified_at = Carbon::now();
        $user->save();

        $verificationCode->update([
            'is_used' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Email berhasil diverifikasi. Silakan login.',
        ], 200);
    }

    public function resendRegisterOtp(Request $request)
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

        if ($user->email_verified_at !== null) {
            return response()->json([
                'success' => true,
                'message' => 'Email sudah diverifikasi',
            ], 200);
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
            function ($message) use ($email) {
                $message->to($email)
                    ->subject('Kode Verifikasi Akun Campus Care');
            }
        );

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP baru berhasil dikirim ke email',
        ], 200);
    }
}