<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CampusMailService
{
    public static function send(
        ?string $to,
        string $subject,
        string $title,
        string $message,
        ?string $url = null,
        string $buttonText = 'Lihat Detail'
    ): void {
        if (empty($to)) {
            return;
        }

        try {
            Mail::send('emails.campus-notification', [
                'title' => $title,
                'messageText' => $message,
                'url' => $url,
                'buttonText' => $buttonText,
            ], function ($mail) use ($to, $subject) {
                $mail->to($to)->subject($subject);
            });
        } catch (\Throwable $e) {
            Log::error('Gagal mengirim email Campus Care: ' . $e->getMessage());
        }
    }

    public static function adminEmail(): ?string
    {
        return config('services.campus_care.admin_email');
    }

    public static function webUrl(string $path = ''): string
    {
        $baseUrl = rtrim(config('services.campus_care.web_url'), '/');
        $path = '/' . ltrim($path, '/');

        return $baseUrl . $path;
    }
}