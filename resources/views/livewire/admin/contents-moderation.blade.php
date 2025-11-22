{{-- resources/views/livewire/admin/contents-moderation.blade.php --}}
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
                    Lihat, cek, dan moderasi konten yang diajukan kreator sebelum ditayangkan ke publik.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                {{-- filter status sederhana --}}
                <select
                    wire:model="statusFilter"
                    class="cursor-pointer rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                >
                    <option value="pending_review">Menunggu review</option>
                    <option value="rejected">Ditolak</option>
                    <option value="published">Sudah terbit</option>
                </select>

                <div class="w-full md:w-52">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 103.473 9.8l3.613 3.614a.75.75 0 101.06-1.06l-3.614-3.613A5.5 5.5 0 009 3.5zm-4 5.5a4 4 0 118 0 4 4 0 01-8 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <input
                            type="text"
                            wire:model.debounce.400ms="search"
                            class="block w-full rounded-full border border-slate-200 bg-white pl-8 pr-3 py-1.5 text-xs text-slate-900 placeholder:text-slate-400 focus:border-[#1d428a] focus:ring-[#1d428a]"
                            placeholder="Cari judul atau kreator..."
                        >
                    </div>
                </div>
            </div>
        </header>

        {{-- ALERT FLASH --}}
        @if (session()->has('moderation_status'))
            <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-100 px-4 py-2.5 text-xs text-emerald-700">
                {{ session('moderation_status') }}
            </div>
        @endif

        {{-- LIST KONTEN --}}
        @if($contents->count() > 0)
            <section class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-slate-100 text-xs">
                    <thead class="bg-slate-50">
                        <tr class="text-[11px] uppercase tracking-wide text-slate-500">
                            <th class="px-4 py-2 text-left">Konten</th>
                            <th class="px-4 py-2 text-left">Kreator</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Diajukan</th>
                            <th class="px-4 py-2 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($contents as $content)
                            @php
                                $creator = $content->user ?? $content->creator;
                                $publicProfileUrl = route('creator.public.show', $creator->id);
                                $canOpenCreatorPanel = auth()->id() === $creator->id;
                            @endphp

                            <tr>
                                {{-- KONTEN --}}
                                <td class="px-4 py-3 align-top">
                                    <p class="text-sm font-semibold text-slate-900">
                                        {{ $content->title }}
                                    </p>
                                    <p class="mt-0.5 text-[11px] text-slate-500">
                                        Tipe: <span class="font-medium">{{ $content->type ?? 'Produk digital' }}</span>
                                        • Harga:
                                        @if(($content->price ?? 0) > 0)
                                            <span class="font-semibold text-slate-900">
                                                Rp {{ number_format($content->price, 0, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="font-semibold text-emerald-600">Gratis</span>
                                        @endif
                                    </p>
                                </td>

                                {{-- KREATOR --}}
                                <td class="px-4 py-3 align-top">
                                    @if($creator)
                                        <p class="text-[12px] font-medium text-slate-900">
                                            {{ $creator->name }}
                                        </p>
                                        <p class="text-[11px] text-slate-400">
                                            ID: {{ $creator->id }}
                                        </p>
                                    @else
                                        <p class="text-[11px] text-slate-400">-</p>
                                    @endif
                                </td>

                                {{-- STATUS --}}
                                <td class="px-4 py-3 align-top">
                                    @if($content->status === 'pending_review')
                                        <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-[11px] text-amber-700">
                                            Menunggu review
                                        </span>
                                    @elseif($content->status === 'published')
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] text-emerald-700">
                                            Terbit
                                        </span>
                                    @elseif($content->status === 'rejected')
                                        <span class="inline-flex items-center rounded-full bg-rose-50 px-2 py-0.5 text-[11px] text-rose-700">
                                            Ditolak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] text-slate-600">
                                            {{ $content->status }}
                                        </span>
                                    @endif
                                </td>

                                {{-- DIAJUKAN --}}
                                <td class="px-4 py-3 align-top text-[11px] text-slate-500">
                                    <p>{{ optional($content->created_at)->format('d M Y H:i') ?? '-' }}</p>
                                    <p>{{ optional($content->created_at)->diffForHumans() ?? '' }}</p>
                                </td>

                                {{-- AKSI --}}
                                <td class="px-4 py-3 align-top">
                                    <div class="flex flex-col items-end gap-1 text-[11px]">
                                        {{-- Baris 1: tombol moderasi --}}
                                        <div class="flex flex-wrap gap-1 justify-end">
                                            @if($content->status === 'pending_review')
                                                <button
                                                    type="button"
                                                    wire:click="approve({{ $content->id }})"
                                                    class="cursor-pointer inline-flex items-center rounded-full bg-emerald-600 px-3 py-1 text-[11px] font-medium text-white hover:bg-emerald-700"
                                                >
                                                    Setujui &amp; terbitkan
                                                </button>
                                                <button
                                                    type="button"
                                                    wire:click="openRejectModal({{ $content->id }})"
                                                    class="cursor-pointer inline-flex items-center rounded-full border border-rose-200 bg-white px-3 py-1 text-[11px] font-medium text-rose-600 hover:bg-rose-50"
                                                >
                                                    Tolak
                                                </button>
                                            @endif
                                        </div>

                                        {{-- Baris 2: aksi lihat / navigasi --}}
                                        <div class="flex flex-wrap gap-1 justify-end">
                                            {{-- Lihat halaman konten (detail) --}}
                                            <a
                                                href="{{ route('contents.show', $content->slug) }}"
                                                target="_blank"
                                                class="cursor-pointer inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-medium text-slate-700 hover:bg-slate-50"
                                            >
                                                Lihat konten
                                            </a>

                                            {{-- Halaman publik kreator --}}
                                            <a
                                                href="{{ $publicProfileUrl }}"
                                                target="_blank"
                                                class="cursor-pointer inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-medium text-slate-700 hover:bg-slate-50"
                                            >
                                                Halaman publik kreator
                                            </a>

                                            {{-- Buka di panel kreator (hanya jika admin == kreator) --}}
                                            @if($canOpenCreatorPanel)
                                                <a
                                                    href="{{ route('creator.contents.edit', $content->id) }}"
                                                    target="_blank"
                                                    class="cursor-pointer inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-medium text-slate-700 hover:bg-slate-50"
                                                >
                                                    Buka di panel kreator
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="px-4 py-3 border-t border-slate-100">
                    {{ $contents->links() }}
                </div>
            </section>
        @else
            <section class="mt-8 flex flex-col items-center justify-center text-center">
                <div class="mb-4 h-16 w-16 rounded-full bg-slate-100 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-300" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M4 3a2 2 0 00-2 2v9.5A1.5 1.5 0 003.5 16h13a1.5 1.5 0 001.5-1.5V7a2 2 0 00-2-2h-5.586a2 2 0 01-1.414-.586L7.586 3H4z" />
                    </svg>
                </div>
                <h2 class="text-sm font-semibold text-slate-900 mb-1">
                    Belum ada konten yang menunggu review
                </h2>
                <p class="text-xs text-slate-500 max-w-sm">
                    Konten baru yang diajukan kreator akan otomatis muncul di antrian ini.
                </p>
            </section>
        @endif
    </main>
</div>
