<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Services\FonnteOtpService;
use Illuminate\Support\Str;

class LoginForm extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public string $phone = '';
    public array $otpDigits = [];

    public function mount(): void
    {
        // siapkan 6 kotak OTP
        $this->otpDigits = array_fill(0, 6, '');
    }

    /**
     * Key cache OTP berbasis purpose + nomor (sudah dinormalisasi)
     */
    protected function otpCacheKey(string $purpose = 'login'): string
    {
        return "otp:{$purpose}:{$this->phone}";
    }

    /**
     * Normalisasi nomor ke format 62xxxxxxxxx
     */
    protected function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone ?? '');

        if (Str::startsWith($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        } elseif (! Str::startsWith($digits, '62')) {
            $digits = '62' . $digits;
        }

        return $digits;
    }

    /**
     * Validasi OTP dari cache
     */
    protected function validateOtp(string $purpose, string $otp): bool
    {
        $key = $this->otpCacheKey($purpose);
        $expected = cache()->get($key);

        if (! $expected) {
            return false;
        }

        if ($otp !== (string) $expected) {
            return false;
        }

        // sekali pakai
        cache()->forget($key);

        return true;
    }

    /**
     * Kirim OTP untuk login via nomor telp
     */
    public function sendOtpForLogin(): void
    {
        $this->validate([
            'phone' => 'required|string|min:8',
        ], [], [
            'phone' => 'nomor telepon',
        ]);

        // samakan format dengan yang tersimpan di DB
        $this->phone = $this->normalizePhone($this->phone);

        $code = (string) random_int(100000, 999999);

        // simpan ke cache 10 menit
        cache()->put($this->otpCacheKey('login'), $code, now()->addMinutes(10));

        /** @var FonnteOtpService $fonnte */
        $fonnte = app(FonnteOtpService::class);

        $sent = $fonnte->sendOtp($this->phone, $code, 'login');

        if (! $sent) {
            $this->addError('otp', 'Gagal mengirim kode OTP. Silakan coba lagi.');
            return;
        }

        // event kalau mau dipakai di JS (opsional)
        $this->dispatch('otp-sent-login');
    }

    public function login()
    {
        // gabung 6 digit OTP
        $otp = trim(implode('', $this->otpDigits));

        // Kalau user isi phone tapi belum isi kode → tampilkan pesan
        if ($this->phone !== '' && $otp === '') {
            $this->addError('otp', 'Silakan masukkan kode OTP.');
            return;
        }

        // MODE 1: Login via OTP (phone + otp)
        if ($this->phone !== '' && $otp !== '') {
            $this->validate([
                'phone' => 'required|string',
            ], [], [
                'phone' => 'nomor telepon',
            ]);

            // normalisasi agar sama dengan yang di-cache dan di-DB
            $this->phone = $this->normalizePhone($this->phone);

            if (! $this->validateOtp('login', $otp)) {
                $this->addError('otp', 'Kode OTP tidak valid atau sudah kedaluwarsa.');
                return;
            }

            $user = User::where('phone', $this->phone)->first();

            if (! $user) {
                $this->addError('phone', 'Nomor telepon belum terdaftar.');
                return;
            }

            Auth::login($user, true);

            if ($user->isCreator()) {
                return redirect()->route('creator.dashboard');
            }

            return redirect()->route('users.dashboard');
        }

        // MODE 2: Default → login pakai email + password
        $this->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(
            ['email' => $this->email, 'password' => $this->password],
            $this->remember
        )) {
            request()->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();

            if ($user->isCreator()) {
                return redirect()->route('creator.dashboard');
            }

              return redirect()->route('home'); 
        }

        $this->addError('email', 'Email atau kata sandi tidak sesuai.');
    }

    public function render()
    {
        return view('livewire.auth.login-form');
    }
}
