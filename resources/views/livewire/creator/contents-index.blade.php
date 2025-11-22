<div class="flex h-screen bg-slate-50">
    {{-- SIDEBAR KIRI --}}
    @include('livewire.creator.sidebar', ['sidebarOpen' => $sidebarOpen])

    {{-- KONTEN KANAN --}}
    <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8 overflow-y-auto">
        {{-- HEADER --}}
        <header class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-slate-900">
                    Konten saya
                </h1>
                <p class="mt-1 text-sm text-slate-500 max-w-2xl">
                    Kelola semua produk digital dan konten berbayar yang kamu buat di Noorly.
                </p>

                <div class="mt-3 flex flex-wrap gap-3 text-[11px] text-slate-500">
                    <span>Semua: <span class="font-semibold text-slate-700">{{ $totalAll }}</span></span>
                    <span>Aktif: <span class="font-semibold text-emerald-600">{{ $totalActive }}</span></span>
                    <span>Draft: <span class="font-semibold text-slate-700">{{ $totalDraft }}</span></span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @if($topContent)
                    <div class="hidden md:flex flex-col items-end text-right text-[11px] text-slate-500 mr-2">
                        <span class="font-semibold text-slate-700">Konten terlaris 30 hari terakhir</span>
                        <span class="truncate max-w-[220px] text-[11px] text-slate-500">
                            {{ $topContent->title }} • {{ $topContent->buyers_count }} pembeli
                        </span>
                    </div>
                @endif

                <a
                    href="{{ route('creator.ebooks.create') }}"
                    class="cursor-pointer inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-xs md:text-sm font-medium text-slate-700 hover:bg-slate-50"
                >
                    Buat e-book
                </a>

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

        {{-- FILTER STATUS --}}
        <section class="mb-4 border-b border-slate-200">
            <div class="flex flex-wrap gap-2">
                @php
                    $tabs = [
                        'all'       => 'Semua',
                        'draft'     => 'Draft',
                        'published' => 'Terbit',
                    ];
                @endphp

                @foreach($tabs as $key => $label)
                    <button
                        type="button"
                        wire:click="$set('statusFilter', '{{ $key }}')"
                        class="cursor-pointer inline-flex items-center rounded-full px-3 py-1.5 text-xs font-medium
                            @if($statusFilter === $key)
                                bg-[#1d428a] text-white
                            @else
                                bg-slate-100 text-slate-600 hover:bg-slate-200
                            @endif"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </section>

        {{-- BAR ATAS: SEARCH + SORT --}}
        <section class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div class="w-full md:max-w-xs">
                <label class="sr-only" for="search-contents">Cari konten</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 103.473 9.8l3.613 3.614a.75.75 0 101.06-1.06l-3.614-3.613A5.5 5.5 0 009 3.5zm-4 5.5a4 4 0 118 0 4 4 0 01-8 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    <input
                        id="search-contents"
                        type="text"
                        wire:model.debounce.400ms="search"
                        class="block w-full rounded-full border border-slate-200 bg-white pl-8 pr-3 py-1.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#1d428a] focus:ring-[#1d428a]"
                        placeholder="Cari judul konten..."
                    >
                </div>
            </div>

            <div class="flex items-center gap-2 justify-end">
                <label class="text-[11px] text-slate-500">Urutkan:</label>
                <select
                    wire:model="sort"
                    class="cursor-pointer rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                >
                    <option value="latest">Terbaru</option>
                    <option value="oldest">Terlama</option>
                    <option value="most_buyers">Pembeli terbanyak</option>
                    <option value="most_revenue">Pendapatan tertinggi</option>
                    <option value="most_views">Dilihat terbanyak</option>
                </select>
            </div>
        </section>


        {{-- ALERT STATUS (misal: konten berhasil dihapus / dibuat) --}}
        @if (session('status_contents'))
            <div
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-cloak
                class="mb-4 rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 flex items-start gap-3"
            >
                <div class="mt-0.5">
                    {{-- icon check --}}
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293A1 1 0 006.293 10.707l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="flex-1 text-[13px] leading-snug">
                    {{ session('status_contents') }}
                </div>
                <button
                    type="button"
                    class="ml-2 inline-flex h-6 w-6 items-center justify-center rounded-full hover:bg-emerald-100 text-emerald-700 cursor-pointer"
                    @click="show = false"
                >
                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        @endif


        {{-- LIST KONTEN --}}
        @if($contents->count() > 0)
            <section class="space-y-3">
                @foreach($contents as $content)
                    @php
                        $shareUrl = route('contents.show', $content->slug);
                        $waText   = urlencode("Coba cek konten Noorly aku: {$content->title} - {$shareUrl}");
                        $igUrl    = 'https://www.instagram.com/?url='.urlencode($shareUrl);
                        $tgUrl    = 'https://t.me/share/url?url='.urlencode($shareUrl).'&text='.urlencode($content->title);
                    @endphp

                    <article
                        x-data="{ openShare: false, openDelete: false }"
                        class="relative rounded-2xl border border-slate-100 bg-white px-4 py-3 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-3"
                    >
                        {{-- Bagian kiri: info utama --}}
                        <div class="flex-1 flex items-start gap-3">
                            {{-- Thumbnail / icon tipe konten --}}
                            <div class="mt-0.5 h-10 w-10 flex items-center justify-center rounded-xl bg-[#fef6e0] text-[#d4a116] text-xs font-semibold">
                                {{ strtoupper(substr($content->type ?? 'K', 0, 2)) }}
                            </div>

                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="{{ route('creator.contents.edit', $content->id) }}"
                                    class="text-sm font-semibold text-slate-900 hover:text-[#1d428a] cursor-pointer">
                                        {{ $content->title }}
                                    </a>

                                    @if($content->status === 'draft')
                                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] text-slate-600">Draft</span>
                                    @elseif($content->status === 'published')
                                        <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] text-emerald-700">Terbit</span>
                                    @elseif($content->status === 'pending_review')
                                        <span class="rounded-full bg-amber-50 px-2 py-0.5 text-[11px] text-amber-700">Menunggu review</span>
                                    @endif
                                </div>

                                <p class="text-[11px] text-slate-500">
                                    Tipe: <span class="font-medium text-slate-700">{{ $content->type ?? 'Produk digital' }}</span>
                                    • Harga:
                                    @if(($content->price ?? 0) > 0)
                                        <span class="font-semibold text-slate-900">
                                            Rp {{ number_format($content->price, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="font-semibold text-emerald-600">Gratis</span>
                                    @endif
                                </p>

                                <p class="text-[11px] text-slate-400">
                                    Terakhir diubah {{ optional($content->updated_at)->diffForHumans() ?? '-' }}
                                </p>
                            </div>
                        </div>

                        {{-- Bagian kanan: metrik + aksi --}}
                        <div class="flex flex-col items-start md:items-end gap-2 text-[11px] text-slate-500">
                            <div class="flex flex-wrap gap-3">
                                <div>
                                    <p class="text-[10px] uppercase tracking-wide text-slate-400">Pembeli</p>
                                    <p class="text-sm font-semibold text-slate-900">
                                        {{ $content->buyers_count ?? 0 }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-[10px] uppercase tracking-wide text-slate-400">Pendapatan</p>
                                    <p class="text-sm font-semibold text-emerald-600">
                                        Rp {{ number_format($content->revenue_total ?? 0, 0, ',', '.') }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-[10px] uppercase tracking-wide text-slate-400">Dilihat</p>
                                    <p class="text-sm font-semibold text-slate-900">
                                        {{ $content->views_count ?? 0 }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-1 items-center">
                                {{-- Edit --}}
                                <a
                                    href="{{ route('creator.contents.edit', $content->id) }}"
                                    class="cursor-pointer inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-medium text-slate-700 hover:bg-slate-50"
                                >
                                    Edit
                                </a>

                                {{-- Lihat (kalau route-nya sudah ada) --}}
                                @if(Route::has('contents.show'))
                                    <a
                                        href="{{ route('contents.show', $content->slug) }}"
                                        target="_blank"
                                        class="cursor-pointer inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-medium text-slate-700 hover:bg-slate-50"
                                    >
                                        Lihat
                                    </a>
                                @endif

                                {{-- HAPUS: buka modal konfirmasi --}}
                                <button
                                    type="button"
                                    @click="openDelete = true"
                                    class="cursor-pointer inline-flex items-center rounded-full border border-red-200 bg-white px-3 py-1 text-[11px] font-medium text-red-600 hover:bg-red-50"
                                >
                                    Hapus
                                </button>

                                {{-- BAGIKAN --}}
                                <div class="relative">
                                    <button
                                        type="button"
                                        @click="openShare = !openShare"
                                        class="cursor-pointer inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-medium text-slate-700 hover:bg-slate-50"
                                    >
                                        Bagikan
                                    </button>

                                    {{-- Popover share --}}
                                    <div
                                        x-show="openShare"
                                        x-transition
                                        x-cloak
                                        @click.outside="openShare = false"
                                        class="absolute right-0 mt-2 w-64 rounded-2xl border border-slate-100 bg-white shadow-xl z-20 p-3 text-[11px]"
                                    >
                                        <p class="font-semibold text-slate-800 mb-1">Bagikan konten</p>
                                        <p class="text-slate-500 mb-2">
                                            Gunakan link atau QR code di bawah untuk membagikan konten ini.
                                        </p>

                                        {{-- Link + copy --}}
                                        <div class="mb-2">
                                            <div class="flex items-center gap-2">
                                                <input
                                                    type="text"
                                                    readonly
                                                    value="{{ $shareUrl }}"
                                                    class="flex-1 rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-[10px] text-slate-700"
                                                >
                                                <button
                                                    type="button"
                                                    class="cursor-pointer rounded-lg border border-slate-200 bg-white px-2 py-1 text-[10px] text-slate-700 hover:bg-slate-50"
                                                    x-on:click="navigator.clipboard.writeText('{{ $shareUrl }}')"
                                                >
                                                    Copy
                                                </button>
                                            </div>
                                        </div>

                                        {{-- QR code --}}
                                        <div class="mb-2 flex justify-center">
                                            <img
                                                src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data={{ urlencode($shareUrl) }}"
                                                alt="QR {{ $content->title }}"
                                                class="h-28 w-28 rounded-lg border border-slate-100"
                                            >
                                        </div>

                                        {{-- Tombol share cepat --}}
                                        <div class="flex flex-wrap gap-2">
                                            <a
                                                href="https://wa.me/?text={{ $waText }}"
                                                target="_blank"
                                                class="cursor-pointer inline-flex items-center rounded-full bg-emerald-500 px-3 py-1 text-[10px] font-medium text-white hover:bg-emerald-600"
                                            >
                                                WhatsApp
                                            </a>
                                            <a
                                                href="{{ $tgUrl }}"
                                                target="_blank"
                                                class="cursor-pointer inline-flex items-center rounded-full bg-sky-500 px-3 py-1 text-[10px] font-medium text-white hover:bg-sky-600"
                                            >
                                                Telegram
                                            </a>
                                            <a
                                                href="{{ $igUrl }}"
                                                target="_blank"
                                                class="cursor-pointer inline-flex items-center rounded-full bg-gradient-to-r from-pink-500 via-red-500 to-yellow-400 px-3 py-1 text-[10px] font-medium text-white"
                                            >
                                                Instagram
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- MODAL KONFIRMASI HAPUS --}}
                        <div
                            x-show="openDelete"
                            x-transition
                            x-cloak
                            class="fixed inset-0 z-40 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm"
                        >
                            <div
                                @click.outside="openDelete = false"
                                class="w-full max-w-sm rounded-2xl bg-white shadow-xl border border-slate-100 p-5"
                            >
                                <div class="flex items-start gap-3">
                                    <div class="mt-0.5 h-8 w-8 rounded-full bg-red-50 flex items-center justify-center text-red-500">
                                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v9a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zm1 5a1 1 0 00-1 1v5a1 1 0 102 0V8a1 1 0 00-1-1zm-3 1a1 1 0 011 1v5a1 1 0 11-2 0V9a1 1 0 011-1zm7 1a1 1 0 00-1 1v5a1 1 0 002 0V9a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>

                                    <div class="flex-1">
                                        <h3 class="text-sm font-semibold text-slate-900">
                                            Hapus konten ini?
                                        </h3>
                                        <p class="mt-1 text-[11px] text-slate-500">
                                            Konten <span class="font-semibold">"{{ $content->title }}"</span> akan dihapus dari Noorly.
                                            Tindakan ini tidak dapat dibatalkan dan link yang sudah dibagikan tidak akan bisa diakses lagi.
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-4 flex items-center justify-end gap-2">
                                    <button
                                        type="button"
                                        @click="openDelete = false"
                                        class="cursor-pointer inline-flex items-center rounded-full border border-slate-200 bg-white px-4 py-1.5 text-[11px] font-medium text-slate-700 hover:bg-slate-50"
                                    >
                                        Batal
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="deleteContent({{ $content->id }})"
                                        @click="openDelete = false"
                                        class="cursor-pointer inline-flex items-center gap-1 rounded-full bg-red-600 px-4 py-1.5 text-[11px] font-semibold text-white shadow-sm hover:bg-red-700"
                                    >
                                        Hapus permanen
                                    </button>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach

            </section>

            <div class="mt-4">
                {{ $contents->links() }}
            </div>
        @else
            {{-- EMPTY STATE --}}
            <section class="mt-8 flex flex-col items-center justify-center text-center">
                <div class="mb-4 h-16 w-16 rounded-full bg-[#fef6e0] flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#d4a116]" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M4 3a2 2 0 00-2 2v9.5A1.5 1.5 0 003.5 16h13a1.5 1.5 0 001.5-1.5V7a2 2 0 00-2-2h-5.586a2 2 0 01-1.414-.586L7.586 3H4z" />
                    </svg>
                </div>
                <h2 class="text-sm font-semibold text-slate-900 mb-1">
                    Kamu belum punya konten berbayar
                </h2>
                <p class="text-xs text-slate-500 max-w-sm mb-3">
                    Mulai dari membuat konten pertamamu. Kamu bisa menjual e-book, kelas video, rekaman webinar, template, dan banyak lagi.
                </p>
                <a
                    href="{{ route('creator.contents.create') }}"
                    class="cursor-pointer inline-flex items-center gap-2 rounded-full bg-[#1d428a] px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-[#163268]"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.75 4a.75.75 0 00-1.5 0v4.25H5a.75.75 0 000 1.5h4.25V14a.75.75 0 001.5 0v-4.25H15a.75.75 0 000-1.5h-4.25V4z" />
                    </svg>
                    Buat konten pertama
                </a>
            </section>
        @endif
    </main>
</div>
