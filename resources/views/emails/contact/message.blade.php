<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pesan Baru dari Form Kontak Noorly</title>
</head>
<body style="font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background:#f5f5f5; padding:24px;">
    <div style="max-width:600px;margin:0 auto;background:#ffffff;border-radius:12px;padding:24px;">
        <h2 style="margin-top:0;margin-bottom:16px;">📩 Pesan Baru dari Noorly</h2>

        <p style="margin:0 0 8px 0;"><strong>Nama:</strong> {{ $data['name'] }}</p>
        <p style="margin:0 0 8px 0;"><strong>Email:</strong> {{ $data['email'] }}</p>
        <p style="margin:0 0 16px 0;"><strong>Subjek:</strong> {{ $data['subject'] }}</p>

        <hr style="border:none;border-top:1px solid #e5e7eb;margin:16px 0;">

        <p style="margin-bottom:8px;"><strong>Pesan:</strong></p>
        <p style="white-space:pre-wrap;line-height:1.6;">
            {{ $data['message'] }}
        </p>

        <hr style="border:none;border-top:1px solid #e5e7eb;margin:24px 0;">

        <p style="font-size:12px;color:#6b7280;margin:0;">
            Email ini dikirim otomatis dari form kontak Noorly (noorly.digital).
        </p>
    </div>
</body>
</html>
