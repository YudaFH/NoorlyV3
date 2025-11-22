{{-- resources/views/coming-soon.blade.php --}}
@extends('layouts.noorly')

@section('title', 'Segera Hadir - Noorly')

@section('content')
    <div class="min-h-[calc(100vh-72px)] bg-slate-50 pt-24 pb-10 px-4 flex items-center justify-center">
        @php
            // Biar judulnya menyesuaikan menu yang diklik
            $section = match (true) {
                request()->routeIs('konten')     => 'Konten',
                request()->routeIs('komunitas')  => 'Komunitas',
                request()->routeIs('acara')      => 'Acara',
                request()->routeIs('kontak')     => 'Kontak',
                default                          => 'Halaman',
            };
        @endphp

        <div class="max-w-xl w-full text-center">
            {{-- Chip kecil --}}
            <div class="inline-flex items-center gap-2 rounded-full bg-[#fff7d6] px-3 py-1 mb-5 border border-[#fbc926]/40">
                <span class="h-2 w-2 rounded-full bg-[#fbc926] animate-pulse"></span>
                <span class="text-[11px] font-semibold uppercase tracking-wide text-[#1d428a]">
                    Coming Soon
                </span>
            </div>

            {{-- Title --}}
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-semibold text-slate-900 mb-3">
                {{ $section }} sedang kami siapkan ✨
            </h1>

            {{-- Subtext --}}
            <p class="text-sm sm:text-base text-slate-500 mb-6">
                Tim Noorly sedang menyempurnakan fitur {{ strtolower($section) }} untuk kamu.
                Sementara itu, kamu bisa kembali ke beranda dan mulai jelajahi konten yang sudah tersedia.
            </p>

            {{-- Optional little "progress" bar --}}
            <div class="w-full max-w-xs mx-auto mb-6">
                <div class="h-1.5 rounded-full bg-slate-200 overflow-hidden">
                    <div class="h-full w-2/3 bg-[#fbc926] animate-pulse"></div>
                </div>
                <p class="mt-2 text-[11px] text-slate-400">
                    Terima kasih sudah menunggu 💛
                </p>
            </div>

            {{-- Buttons --}}
            <div class="flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('home') }}"
                   class="inline-flex items-center rounded-full bg-[#fbc926] px-5 py-2.5 text-sm font-semibold text-white border border-transparent hover:bg-white hover:text-[#fbc926] hover:border-[#fbc926] transform hover:scale-105 transition">
                    Kembali ke Beranda
                </a>

                <a href="mailto:halo@noorly.com"
                   class="inline-flex items-center gap-2 text-sm font-medium text-[#1d428a] hover:underline">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M2.94 6.94A2.5 2.5 0 015.5 5h9a2.5 2.5 0 012.5 2.5v5a2.5 2.5 0 01-2.5 2.5h-9A2.5 2.5 0 013 12.5v-5c0-.69.28-1.32.78-1.81z" />
                        <path d="M4.21 7.56a.75.75 0 011.04-.15L10 10.4l4.75-2.99a.75.75 0 11.8 1.26l-5.14 3.23a.75.75 0 01-.8 0L4.36 8.67a.75.75 0 01-.15-1.11z" />
                    </svg>
                    Hubungi kami
                </a>
            </div>
        </div>
    </div>
@endsection
