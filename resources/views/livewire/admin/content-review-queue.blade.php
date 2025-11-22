<div class="flex h-screen bg-slate-50">
    {{-- SIDEBAR ADMIN --}}
    @include('livewire.admin.sidebar', ['sidebarOpen' => $sidebarOpen])

    {{-- MAIN --}}
    <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8 overflow-y-auto">
        {{-- HEADER --}}
        <header class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-slate-900">
                    Antrian review konten
                </h1>
                <p class="mt-1 text-sm text-slate-500 max-w-2xl">
                    Lihat dan review semua konten kreator yang menunggu persetujuan sebelum bisa terbit di Noorly.
                </p>

                <div class="mt-3 flex flex-wrap gap-3 text-[11px] text-slate-500">
                    <span>Menunggu review: <span class="font-semibold text-amber-600">{{ $totalPending }}</span> konten</span>
                </div>
            </div>

            <div class="flex flex-col items-end gap-2">
                @if (session('status_admin_review'))
                    <div class="rounded-full bg-emerald-50 border border-emerald-100 px-3 py-1 text-[11px] text-emerald-700">
                        {{ session('status_admin_review') }}
                    </div>
                @endif
            </div>
        </header>

        {{-- FILTER BAR --}}
        <section class="mb-4 rounded-2xl border border-slate-100 bg-white px-4 py-3 shadow-sm">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                {{-- Search --}}
                <div class="w-full md:max-w-sm">
                    <label class="sr-only" for="search-review">Cari konten / kreator</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 103.473 9.8l3.613 3.614a.75.75 0 101.06-1.06l-3.614-3.613A5.5 5.5 0 009 3.5zm-4 5.5a4 4 0 118 0 4 4 0 01-8 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <input
                            id="search-review"
                            type="text"
                            wire:model.debounce.400ms="search"
                            class="block w-full rounded-full border border-slate-200 bg-slate-50 pl-8 pr-3 py-1.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#1d428a] focus:ring-[#1d428a]"
                            placeholder="Cari judul konten atau nama kreator..."
                        >
                    </div>
                </div>

                {{-- Filters --}}
                <div class="flex flex-wrap items-center gap-2 justify-end text-[11px]">
                    {{-- Tipe konten --}}
                    <div class="flex items-center gap-1">
                        <span class="text-slate-500">Tipe:</span>
                        <select
                            wire:model="typeFilter"
                            class="cursor-pointer rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-[11px] text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                        >
                            <option value="all">Semua</option>
                            <option value="ebook">E-book</option>
                            <option value="video">Video</option>
                            <option value="webinar">Webinar</option>
                            <option value="template">Template</option>
                            <option value="bundle">Bundle</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>

                    {{-- Preferensi setelah review --}}
                    <div class="flex items-center gap-1">
                        <span class="text-slate-500">Setelah review:</span>
                        <select
                            wire:model="postReviewFilter"
                            class="cursor-pointer rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-[11px] text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                        >
                            <option value="all">Semua</option>
                            <option value="publish">Terbit langsung</option>
                            <option value="draft">Masuk draft dulu</option>
                        </select>
                    </div>

                    {{-- Sort --}}
                    <div class="flex items-center gap-1">
                        <span class="text-slate-500">Urutkan:</span>
                        <select
                            wire:model="sort"
                            class="cursor-pointer rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-[11px] text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                        >
                            <option value="newest">Terbaru</option>
                            <option value="oldest">Terlama</option>
                            <option value="price_high">Harga tertinggi</option>
                            <option value="price_low">Harga terendah</option>
                        </select>
                    </div>
                </div>
            </div>
        </section>

        {{-- LIST ANTRIAN --}}
        @if($contents->count() > 0)
            <section class="space-y-3">
                @foreach($contents as $content)
                    @php
                        $creator           = $content->user;
                        $shareUrl          = route('contents.show', $content->slug); // halaman konten (preview/publik)
                        $publicProfileUrl  = $creator ? route('creator.public.show', $creator->id) : null; // halaman publik kreator
                        $canOpenCreatorPanel = $creator && auth()->check() && auth()->id() === $creator->id;
                    @endphp

                    <article class="rounded-2xl border border-slate-100 bg-white px-4 py-3 shadow-sm flex flex-col gap-3">
                        {{-- Bar atas: judul + kreator --}}
                        <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
                            <div class="flex items-start gap-3">
                                {{-- Icon tipe --}}
                                <div class="mt-0.5 h-10 w-10 flex items-center justify-center rounded-xl bg-[#fef6e0] text-[#d4a116] text-xs font-semibold">
                                    {{ strtoupper(substr($content->type ?? 'K', 0, 2)) }}
                                </div>

                                <div class="space-y-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h2 class="text-sm font-semibold text-slate-900">
                                            {{ $content->title }}
                                        </h2>

                                        <span class="rounded-full bg-amber-50 px-2 py-0.5 text-[11px] text-amber-700">
                                            Menunggu review
                                        </span>

                                        @if($content->post_review_action === 'draft')
                                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] text-slate-600">
                                                Preferensi kreator: masuk draft setelah review
                                            </span>
                                        @else
                                            <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] text-emerald-700">
                                                Preferensi kreator: terbit setelah review
                                            </span>
                                        @endif
                                    </div>

                                    <p class="text-[11px] text-slate-500">
                                        Tipe:
                                        <span class="font-medium text-slate-700">{{ $content->type ?? 'Produk digital' }}</span>
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
                                        Dibuat {{ optional($content->created_at)->diffForHumans() ?? '-' }}
                                        @if($creator)
                                            • Oleh: <span class="font-medium text-slate-700">{{ $creator->name }}</span>
                                            <span class="text-slate-400">({{ $creator->email }})</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            {{-- Akses cepat --}}
                            <div class="flex flex-wrap gap-2 justify-end text-[11px]">
                                {{-- Lihat konten (preview/publik) --}}
                                <a
                                    href="{{ $shareUrl }}"
                                    target="_blank"
                                    class="cursor-pointer inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 font-medium text-slate-700 hover:bg-slate-50"
                                >
                                    Lihat konten
                                </a>

                                {{-- Halaman publik kreator --}}
                                @if($publicProfileUrl)
                                    <a
                                        href="{{ $publicProfileUrl }}"
                                        target="_blank"
                                        class="cursor-pointer inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 font-medium text-slate-700 hover:bg-slate-50"
                                    >
                                        Lihat halaman publik kreator
                                    </a>
                                @endif

                                {{-- Buka di panel kreator: hanya kalau admin saat ini = kreatornya --}}
                                @if($canOpenCreatorPanel)
                                    <a
                                        href="{{ route('creator.contents.edit', $content->id) }}"
                                        target="_blank"
                                        class="cursor-pointer inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 font-medium text-slate-700 hover:bg-slate-50"
                                    >
                                        Buka di panel kreator
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- Ringkasan metrik kecil --}}
                        <div class="flex flex-wrap gap-4 text-[11px] text-slate-500">
                            <div>
                                <p class="text-[10px] uppercase tracking-wide text-slate-400">Perkiraan pembeli</p>
                                <p class="text-sm font-semibold text-slate-900">
                                    {{ $content->buyers_count ?? 0 }}
                                </p>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase tracking-wide text-slate-400">Perkiraan pendapatan</p>
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

                        {{-- Tombol aksi review --}}
                        <div class="flex flex-wrap items-center justify-end gap-2 pt-1">
                            <button
                                type="button"
                                wire:click="approve({{ $content->id }})"
                                class="cursor-pointer inline-flex items-center gap-1 rounded-full bg-emerald-600 px-4 py-1.5 text-[11px] font-semibold text-white shadow-sm hover:bg-emerald-700"
                            >
                                Setujui
                                @if($content->post_review_action === 'draft')
                                    <span class="opacity-80">(jadikan draft)</span>
                                @else
                                    <span class="opacity-80">(terbitkan)</span>
                                @endif
                            </button>

                            <button
                                type="button"
                                wire:click="reject({{ $content->id }})"
                                onclick="return confirm('Yakin ingin mengembalikan konten ini ke draft?')"
                                class="cursor-pointer inline-flex items-center gap-1 rounded-full bg-white px-4 py-1.5 text-[11px] font-semibold text-rose-600 border border-rose-200 hover:bg-rose-50"
                            >
                                Tolak & kembalikan ke draft
                            </button>
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
                <div class="mb-4 h-16 w-16 rounded-full bg-slate-100 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M4 3a2 2 0 00-2 2v9.5A1.5 1.5 0 003.5 16h13a1.5 1.5 0 001.5-1.5V7a2 2 0 00-2-2h-5.586a2 2 0 01-1.414-.586L7.586 3H4z" />
                    </svg>
                </div>
                <h2 class="text-sm font-semibold text-slate-900 mb-1">
                    Belum ada konten yang menunggu review
                </h2>
                <p class="text-xs text-slate-500 max-w-sm">
                    Kalau kreator mengirim konten baru, konten tersebut akan muncul di antrian review ini
                    dengan status <span class="font-semibold">Menunggu review</span>.
                </p>
            </section>
        @endif
    </main>
</div>
