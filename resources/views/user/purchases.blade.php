{{-- resources/views/user/purchases.blade.php --}}
@extends('layouts.noorly')

@section('title', 'Konten yang Saya Beli — Noorly')

@section('content')
<div class="pt-24 pb-12 bg-slate-50 min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <header class="mb-6">
            <h1 class="text-2xl md:text-3xl font-semibold text-slate-900">
                Konten yang saya beli
            </h1>
            <p class="mt-1 text-sm text-slate-500 max-w-2xl">
                Semua konten digital yang sudah kamu beli di Noorly akan muncul di sini.
                Kamu bisa mengakses ulang link konten, kelas, atau materi kapan saja.
            </p>
        </header>

        {{-- Info kecil --}}
        @if (session('purchases_info'))
            <div class="mb-4 rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('purchases_info') }}
            </div>
        @endif

        @if ($orders->count() > 0)
            <section class="space-y-3">
                @foreach ($orders as $order)
                    @php
                        $content = $order->content;
                        $isAccessible = $content && in_array($content->status ?? 'published', ['published', 'active']);
                        $price = $order->amount ?? $content->price ?? 0;
                    @endphp

                    <article class="rounded-2xl border border-slate-100 bg-white px-4 py-3 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                        {{-- Kiri: info konten --}}
                        <div class="flex-1 flex items-start gap-3">
                            {{-- Icon / inisial konten --}}
                            <div class="mt-0.5 h-10 w-10 flex items-center justify-center rounded-xl bg-[#fef6e0] text-[#d4a116] text-xs font-semibold">
                                @if($content && !empty($content->type))
                                    {{ strtoupper(substr($content->type, 0, 2)) }}
                                @else
                                    NO
                                @endif
                            </div>

                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    @if($content)
                                        <p class="text-sm font-semibold text-slate-900">
                                            {{ $content->title }}
                                        </p>
                                    @else
                                        <p class="text-sm font-semibold text-slate-900">
                                            Konten tidak tersedia
                                        </p>
                                    @endif

                                    {{-- Status akses --}}
                                    @if ($isAccessible)
                                        <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] text-emerald-700">
                                            Akses masih aktif
                                        </span>
                                    @else
                                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] text-slate-500">
                                            Tidak dapat diakses
                                        </span>
                                    @endif

                                    {{-- Status pembayaran --}}
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] text-slate-500">
                                        {{ strtoupper($order->payment_status ?? $order->status ?? 'paid') }}
                                    </span>
                                </div>

                                <p class="text-[11px] text-slate-500">
                                    Nomor order:
                                    <span class="font-mono text-slate-700">
                                        #{{ $order->id }}
                                    </span>
                                    • Tanggal:
                                    <span class="font-medium text-slate-700">
                                        {{ optional($order->created_at)->format('d M Y, H:i') }}
                                    </span>
                                </p>

                                <p class="text-[11px] text-slate-500">
                                    Harga:
                                    <span class="font-semibold text-slate-900">
                                        Rp {{ number_format($price, 0, ',', '.') }}
                                    </span>
                                </p>

                                @if($content && !empty($content->short_description ?? null))
                                    <p class="text-[11px] text-slate-400 line-clamp-2">
                                        {{ $content->short_description }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        {{-- Kanan: aksi --}}
                        <div class="flex flex-col items-start md:items-end gap-2 text-[11px] text-slate-500">
                            {{-- Metrik kecil kalau ada --}}
                            @if($content)
                                <div class="flex flex-wrap gap-3">
                                    @if(isset($content->buyers_count))
                                        <div>
                                            <p class="text-[10px] uppercase tracking-wide text-slate-400">
                                                Pembeli
                                            </p>
                                            <p class="text-sm font-semibold text-slate-900">
                                                {{ $content->buyers_count }}
                                            </p>
                                        </div>
                                    @endif

                                    @if(isset($content->views_count))
                                        <div>
                                            <p class="text-[10px] uppercase tracking-wide text-slate-400">
                                                Dilihat
                                            </p>
                                            <p class="text-sm font-semibold text-slate-900">
                                                {{ $content->views_count }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <div class="flex flex-wrap gap-2 mt-1">
                                @if($content && Route::has('contents.show') && $isAccessible)
                                    <a
                                        href="{{ route('contents.show', $content->slug) }}"
                                        class="cursor-pointer inline-flex items-center rounded-full bg-[#1d428a] px-4 py-1.5 text-[11px] font-semibold text-white shadow-sm hover:bg-[#163268]"
                                        target="_blank"
                                    >
                                        Buka konten
                                    </a>
                                @else
                                    <button
                                        type="button"
                                        class="inline-flex items-center rounded-full bg-slate-100 px-4 py-1.5 text-[11px] font-medium text-slate-500 cursor-not-allowed"
                                    >
                                        Konten tidak tersedia
                                    </button>
                                @endif

                                @if(Route::has('support.tickets.index'))
                                    <a
                                        href="{{ route('support.tickets.index') }}"
                                        class="cursor-pointer inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1.5 text-[11px] font-medium text-slate-700 hover:bg-slate-50"
                                    >
                                        Butuh bantuan?
                                    </a>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </section>

            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        @else
            {{-- Empty state --}}
            <section class="mt-10 flex flex-col items-center justify-center text-center">
                <div class="mb-4 h-16 w-16 rounded-full bg-[#fef6e0] flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#d4a116]" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M4 3a2 2 0 00-2 2v9.5A1.5 1.5 0 003.5 16h13a1.5 1.5 0 001.5-1.5V7a2 2 0 00-2-2h-5.586a2 2 0 01-1.414-.586L7.586 3H4z" />
                    </svg>
                </div>
                <h2 class="text-sm font-semibold text-slate-900 mb-1">
                    Kamu belum membeli konten apa pun
                </h2>
                <p class="text-xs text-slate-500 max-w-sm mb-3">
                    Jelajahi e-book, kelas, dan produk digital dari kreator di Noorly.
                    Setelah membeli, konten akan muncul di halaman ini dan bisa kamu akses kapan saja.
                </p>
                <a
                    href="{{ route('contents.index') }}"
                    class="cursor-pointer inline-flex items-center gap-2 rounded-full bg-[#1d428a] px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-[#163268]"
                >
                    Jelajahi konten
                </a>
            </section>
        @endif
    </div>
</div>
@endsection
