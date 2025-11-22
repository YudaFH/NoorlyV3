{{-- resources/views/contents/show.blade.php --}}
@extends('layouts.noorly')

@section('content')
@php
    /** @var \App\Models\Content $content */
    $creator = $content->user; // relasi ke kreator (bisa null kalau belum diset)
@endphp

<div class="min-h-screen bg-slate-50 pt-20 pb-16">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- BAR ATAS: BACK + INFO SINGKAT --}}
        <div class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <a
                href="{{ route('contents.index') }}"
                class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-[11px] font-medium text-slate-700 hover:bg-slate-50 cursor-pointer"
            >
                ← Kembali ke semua konten
            </a>

            @if($creator)
                <a
                    href="{{ route('creator.public.show', $creator->id) }}"
                    class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-[11px] font-medium text-slate-700 hover:bg-slate-50 cursor-pointer"
                    target="_blank"
                >
                    Lihat halaman kreator
                </a>
            @endif
        </div>

        <div class="grid gap-6 lg:grid-cols-[2fr,1.4fr]">
            {{-- KOLOM KIRI: DETAIL KONTEN --}}
            <section class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                {{-- Header konten --}}
                <div class="flex gap-4">
                    {{-- Cover / placeholder --}}
                    <div class="hidden sm:block">
                        <div class="h-28 w-28 rounded-xl bg-slate-100 overflow-hidden flex items-center justify-center">
                            @if($content->cover_path)
                                <img
                                    src="{{ asset('storage/'.$content->cover_path) }}"
                                    alt="{{ $content->title }}"
                                    class="h-full w-full object-cover"
                                >
                            @else
                                <span class="text-[11px] text-slate-400 px-2 text-center">
                                    Tidak ada cover
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="flex-1">
                        <h1 class="text-xl md:text-2xl font-semibold text-slate-900">
                            {{ $content->title }}
                        </h1>

                        <div class="mt-1 flex flex-wrap items-center gap-2 text-[11px] text-slate-500">
                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5">
                                {{-- tipe konten --}}
                                <span class="h-1.5 w-1.5 rounded-full bg-[#fbc926]"></span>
                                <span class="font-medium text-slate-700">
                                    {{ $content->type ? strtoupper($content->type) : 'PRODUK DIGITAL' }}
                                </span>
                            </span>

                            @if($creator)
                                <span>•</span>
                                <span>
                                    oleh
                                    <span class="font-medium text-slate-800">
                                        {{ $creator->name }}
                                    </span>
                                </span>
                            @endif

                            <span>•</span>
                            <span>
                                Diterbitkan
                                {{ optional($content->created_at)->translatedFormat('d M Y') ?? '-' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Deskripsi --}}
                <div class="mt-4 border-t border-slate-100 pt-4 text-sm text-slate-700 leading-relaxed">
                    @if(!empty($content->description))
                        {!! nl2br(e($content->description)) !!}
                    @else
                        <p class="text-slate-500 text-sm">
                            Belum ada deskripsi yang ditambahkan untuk konten ini.
                        </p>
                    @endif
                </div>

                {{-- Info tambahan (opsional) --}}
                <div class="mt-5 grid gap-3 sm:grid-cols-3 text-[11px] text-slate-500">
                    <div>
                        <p class="text-[10px] uppercase tracking-wide text-slate-400">Jenis konten</p>
                        <p class="mt-0.5 font-medium text-slate-800">
                            {{ $content->type ? ucfirst($content->type) : 'Produk digital' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-wide text-slate-400">Status</p>
                        <p class="mt-0.5 font-medium text-emerald-700">
                            Terbit
                        </p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-wide text-slate-400">Terakhir diubah</p>
                        <p class="mt-0.5">
                            {{ optional($content->updated_at)->diffForHumans() ?? '-' }}
                        </p>
                    </div>
                </div>
            </section>

            {{-- KOLOM KANAN: CARD PEMBELIAN / AKSES --}}
            <aside class="space-y-4">
                <section class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <p class="text-[11px] font-medium text-slate-500">
                        Akses konten
                    </p>

                    <div class="mt-3 flex items-baseline gap-2">
                        @if(($content->price ?? 0) > 0)
                            <p class="text-2xl font-semibold text-slate-900">
                                Rp {{ number_format($content->price, 0, ',', '.') }}
                            </p>
                            <p class="text-[11px] text-slate-400">
                                sekali bayar, akses selamanya
                            </p>
                        @else
                            <p class="text-lg font-semibold text-emerald-600">
                                Gratis
                            </p>
                        @endif
                    </div>

                    {{-- Tombol beli / akses (placeholder, nanti dihubungkan ke sistem order) --}}
                    <div class="mt-4 flex flex-col gap-2">
                        @if(($content->price ?? 0) > 0)
                            <button
                                type="button"
                                class="cursor-pointer inline-flex items-center justify-center gap-2 rounded-full bg-[#1d428a] px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-[#163268] w-full"
                            >
                                Beli konten ini
                            </button>
                        @else
                            <button
                                type="button"
                                class="cursor-pointer inline-flex items-center justify-center gap-2 rounded-full bg-emerald-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-emerald-700 w-full"
                            >
                                Akses konten gratis
                            </button>
                        @endif

                        {{-- Info kecil --}}
                        <p class="text-[10px] text-slate-400 text-center">
                            Pembayaran dan akses konten akan diatur langsung oleh platform Noorly.
                        </p>
                    </div>
                </section>

                {{-- Info kreator (kalau ada) --}}
                @if($creator)
                    <section class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-[#fbc926] text-white flex items-center justify-center text-sm font-semibold">
                                {{ strtoupper(mb_substr($creator->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-900">
                                    {{ $creator->name }}
                                </p>
                                <p class="text-[11px] text-slate-500">
                                    Kreator di Noorly
                                </p>
                            </div>
                        </div>

                        <a
                            href="{{ route('creator.public.show', $creator->id) }}"
                            class="mt-3 inline-flex items-center gap-1 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-[11px] font-medium text-slate-700 hover:bg-slate-50 cursor-pointer"
                        >
                            Lihat profil kreator
                        </a>
                    </section>
                @endif
            </aside>
        </div>
    </div>
</div>
@endsection
