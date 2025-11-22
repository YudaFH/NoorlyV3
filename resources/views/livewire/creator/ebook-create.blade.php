<div class="flex h-screen bg-slate-50">
    {{-- SIDEBAR --}}
    @include('livewire.creator.sidebar', ['sidebarOpen' => $sidebarOpen])

    {{-- MAIN --}}
    <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8 overflow-y-auto">
        {{-- HEADER --}}
        <header class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-slate-900">
                    Buat e-book baru
                </h1>
                <p class="mt-1 text-sm text-slate-500 max-w-2xl">
                    Tulis e-book digital kamu langsung di Noorly. Kamu bisa mengatur bab, harga, dan cover,
                    lalu menjualnya sebagai produk digital.
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
        <section class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 max-w-4xl">
            <form wire:submit.prevent="save" class="space-y-6">
                {{-- Judul + harga --}}
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">
                            Judul e-book
                        </label>
                        <input
                            type="text"
                            wire:model.defer="title"
                            class="block w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#1d428a] focus:ring-[#1d428a]"
                            placeholder="Misal: Panduan lengkap membangun personal brand di Instagram"
                        >
                        @error('title')
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
                            placeholder="0 untuk e-book gratis"
                        >
                        @error('price')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-[11px] text-slate-400">
                            Kamu bisa mengubah harga kapan saja.
                        </p>
                    </div>
                </div>

                {{-- Cover + file PDF opsional --}}
                <div class="grid gap-4 md:grid-cols-[1.25fr,2fr] items-start">
                    {{-- Cover --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">
                            Cover e-book
                        </label>
                        <div class="flex items-center gap-3">
                            <div class="h-24 w-24 rounded-xl bg-slate-100 flex items-center justify-center overflow-hidden">
                                @if($cover)
                                    <img src="{{ $cover->temporaryUrl() }}" class="h-full w-full object-cover" alt="Preview cover">
                                @else
                                    <span class="text-[10px] text-slate-400 text-center px-2">
                                        Preview cover
                                    </span>
                                @endif
                            </div>
                            <div class="space-y-1">
                                <label class="cursor-pointer inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1.5 text-[11px] font-medium text-slate-700 hover:bg-slate-50">
                                    Pilih gambar
                                    <input type="file" class="hidden" wire:model="cover" accept="image/*">
                                </label>
                                <p class="text-[11px] text-slate-400 max-w-[220px]">
                                    Disarankan rasio 3:4 atau 1:1, format JPG/PNG, maksimal 2MB.
                                </p>
                            </div>
                        </div>
                        @error('cover')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- File PDF opsional --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">
                            File PDF e-book <span class="text-slate-400 font-normal">(opsional)</span>
                        </label>

                        <div class="flex flex-col gap-2 rounded-xl border border-slate-100 bg-slate-50/60 p-3">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-[11px] text-slate-600">
                                    Jika kamu sudah punya versi PDF, upload di sini untuk diunduh pembeli.
                                    Kalau belum, kamu tetap bisa menjual e-book hanya dari bab yang kamu tulis di bawah.
                                </p>
                                <label class="cursor-pointer inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-medium text-slate-700 hover:bg-slate-50 shrink-0">
                                    Unggah PDF
                                    <input type="file" class="hidden" wire:model="primary_file" accept="application/pdf">
                                </label>
                            </div>

                            @error('primary_file')
                                <p class="text-[11px] text-red-500">{{ $message }}</p>
                            @enderror
                            <p class="text-[11px] text-slate-400">
                                Format PDF, maksimal sekitar 20MB.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- E-BOOK BUILDER --}}
                <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-4 space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-xs font-semibold text-slate-800">
                                Editor bab e-book
                            </h2>
                            <p class="mt-1 text-[11px] text-slate-500 max-w-xl">
                                Tulis isi e-book langsung di Noorly. Kamu bisa menambahkan beberapa bab,
                                mengurutkan, dan menghapus bab. Pembeli akan membaca e-book ini dari dashboard mereka.
                            </p>
                        </div>
                        <button
                            type="button"
                            wire:click="addChapter"
                            class="cursor-pointer inline-flex items-center gap-1 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-[11px] font-medium text-slate-700 hover:bg-slate-50"
                        >
                            + Tambah bab
                        </button>
                    </div>

                    @error('ebookChapters')
                        <p class="text-[11px] text-red-500">{{ $message }}</p>
                    @enderror

                    <div class="space-y-3">
                        @forelse($ebookChapters as $index => $chapter)
                            <div class="rounded-xl border border-slate-200 bg-white p-3 space-y-2">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-slate-100 text-[11px] text-slate-600">
                                            {{ $index + 1 }}
                                        </span>
                                        <input
                                            type="text"
                                            wire:model.defer="ebookChapters.{{ $index }}.title"
                                            class="block w-full rounded-lg border-slate-200 bg-slate-50 px-2 py-1 text-xs text-slate-900 placeholder:text-slate-400 focus:border-[#1d428a] focus:ring-[#1d428a]"
                                            placeholder="Judul bab (misal: Pendahuluan)"
                                        >
                                    </div>

                                    <div class="flex items-center gap-1">
                                        <button
                                            type="button"
                                            wire:click="moveChapterUp({{ $index }})"
                                            class="cursor-pointer inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-2 py-1 text-[10px] text-slate-500 hover:bg-slate-50"
                                            title="Geser ke atas"
                                        >
                                            ↑
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="moveChapterDown({{ $index }})"
                                            class="cursor-pointer inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-2 py-1 text-[10px] text-slate-500 hover:bg-slate-50"
                                            title="Geser ke bawah"
                                        >
                                            ↓
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="removeChapter('{{ $chapter['id'] ?? '' }}')"
                                            class="cursor-pointer inline-flex items-center justify-center rounded-full border border-red-100 bg-red-50 px-2 py-1 text-[10px] text-red-500 hover:bg-red-100"
                                            title="Hapus bab"
                                        >
                                            Hapus
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <textarea
                                        rows="5"
                                        wire:model.defer="ebookChapters.{{ $index }}.body"
                                        class="block w-full rounded-lg border-slate-200 bg-slate-50 px-2 py-1.5 text-xs text-slate-900 placeholder:text-slate-400 focus:border-[#1d428a] focus:ring-[#1d428a]"
                                        placeholder="Isi bab ini..."
                                    ></textarea>
                                </div>
                            </div>
                        @empty
                            <p class="text-[11px] text-slate-500">
                                Belum ada bab. Klik <span class="font-semibold">“Tambah bab”</span> untuk mulai menulis e-book.
                            </p>
                        @endforelse
                    </div>
                </div>

                {{-- Pedoman konten --}}
                <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-3">
                    <p class="text-[11px] text-slate-600">
                        Dengan mengunggah e-book, kamu setuju untuk mengikuti
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
                            Saya menyatakan e-book ini tidak mengandung materi pornografi, judi online, SARA, kebencian,
                            penipuan, atau pelanggaran hukum lainnya.
                        </span>
                    </label>

                    @error('accept_guidelines')
                        <p class="mt-1 text-[11px] text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tombol aksi --}}
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
                        Simpan e-book
                    </button>
                </div>
            </form>
        </section>
    </main>
</div>
