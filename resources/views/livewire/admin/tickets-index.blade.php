@php
    use Illuminate\Support\Str;
@endphp

<div class="flex h-screen bg-slate-50">
    {{-- SIDEBAR ADMIN --}}
    @include('livewire.admin.sidebar', ['sidebarOpen' => $sidebarOpen])

    {{-- MAIN --}}
    <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8 overflow-y-auto">
        {{-- HEADER --}}
        <header class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-slate-900">
                    Tiket support
                </h1>
                <p class="mt-1 text-sm text-slate-500 max-w-2xl">
                    Kelola pertanyaan, komplain, dan bantuan dari kreator maupun pengguna Noorly.
                </p>

                <div class="mt-3 flex flex-wrap gap-3 text-[11px] text-slate-500">
                    <span>Semua: <span class="font-semibold text-slate-700">{{ $stats['total_all'] }}</span></span>
                    <span>Open: <span class="font-semibold text-emerald-600">{{ $stats['total_open'] }}</span></span>
                    <span>Sedang diproses: <span class="font-semibold text-sky-600">{{ $stats['total_progress'] }}</span></span>
                    <span>Terselesaikan: <span class="font-semibold text-emerald-700">{{ $stats['total_resolved'] }}</span></span>
                    <span>Ditutup: <span class="font-semibold text-slate-700">{{ $stats['total_closed'] }}</span></span>
                </div>
            </div>

            <div class="flex flex-col items-end gap-2">
                @if (session('status_admin_tickets'))
                    <div class="rounded-full bg-emerald-50 border border-emerald-100 px-3 py-1 text-[11px] text-emerald-700">
                        {{ session('status_admin_tickets') }}
                    </div>
                @endif
            </div>
        </header>

        {{-- FILTER BAR --}}
        <section class="mb-4 rounded-2xl border border-slate-100 bg-white px-4 py-3 shadow-sm">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                {{-- Search --}}
                <div class="w-full md:max-w-sm">
                    <label class="sr-only" for="search-tickets">Cari tiket</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 103.473 9.8l3.613 3.614a.75.75 0 101.06-1.06l-3.614-3.613A5.5 5.5 0 009 3.5zm-4 5.5a4 4 0 118 0 4 4 0 01-8 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <input
                            id="search-tickets"
                            type="text"
                            wire:model.debounce.400ms="search"
                            class="block w-full rounded-full border border-slate-200 bg-slate-50 pl-8 pr-3 py-1.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#1d428a] focus:ring-[#1d428a]"
                            placeholder="Cari subjek tiket, isi pesan, atau nama pengguna..."
                        >
                    </div>
                </div>

                {{-- Filters --}}
                <div class="flex flex-wrap items-center gap-2 justify-end text-[11px]">
                    {{-- Status --}}
                    <div class="flex items-center gap-1">
                        <span class="text-slate-500">Status:</span>
                        <select
                            wire:model="statusFilter"
                            class="cursor-pointer rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-[11px] text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                        >
                            <option value="all">Semua</option>
                            <option value="open">Open</option>
                            <option value="in_progress">Sedang diproses</option>
                            <option value="resolved">Terselesaikan</option>
                            <option value="closed">Ditutup</option>
                        </select>
                    </div>

                    {{-- Kategori --}}
                    <div class="flex items-center gap-1">
                        <span class="text-slate-500">Kategori:</span>
                        <select
                            wire:model="categoryFilter"
                            class="cursor-pointer rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-[11px] text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                        >
                            <option value="all">Semua</option>
                            <option value="payout">Penarikan dana</option>
                            <option value="order">Order & pembayaran</option>
                            <option value="technical">Teknis / bug</option>
                            <option value="account">Akun & login</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>

                    {{-- Sort --}}
                    <div class="flex items-center gap-1">
                        <span class="text-slate-500">Urutkan:</span>
                        <select
                            wire:model="sort"
                            class="cursor-pointer rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-[11px] text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                        >
                            <option value="latest">Terbaru</option>
                            <option value="oldest">Terlama</option>
                            <option value="priority_high">Prioritas tertinggi</option>
                            <option value="priority_low">Prioritas terendah</option>
                        </select>
                    </div>
                </div>
            </div>
        </section>

        {{-- LIST TIKET --}}
        @if($tickets->count() > 0)
            <section class="space-y-3">
                @foreach($tickets as $ticket)
                    @php
                        $user = $ticket->user;
                    @endphp

                    <article class="rounded-2xl border border-slate-100 bg-white px-4 py-3 shadow-sm flex flex-col gap-3">
                        {{-- BAR ATAS --}}
                        <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
                            <div class="flex items-start gap-3">
                                {{-- Icon kategori --}}
                                <div class="mt-0.5 h-10 w-10 flex items-center justify-center rounded-xl bg-[#fef6e0] text-[#d4a116] text-xs font-semibold">
                                    @switch($ticket->category)
                                        @case('payout')
                                            WD
                                            @break
                                        @case('order')
                                            ORD
                                            @break
                                        @case('technical')
                                            BUG
                                            @break
                                        @case('account')
                                            ACC
                                            @break
                                        @default
                                            TKT
                                    @endswitch
                                </div>

                                <div class="space-y-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h2 class="text-sm font-semibold text-slate-900">
                                            {{ $ticket->subject ?? 'Tiket tanpa subjek' }}
                                        </h2>

                                        {{-- Badge status --}}
                                        @if($ticket->status === 'open')
                                            <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] text-emerald-700">
                                                Open
                                            </span>
                                        @elseif($ticket->status === 'in_progress')
                                            <span class="rounded-full bg-sky-50 px-2 py-0.5 text-[11px] text-sky-700">
                                                Sedang diproses
                                            </span>
                                        @elseif($ticket->status === 'resolved')
                                            <span class="rounded-full bg-indigo-50 px-2 py-0.5 text-[11px] text-indigo-700">
                                                Terselesaikan
                                            </span>
                                        @elseif($ticket->status === 'closed')
                                            <span class="rounded-full bg-slate-200 px-2 py-0.5 text-[11px] text-slate-700">
                                                Ditutup
                                            </span>
                                        @endif

                                        {{-- Badge prioritas --}}
                                        @if($ticket->priority === 'high')
                                            <span class="rounded-full bg-rose-50 px-2 py-0.5 text-[10px] text-rose-700">
                                                Prioritas tinggi
                                            </span>
                                        @elseif($ticket->priority === 'low')
                                            <span class="rounded-full bg-slate-50 px-2 py-0.5 text-[10px] text-slate-600">
                                                Prioritas rendah
                                            </span>
                                        @else
                                            <span class="rounded-full bg-amber-50 px-2 py-0.5 text-[10px] text-amber-700">
                                                Prioritas normal
                                            </span>
                                        @endif
                                    </div>

                                    <p class="text-[11px] text-slate-500">
                                        Kategori:
                                        <span class="font-medium text-slate-700">
                                            @switch($ticket->category)
                                                @case('payout') Penarikan dana @break
                                                @case('order') Order & pembayaran @break
                                                @case('technical') Teknis / bug @break
                                                @case('account') Akun & login @break
                                                @default Lainnya
                                            @endswitch
                                        </span>
                                        • Dibuat {{ optional($ticket->created_at)->diffForHumans() ?? '-' }}
                                    </p>

                                    <p class="text-[11px] text-slate-400">
                                        @if($user)
                                            Dari:
                                            <span class="font-medium text-slate-700">
                                                {{ $user->name }}
                                            </span>
                                            <span class="text-slate-400">
                                                ({{ $user->email }})
                                            </span>
                                        @else
                                            Pengguna tidak teridentifikasi
                                        @endif
                                    </p>

                                    @if(!empty($ticket->message))
                                        <p class="text-[11px] text-slate-500">
                                            {{ Str::limit($ticket->message, 140) }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            {{-- Aksi cepat --}}
                            <div class="flex flex-col items-end gap-2 text-[11px]">
                                {{-- Link detail --}}
                                <a
                                    href="{{ route('admin.tickets.show', $ticket->id) }}"
                                    class="cursor-pointer inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 font-medium text-slate-700 hover:bg-slate-50"
                                >
                                    Lihat detail
                                </a>

                                {{-- Ubah status --}}
                                <div class="flex flex-wrap gap-2 justify-end">
                                    <button
                                        type="button"
                                        wire:click="setStatus({{ $ticket->id }}, 'open')"
                                        class="cursor-pointer inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 font-medium text-slate-700 hover:bg-slate-50"
                                    >
                                        Tandai open
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="setStatus({{ $ticket->id }}, 'in_progress')"
                                        class="cursor-pointer inline-flex items-center rounded-full border border-sky-200 bg-sky-50 px-3 py-1 font-medium text-sky-700 hover:bg-sky-100"
                                    >
                                        Sedang diproses
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="setStatus({{ $ticket->id }}, 'resolved')"
                                        class="cursor-pointer inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 font-medium text-emerald-700 hover:bg-emerald-100"
                                    >
                                        Tandai selesai
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="setStatus({{ $ticket->id }}, 'closed')"
                                        class="cursor-pointer inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-3 py-1 font-medium text-slate-700 hover:bg-slate-100"
                                    >
                                        Tutup tiket
                                    </button>
                                </div>

                                {{-- Hapus --}}
                                <div class="flex items-center justify-end">
                                    <button
                                        type="button"
                                        onclick="if(confirm('Yakin ingin menghapus tiket ini secara permanen?')) { @this.call('delete', {{ $ticket->id }}); }"
                                        class="cursor-pointer inline-flex items-center gap-1 rounded-full bg-white px-4 py-1.5 text-[11px] font-semibold text-rose-600 border border-rose-200 hover:bg-rose-50"
                                    >
                                        Hapus tiket
                                    </button>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </section>

            <div class="mt-4">
                {{ $tickets->links() }}
            </div>
        @else
            {{-- EMPTY STATE --}}
            <section class="mt-8 flex flex-col items-center justify-center text-center">
                <div class="mb-4 h-16 w-16 rounded-full bg-slate-100 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M2.003 5.884L10 2l7.997 3.884A1 1 0 0118 6.764V9a8 8 0 11-16 0V6.764a1 1 0 01.003-.88z" />
                    </svg>
                </div>
                <h2 class="text-sm font-semibold text-slate-900 mb-1">
                    Belum ada tiket support
                </h2>
                <p class="text-xs text-slate-500 max-w-sm">
                    Saat kreator atau pengguna mengirimkan pertanyaan atau keluhan,
                    tiket akan muncul di halaman ini untuk kamu follow-up.
                </p>
            </section>
        @endif
    </main>
</div>
