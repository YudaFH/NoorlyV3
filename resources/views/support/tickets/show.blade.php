@extends('layouts.app')

@section('title', 'Detail tiket bantuan')

@section('content')
<div class="min-h-screen bg-slate-50 pt-24 pb-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Back --}}
        <div class="mb-4">
            <a href="{{ route('support.tickets.index') }}"
               class="inline-flex items-center gap-1 text-xs text-slate-500 hover:text-slate-700 cursor-pointer">
                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                          d="M12.79 5.23a.75.75 0 010 1.06L9.56 9.5H16a.75.75 0 010 1.5H9.56l3.23 3.21a.75.75 0 11-1.06 1.06l-4.5-4.5a.75.75 0 010-1.06l4.5-4.5a.75.75 0 011.06 0z"
                          clip-rule="evenodd" />
                </svg>
                <span>Kembali ke daftar tiket</span>
            </a>
        </div>

        {{-- Header tiket --}}
        <div class="mb-4 rounded-2xl bg-white border border-slate-100 p-5 shadow-sm">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-[11px] text-slate-400 mb-1">
                        Tiket #{{ $ticket->id }}
                    </p>
                    <h1 class="text-lg sm:text-xl font-semibold text-slate-900">
                        {{ $ticket->subject }}
                    </h1>
                    <p class="mt-1 text-[11px] text-slate-500">
                        Dibuat {{ optional($ticket->created_at)->diffForHumans() ?? '-' }}
                    </p>
                </div>

                <div class="flex flex-col items-start sm:items-end gap-1">
                    @php $status = $ticket->status ?? 'open'; @endphp
                    @if($status === 'open')
                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-3 py-0.5 text-[11px] text-amber-700">
                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                            Open
                        </span>
                    @elseif($status === 'answered')
                        <span class="inline-flex items-center gap-1 rounded-full bg-sky-50 px-3 py-0.5 text-[11px] text-sky-700">
                            <span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>
                            Dijawab tim Noorly
                        </span>
                    @elseif($status === 'closed')
                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-0.5 text-[11px] text-slate-600">
                            <span class="h-1.5 w-1.5 rounded-full bg-slate-500"></span>
                            Selesai
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-0.5 text-[11px] text-slate-600">
                            {{ ucfirst($status) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

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

        {{-- Percakapan --}}
        <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-5">
            <h2 class="text-sm font-semibold text-slate-900 mb-3">
                Percakapan
            </h2>

            <div class="space-y-3 mb-4 max-h-[420px] overflow-y-auto pr-1">
                @forelse($messages as $message)
                    @php
                        $isUser = $message->user_id === $user->id;
                    @endphp
                    <div class="flex {{ $isUser ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[80%] rounded-2xl px-3 py-2 text-[13px]
                            {{ $isUser ? 'bg-[#1d428a] text-white rounded-br-sm' : 'bg-slate-100 text-slate-800 rounded-bl-sm' }}">
                            <p class="whitespace-pre-line">
                                {{ $message->message }}
                            </p>
                            <p class="mt-1 text-[10px] {{ $isUser ? 'text-slate-200/80' : 'text-slate-500' }}">
                                {{ optional($message->created_at)->format('d M Y, H:i') ?? '' }}
                                @unless($isUser)
                                    • Tim Noorly
                                @endunless
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-[12px] text-slate-500">
                        Belum ada pesan di tiket ini.
                    </p>
                @endforelse
            </div>

            {{-- Form balas --}}
            <div class="border-t border-slate-100 pt-3">
                @if($ticket->status === 'closed')
                    <p class="text-[12px] text-slate-500 mb-2">
                        Tiket ini sudah ditandai <span class="font-semibold">selesai</span>. 
                        Jika kamu masih mengalami kendala, kamu bisa membuat tiket baru dari halaman "Tiket & bantuan".
                    </p>
                @else
                    <form action="{{ route('support.tickets.reply', $ticket->id) }}" method="POST" class="space-y-2">
                        @csrf
                        <label class="block text-[12px] font-medium text-slate-700">
                            Balas pesan
                        </label>
                        <textarea
                            name="message"
                            rows="3"
                            placeholder="Tulis balasan atau update terbaru terkait masalahmu..."
                            class="block w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-[#fbc926] focus:ring-[#fbc926]"
                        >{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                        @enderror

                        <div class="flex items-center justify-between">
                            <p class="text-[11px] text-slate-400">
                                Tim Noorly akan memberi notifikasi ketika ada balasan baru.
                            </p>
                            <button
                                type="submit"
                                class="inline-flex items-center gap-2 rounded-full bg-[#1d428a] px-4 py-1.5 text-[13px] font-semibold text-white shadow-sm hover:bg-[#163268] cursor-pointer"
                            >
                                Kirim balasan
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
