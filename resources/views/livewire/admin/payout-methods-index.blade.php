{{-- resources/views/livewire/admin/payout-methods-index.blade.php --}}
<div class="flex h-screen bg-slate-50">
    @include('livewire.admin.sidebar', ['sidebarOpen' => $sidebarOpen])

    <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8 overflow-y-auto">
        <header class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-slate-900">
                    Metode penarikan kreator
                </h1>
                <p class="mt-1 text-sm text-slate-500 max-w-2xl">
                    Review dan verifikasi rekening bank atau e-wallet yang diajukan kreator sebelum mereka bisa menerima penarikan saldo.
                </p>

                <div class="mt-3 flex flex-wrap gap-3 text-[11px] text-slate-500">
                    <span>Pending: <span class="font-semibold text-amber-600">{{ $totalPending }}</span></span>
                    <span>Terverifikasi: <span class="font-semibold text-emerald-600">{{ $totalVerified }}</span></span>
                    <span>Ditolak: <span class="font-semibold text-rose-600">{{ $totalRejected }}</span></span>
                </div>
            </div>

            @if(session('status_admin_payout'))
                <div class="rounded-full bg-emerald-50 border border-emerald-100 px-3 py-1 text-[11px] text-emerald-700">
                    {{ session('status_admin_payout') }}
                </div>
            @endif
        </header>

        {{-- Filter + search --}}
        <section class="mb-4 rounded-2xl border border-slate-100 bg-white px-4 py-3 shadow-sm">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div class="w-full md:max-w-sm">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 103.473 9.8l3.613 3.614a.75.75 0 101.06-1.06l-3.614-3.613A5.5 5.5 0 009 3.5zm-4 5.5a4 4 0 118 0 4 4 0 01-8 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <input
                            type="text"
                            wire:model.debounce.400ms="search"
                            class="block w-full rounded-full border border-slate-200 bg-slate-50 pl-8 pr-3 py-1.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#1d428a] focus:ring-[#1d428a]"
                            placeholder="Cari nama kreator, email, provider, atau nomor rekening..."
                        >
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2 justify-end text-[11px]">
                    <div class="flex items-center gap-1">
                        <span class="text-slate-500">Status:</span>
                        <select
                            wire:model="statusFilter"
                            class="cursor-pointer rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-[11px] text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                        >
                            <option value="pending">Pending</option>
                            <option value="verified">Terverifikasi</option>
                            <option value="rejected">Ditolak</option>
                            <option value="all">Semua</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-1">
                        <span class="text-slate-500">Tipe:</span>
                        <select
                            wire:model="typeFilter"
                            class="cursor-pointer rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-[11px] text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                        >
                            <option value="all">Semua</option>
                            <option value="bank">Bank</option>
                            <option value="ewallet">E-wallet</option>
                        </select>
                    </div>
                </div>
            </div>
        </section>

        @if($methods->count())
            <section class="space-y-3">
                @foreach($methods as $method)
                    @php
                        $creator    = $method->user;
                        $isBank     = $method->type === 'bank';
                        $typeLabel  = $isBank ? 'Rekening bank' : 'E-wallet';
                        $provider   = $method->provider_name ?? $method->provider_code ?? '-';

                        // mapping logo berdasarkan provider_code
                        $logoFile = null;

                        if ($isBank) {
                            $logoFile = match ($method->provider_code) {
                                'BCA'     => 'banks/bca.png',
                                'BNI'     => 'banks/bni.png',
                                'BRI'     => 'banks/bri.png',
                                'MANDIRI' => 'banks/mandiri.png',
                                'CIMB'    => 'banks/cimb.png',
                                'PERMATA' => 'banks/permata.png',
                                'JAGO'    => 'banks/jago.png',
                                default   => null,
                            };
                        } else {
                            $logoFile = match ($method->provider_code) {
                                'DANA'    => 'ewallet/dana.png',
                                'OVO'     => 'ewallet/ovo.png',
                                'GOPAY'   => 'ewallet/gopay.png',
                                'SHOPEE'  => 'ewallet/shopeepay.png',
                                'LINKAJA' => 'ewallet/linkaja.png',
                                default   => null,
                            };
                        }

                        $logoUrl = $logoFile ? asset('images/'.$logoFile) : null;
                    @endphp

                    <article class="rounded-2xl border border-slate-100 bg-white px-4 py-3 shadow-sm flex flex-col md:flex-row md:justify-between gap-3">
                        <div class="flex gap-3">
                            {{-- Logo provider --}}
                            <div class="mt-0.5 h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center overflow-hidden">
                                @if($logoUrl)
                                    <img
                                        src="{{ $logoUrl }}"
                                        alt="{{ $provider }}"
                                        class="h-8 w-8 object-contain"
                                    >
                                @else
                                    <span class="text-xs font-semibold text-slate-500">
                                        {{ strtoupper(substr($provider, 0, 3)) }}
                                    </span>
                                @endif
                            </div>

                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="text-sm font-semibold text-slate-900">
                                        {{ $provider }} • {{ $typeLabel }}
                                    </p>

                                    @if($method->status === 'pending')
                                        <span class="rounded-full bg-amber-50 px-2 py-0.5 text-[11px] text-amber-700">
                                            Pending verifikasi
                                        </span>
                                    @elseif($method->status === 'verified')
                                        <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] text-emerald-700">
                                            Terverifikasi
                                        </span>
                                    @elseif($method->status === 'rejected')
                                        <span class="rounded-full bg-rose-50 px-2 py-0.5 text-[11px] text-rose-700">
                                            Ditolak
                                        </span>
                                    @endif

                                    @if($method->is_default && $method->status === 'verified')
                                        <span class="rounded-full bg-sky-50 px-2 py-0.5 text-[11px] text-sky-700">
                                            Metode utama
                                        </span>
                                    @endif
                                </div>

                                <p class="text-[11px] text-slate-600">
                                    Atas nama: <span class="font-medium">{{ $method->account_name }}</span> •
                                    Nomor: <span class="font-mono">{{ $method->account_number }}</span>
                                </p>

                                @if($creator)
                                    <p class="text-[11px] text-slate-500">
                                        Kreator:
                                        <span class="font-medium text-slate-800">{{ $creator->name }}</span>
                                        <span class="text-slate-400">({{ $creator->email }})</span>
                                    </p>
                                @endif

                                @if($method->status_note)
                                    <p class="text-[11px] text-rose-600">
                                        Catatan admin: {{ $method->status_note }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div class="flex flex-col items-end gap-2 text-[11px]">
                            @if($method->status === 'pending')
                                <div class="flex flex-wrap gap-2">
                                    <button
                                        type="button"
                                        wire:click="approve({{ $method->id }})"
                                        class="cursor-pointer inline-flex items-center rounded-full bg-emerald-600 px-4 py-1.5 text-[11px] font-semibold text-white shadow-sm hover:bg-emerald-700"
                                    >
                                        Verifikasi
                                    </button>

                                    <button
                                        type="button"
                                        x-data
                                        @click="
                                            const reason = prompt('Alasan penolakan (opsional):', '');
                                            if (reason !== null) {
                                                Livewire.dispatch('callMethod', { method: 'reject', params: [{{ $method->id }}, reason] });
                                            }
                                        "
                                        class="cursor-pointer inline-flex items-center rounded-full bg-white px-4 py-1.5 text-[11px] font-semibold text-rose-600 border border-rose-200 hover:bg-rose-50"
                                    >
                                        Tolak
                                    </button>
                                </div>
                            @else
                                <p class="text-[11px] text-slate-400">
                                    Diperbarui {{ optional($method->updated_at)->diffForHumans() ?? '-' }}
                                </p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </section>

            <div class="mt-4">
                {{ $methods->links() }}
            </div>
        @else
            <section class="mt-8 flex flex-col items-center justify-center text-center">
                <div class="mb-4 h-16 w-16 rounded-full bg-slate-100 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M4 3a2 2 0 00-2 2v9.5A1.5 1.5 0 003.5 16h13a1.5 1.5 0 001.5-1.5V7a2 2 0 00-2-2h-5.586a2 2 0 01-1.414-.586L7.586 3H4z" />
                    </svg>
                </div>
                <h2 class="text-sm font-semibold text-slate-900 mb-1">
                    Belum ada pengajuan metode penarikan
                </h2>
                <p class="text-xs text-slate-500 max-w-sm">
                    Kreator yang mengisi formulir metode penarikan (rekening / e-wallet) akan muncul di sini untuk kamu review dan verifikasi.
                </p>
            </section>
        @endif
    </main>
</div>
