@extends('layouts.noorly')

@section('title', 'Tiket & bantuan')

@section('content')
<div class="min-h-screen bg-slate-50 pt-24 pb-16">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <header class="mb-8 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-slate-900">
                    Tiket & bantuan
                </h1>
                <p class="mt-1 text-sm text-slate-500 max-w-2xl">
                    Kirim pertanyaan atau laporan terkait akun, pembayaran, atau konten Noorly. 
                    Tim kami akan merespons lewat tiket ini dan email yang terdaftar.
                </p>
            </div>
        </header>

        {{-- Alert status --}}
        @if(session('status_support'))
            <div class="mb-4 rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 flex items-start gap-3">
                <div class="mt-0.5">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293A1 1 0 006.293 10.707l2 2a1 1 0 001.414 0l4-4z"
                              clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="flex-1 text-[13px] leading-snug">
                    {{ session('status_support') }}
                </div>
            </div>
        @endif

        <div class="grid gap-6 md:grid-cols-[minmax(0,1.4fr),minmax(0,1fr)]">
            {{-- List tiket --}}
            <section class="rounded-2xl bg-white border border-slate-100 shadow-sm p-5 sm:p-6">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-slate-900">
                        Riwayat tiket saya
                    </h2>
                    <span class="text-[11px] text-slate-400">
                        Total: {{ $tickets->total() }} tiket
                    </span>
                </div>

                @if($tickets->count())
                    <div class="space-y-2">
                        @foreach($tickets as $ticket)
                            <a
                                href="{{ route('support.tickets.show', $ticket->id) }}"
                                class="block rounded-xl border border-slate-100 bg-slate-50/60 hover:bg-slate-100 px-3 py-2.5 text-sm transition cursor-pointer"
                            >
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex-1">
                                        <p class="font-medium text-slate-900 truncate">
                                            #{{ $ticket->id }} • {{ $ticket->subject }}
                                        </p>
                                        <p class="mt-0.5 text-[11px] text-slate-500">
                                            Dibuat {{ optional($ticket->created_at)->diffForHumans() ?? '-' }}
                                        </p>
                                    </div>
                                    <div class="flex flex-col items-end gap-1">
                                        @php
                                            $status = $ticket->status ?? 'open';
                                        @endphp

                                        @if($status === 'open')
                                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 text-[11px] text-amber-700">
                                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                                Open
                                            </span>
                                        @elseif($status === 'answered')
                                            <span class="inline-flex items-center gap-1 rounded-full bg-sky-50 px-2 py-0.5 text-[11px] text-sky-700">
                                                <span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>
                                                Dijawab
                                            </span>
                                        @elseif($status === 'closed')
                                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-[11px] text-slate-600">
                                                <span class="h-1.5 w-1.5 rounded-full bg-slate-500"></span>
                                                Selesai
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-[11px] text-slate-600">
                                                {{ ucfirst($status) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        {{ $tickets->links() }}
                    </div>
                @else
                    <div class="py-6 text-center text-sm text-slate-500">
                        Kamu belum pernah membuat tiket bantuan.
                        <br>
                        <span class="text-[11px] text-slate-400">
                            Gunakan formulir di samping untuk mengirim pertanyaan pertamamu.
                        </span>
                    </div>
                @endif
            </section>

            {{-- Form tiket baru --}}
            <section class="rounded-2xl bg-white border border-slate-100 shadow-sm p-5 sm:p-6">
                <h2 class="text-sm font-semibold text-slate-900 mb-2">
                    Kirim tiket bantuan baru
                </h2>
                <p class="text-[12px] text-slate-500 mb-4">
                    Jelaskan kendalamu dengan jelas agar tim Noorly bisa membantu lebih cepat
                    (contoh: masalah pembayaran, akses konten, atau akun).
                </p>

                <form action="{{ route('support.tickets.store') }}" method="POST" class="space-y-4">
                    @csrf

                    {{-- Subjek --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Subjek tiket
                            <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="subject"
                            value="{{ old('subject') }}"
                            placeholder="Contoh: Tidak bisa mengakses konten yang sudah saya beli"
                            class="block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-[#fbc926] focus:ring-[#fbc926]"
                        >
                        @error('subject')
                            <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Pesan --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Jelaskan masalahmu
                            <span class="text-rose-500">*</span>
                        </label>
                        <textarea
                            name="message"
                            rows="4"
                            placeholder="Ceritakan detailnya: kapan mulai terjadi, email akun yang dipakai, dan jika ada, sertakan kode/ID pesanan."
                            class="block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-[#fbc926] focus:ring-[#fbc926]"
                        >{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 rounded-full bg-[#1d428a] px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#163268] cursor-pointer"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2.94 2.94a.75.75 0 01.82-.17l13 5a.75.75 0 01.02 1.38l-5.55 2.6-2.6 5.55a.75.75 0 01-1.38-.02l-5-13a.75.75 0 01.17-.82z" />
                        </svg>
                        <span>Kirim tiket</span>
                    </button>
                </form>
            </section>
        </div>
    </div>
</div>
@endsection
