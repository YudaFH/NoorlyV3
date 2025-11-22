@php
    $data = $notification->data ?? [];
    $ctaUrl = $data['url'] ?? null;

    $typeLabels = [
        'balance' => 'Saldo & penarikan',
        'content' => 'Konten',
        'buyer'   => 'Pembeli',
        'support' => 'Support',
        'system'  => 'Platform',
    ];

    $typeLabel = $typeLabels[$notification->type] ?? ucfirst($notification->type);
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $notification->title }} - Noorly</title>
</head>
<body style="font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background:#f5f5f5; padding:24px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px; margin:0 auto; background:#ffffff; border-radius:16px; overflow:hidden;">
        <tr>
            <td style="background:#1d428a; color:#ffffff; padding:16px 20px;">
                <h1 style="margin:0; font-size:18px; font-weight:600;">
                    Notifikasi kreator Noorly
                </h1>
                <p style="margin:4px 0 0; font-size:12px; opacity:0.9;">
                    {{ $typeLabel }}
                </p>
            </td>
        </tr>

        <tr>
            <td style="padding:20px;">
                <h2 style="margin:0 0 8px; font-size:16px; font-weight:600; color:#111827;">
                    {{ $notification->title }}
                </h2>

                @if($notification->body)
                    <p style="margin:0 0 16px; font-size:13px; line-height:1.5; color:#4b5563;">
                        {!! nl2br(e($notification->body)) !!}
                    </p>
                @endif

                @if($ctaUrl)
                    <p style="margin:0 0 20px; font-size:13px; color:#4b5563;">
                        Kamu bisa melihat detailnya di dashboard Noorly.
                    </p>

                    <a href="{{ $ctaUrl }}"
                       style="display:inline-block; padding:8px 16px; border-radius:999px; background:#1d428a; color:#ffffff; font-size:13px; font-weight:600; text-decoration:none;">
                        Buka di Noorly
                    </a>
                @endif

                <p style="margin:24px 0 0; font-size:11px; color:#9ca3af;">
                    Jika kamu merasa tidak melakukan aktivitas terkait notifikasi ini,
                    segera periksa aktivitas akunmu dan hubungi support Noorly.
                </p>
            </td>
        </tr>

        <tr>
            <td style="padding:12px 20px 16px; border-top:1px solid #e5e7eb; background:#f9fafb;">
                <p style="margin:0; font-size:11px; color:#9ca3af;">
                    Email ini dikirim otomatis oleh sistem Noorly. Mohon tidak membalas email ini.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
