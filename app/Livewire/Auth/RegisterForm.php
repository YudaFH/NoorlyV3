<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Services\FonnteOtpService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class RegisterForm extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $role = 'user';
    public string $password = '';
    public string $password_confirmation = '';

    // optional kreator
    public ?string $creator_name = null;
    public ?string $main_content_type = null;

    // OTP state
    public bool $otpSent = false;
    public ?string $otp_code = null;                  // hasil gabungan 6 digit
    public array $otpDigits = ['', '', '', '', '', '']; // (kalau mau pakai, tapi sekarang utama dari front-end)

    public ?int $otpExpiresAt = null;          // timestamp detik
    public int $otpAttempts = 0;
    public int $maxOtpAttempts = 3;
    public ?string $maskedPhone = null;

    // checkbox terms
    public bool $accept_terms = false;

    protected function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone'         => ['required', 'string', 'min:8', 'max:20', 'unique:users,phone'],
            'role'          => ['required', 'in:user,creator'],
            'password'      => ['required', 'string', 'min:8', 'confirmed'],
            'accept_terms'  => ['accepted'],
        ];
    }

    /**
     * STEP 1 - Validasi & kirim OTP (belum buat user di DB)
     */
    public function register(FonnteOtpService $otpService)
    {
        $validated = $this->validate();

        // normalisasi nomor
        $normalizedPhone = $this->normalizePhone($validated['phone']);

        // generate OTP 6 digit
        $code = random_int(100000, 999999);

        // simpan FORM DATA di session (password SUDAH di-hash)
        session([
            'register_form_data' => [
                'name'             => $validated['name'],
                'email'            => $validated['email'],
                'phone'            => $normalizedPhone,
                'role'             => $validated['role'],
                'password'         => Hash::make($validated['password']),
                'creator_name'     => $this->creator_name,
                'main_content_type'=> $this->main_content_type,
            ],
            'register_otp_code' => (string) $code,
        ]);

        // kirim OTP via Fonnte
        $sent = $otpService->sendOtp($normalizedPhone, (string) $code, 'register');

        if (! $sent) {
            $this->addError('register', 'Gagal mengirim kode verifikasi. Silakan coba beberapa saat lagi.');
            // bersihkan form session otp kalau gagal
            session()->forget(['register_form_data', 'register_otp_code']);
            return;
        }

        // pindah ke step OTP
        $this->otpSent       = true;
        $this->otp_code      = null;
        $this->otpDigits     = ['', '', '', '', '', ''];
        $this->otpAttempts   = 0;
        $this->otpExpiresAt  = now()->addMinutes(10)->timestamp;
        $this->maskedPhone   = $this->maskPhone($normalizedPhone);

        // kosongkan password di form
        $this->password = $this->password_confirmation = '';
    }

    /**
     * Kirim ulang OTP (pakai data form di session, user belum dibuat)
     */
    public function resendOtp(FonnteOtpService $otpService)
    {
        $formData = session('register_form_data');

        if (! $formData || empty($formData['phone'])) {
            $this->addError('otp_general', 'Sesi verifikasi tidak ditemukan. Silakan daftar ulang.');
            return;
        }

        $phone = $formData['phone'];

        // buat kode baru
        $code = random_int(100000, 999999);
        session(['register_otp_code' => (string) $code]);

        $sent = $otpService->sendOtp($phone, (string) $code, 'register');

        if (! $sent) {
            $this->addError('otp_general', 'Gagal mengirim ulang kode. Silakan coba beberapa saat lagi.');
            return;
        }

        // reset state OTP
        $this->otp_code      = null;
        $this->otpDigits     = ['', '', '', '', '', ''];
        $this->otpAttempts   = 0;
        $this->otpExpiresAt  = now()->addMinutes(10)->timestamp;
        $this->maskedPhone   = $this->maskPhone($phone);
    }

    /**
     * STEP 2 - konfirmasi OTP → baru buat user di DB
     */
    public function confirmOtp()
    {
        // Kalau dari front-end kamu masih ada sync otpDigits, boleh gabungkan di sini:
        if (! $this->otp_code || strlen($this->otp_code) !== 6) {
            // override dulu dengan kombinasi kotak kalau ada
            $combined = implode('', $this->otpDigits);
            if ($combined && strlen($combined) === 6) {
                $this->otp_code = $combined;
            }
        }

        $formData = session('register_form_data');
        $expected = session('register_otp_code');

        if (! $formData || ! $expected) {
            $this->addError('otp_general', 'Sesi verifikasi tidak ditemukan. Silakan daftar ulang.');
            return;
        }

        // 1) Belum isi sama sekali
        if ($this->otp_code === null || $this->otp_code === '') {
            $this->addError('otp_code', 'Silakan masukkan kode OTP.');
            return;
        }

        // 2) Sudah isi tapi tidak 6 digit
        if (strlen($this->otp_code) !== 6) {
            $this->addError('otp_code', 'Kode OTP harus terdiri dari 6 digit.');
            return;
        }

        // 3) Cek expired
        if ($this->otpExpiresAt && now()->timestamp > $this->otpExpiresAt) {
            $this->addError('otp_general', 'Kode verifikasi sudah kedaluwarsa. Silakan kirim kode baru.');
            return;
        }

        // 4) Batas percobaan
        if ($this->otpAttempts >= $this->maxOtpAttempts) {
            $this->addError('otp_general', 'Percobaan verifikasi sudah melewati batas. Silakan kirim kode baru.');
            return;
        }

        $this->otpAttempts++;

        // 5) Kode salah
        if ($this->otp_code !== (string) $expected) {
            $this->addError('otp_code', 'Kode OTP yang Anda masukkan salah.');
            return;
        }

        // 6) Kode benar → baru create user di database
        $user = User::create([
            'name'             => $formData['name'],
            'email'            => $formData['email'],
            'phone'            => $formData['phone'],
            'role'             => $formData['role'],
            'password'         => $formData['password'], // sudah di-hash
            'phone_verified'   => true,
            'phone_verified_at'=> now(),
            'creator_name'     => $formData['creator_name'] ?? null,
            'main_content_type'=> $formData['main_content_type'] ?? null,
        ]);

        // bersihkan session OTP & form
        session()->forget(['register_form_data', 'register_otp_code']);

        Auth::login($user);

        if ($user->isCreator()) {
            return redirect()->route('creator.dashboard');
        }

       return redirect()->route('home'); 
    }

    /**
     * Ubah nomor → balik ke step 1, bersihkan session
     */
    public function editPhone()
    {
        $this->otpSent      = false;
        $this->otp_code     = null;
        $this->otpDigits    = ['', '', '', '', '', ''];
        $this->otpAttempts  = 0;
        $this->otpExpiresAt = null;
        $this->maskedPhone  = null;

        session()->forget(['register_form_data', 'register_otp_code']);
    }

    protected function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone ?? '');

        if (Str::startsWith($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        } elseif (! Str::startsWith($digits, '62')) {
            $digits = '62'.$digits;
        }

        return $digits;
    }

    protected function maskPhone(string $phone): string
    {
        $len = strlen($phone);
        if ($len <= 6) return $phone;

        return substr($phone, 0, $len - 4) . str_repeat('•', 3) . substr($phone, -1);
    }

    public function render()
    {
        return view('livewire.auth.register-form');
    }
}
