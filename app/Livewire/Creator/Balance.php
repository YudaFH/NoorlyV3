<?php

namespace App\Livewire\Creator;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\WithdrawRequest;
use App\Models\PayoutMethod;
use App\Services\CreatorNotifier;


class Balance extends Component
{
    public bool $sidebarOpen = true;

    // Saldo kreator
    public float $availableBalance = 0.0;

    // Form metode penarikan baru
    public string $withdrawal_type = 'bank'; // bank | ewallet

    public ?string $bank_name = null;
    public ?string $bank_account_name = null;
    public ?string $bank_account_number = null;

    public ?string $ewallet_provider = null;
    public ?string $ewallet_number = null;

    // Form penarikan saldo
    public $withdraw_amount;
    public ?string $withdraw_note = null;

    // State dropdown custom
    public bool $bankDropdownOpen = false;
    public bool $ewalletDropdownOpen = false;

    // id metode yang sedang dibuka menu titik-tiganya
    public ?int $methodMenuOpenId = null;

    // Layout dashboard
    protected string $layout = 'livewire.components.layouts.app';

    // Daftar bank & e-wallet (logo di public/images/...)
    public array $bankOptions = [
        ['code' => 'BCA',     'name' => 'BCA',         'logo' => 'banks/bca.png'],
        ['code' => 'BNI',     'name' => 'BNI',         'logo' => 'banks/bni.png'],
        ['code' => 'BRI',     'name' => 'BRI',         'logo' => 'banks/bri.png'],
        ['code' => 'MANDIRI', 'name' => 'Mandiri',     'logo' => 'banks/mandiri.png'],
        ['code' => 'CIMB',    'name' => 'CIMB Niaga',  'logo' => 'banks/cimb.png'],
        ['code' => 'PERMATA', 'name' => 'Permata',     'logo' => 'banks/permata.png'],
        ['code' => 'JAGO',    'name' => 'Bank Jago',   'logo' => 'banks/jago.png'],
    ];

    public array $ewalletOptions = [
        ['code' => 'DANA',    'name' => 'DANA',       'logo' => 'ewallet/dana.png'],
        ['code' => 'OVO',     'name' => 'OVO',        'logo' => 'ewallet/ovo.png'],
        ['code' => 'GOPAY',   'name' => 'GoPay',      'logo' => 'ewallet/gopay.png'],
        ['code' => 'SHOPEE',  'name' => 'ShopeePay',  'logo' => 'ewallet/shopeepay.png'],
        ['code' => 'LINKAJA', 'name' => 'LinkAja',    'logo' => 'ewallet/linkaja.png'],
    ];

    /*
    |--------------------------------------------------------------------------
    | Lifecycle
    |--------------------------------------------------------------------------
    */
    public function mount(): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user) {
            return;
        }

        // Saldo yang bisa ditarik
        $this->availableBalance = (float) ($user->balance_available ?? 0);

        // Kalau sudah punya metode, pakai tipe terakhir sebagai default tab
        $lastMethod = PayoutMethod::where('user_id', $user->id)->latest()->first();
        if ($lastMethod) {
            $this->withdrawal_type = $lastMethod->type;
        }
    }

    public function toggleSidebar(): void
    {
        $this->sidebarOpen = ! $this->sidebarOpen;
    }

    /*
    |--------------------------------------------------------------------------
    | Dropdown custom bank / e-wallet
    |--------------------------------------------------------------------------
    */
    public function toggleBankDropdown(): void
    {
        $this->bankDropdownOpen = ! $this->bankDropdownOpen;
        $this->ewalletDropdownOpen = false;
    }

    public function toggleEwalletDropdown(): void
    {
        $this->ewalletDropdownOpen = ! $this->ewalletDropdownOpen;
        $this->bankDropdownOpen = false;
    }

    public function selectBank(string $code): void
    {
        $this->bank_name = $code;
        $this->bankDropdownOpen = false;
    }

    public function selectEwallet(string $code): void
    {
        $this->ewallet_provider = $code;
        $this->ewalletDropdownOpen = false;
    }

    public function toggleMethodMenu(int $id): void
    {
        $this->methodMenuOpenId = $this->methodMenuOpenId === $id ? null : $id;
    }

    public function deletePayoutMethod(int $id): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $method = PayoutMethod::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (! $method) {
            return;
        }

        $wasDefault = $method->is_default && $method->status === 'verified';

        $method->delete();

        // Kalau yang dihapus adalah default, coba set default ke metode verified lain (kalau ada)
        if ($wasDefault) {
            $newDefault = PayoutMethod::where('user_id', $user->id)
                ->where('status', 'verified')
                ->first();

            if ($newDefault) {
                $newDefault->is_default = true;
                $newDefault->save();
            }
        }

        $this->methodMenuOpenId = null;

        session()->flash('status_payout', 'Metode penarikan berhasil dihapus.');
    }

    public function setDefaultPayoutMethod(int $id): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $method = PayoutMethod::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        // Hanya bisa jadi default kalau statusnya sudah verified
        if (! $method || $method->status !== 'verified') {
            return;
        }

        // Reset default lain
        PayoutMethod::where('user_id', $user->id)->update(['is_default' => false]);

        $method->is_default = true;
        $method->save();

        $this->methodMenuOpenId = null;

        session()->flash('status_payout', 'Metode penarikan utama berhasil diubah.');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper & preview metode aktif
    |--------------------------------------------------------------------------
    */
    private function maskNumber(?string $number): ?string
    {
        if (! $number) {
            return null;
        }

        $plain = preg_replace('/\D+/', '', $number);

        if (strlen($plain) <= 4) {
            return $plain;
        }

        $first = substr($plain, 0, 4);

        return $first . '••••';
    }

    /**
     * Preview metode penarikan aktif (verified + default)
     * Dipakai di Blade sebagai $this->activePayoutPreview
     */
    public function getActivePayoutPreviewProperty(): ?string
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            return null;
        }

        $method = PayoutMethod::where('user_id', $user->id)
            ->where('status', 'verified')
            ->where('is_default', true)
            ->first();

        if (! $method) {
            return null;
        }

        $masked = $this->maskNumber($method->account_number);

        if ($method->type === 'bank') {
            return "Dikirim ke: {$method->provider_name} • {$masked} a.n {$method->account_name}";
        }

        return "Dikirim ke: {$method->provider_name} • {$masked}";
    }

    /*
    |--------------------------------------------------------------------------
    | Validasi
    |--------------------------------------------------------------------------
    */
    protected function payoutRules(): array
    {
        $rules = [
            'withdrawal_type' => ['required', 'in:bank,ewallet'],
        ];

        if ($this->withdrawal_type === 'bank') {
            $rules['bank_name']           = ['required', 'string', 'max:191'];
            $rules['bank_account_name']   = ['required', 'string', 'max:191'];
            $rules['bank_account_number'] = ['required', 'string', 'max:50'];
        } else {
            $rules['ewallet_provider'] = ['required', 'string', 'max:191'];
            $rules['ewallet_number']   = ['required', 'string', 'max:50'];
        }

        return $rules;
    }

    protected function withdrawRules(): array
    {
        return [
            'withdraw_amount' => ['required', 'numeric', 'min:10000'],
            'withdraw_note'   => ['nullable', 'string', 'max:500'],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Simpan metode penarikan (request ke admin)
    |--------------------------------------------------------------------------
    */
    public function savePayoutMethod(): void
    {
        $this->validate($this->payoutRules());

        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            return;
        }

        if ($this->withdrawal_type === 'bank') {
            $providerCode = $this->bank_name;
            $config       = collect($this->bankOptions)->firstWhere('code', $providerCode);
            $providerName = $config['name'] ?? $providerCode;

            $accountName   = $this->bank_account_name;
            $accountNumber = $this->bank_account_number;
        } else {
            $providerCode = $this->ewallet_provider;
            $config       = collect($this->ewalletOptions)->firstWhere('code', $providerCode);
            $providerName = $config['name'] ?? $providerCode;

            // nama pemilik e-wallet pakai nama akun kreator
            $accountName   = $user->name;
            $accountNumber = $this->ewallet_number;
        }

        // cek apakah sudah punya metode default yang VERIFIED
        $hasDefault = PayoutMethod::where('user_id', $user->id)
            ->where('status', 'verified')
            ->where('is_default', true)
            ->exists();

        PayoutMethod::create([
            'user_id'        => $user->id,
            'type'           => $this->withdrawal_type,
            'provider_code'  => $providerCode,
            'provider_name'  => $providerName,
            'account_name'   => $accountName,
            'account_number' => $accountNumber,
            'is_default'     => ! $hasDefault,   // kalau belum ada default, calon default setelah verified
            'status'         => 'pending',       // admin akan verifikasi dulu
        ]);

        // reset form input
        $this->bank_name           = null;
        $this->bank_account_name   = null;
        $this->bank_account_number = null;
        $this->ewallet_provider    = null;
        $this->ewallet_number      = null;

        session()->flash(
            'status_payout',
            'Metode penarikan baru berhasil diajukan. Admin Noorly akan memverifikasi dalam ±1 hari kerja.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Ajukan penarikan saldo
    |--------------------------------------------------------------------------
    */
    public function submitWithdrawal(): void
    {
        $this->validate($this->withdrawRules());

        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $amount = (float) $this->withdraw_amount;

        if ($amount > $this->availableBalance) {
            $this->addError('withdraw_amount', 'Jumlah penarikan melebihi saldo tersedia.');
            return;
        }

        // Ambil metode penarikan yang sudah diverifikasi & menjadi default
        $method = PayoutMethod::where('user_id', $user->id)
            ->where('status', 'verified')
            ->where('is_default', true)
            ->first();

        if (! $method) {
            $this->addError(
                'withdraw_amount',
                'Belum ada metode penarikan yang terverifikasi. Ajukan metode penarikan dan tunggu verifikasi admin (±1 hari kerja).'
            );
            return;
        }

        $methodType  = $method->type; // bank / ewallet
        $masked      = $this->maskNumber($method->account_number);
        $methodLabel = $method->provider_name . ' - ' . $masked . ' a.n ' . $method->account_name;

        // Buat request penarikan
        $withdraw = WithdrawRequest::create([
            'user_id'      => $user->id,
            'amount'       => $amount,
            'status'       => 'pending',
            'method_type'  => $methodType,
            'method_label' => $methodLabel,
            'notes'        => $this->withdraw_note,
        ]);

        // Kurangi saldo tersedia
        $user->balance_available = max(0, ($user->balance_available ?? 0) - $amount);
        $user->save();

        $this->availableBalance = (float) $user->balance_available;

        // Reset form penarikan
        $this->withdraw_amount = null;
        $this->withdraw_note   = null;

        // === NOTIFIKASI + EMAIL KE KREATOR ===
        $formattedAmount = 'Rp ' . number_format($amount, 0, ',', '.');

        CreatorNotifier::notify(
            user:  $user,
            type:  'balance',
            title: 'Permintaan penarikan saldo dibuat',
            body:  "Permintaan penarikan sebesar {$formattedAmount} sudah kami terima dan sedang menunggu review tim Noorly.\n\n"
                ."Metode: {$methodLabel}\n\n"
                ."Estimasi proses maksimal 1×24 jam kerja. "
                ."Kamu akan mendapat notifikasi lagi ketika penarikan disetujui atau ditolak.",
            data: [
                'url'          => route('creator.balance.index'),
                'withdraw_id'  => $withdraw->id,
                'amount'       => $amount,
                'method_type'  => $methodType,
                'method_id'    => $method->id,
            ],
            sendEmail: true
        );

        session()->flash('status_withdraw', 'Permintaan penarikan saldo berhasil diajukan.');
    }


    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */
    public function render()
    {
        /** @var User|null $user */
        $user = Auth::user();

        $pendingBalance = 0;
        $withdrawnTotal = 0;
        $withdrawals    = collect();
        $methods        = collect();

        if ($user) {
            $pendingBalance = (float) WithdrawRequest::where('user_id', $user->id)
                ->where('status', 'pending')
                ->sum('amount');

            $withdrawnTotal = (float) WithdrawRequest::where('user_id', $user->id)
                ->whereIn('status', ['approved', 'paid'])
                ->sum('amount');

            $withdrawals = WithdrawRequest::where('user_id', $user->id)
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();

            $methods = PayoutMethod::where('user_id', $user->id)
                ->orderByDesc('is_default')
                ->orderByDesc('created_at')
                ->get();
        }

        return view('livewire.creator.balance', [
            'pendingBalance' => $pendingBalance,
            'withdrawnTotal' => $withdrawnTotal,
            'withdrawals'    => $withdrawals,
            'methods'        => $methods,
        ]);
    }
}
