<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
</head>
<body style="margin:0; padding:0; background:#f6f7fb; font-family:Arial, Helvetica, sans-serif;">
    <div style="max-width:640px; margin:32px auto; background:#ffffff; border-radius:22px; overflow:hidden; border:1px solid #edf0f5; box-shadow:0 20px 50px rgba(15,23,42,0.08);">
        <div style="background:linear-gradient(135deg,#d40000,#8b0000); padding:28px 32px; color:#ffffff;">
            <div style="font-size:13px; font-weight:700; opacity:.85; letter-spacing:.08em; text-transform:uppercase;">
                Campus Care Notification
            </div>
            <h1 style="margin:8px 0 0; font-size:25px; line-height:1.3;">
                {{ $title }}
            </h1>
        </div>

        <div style="padding:30px 32px;">
            <p style="margin:0; color:#4b5563; font-size:15px; line-height:1.75;">
                {{ $messageText }}
            </p>

            @if(!empty($url))
                <div style="margin-top:28px;">
                    <a href="{{ $url }}"
                       style="display:inline-block; background:#cc0000; color:#ffffff; padding:13px 22px; border-radius:999px; text-decoration:none; font-weight:700; font-size:14px;">
                        {{ $buttonText }}
                    </a>
                </div>
            @endif

            <div style="margin-top:28px; padding:16px; background:#f9fafb; border:1px solid #edf0f5; border-radius:16px; color:#6b7280; font-size:13px; line-height:1.6;">
                Email ini dikirim otomatis oleh sistem Campus Care. Mohon tidak membalas email ini secara langsung.
            </div>
        </div>

        <div style="padding:18px 32px; background:#f9fafb; color:#9ca3af; font-size:12px;">
            © {{ date('Y') }} Campus Care
        </div>
    </div>
</body>
</html>