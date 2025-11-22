<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteOtpService
{
    protected string $endpoint = 'https://api.fonnte.com/send';

    /** Normalisasi nomor (buang +, spasi, dll) */
    public function normalizePhone(string $phone): string
    {
        // buang semua karakter non-digit
        $digits = preg_replace('/\D+/', '', $phone ?? '');

        // kalau mulai dengan 0 -> ganti ke 62 (Indonesia)
        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        }

        return $digits;
    }

    /**
     * Kirim OTP via WhatsApp menggunakan Fonnte.
     */
    public function sendOtp(string $phone, string $code, string $context = 'register'): bool
{
    $token = config('services.fonnte.token');

    $target = $this->normalizePhone($phone);

    $contextText = match ($context) {
        'register' => 'pendaftaran akun Noorly',
        'login'    => 'proses masuk akun Noorly',
        default    => 'verifikasi di Noorly',
    };

    $message =
        "Noorly - Kode Verifikasi Akun\n\n" .
        "Kode OTP Anda: *{$code}*\n\n" .
        "Kode ini digunakan untuk {$contextText} dan berlaku selama 10 menit.\n" .
        "Jaga kerahasiaan kode ini dan jangan memberikannya kepada siapa pun, " .
        "termasuk pihak yang mengatasnamakan Noorly.\n\n" .
        "Jika Anda tidak merasa melakukan permintaan ini, abaikan pesan ini.";


        try {
            $response = Http::withHeaders([
                    'Authorization' => $token,
                ])
                ->asForm()
                ->post($this->endpoint, [
                    'target'      => $target,
                    'message'     => $message,
                    'countryCode' => config('services.fonnte.default_cc', '62'),
                ]);

            if (! $response->successful()) {
                Log::error('Fonnte OTP gagal', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return false;
            }

            // kalau mau, bisa cek isi json-nya:
            // $data = $response->json();

            return true;
        } catch (\Throwable $e) {
            Log::error('Error kirim OTP ke Fonnte', [
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
