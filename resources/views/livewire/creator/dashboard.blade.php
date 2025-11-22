{{-- resources/views/livewire/creator/dashboard.blade.php --}}
<div class="flex h-screen bg-slate-50">
    {{-- SIDEBAR KIRI (full height) --}}
    @include('livewire.creator.sidebar', ['sidebarOpen' => $sidebarOpen])

    {{-- KONTEN KANAN --}}
    <main class="flex-1 px-4 sm:px-6 lg:px-8 py-10 overflow-y-auto">
        {{-- Header halaman --}}
        <header class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-slate-900">
                    Dashboard Kreator
                </h1>
                <p class="mt-1 text-sm text-slate-500 max-w-2xl">
                    Selamat datang kembali, {{ strtok(auth()->user()->name, ' ') }}.
                    Di sini kamu bisa melihat performa konten dan mulai membuat karya baru di Noorly.
                </p>
            </div>

            {{-- Aksi kanan: lihat publik + filter + buat konten --}}
            <div class="flex flex-wrap items-center gap-2 justify-end">
                {{-- Lihat halaman publik --}}
                <a
                    href="{{ route('creator.public.show', auth()->id()) }}"
                    target="_blank"
                    class="cursor-pointer inline-flex items-center gap-1 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-[11px] md:text-xs font-medium text-slate-700 hover:bg-slate-50"
                >
                    Lihat halaman publik
                </a>

                {{-- Filter range waktu (sementara statis: 30 hari terakhir) --}}
                <button
                    type="button"
                    class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3.5 py-1.5 text-[11px] md:text-xs font-medium text-slate-700 hover:bg-slate-50 cursor-pointer"
                >
                    30 hari terakhir
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                    </svg>
                </button>

                {{-- Buat konten baru --}}
                <a
                    href="{{ route('creator.contents.create') }}"
                    class="inline-flex items-center gap-2 rounded-full bg-[#1d428a] px-4 py-2 text-xs md:text-sm font-semibold text-white shadow-sm hover:bg-[#163268] cursor-pointer"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.75 4a.75.75 0 00-1.5 0v4.25H5a.75.75 0 000 1.5h4.25V14a.75.75 0 001.5 0v-4.25H15a.75.75 0 000-1.5h-4.25V4z" />
                    </svg>
                    <span>Buat konten baru</span>
                </a>
            </div>
        </header>

        {{-- Stat cards --}}
        @php
            $stats = [
                'total_contents'   => $totalContents    ?? 0,
                'total_buyers'     => $totalBuyers      ?? 0,
                'estimated_income' => $estimatedIncome  ?? 0,
            ];
        @endphp

        <section class="grid gap-4 md:grid-cols-3 mb-6">
            <article class="bg-white rounded-2xl border border-slate-100 shadow-sm px-5 py-4 flex flex-col justify-between">
                <p class="text-xs font-medium text-slate-500">Konten aktif</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">
                    {{ $stats['total_contents'] }}
                </p>
                <p class="mt-1 text-[11px] text-slate-400">
                    Jumlah produk digital / konten berbayar yang sudah terpublikasi.
                </p>
            </article>

            <article class="bg-white rounded-2xl border border-slate-100 shadow-sm px-5 py-4 flex flex-col justify-between">
                <p class="text-xs font-medium text-slate-500">Total pembeli</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">
                    {{ $stats['total_buyers'] }}
                </p>
                <p class="mt-1 text-[11px] text-slate-400">
                    Pengguna unik yang pernah membeli karya kamu.
                </p>
            </article>

            <article class="bg-white rounded-2xl border border-slate-100 shadow-sm px-5 py-4 flex flex-col justify-between">
                <p class="text-xs font-medium text-slate-500">Perkiraan pendapatan</p>
                <p class="mt-3 text-xl font-semibold text-emerald-600">
                    Rp {{ number_format($stats['estimated_income'], 0, ',', '.') }}
                </p>
                <p class="mt-1 text-[11px] text-slate-400">
                    Akumulasi transaksi yang sudah berhasil (belum termasuk biaya platform & pajak).
                </p>
            </article>
        </section>

        {{-- Konten lanjutan --}}
        <section class="grid gap-6 lg:grid-cols-3">
            <article class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-slate-900">
                        Konten terbaru kamu
                    </h2>
                    <a href="{{ route('creator.contents.index') }}" class="text-xs text-[#1d428a] hover:underline">
                        Lihat semua
                    </a>
                </div>

                <p class="text-sm text-slate-500">
                    Belum ada konten yang ditampilkan. Setelah kamu membuat konten pertama,
                    daftar konten dan performanya akan muncul di sini.
                </p>
            </article>

            <article class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                <h2 class="text-sm font-semibold text-slate-900 mb-3">
                    Info cepat
                </h2>
                <ul class="space-y-2 text-xs text-slate-500">
                    <li class="flex gap-2">
                        <span class="mt-1 inline-block h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        <span>Profil kreator kamu sudah aktif. Lengkapi bio dan foto profil untuk terlihat lebih profesional.</span>
                    </li>
                    <li class="flex gap-2">
                        <span class="mt-1 inline-block h-1.5 w-1.5 rounded-full bg-slate-300"></span>
                        <span>Atur metode penarikan dana sebelum mulai menjual konten berbayar.</span>
                    </li>
                </ul>
            </article>
        </section>
    </main>
</div>
