<div class="flex h-screen bg-slate-50">
    {{-- SIDEBAR --}}
    @include('livewire.creator.sidebar', ['sidebarOpen' => $sidebarOpen])

    {{-- MAIN --}}
    <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8 overflow-y-auto">
        {{-- HEADER --}}
        <header class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-slate-900">
                    Edit konten
                </h1>
                <p class="mt-1 text-sm text-slate-500 max-w-2xl">
                    Perbarui judul, deskripsi, harga, cover, dan isi utama kontenmu di Noorly.
                </p>

                <div class="mt-2 flex flex-wrap items-center gap-2 text-[11px] text-slate-500">
                    <span>ID: {{ $content->id }}</span>
                    <span>•</span>
                    <span>
                        Status:
                        @if($content->status === 'draft')
                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] text-slate-600">Draft</span>
                        @elseif($content->status === 'published')
                            <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] text-emerald-700">Terbit</span>
                        @elseif($content->status === 'scheduled')
                            <span class="rounded-full bg-sky-50 px-2 py-0.5 text-[11px] text-sky-700">Dijadwalkan</span>
                        @elseif($content->status === 'pending_review')
                            <span class="rounded-full bg-amber-50 px-2 py-0.5 text-[11px] text-amber-700">Menunggu review</span>
                        @elseif($content->status === 'archived')
                            <span class="rounded-full bg-slate-200 px-2 py-0.5 text-[11px] text-slate-700">Arsip</span>
                        @endif
                    </span>
                </div>

                {{-- Info "setelah review" (hanya kalau masih pending_review) --}}
                @if($content->status === 'pending_review')
                    <div class="mt-2 inline-flex items-start gap-2 rounded-xl bg-amber-50 border border-amber-100 px-3 py-2 text-[11px] text-amber-900">
                        <span class="mt-[2px] inline-flex h-3 w-3 items-center justify-center rounded-full bg-amber-400 text-[9px] text-white">
                            i
                        </span>
                        <span>
                            Setelah tim Noorly menyelesaikan review:
                            @if($content->post_review_action === 'publish')
                                <span class="font-semibold">konten ini akan otomatis diterbitkan (status: Terbit).</span>
                            @elseif($content->post_review_action === 'draft')
                                <span class="font-semibold">konten ini akan diset ke Draft (tidak langsung tampil ke publik).</span>
                            @else
                                <span class="font-semibold">pengaturan mengikuti kebijakan default Noorly (biasanya diterbitkan jika disetujui).</span>
                            @endif
                        </span>
                    </div>
                @endif
            </div>

            <a
                href="{{ route('creator.contents.index') }}"
                class="cursor-pointer inline-flex items-center gap-1 rounded-full border border-slate-200 bg-white px-4 py-2 text-xs md:text-sm font-medium text-slate-700 hover:bg-slate-50"
            >
                ← Kembali ke konten saya
            </a>
        </header>

        {{-- FORM --}}
        <section class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 max-w-3xl">
            <form wire:submit.prevent="save" class="space-y-5">
                {{-- Judul --}}
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1.5">
                        Judul konten
                    </label>
                    <input
                        type="text"
                        wire:model.defer="title"
                        class="block w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#1d428a] focus:ring-[#1d428a]"
                        placeholder="Judul konten"
                    >
                    @error('title')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1.5">
                        Deskripsi konten
                    </label>
                    <textarea
                        rows="4"
                        wire:model.defer="description"
                        class="block w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#1d428a] focus:ring-[#1d428a]"
                        placeholder="Ceritakan secara singkat manfaat, isi utama, dan siapa yang cocok membeli konten ini."
                    ></textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-[11px] text-slate-400">
                        Deskripsi ini akan tampil di halaman konten publik (landing konten).
                    </p>
                </div>

                {{-- Grid: tipe + harga --}}
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">
                            Tipe konten
                        </label>
                        <select
                            wire:model.defer="type"
                            class="cursor-pointer block w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                        >
                            <option value="">Pilih tipe</option>
                            <option value="ebook">E-book / PDF</option>
                            <option value="video">Kelas video</option>
                            <option value="webinar">Rekaman webinar</option>
                            <option value="template">Template / file</option>
                            <option value="bundle">Bundle konten</option>
                            <option value="other">Lainnya</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">
                            Harga (Rp)
                        </label>
                        <input
                            type="number"
                            min="0"
                            wire:model.defer="price"
                            class="block w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#1d428a] focus:ring-[#1d428a]"
                            placeholder="0 untuk konten gratis"
                        >
                        @error('price')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-[11px] text-slate-400">
                            Atur 0 untuk konten gratis. Perubahan harga tidak mengubah transaksi yang sudah terjadi.
                        </p>
                    </div>
                </div>

                {{-- Cover + isi konten --}}
                <div class="grid gap-4 md:grid-cols-[1.25fr,2fr] items-start">
                    {{-- Cover --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">
                            Cover konten
                        </label>
                        <div class="flex items-center gap-3">
                            <div class="h-20 w-20 rounded-xl bg-slate-100 flex items-center justify-center overflow-hidden">
                                @if($new_cover)
                                    <img src="{{ $new_cover->temporaryUrl() }}" class="h-full w-full object-cover" alt="Preview cover baru">
                                @elseif($content->cover_path)
                                    <img src="{{ asset('storage/'.$content->cover_path) }}" class="h-full w-full object-cover" alt="Cover sekarang">
                                @else
                                    <span class="text-[10px] text-slate-400 text-center px-2">
                                        Preview cover
                                    </span>
                                @endif
                            </div>
                            <label class="cursor-pointer inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1.5 text-[11px] font-medium text-slate-700 hover:bg-slate-50">
                                Ganti cover
                                <input type="file" class="hidden" wire:model="new_cover" accept="image/*">
                            </label>
                        </div>
                        @if($content->cover_path && ! $new_cover)
                            <p class="mt-1 text-[11px] text-slate-400">
                                Cover sekarang diambil dari: <span class="font-mono">storage/{{ $content->cover_path }}</span>
                            </p>
                        @endif
                        @error('new_cover')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-[11px] text-slate-400">
                            Disarankan rasio 16:9 atau 1:1, format JPG/PNG, maksimal 2MB.
                        </p>
                    </div>

                    {{-- Isi konten --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">
                            Isi utama konten
                        </label>

                        {{-- Upload file --}}
                        <div class="flex flex-col gap-2 rounded-xl border border-slate-100 bg-slate-50/60 p-3">
                            <div class="flex items-center justify-between">
                                <p class="text-[11px] font-medium text-slate-700">
                                    File utama (PDF / video / template)
                                </p>
                                <label class="cursor-pointer inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-medium text-slate-700 hover:bg-slate-50">
                                    Ganti file
                                    <input type="file" class="hidden" wire:model="new_primary_file">
                                </label>
                            </div>

                            @if($content->primary_file_path && ! $new_primary_file)
                                <p class="text-[11px] text-slate-500">
                                    File saat ini:
                                    <span class="font-mono">storage/{{ $content->primary_file_path }}</span>
                                </p>
                            @elseif($new_primary_file)
                                <p class="text-[11px] text-slate-500">
                                    File baru diunggah.
                                </p>
                            @endif

                            @error('new_primary_file')
                                <p class="text-[11px] text-red-500">{{ $message }}</p>
                            @enderror

                            <p class="text-[11px] text-slate-400">
                                @if($type === 'video' || $type === 'webinar')
                                    Untuk kelas video / rekaman webinar, <span class="font-semibold text-slate-700">disarankan</span>
                                    upload videonya ke YouTube (unlisted) atau Google Drive, lalu tempel link di bawah.
                                    Jika tetap mengunggah video ke Noorly, batasi ukuran file sekitar
                                    <span class="font-semibold">maks. 50&nbsp;MB</span> agar website tetap ringan.
                                @elseif($type === 'ebook')
                                    Untuk e-book, kamu bisa upload file PDF jika sudah jadi, atau gunakan format lain sesuai kebutuhan.
                                @elseif($type === 'template' || $type === 'bundle')
                                    Untuk template / bundle, upload file ZIP atau file utama yang akan diunduh pembeli.
                                @else
                                    Upload file utama yang akan diakses pembeli (boleh dikombinasikan dengan link di bawah).
                                @endif
                            </p>
                        </div>

                        {{-- Link utama --}}
                        <div class="mt-3">
                            <label class="block text-[11px] font-medium text-slate-700 mb-1">
                                Link utama (YouTube, Google Drive, dsb) <span class="font-normal text-slate-400">(opsional)</span>
                            </label>
                            <input
                                type="url"
                                wire:model.defer="primary_link_url"
                                class="block w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#1d428a] focus:ring-[#1d428a]"
                                placeholder="https://..."
                            >
                            @error('primary_link_url')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror

                            @if($content->primary_link_url && ! $primary_link_url)
                                <p class="mt-1 text-[11px] text-slate-400">
                                    Link sekarang: {{ $content->primary_link_url }}
                                </p>
                            @endif

                            @if($type === 'video' || $type === 'webinar')
                                <p class="mt-1 text-[11px] text-slate-400">
                                    Untuk kualitas video besar, sebaiknya upload di platform video (YouTube, Vimeo) atau cloud (Google Drive),
                                    lalu masukkan link di sini.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Catatan status --}}
                <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-3 text-[11px] text-slate-600">
                    <p>
                        Perubahan yang kamu simpan tidak otomatis mengubah status konten
                        (<span class="font-semibold">{{ $content->status }}</span>).
                        Jika perubahan besar dan dirasa perlu, tim Noorly bisa melakukan review ulang.
                    </p>
                </div>

                {{-- ACTION BUTTONS --}}
                <div class="pt-3 flex items-center justify-end gap-3">
                    <a
                        href="{{ route('creator.contents.index') }}"
                        class="cursor-pointer inline-flex items-center rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50"
                    >
                        Batal
                    </a>
                    <button
                        type="submit"
                        class="cursor-pointer inline-flex items-center gap-2 rounded-full bg-[#1d428a] px-5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-[#163268]"
                    >
                        Simpan perubahan
                    </button>
                </div>
            </form>
        </section>
    </main>
</div>
