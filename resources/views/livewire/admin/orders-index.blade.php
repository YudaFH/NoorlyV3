@php
    use Illuminate\Support\Str;
@endphp

<div class="flex h-screen bg-slate-50">
    {{-- SIDEBAR ADMIN --}}
    @include('livewire.admin.sidebar', ['sidebarOpen' => $sidebarOpen])

    {{-- MAIN --}}
    <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8 overflow-y-auto">
        {{-- HEADER --}}
        <header class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-slate-900">
                    Order &amp; pembayaran
                </h1>
                <p class="mt-1 text-sm text-slate-500 max-w-2xl">
                    Pantau transaksi pembelian konten, status pembayaran, dan performa omzet di Noorly.
                </p>

                <div class="mt-3 flex flex-wrap gap-3 text-[11px] text-slate-500">
                    <span>Semua order: <span class="font-semibold text-slate-700">{{ $totalAll }}</span></span>
                    <span>Berhasil dibayar: <span class="font-semibold text-emerald-600">{{ $totalPaid }}</span></span>
                    <span>Pending: <span class="font-semibold text-amber-600">{{ $totalPending }}</span></span>
                    <span>Gagal / kedaluwarsa: <span class="font-semibold text-rose-600">{{ $totalFailed }}</span></span>
                    <span>Total omzet (paid): <span class="font-semibold text-slate-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span></span>
                </div>
            </div>

            <div class="flex flex-col items-end gap-2">
                @if (session('status_admin_orders'))
                    <div class="rounded-full bg-emerald-50 border border-emerald-100 px-3 py-1 text-[11px] text-emerald-700">
                        {{ session('status_admin_orders') }}
                    </div>
                @endif
            </div>
        </header>

        {{-- FILTER BAR --}}
        <section class="mb-4 rounded-2xl border border-slate-100 bg-white px-4 py-3 shadow-sm">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                {{-- Search --}}
                <div class="w-full md:max-w-sm">
                    <label class="sr-only" for="search-orders">Cari order</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 103.473 9.8l3.613 3.614a.75.75 0 101.06-1.06l-3.614-3.613A5.5 5.5 0 009 3.5zm-4 5.5a4 4 0 118 0 4 4 0 01-8 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <input
                            id="search-orders"
                            type="text"
                            wire:model.debounce.400ms="search"
                            class="block w-full rounded-full border border-slate-200 bg-slate-50 pl-8 pr-3 py-1.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#1d428a] focus:ring-[#1d428a]"
                            placeholder="Cari ID order, nama pembeli, email, atau judul konten..."
                        >
                    </div>
                </div>

                {{-- Filters --}}
                <div class="flex flex-wrap items-center gap-2 justify-end text-[11px]">
                    {{-- Status --}}
                    <div class="flex items-center gap-1">
                        <span class="text-slate-500">Status:</span>
                        <select
                            wire:model.defer="statusFilter"
                            class="cursor-pointer rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-[11px] text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                        >
                            <option value="all">Semua</option>
                            <option value="paid">Berhasil dibayar</option>
                            <option value="pending">Pending</option>
                            <option value="failed">Gagal / kedaluwarsa</option>
                            <option value="refunded">Refunded</option>
                        </select>
                    </div>

                    {{-- Metode pembayaran --}}
                    <div class="flex items-center gap-1">
                        <span class="text-slate-500">Metode:</span>
                        <select
                            wire:model.defer="paymentFilter"
                            class="cursor-pointer rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-[11px] text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                        >
                            <option value="all">Semua</option>
                            <option value="bank_transfer">Bank transfer</option>
                            <option value="ewallet">E-Wallet</option>
                            <option value="va">Virtual Account</option>
                            <option value="qris">QRIS</option>
                            {{-- Tambah opsi lain sesuai implementasi Stripe/Midtrans-mu --}}
                        </select>
                    </div>

                    {{-- Rentang waktu --}}
                    <div class="flex items-center gap-1">
                        <span class="text-slate-500">Waktu:</span>
                        <select
                            wire:model.defer="dateFilter"
                            class="cursor-pointer rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-[11px] text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                        >
                            <option value="all">Semua</option>
                            <option value="today">Hari ini</option>
                            <option value="7d">7 hari</option>
                            <option value="30d">30 hari</option>
                            <option value="90d">90 hari</option>
                        </select>
                    </div>

                    {{-- Sort --}}
                    <div class="flex items-center gap-1">
                        <span class="text-slate-500">Urutkan:</span>
                        <select
                            wire:model.defer="sort"
                            class="cursor-pointer rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-[11px] text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                        >
                            <option value="latest">Terbaru</option>
                            <option value="oldest">Terlama</option>
                            <option value="amount_high">Nominal tertinggi</option>
                            <option value="amount_low">Nominal terendah</option>
                        </select>
                    </div>

                    {{-- Tombol Terapkan --}}
                    <button
                        type="button"
                        wire:click="applyFilters"
                        class="cursor-pointer inline-flex items-center gap-1 rounded-full bg-[#1d428a] px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm hover:bg-[#163268]"
                    >
                        Terapkan
                    </button>
                </div>
            </div>
        </section>

        {{-- LIST ORDER --}}
        @if ($orders->count() > 0)
            <section class="space-y-3">
                @foreach ($orders as $order)
                    @php
                        $buyer   = $order->user ?? null;
                        $content = $order->content ?? null;
                        $creator = $content?->user;

                        $contentUrl  = $content ? route('contents.show', $content->slug) : null;
                        $creatorUrl  = $creator ? route('creator.public.show', $creator->id) : null;
                        $statusLabel = $order->status ?? 'pending';
                    @endphp

                    <article class="rounded-2xl border border-slate-100 bg-white px-4 py-3 shadow-sm flex flex-col gap-3">
                        {{-- BAR ATAS --}}
                        <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
                            <div class="flex items-start gap-3">
                                {{-- Icon --}}
                                <div class="mt-0.5 h-10 w-10 flex items-center justify-center rounded-xl bg-[#fef6e0] text-[#d4a116] text-xs font-semibold">
                                    {{ $content ? strtoupper(substr($content->type ?? 'K', 0, 2)) : 'OR' }}
                                </div>

                                <div class="space-y-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h2 class="text-sm font-semibold text-slate-900">
                                            Order #{{ $order->id }}
                                            @if(!empty($order->code))
                                                <span class="text-[11px] text-slate-400">({{ $order->code }})</span>
                                            @endif
                                        </h2>

                                        {{-- Badge status --}}
                                        @if($statusLabel === 'paid')
                                            <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] text-emerald-700">
                                                Berhasil dibayar
                                            </span>
                                        @elseif($statusLabel === 'pending')
                                            <span class="rounded-full bg-amber-50 px-2 py-0.5 text-[11px] text-amber-700">
                                                Pending
                                            </span>
                                        @elseif(in_array($statusLabel, ['failed', 'expired']))
                                            <span class="rounded-full bg-rose-50 px-2 py-0.5 text-[11px] text-rose-700">
                                                Gagal / kedaluwarsa
                                            </span>
                                        @elseif($statusLabel === 'refunded')
                                            <span class="rounded-full bg-sky-50 px-2 py-0.5 text-[11px] text-sky-700">
                                                Refunded
                                            </span>
                                        @endif
                                    </div>

                                    <p class="text-[11px] text-slate-500">
                                        Konten:
                                        @if($content && $contentUrl)
                                            <a href="{{ $contentUrl }}" target="_blank" class="font-medium text-[#1d428a] hover:underline">
                                                {{ $content->title }}
                                            </a>
                                        @else
                                            <span class="font-medium text-slate-700">
                                                (konten tidak ditemukan)
                                            </span>
                                        @endif
                                    </p>

                                    <p class="text-[11px] text-slate-500">
                                        Pembeli:
                                        @if($buyer)
                                            <span class="font-medium text-slate-700">{{ $buyer->name }}</span>
                                            <span class="text-slate-400">({{ $buyer->email }})</span>
                                        @else
                                            <span class="font-medium text-slate-700">(user tidak ditemukan)</span>
                                        @endif
                                    </p>

                                    @if($creator)
                                        <p class="text-[11px] text-slate-500">
                                            Kreator:
                                            @if($creatorUrl)
                                                <a href="{{ $creatorUrl }}" target="_blank" class="font-medium text-[#1d428a] hover:underline">
                                                    {{ $creator->name }}
                                                </a>
                                            @else
                                                <span class="font-medium text-slate-700">{{ $creator->name }}</span>
                                            @endif
                                        </p>
                                    @endif

                                    <p class="text-[11px] text-slate-400">
                                        Dibuat: {{ optional($order->created_at)->diffForHumans() ?? '-' }}
                                        @if($order->paid_at)
                                            • Dibayar: {{ optional($order->paid_at)->diffForHumans() }}
                                        @endif
                                    </p>
                                </div>
                            </div>

                            {{-- KANAN: nominal & aksi --}}
                            <div class="flex flex-col items-end gap-2 text-[11px]">
                                <div class="text-right">
                                    <p class="text-[10px] uppercase tracking-wide text-slate-400">Total pembayaran</p>
                                    <p class="text-sm font-semibold text-slate-900">
                                        Rp {{ number_format($order->total_amount ?? 0, 0, ',', '.') }}
                                    </p>
                                    <p class="mt-0.5 text-[11px] text-slate-500">
                                        Metode:
                                        <span class="font-medium text-slate-700">
                                            {{ $order->payment_method ?? '-' }}
                                        </span>
                                        @if(!empty($order->payment_status))
                                            • Status gateway:
                                            <span class="font-medium text-slate-700">
                                                {{ $order->payment_status }}
                                            </span>
                                        @endif
                                    </p>
                                </div>

                                {{-- Ubah status secara cepat --}}
                                <div class="flex items-center gap-1">
                                    <span class="text-slate-500">Ubah status:</span>
                                    <select
                                        wire:change="updateStatus({{ $order->id }}, $event.target.value)"
                                        class="cursor-pointer rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-[11px] text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                                    >
                                        <option value="">Pilih</option>
                                        <option value="paid">Berhasil dibayar</option>
                                        <option value="pending">Pending</option>
                                        <option value="failed">Gagal</option>
                                        <option value="expired">Kedaluwarsa</option>
                                        <option value="refunded">Refunded</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </section>

            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        @else
            {{-- EMPTY STATE --}}
            <section class="mt-8 flex flex-col items-center justify-center text-center">
                <div class="mb-4 h-16 w-16 rounded-full bg-slate-100 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M4 3a2 2 0 00-2 2v9.5A1.5 1.5 0 003.5 16h13a1.5 1.5 0 001.5-1.5V7a2 2 0 00-2-2h-5.586a2 2 0 01-1.414-.586L7.586 3H4z" />
                    </svg>
                </div>
                <h2 class="text-sm font-semibold text-slate-900 mb-1">
                    Belum ada order yang ditampilkan
                </h2>
                <p class="text-xs text-slate-500 max-w-sm">
                    Order yang dibuat pengguna akan muncul di sini. Coba ubah filter, atau lakukan transaksi dummy
                    untuk memastikan integrasi payment gateway berjalan dengan baik.
                </p>
            </section>
        @endif
    </main>
</div>
