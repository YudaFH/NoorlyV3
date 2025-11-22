@extends('layouts.noorly') {{-- sesuaikan dengan layout utamamu --}}

@section('title', 'Jadi kreator di Noorly')

@section('content')
<div class="min-h-screen bg-slate-50 pt-24 pb-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Alert status --}}
        @if(session('status_creator'))
            <div class="mb-4 rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 flex items-start gap-3">
                <div class="mt-0.5">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293A1 1 0 006.293 10.707l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="flex-1 text-[13px] leading-snug">
                    {{ session('status_creator') }}
                </div>
            </div>
        @endif

        {{-- Header --}}
        <header class="mb-8">
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-semibold text-slate-900 mb-3">
                Jadi kreator di Noorly
            </h1>
            <p class="text-sm sm:text-base text-slate-600 max-w-2xl">
                Monetisasi keahlianmu lewat e-book, kelas video, template, dan produk digital lainnya.
                Isi formulir singkat di bawah ini supaya tim Noorly bisa mengenal profilmu dan menyiapkan akses dashboard kreator.
            </p>

            @if(isset($application) && $application)
                <div class="mt-4 inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-[11px] text-slate-600">
                    <span>Status pengajuan terakhir:</span>
                    @if($application->status === 'pending')
                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 text-amber-700">
                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> Pending review
                        </span>
                    @elseif($application->status === 'approved')
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-emerald-700">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Disetujui
                        </span>
                    @elseif($application->status === 'rejected')
                        <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2 py-0.5 text-rose-700">
                            <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span> Ditolak
                        </span>
                    @endif

                    <span class="text-[10px] text-slate-400 ml-1">
                        Diajukan {{ optional($application->created_at)->diffForHumans() }}
                    </span>
                </div>
            @endif
        </header>

        <div class="grid gap-6 md:grid-cols-[minmax(0,2fr),minmax(0,1.3fr)]">
            {{-- FORM --}}
            <section class="rounded-2xl bg-white border border-slate-100 shadow-sm p-5 sm:p-6">
                <form action="{{ route('creators.onboarding.submit') }}" method="POST" class="space-y-5">
                    @csrf

                    {{-- Nama lengkap --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Nama lengkap
                            <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="full_name"
                            value="{{ old('full_name', $user->name) }}"
                            class="block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-[#fbc926] focus:ring-[#fbc926]"
                        >
                        @error('full_name')
                            <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tagline --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Tagline singkat
                            <span class="text-[11px] font-normal text-slate-400">(opsional)</span>
                        </label>
                        <input
                            type="text"
                            name="tagline"
                            value="{{ old('tagline', $application->tagline ?? '') }}"
                            placeholder="Contoh: Mengajar investasi saham untuk pemula"
                            class="block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-[#fbc926] focus:ring-[#fbc926]"
                        >
                        @error('tagline')
                            <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Niche + level pengalaman --}}
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                Niche utama
                                <span class="text-[11px] font-normal text-slate-400">(opsional)</span>
                            </label>
                            <input
                                type="text"
                                name="niche"
                                value="{{ old('niche', $application->niche ?? '') }}"
                                placeholder="Contoh: Pasar modal, personal finance, desain, dll."
                                class="block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-[#fbc926] focus:ring-[#fbc926]"
                            >
                            @error('niche')
                                <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                Level pengalaman
                                <span class="text-rose-500">*</span>
                            </label>
                            @php
                                $exp = old('experience_level', $application->experience_level ?? 'pemula');
                            @endphp
                            <select
                                name="experience_level"
                                class="block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-[#fbc926] focus:ring-[#fbc926]"
                            >
                                <option value="pemula" {{ $exp === 'pemula' ? 'selected' : '' }}>Pemula (baru mulai jual konten)</option>
                                <option value="menengah" {{ $exp === 'menengah' ? 'selected' : '' }}>Menengah (sudah punya beberapa produk)</option>
                                <option value="berpengalaman" {{ $exp === 'berpengalaman' ? 'selected' : '' }}>Berpengalaman (sering jual kelas / produk digital)</option>
                            </select>
                            @error('experience_level')
                                <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Jenis konten --}}
                    @php
                        $selectedTypes = collect(explode(',', old('content_types_string', $application->content_types ?? '')))
                            ->map(fn($v) => trim($v))
                            ->filter()
                            ->values()
                            ->all();
                    @endphp

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Jenis konten yang ingin kamu jual
                            <span class="text-[11px] font-normal text-slate-400">(boleh lebih dari satu)</span>
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-[13px]">
                            @php
                                $options = [
                                    'kelas online' => 'Kelas online / webinar',
                                    'ebook'        => 'E-book / modul',
                                    'template'     => 'Template (Canva, Notion, dsb.)',
                                    'rekaman'      => 'Rekaman kelas / webinar',
                                    'membership'   => 'Membership / komunitas',
                                    'lainnya'      => 'Lainnya',
                                ];
                            @endphp

                            @foreach($options as $value => $label)
                                <label class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1.5 cursor-pointer hover:bg-slate-100">
                                    <input
                                        type="checkbox"
                                        name="content_types[]"
                                        value="{{ $value }}"
                                        @checked(in_array($value, $selectedTypes))
                                        class="h-3.5 w-3.5 rounded border-slate-300 text-[#fbc926] focus:ring-[#fbc926]"
                                    >
                                    <span class="text-[12px] text-slate-700">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('content_types')
                            <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Sosial media & kontak --}}
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                Instagram (username / link)
                            </label>
                            <input
                                type="text"
                                name="social_instagram"
                                value="{{ old('social_instagram', $application->social_instagram ?? '') }}"
                                placeholder="@username atau link profil"
                                class="block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-[#fbc926] focus:ring-[#fbc926]"
                            >
                            @error('social_instagram')
                                <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                TikTok (username / link)
                            </label>
                            <input
                                type="text"
                                name="social_tiktok"
                                value="{{ old('social_tiktok', $application->social_tiktok ?? '') }}"
                                placeholder="@username atau link profil"
                                class="block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-[#fbc926] focus:ring-[#fbc926]"
                            >
                            @error('social_tiktok')
                                <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                YouTube (channel / link)
                            </label>
                            <input
                                type="text"
                                name="social_youtube"
                                value="{{ old('social_youtube', $application->social_youtube ?? '') }}"
                                placeholder="Link channel (opsional)"
                                class="block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-[#fbc926] focus:ring-[#fbc926]"
                            >
                            @error('social_youtube')
                                <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                Nomor WhatsApp
                                <span class="text-[11px] font-normal text-slate-400">(opsional, untuk komunikasi tim Noorly)</span>
                            </label>
                            <input
                                type="text"
                                name="phone"
                                value="{{ old('phone', $application->phone ?? '') }}"
                                placeholder="Contoh: 0812xxxxxxx"
                                class="block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-[#fbc926] focus:ring-[#fbc926]"
                            >
                            @error('phone')
                                <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Portfolio --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Link portfolio / contoh karya
                            <span class="text-[11px] font-normal text-slate-400">(opsional)</span>
                        </label>
                        <input
                            type="url"
                            name="portfolio_url"
                            value="{{ old('portfolio_url', $application->portfolio_url ?? '') }}"
                            placeholder="Contoh: link Notion, Google Drive, website pribadi, dsb."
                            class="block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-[#fbc926] focus:ring-[#fbc926]"
                        >
                        @error('portfolio_url')
                            <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tentang kamu --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Ceritakan tentang dirimu dan rencana kontenmu di Noorly
                        </label>
                        <textarea
                            name="about"
                            rows="4"
                            placeholder="Contoh: pengalaman mengajar, target audiens, jenis konten yang ingin dibuat, dsb."
                            class="block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-[#fbc926] focus:ring-[#fbc926]"
                        >{{ old('about', $application->about ?? '') }}</textarea>
                        @error('about')
                            <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tombol submit --}}
                    <div class="flex items-center justify-between pt-3">
                        <p class="text-[11px] text-slate-500 max-w-xs">
                            Dengan mengirim formulir ini, kamu setuju mengikuti kebijakan kreator Noorly.
                            Tim kami bisa menghubungi kamu jika perlu info tambahan.
                        </p>

                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 rounded-full bg-[#1d428a] px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#163268] cursor-pointer"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M3.172 7.172a4 4 0 015.656-5.656l.586.586.586-.586a4 4 0 115.656 5.656L10 16.414l-6.828-6.828z" />
                            </svg>
                            <span>Kirim pengajuan kreator</span>
                        </button>
                    </div>
                </form>
            </section>

            {{-- SIDEBAR INFO --}}
            <aside class="space-y-4">
                <div class="rounded-2xl bg-[#0e141b] text-slate-100 p-5">
                    <h2 class="text-sm font-semibold mb-2">
                        Kenapa jadi kreator di Noorly?
                    </h2>
                    <ul class="space-y-2 text-[12px] text-slate-300">
                        <li>• Jual e-book, kelas, dan produk digital tanpa ribet.</li>
                        <li>• Sistem pembayaran aman & dukungan tim Noorly.</li>
                        <li>• Dashboard kreator untuk monitor penjualan & saldo.</li>
                        <li>• Cocok untuk mahasiswa, praktisi, mentor, dan content creator.</li>
                    </ul>
                </div>

                <div class="rounded-2xl bg-white border border-slate-100 p-5">
                    <h3 class="text-sm font-semibold text-slate-900 mb-2">
                        Apa yang terjadi setelah kamu daftar?
                    </h3>
                    <ol class="space-y-2 text-[12px] text-slate-600">
                        <li><span class="font-semibold">1.</span> Tim Noorly akan review profil dan pengajuanmu.</li>
                        <li><span class="font-semibold">2.</span> Kamu akan mendapat notifikasi via email / di Noorly.</li>
                        <li><span class="font-semibold">3.</span> Kalau disetujui, kamu akan mendapat akses dashboard kreator.</li>
                    </ol>
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection
