<div class="flex h-screen bg-slate-50">
    {{-- SIDEBAR KIRI --}}
    @include('livewire.creator.sidebar', ['sidebarOpen' => $sidebarOpen])

    {{-- KONTEN KANAN --}}
    <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8 overflow-y-auto">
        {{-- Header --}}
        <header class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-slate-900">
                    Saldo & penarikan
                </h1>
                <p class="mt-1 text-sm text-slate-500 max-w-2xl">
                    Pantau saldo kreator, atur metode penarikan dana, dan ajukan penarikan dari penjualan konten di Noorly.
                </p>
            </div>
        </header>

        {{-- Stat cards --}}
        <section class="grid gap-4 md:grid-cols-3 mb-6">
            <article class="bg-white rounded-2xl border border-slate-100 shadow-sm px-5 py-4 flex flex-col justify-between">
                <p class="text-xs font-medium text-slate-500">Saldo tersedia</p>
                <p class="mt-3 text-2xl md:text-3xl font-semibold text-emerald-600">
                    Rp {{ number_format($availableBalance, 0, ',', '.') }}
                </p>
                <p class="mt-1 text-[11px] text-slate-400">
                    Saldo yang siap kamu tarik ke rekening atau e-wallet.
                </p>
            </article>

            <article class="bg-white rounded-2xl border border-slate-100 shadow-sm px-5 py-4 flex flex-col justify-between">
                <p class="text-xs font-medium text-slate-500">Sedang diproses</p>
                <p class="mt-3 text-2xl md:text-3xl font-semibold text-amber-600">
                    Rp {{ number_format($pendingBalance, 0, ',', '.') }}
                </p>
                <p class="mt-1 text-[11px] text-slate-400">
                    Total pengajuan penarikan yang masih menunggu diproses.
                </p>
            </article>

            <article class="bg-white rounded-2xl border border-slate-100 shadow-sm px-5 py-4 flex flex-col justify-between">
                <p class="text-xs font-medium text-slate-500">Total ditarik</p>
                <p class="mt-3 text-2xl md:text-3xl font-semibold text-slate-900">
                    Rp {{ number_format($withdrawnTotal, 0, ',', '.') }}
                </p>
                <p class="mt-1 text-[11px] text-slate-400">
                    Akumulasi dana yang sudah berhasil dikirim ke kamu.
                </p>
            </article>
        </section>

        {{-- Flash messages --}}
        @if (session('status_payout'))
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('status_payout') }}
            </div>
        @endif

        @if (session('status_withdraw'))
            <div class="mb-4 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-800">
                {{ session('status_withdraw') }}
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Kiri: Metode penarikan --}}
            <section class="lg:col-span-2 space-y-4">
                            {{-- Kartu rekening / e-wallet terdaftar --}}
            @if ($methods->isNotEmpty())
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-semibold text-slate-900">
                                Rekening & e-wallet terdaftar
                            </h2>
                            <p class="text-xs text-slate-500">
                                Metode berikut ini akan digunakan setelah diverifikasi oleh admin Noorly.
                            </p>
                        </div>
                    </div>

                    <div class="grid gap-3 md:grid-cols-2">
                        @foreach ($methods as $method)
                            @php
                                $config = $method->type === 'bank'
                                    ? collect($bankOptions)->firstWhere('code', $method->provider_code)
                                    : collect($ewalletOptions)->firstWhere('code', $method->provider_code);
                            @endphp

                            <div class="relative rounded-2xl border border-slate-100 bg-slate-50/60 px-4 py-3 flex flex-col gap-2">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center gap-2">
                                        @if($config && !empty($config['logo']))
                                            <img
                                                src="{{ asset('images/' . $config['logo']) }}"
                                                alt="{{ $config['name'] }}"
                                                class="h-6 w-auto"
                                            >
                                        @else
                                            <div class="h-6 w-10 rounded-md bg-slate-200 flex items-center justify-center text-[10px] text-slate-500">
                                                {{ $method->provider_code }}
                                            </div>
                                        @endif
                                    </div>

                                    {{-- tombol titik 3 + menu --}}
                                    <div class="relative">
                                        <button
                                            type="button"
                                            wire:click="toggleMethodMenu({{ $method->id }})"
                                            class="text-slate-400 hover:text-slate-600 cursor-pointer inline-flex items-center justify-center h-7 w-7 rounded-full hover:bg-slate-100"
                                        >
                                            
                                            &#8942;
                                        </button>

                                        @if($methodMenuOpenId === $method->id)
                                            <div class="absolute right-0 mt-1 w-40 rounded-xl border border-slate-200 bg-white shadow-lg z-30 text-xs">
                                                @if($method->status === 'verified' && ! $method->is_default)
                                                    <button
                                                        type="button"
                                                        wire:click="setDefaultPayoutMethod({{ $method->id }})"
                                                        class="block w-full text-left px-3 py-2 hover:bg-slate-50"
                                                    >
                                                        Jadikan default
                                                    </button>
                                                @endif

                                                <button
                                                    type="button"
                                                    wire:click="deletePayoutMethod({{ $method->id }})"
                                                    class="block w-full text-left px-3 py-2 text-red-600 hover:bg-red-50"
                                                >
                                                    Hapus
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <p class="text-lg font-semibold text-slate-900 tracking-wide">
                                    {{ $method->account_number }}
                                </p>

                                <div>
                                    <p class="text-xs font-medium text-slate-800">
                                        {{ $method->account_name }}
                                    </p>
                                    <p class="text-[11px] text-slate-500">
                                        {{ $method->provider_name }}
                                    </p>
                                </div>

                                {{-- Status: (isi) + warna + badge default --}}
                                <div class="flex flex-wrap items-center gap-2 mt-1">
                                    <span class="text-[11px] text-slate-500">
                                        Status:
                                    </span>

                                    @if ($method->status === 'pending')
                                        <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-[11px] font-medium text-amber-700">
                                            pending
                                        </span>
                                    @elseif ($method->status === 'verified')
                                        <span class="inline-flex items-center rounded-full bg-lime-50 px-2.5 py-0.5 text-[11px] font-medium text-lime-700">
                                            verified
                                        </span>
                                    @elseif ($method->status === 'rejected')
                                        <span class="inline-flex items-center rounded-full bg-red-50 px-2.5 py-0.5 text-[11px] font-medium text-red-700">
                                            rejected
                                        </span>
                                    @endif

                                    @if ($method->is_default && $method->status === 'verified')
                                        <span class="inline-flex items-center rounded-full bg-sky-50 px-2.5 py-0.5 text-[11px] font-medium text-sky-700">
                                            default
                                        </span>
                                    @endif
                                </div>

                                @if ($method->status === 'pending')
                                    <p class="mt-1 text-[11px] text-slate-400">
                                        Menunggu verifikasi admin Noorly (estimasi ±1 hari kerja).
                                    </p>
                                @elseif ($method->status === 'rejected' && $method->status_note)
                                    <p class="mt-1 text-[11px] text-red-500">
                                        Catatan admin: {{ $method->status_note }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>

                </div>
            @endif

                <form wire:submit.prevent="savePayoutMethod" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 space-y-4">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900 mb-1">
                            Metode penarikan dana
                        </h2>
                        <p class="text-xs text-slate-500">
                            Tentukan ke mana Noorly akan mengirimkan saldo penjualan kontenmu.
                        </p>
                        @if ($this->activePayoutPreview)
                            <div class="mt-3 flex items-start gap-2 rounded-xl border border-slate-100 bg-slate-50 px-3 py-2.5 text-[11px] text-slate-600">
                                <span class="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-[#1d428a]/10 text-[#1d428a] text-[10px] font-semibold">
                                    i
                                </span>
                                <span>
                                    {{ $this->activePayoutPreview }}
                                </span>
                            </div>
                        @endif


                    </div>

                    {{-- Pilih tipe --}}
                    <div class="mb-4 flex gap-2">
                        <button
                            type="button"
                            wire:click="$set('withdrawal_type', 'bank')"
                            class="flex-1 inline-flex items-center justify-center rounded-xl border px-3 py-2 text-xs font-medium cursor-pointer
                            {{ $withdrawal_type === 'bank'
                                ? 'border-[#1d428a] bg-[#1d428a]/5 text-[#1d428a]'
                                : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'
                            }}"
                        >
                            Rekening bank
                        </button>
                        <button
                            type="button"
                            wire:click="$set('withdrawal_type', 'ewallet')"
                            class="flex-1 inline-flex items-center justify-center rounded-xl border px-3 py-2 text-xs font-medium cursor-pointer
                            {{ $withdrawal_type === 'ewallet'
                                ? 'border-[#1d428a] bg-[#1d428a]/5 text-[#1d428a]'
                                : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'
                            }}"
                        >
                            E-wallet
                        </button>
                    </div>

                    {{-- Form bank --}}
                    @if ($withdrawal_type === 'bank')
    <div class="space-y-3">
        {{-- Dropdown bank custom --}}
        <div>
            <label class="block text-xs font-medium text-slate-700 mb-1.5">
                Pilih bank
            </label>

            @php
                $selectedBank = collect($bankOptions)->firstWhere('code', $bank_name);
            @endphp

            <div class="relative">
                {{-- Tombol dropdown --}}
                <button
                    type="button"
                    wire:click="toggleBankDropdown"
                    class="flex w-full items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 hover:bg-slate-100 cursor-pointer"
                >
                    <div class="flex items-center gap-3">
                        @if($selectedBank)
                            <img
                                src="{{ asset('images/' . $selectedBank['logo']) }}"
                                alt="{{ $selectedBank['name'] }}"
                                class="h-6 w-auto"
                            >
                            <span>{{ $selectedBank['name'] }}</span>
                        @else
                            <div class="h-6 w-10 rounded-md bg-slate-100 flex items-center justify-center text-[10px] text-slate-400">
                                Bank
                            </div>
                            <span class="text-slate-400 text-xs">
                                Pilih bank tujuan
                            </span>
                        @endif
                    </div>

                    {{-- icon chevron --}}
                    <svg
                        class="h-4 w-4 text-slate-400"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path fill-rule="evenodd"
                              d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z"
                              clip-rule="evenodd" />
                    </svg>
                </button>

                {{-- List dropdown --}}
                @if($bankDropdownOpen)
                    <div
                        class="absolute z-20 mt-1 w-full rounded-xl border border-slate-200 bg-white shadow-lg max-h-60 overflow-y-auto"
                    >
                        @foreach ($bankOptions as $bank)
                            <button
                                type="button"
                                wire:click="selectBank('{{ $bank['code'] }}')"
                                class="flex w-full items-center justify-between px-3 py-2 text-xs cursor-pointer
                                    @if($bank_name === $bank['code'])
                                        bg-[#1d428a]/5 text-[#1d428a]
                                    @else
                                        text-slate-700 hover:bg-slate-50
                                    @endif"
                            >
                                <div class="flex items-center gap-3">
                                    <img
                                        src="{{ asset('images/' . $bank['logo']) }}"
                                        alt="{{ $bank['name'] }}"
                                        class="h-5 w-auto"
                                    >
                                    <span>{{ $bank['name'] }}</span>
                                </div>

                                @if($bank_name === $bank['code'])
                                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-[#1d428a] text-white text-[10px]">
                                        ✓
                                    </span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            @error('bank_name')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Nama pemilik rekening --}}
        <div>
            <label class="block text-xs font-medium text-slate-700 mb-1.5">
                Nama pemilik rekening
            </label>
            <input
                type="text"
                wire:model.defer="bank_account_name"
                class="block w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#1d428a] focus:ring-[#1d428a]"
                placeholder="Sesuai buku tabungan"
            >
            @error('bank_account_name')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Nomor rekening --}}
        <div>
            <label class="block text-xs font-medium text-slate-700 mb-1.5">
                Nomor rekening
            </label>
            <input
                type="text"
                wire:model.defer="bank_account_number"
                class="block w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#1d428a] focus:ring-[#1d428a]"
                placeholder="Nomor rekening aktif"
            >
            @error('bank_account_number')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

                        @else
            {{-- Form e-wallet --}}
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1.5">
                        Pilih e-wallet
                    </label>

                    @php
                        $selectedEwallet = collect($ewalletOptions)->firstWhere('code', $ewallet_provider);
                    @endphp

                    <div class="relative">
                        {{-- Tombol dropdown --}}
                        <button
                            type="button"
                            wire:click="toggleEwalletDropdown"
                            class="flex w-full items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 hover:bg-slate-100 cursor-pointer"
                        >
                            <div class="flex items-center gap-3">
                                @if($selectedEwallet)
                                    <img
                                        src="{{ asset('images/' . $selectedEwallet['logo']) }}"
                                        alt="{{ $selectedEwallet['name'] }}"
                                        class="h-6 w-auto"
                                    >
                                    <span>{{ $selectedEwallet['name'] }}</span>
                                @else
                                    <div class="h-6 w-10 rounded-md bg-slate-100 flex items-center justify-center text-[10px] text-slate-400">
                                        E-wallet
                                    </div>
                                    <span class="text-slate-400 text-xs">
                                        Pilih e-wallet tujuan
                                    </span>
                                @endif
                            </div>

                            <svg
                                class="h-4 w-4 text-slate-400"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path fill-rule="evenodd"
                                    d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>

                        {{-- List dropdown --}}
                        @if($ewalletDropdownOpen)
                            <div
                                class="absolute z-20 mt-1 w-full rounded-xl border border-slate-200 bg-white shadow-lg max-h-60 overflow-y-auto"
                            >
                                @foreach ($ewalletOptions as $ewallet)
                                    <button
                                        type="button"
                                        wire:click="selectEwallet('{{ $ewallet['code'] }}')"
                                        class="flex w-full items-center justify-between px-3 py-2 text-xs cursor-pointer
                                            @if($ewallet_provider === $ewallet['code'])
                                                bg-[#1d428a]/5 text-[#1d428a]
                                            @else
                                                text-slate-700 hover:bg-slate-50
                                            @endif"
                                    >
                                        <div class="flex items-center gap-3">
                                            <img
                                                src="{{ asset('images/' . $ewallet['logo']) }}"
                                                alt="{{ $ewallet['name'] }}"
                                                class="h-5 w-auto"
                                            >
                                            <span>{{ $ewallet['name'] }}</span>
                                        </div>

                                        @if($ewallet_provider === $ewallet['code'])
                                            <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-[#1d428a] text-white text-[10px]">
                                                ✓
                                            </span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    @error('ewallet_provider')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1.5">
                        Nomor e-wallet
                    </label>
                    <input
                        type="text"
                        wire:model.defer="ewallet_number"
                        class="block w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#1d428a] focus:ring-[#1d428a]"
                        placeholder="Nomor HP yang terdaftar"
                    >
                    @error('ewallet_number')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        @endif




                    <p class="mt-4 text-[11px] text-slate-400">
                        Data ini akan digunakan setiap kali kamu mengajukan penarikan saldo. Pastikan informasi benar dan masih aktif.
                    </p>

                    <div class="mt-4">
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-full bg-[#1d428a] px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#163268] focus:outline-none focus:ring-2 focus:ring-[#1d428a] focus:ring-offset-1 focus:ring-offset-slate-50 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer"
                            wire:loading.attr="disabled"
                            wire:target="savePayoutMethod"
                        >
                            <span wire:loading.remove wire:target="savePayoutMethod">
                                Simpan metode penarikan
                            </span>
                            <span wire:loading wire:target="savePayoutMethod">
                                Menyimpan...
                            </span>
                        </button>
                    </div>
                </form>
            </section>

            {{-- Kanan: Ajukan penarikan & riwayat --}}
            <section class="space-y-4">
                {{-- Form penarikan --}}
                <form wire:submit.prevent="submitWithdrawal" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 space-y-4">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900 mb-1">
                            Ajukan penarikan saldo
                        </h2>
                        <p class="text-xs text-slate-500">
                            Minimal penarikan Rp 10.000. Pastikan metode penarikanmu sudah diisi dengan benar.
                        </p>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1.5">
                                Jumlah penarikan
                            </label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-xs text-slate-400">
                                    Rp
                                </span>
                                <input
                                    type="number"
                                    min="0"
                                    step="1000"
                                    wire:model.defer="withdraw_amount"
                                    class="block w-full rounded-xl border-slate-200 bg-slate-50 pl-9 pr-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#1d428a] focus:ring-[#1d428a]"
                                    placeholder="Masukkan nominal"
                                >
                            </div>
                            @error('withdraw_amount')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-[11px] text-slate-400">
                                Saldo tersedia: <span class="font-semibold text-slate-700">Rp {{ number_format($availableBalance, 0, ',', '.') }}</span>
                            </p>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1.5">
                                Catatan (opsional)
                            </label>
                            <textarea
                                rows="2"
                                wire:model.defer="withdraw_note"
                                class="block w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#1d428a] focus:ring-[#1d428a]"
                                placeholder="Contoh: jadwalkan pencairan di hari kerja."
                            ></textarea>
                            @error('withdraw_note')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-1 focus:ring-offset-slate-50 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer"
                        wire:loading.attr="disabled"
                        wire:target="submitWithdrawal"
                    >
                        <span wire:loading.remove wire:target="submitWithdrawal">
                            Ajukan penarikan
                        </span>
                        <span wire:loading wire:target="submitWithdrawal">
                            Mengajukan...
                        </span>
                    </button>
                </form>

                {{-- Riwayat penarikan --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-sm font-semibold text-slate-900">
                            Riwayat penarikan
                        </h2>
                        {{-- nanti bisa tambahkan link "Lihat semua" --}}
                    </div>

                    @if ($withdrawals->isEmpty())
                        <p class="text-xs text-slate-500">
                            Belum ada riwayat penarikan. Setelah kamu mengajukan penarikan, daftar riwayat akan muncul di sini.
                        </p>
                    @else
                        <div class="space-y-2 max-h-72 overflow-y-auto">
                            @foreach ($withdrawals as $withdraw)
                                <div class="flex items-center justify-between rounded-xl border border-slate-100 px-3 py-2.5 text-xs">
                                    <div class="space-y-0.5">
                                        <p class="font-medium text-slate-900">
                                            Rp {{ number_format($withdraw->amount, 0, ',', '.') }}
                                        </p>
                                        <p class="text-[11px] text-slate-500">
                                            {{ $withdraw->method_label }}
                                        </p>
                                        <p class="text-[11px] text-slate-400">
                                            {{ $withdraw->created_at?->format('d M Y, H:i') }}
                                        </p>
                                    </div>
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold
                                        @class([
                                            'bg-amber-50 text-amber-700'   => $withdraw->status === 'pending',
                                            'bg-emerald-50 text-emerald-700' => in_array($withdraw->status, ['approved', 'paid']),
                                            'bg-red-50 text-red-700'       => $withdraw->status === 'rejected',
                                            'bg-slate-100 text-slate-600'  => ! in_array($withdraw->status, ['pending','approved','paid','rejected']),
                                        ])
                                    ">
                                        {{ ucfirst($withdraw->status) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </main>
</div>
