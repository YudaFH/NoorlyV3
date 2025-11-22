<div class="min-h-screen bg-slate-50 pt-20 flex">
    {{-- SIDEBAR --}}
    @include('livewire.creator.sidebar', ['sidebarOpen' => $sidebarOpen])

    {{-- MAIN --}}
    <main class="flex-1 px-4 sm:px-6 lg:px-8 pb-10 overflow-y-auto">
        {{-- HEADER --}}
        <header class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-slate-900">
                    Audience & pembeli
                </h1>
                <p class="mt-1 text-sm text-slate-500 max-w-2xl">
                    Lihat siapa saja yang sudah membeli kontenmu, performa penjualan, dan detail tiap transaksi.
                </p>
            </div>
        </header>

        {{-- RINGKASAN METRIK --}}
        <section class="grid gap-4 md:grid-cols-4 mb-6">
            <article class="bg-white rounded-2xl border border-slate-100 shadow-sm px-5 py-4">
                <p class="text-xs font-medium text-slate-500">Pembeli unik</p>
                <p class="mt-3 text-2xl font-semibold text-slate-900">{{ $totalBuyers }}</p>
                <p class="mt-1 text-[11px] text-slate-400">
                    Pengguna berbeda yang pernah membeli kontenmu.
                </p>
            </article>

            <article class="bg-white rounded-2xl border border-slate-100 shadow-sm px-5 py-4">
                <p class="text-xs font-medium text-slate-500">Pembeli baru 30 hari terakhir</p>
                <p class="mt-3 text-2xl font-semibold text-slate-900">{{ $newBuyers30d }}</p>
                <p class="mt-1 text-[11px] text-slate-400">
                    Pengguna yang pertama kali membeli kontenmu dalam 30 hari terakhir.
                </p>
            </article>

            <article class="bg-white rounded-2xl border border-slate-100 shadow-sm px-5 py-4">
                <p class="text-xs font-medium text-slate-500">Total transaksi</p>
                <p class="mt-3 text-2xl font-semibold text-slate-900">{{ $totalTransactions }}</p>
                <p class="mt-1 text-[11px] text-slate-400">
                    Jumlah pembayaran berhasil untuk semua konten.
                </p>
            </article>

            <article class="bg-white rounded-2xl border border-slate-100 shadow-sm px-5 py-4">
                <p class="text-xs font-medium text-slate-500">Rata-rata nilai transaksi</p>
                <p class="mt-3 text-xl font-semibold text-emerald-600">
                    Rp {{ number_format($avgOrderValue, 0, ',', '.') }}
                </p>
                <p class="mt-1 text-[11px] text-slate-400">
                    Total omzet dibagi jumlah transaksi berhasil.
                </p>
            </article>
        </section>

        {{-- FILTER BAR --}}
        <section class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            {{-- Search --}}
            <div class="w-full md:max-w-xs">
                <label class="sr-only" for="search-buyers">Cari pembeli</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 103.473 9.8l3.613 3.614a.75.75 0 101.06-1.06l-3.614-3.613A5.5 5.5 0 009 3.5zm-4 5.5a4 4 0 118 0 4 4 0 01-8 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    <input
                        id="search-buyers"
                        type="text"
                        wire:model.debounce.400ms="search"
                        class="block w-full rounded-full border border-slate-200 bg-white pl-8 pr-3 py-1.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#1d428a] focus:ring-[#1d428a]"
                        placeholder="Cari nama atau email pembeli..."
                    >
                </div>
            </div>

            {{-- Filter kanan --}}
            <div class="flex flex-wrap items-center gap-2 justify-end text-[11px]">
                {{-- Range --}}
                <div class="flex items-center gap-1">
                    <span class="text-slate-500">Periode:</span>
                    <select
                        wire:model="range"
                        class="cursor-pointer rounded-full border border-slate-200 bg-white px-3 py-1.5 text-[11px] text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                    >
                        <option value="7d">7 hari</option>
                        <option value="30d">30 hari</option>
                        <option value="90d">90 hari</option>
                        <option value="all">Semua waktu</option>
                    </select>
                </div>

                {{-- Konten --}}
                <div class="flex items-center gap-1">
                    <span class="text-slate-500">Konten:</span>
                    <select
                        wire:model="contentId"
                        class="cursor-pointer rounded-full border border-slate-200 bg-white px-3 py-1.5 text-[11px] text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                    >
                        <option value="">Semua konten</option>
                        @foreach($creatorContents as $c)
                            <option value="{{ $c->id }}">{{ $c->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </section>

        {{-- TABEL TRANSAKSI --}}
        <section class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50">
                        <tr class="text-[11px] uppercase tracking-wide text-slate-500">
                            <th class="px-4 py-2 text-left">Pembeli</th>
                            <th class="px-4 py-2 text-left">Konten</th>
                            <th class="px-4 py-2 text-right">Harga</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($transactions as $order)
                            @php
                                $buyer  = $order->buyer;
                                $content = $order->content;
                                $initial = $buyer ? strtoupper(mb_substr($buyer->name, 0, 1)) : '?';
                            @endphp
                            <tr class="hover:bg-slate-50/60">
                                {{-- Pembeli --}}
                                <td class="px-4 py-3 align-top">
                                    <div class="flex items-center gap-2">
                                        <div class="h-8 w-8 rounded-full bg-[#fef6e0] flex items-center justify-center text-[11px] font-semibold text-[#d4a116]">
                                            {{ $initial }}
                                        </div>
                                        <div>
                                            <p class="text-[13px] font-medium text-slate-900">
                                                {{ $buyer?->name ?? 'Pengguna' }}
                                            </p>
                                            <p class="text-[11px] text-slate-400">
                                                {{ $buyer?->email ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Konten --}}
                                <td class="px-4 py-3 align-top">
                                    <div class="flex flex-col">
                                        <p class="text-[13px] font-medium text-slate-900 truncate max-w-xs">
                                            {{ $content?->title ?? '-' }}
                                        </p>
                                        <p class="text-[11px] text-slate-400">
                                            ID: {{ $order->content_id }}
                                        </p>
                                    </div>
                                </td>

                                {{-- Harga --}}
                                <td class="px-4 py-3 align-top text-right">
                                    <p class="text-[13px] font-semibold text-slate-900">
                                        Rp {{ number_format($order->amount ?? 0, 0, ',', '.') }}
                                    </p>
                                </td>

                                {{-- Status --}}
                                <td class="px-4 py-3 align-top">
                                    @if($order->status === 'paid')
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-medium text-emerald-700">
                                            Berhasil
                                        </span>
                                    @elseif($order->status === 'pending')
                                        <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-[11px] font-medium text-amber-700">
                                            Menunggu pembayaran
                                        </span>
                                    @elseif($order->status === 'failed')
                                        <span class="inline-flex items-center rounded-full bg-rose-50 px-2.5 py-0.5 text-[11px] font-medium text-rose-700">
                                            Gagal
                                        </span>
                                    @elseif($order->status === 'refunded')
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-medium text-slate-700">
                                            Refund
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-medium text-slate-600">
                                            {{ $order->status }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Tanggal --}}
                                <td class="px-4 py-3 align-top">
                                    <p class="text-[13px] text-slate-900">
                                        {{ optional($order->paid_at)->format('d M Y H:i') ?? '-' }}
                                    </p>
                                    <p class="text-[11px] text-slate-400">
                                        {{ optional($order->paid_at)->diffForHumans() ?? '' }}
                                    </p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-4 py-6 text-center text-[13px] text-slate-500" colspan="5">
                                    Belum ada transaksi pada periode & filter yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($transactions->hasPages())
                <div class="border-t border-slate-100 px-4 py-3">
                    {{ $transactions->links() }}
                </div>
            @endif
        </section>

        {{-- Keterangan kecil --}}
        <p class="mt-3 text-[11px] text-slate-400">
            Data di atas hanya menampilkan transaksi yang statusnya <span class="font-medium">berhasil (paid)</span>. 
            Pembeli cukup satu kali melakukan pembayaran untuk mendapatkan akses konten selamanya.
        </p>
    </main>
</div>
