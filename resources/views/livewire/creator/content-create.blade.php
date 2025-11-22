<div class="flex h-screen bg-slate-50">
    {{-- SIDEBAR --}}
    @include('livewire.creator.sidebar', ['sidebarOpen' => $sidebarOpen])

    {{-- MAIN --}}
    <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8 overflow-y-auto">
        {{-- HEADER --}}
        <header class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-slate-900">
                    Buat konten baru
                </h1>
                <p class="mt-1 text-sm text-slate-500 max-w-2xl">
                    Konten yang kamu kirim akan dicek dulu oleh tim Noorly jika kamu masih kreator baru.
                    Untuk kreator terpercaya, konten bisa langsung terbit.
                </p>
                <p class="mt-1 text-[11px] text-slate-400">
                    Status awal:
                    <span class="font-semibold text-amber-600">Menunggu review</span>
                    untuk kreator baru,
                    <span class="font-semibold text-emerald-600">Terbit</span>
                    untuk kreator yang sudah ditandai sebagai kreator terpercaya.
                </p>
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
                        placeholder="Misal: Kelas lengkap membuat e-book digital"
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
                        placeholder="Tuliskan ringkasan konten, manfaat utama, dan siapa yang cocok mengikuti / membeli."
                    ></textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-[11px] text-slate-400">
                        Deskripsi ini akan tampil di halaman penjelasan konten untuk calon pembeli.
                    </p>
                </div>

                {{-- Grid: tipe + harga --}}
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">
                            Tipe konten
                        </label>
                        <select
                            wire:model="type"
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
                        <label class="block text-xs font-medium text-slate-700 mb-1.5 flex items-center justify-between">
                            <span>Harga (Rp)</span>
                        </label>
                        <input
                            type="number"
                            min="0"
                            wire:model.defer="price"
                            @if(! $canCreatePaidContent) disabled @endif
                            class="block w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#1d428a] focus:ring-[#1d428a]"
                            placeholder="0 untuk konten gratis"
                        >
                        @error('price')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror

                        @if($canCreatePaidContent)
                            <p class="mt-1 text-[11px] text-slate-400">
                                Atur 0 untuk konten gratis. Untuk konten berbayar, dana akan diteruskan ke
                                <span class="font-semibold text-slate-700">{{ $defaultPayoutLabel }}</span>
                                dengan potongan fee platform sekitar
                                <span class="font-semibold">{{ $platformFeePercent }}%</span>.
                            </p>
                        @else
                            <p class="mt-1 text-[11px] text-amber-700 bg-amber-50 border border-amber-100 rounded-lg px-2 py-1">
                                Kamu belum bisa mengatur harga berbayar karena belum ada metode penarikan
                                yang terverifikasi. Konten akan tersimpan sebagai <span class="font-semibold">gratis</span>.
                                Ajukan metode penarikan di menu <span class="font-semibold">Saldo &amp; penarikan</span>
                                terlebih dahulu.
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Cover + Isi --}}
                <div class="grid gap-4 md:grid-cols-[1.25fr,2fr] items-start">
                    {{-- Cover --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">
                            Cover konten
                        </label>
                        <div class="flex items-center gap-3">
                            <div class="h-20 w-20 rounded-xl bg-slate-100 flex items-center justify-center overflow-hidden">
                                @if($cover)
                                    <img src="{{ $cover->temporaryUrl() }}" class="h-full w-full object-cover" alt="Preview cover">
                                @else
                                    <span class="text-[10px] text-slate-400 text-center px-2">
                                        Preview cover
                                    </span>
                                @endif
                            </div>
                            <label class="cursor-pointer inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1.5 text-[11px] font-medium text-slate-700 hover:bg-slate-50">
                                Pilih gambar
                                <input type="file" class="hidden" wire:model="cover" accept="image/*">
                            </label>
                        </div>
                        @error('cover')
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
                                    Unggah file
                                    <input type="file" class="hidden" wire:model="primary_file">
                                </label>
                            </div>
                            @error('primary_file')
                                <p class="text-[11px] text-red-500">{{ $message }}</p>
                            @enderror

                            <p class="text-[11px] text-slate-400">
                                @if($type === 'video' || $type === 'webinar')
                                    Untuk kelas video / rekaman webinar, <span class="font-semibold text-slate-700">disarankan</span>
                                    upload dulu videonya ke YouTube (unlisted) atau Google Drive, lalu tempel link di bawah.
                                    Jika tetap mengunggah video ke Noorly, batasi ukuran file sekitar
                                    <span class="font-semibold">maks. 50&nbsp;MB</span> agar website tetap ringan.
                                @elseif($type === 'ebook')
                                    Untuk e-book, upload file PDF e-book yang sudah jadi.
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
                                Link utama (YouTube, Google Drive, dsb)
                                <span class="font-normal text-slate-400">(opsional, tapi sangat disarankan untuk video besar)</span>
                            </label>
                            <input
                                type="url"
                                wire:model.defer="primary_link_url"
                                class="block w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#1d428a] focus:ring-[#1d428a]"
                                placeholder="https://youtube.com/... atau https://drive.google.com/..."
                            >
                            @error('primary_link_url')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror

                            @if($type === 'video' || $type === 'webinar')
                                <p class="mt-1 text-[11px] text-slate-400">
                                    Untuk kualitas video yang lebih tinggi (di atas 50&nbsp;MB), sebaiknya upload di platform
                                    video (YouTube, Vimeo) atau cloud (Google Drive), lalu masukkan link di sini.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Pengaturan setelah review (untuk konten yang butuh review admin) --}}
                <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-3">
                    <label class="block text-[11px] font-medium text-slate-700 mb-1.5">
                        Jika konten ini perlu review admin, setelah disetujui:
                    </label>
                    <div class="flex flex-col gap-1 text-[11px] text-slate-700">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input
                                type="radio"
                                class="h-3.5 w-3.5 text-[#1d428a] border-slate-300 focus:ring-[#1d428a]"
                                wire:model="post_review_action"
                                value="publish"
                            >
                            <span>Langsung <span class="font-semibold text-emerald-700">terbit</span> setelah disetujui admin.</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input
                                type="radio"
                                class="h-3.5 w-3.5 text-[#1d428a] border-slate-300 focus:ring-[#1d428a]"
                                wire:model="post_review_action"
                                value="draft"
                            >
                            <span>Masuk <span class="font-semibold text-slate-700">draft dahulu</span>, baru kamu terbitkan manual.</span>
                        </label>
                    </div>
                    @error('post_review_action')
                        <p class="mt-1 text-[11px] text-red-500">{{ $message }}</p>
                    @enderror

                    <p class="mt-2 text-[11px] text-slate-500">
                        Pengaturan ini terutama berlaku untuk kreator baru yang kontennya masih
                        melewati proses review. Untuk kreator terpercaya, konten bisa langsung terbit
                        dan pilihan ini bisa diabaikan.
                    </p>
                </div>

                {{-- Pedoman konten + checkbox --}}
                <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-3">
                    <p class="text-[11px] text-slate-600">
                        Dengan mengunggah konten, kamu setuju untuk mengikuti
                        <a href="{{ route('content.guidelines') }}" target="_blank" class="text-[#1d428a] font-medium hover:underline">
                            Pedoman Konten Noorly
                        </a>.
                    </p>

                    <label class="mt-2 inline-flex items-start gap-2 text-[11px] text-slate-700 cursor-pointer">
                        <input
                            type="checkbox"
                            wire:model.defer="accept_guidelines"
                            class="mt-[2px] h-3.5 w-3.5 rounded border-slate-300 text-[#1d428a] focus:ring-[#1d428a]"
                        >
                        <span>
                            Saya menyatakan konten ini tidak mengandung materi pornografi, judi online, SARA, kebencian,
                            penipuan, atau pelanggaran hukum lainnya.
                        </span>
                    </label>

                    @error('accept_guidelines')
                        <p class="mt-1 text-[11px] text-red-500">{{ $message }}</p>
                    @enderror
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
                        Simpan & kirim
                    </button>
                </div>
            </form>
        </section>
    </main>
</div>
