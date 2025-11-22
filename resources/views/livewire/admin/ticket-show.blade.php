<div class="flex h-screen bg-slate-50">
    {{-- SIDEBAR ADMIN --}}
    @include('livewire.admin.sidebar', ['sidebarOpen' => $sidebarOpen])

    {{-- MAIN --}}
    <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8 overflow-y-auto">
        {{-- HEADER --}}
        <header class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400 mb-1">
                    Tiket support
                </p>
                <h1 class="text-2xl md:text-3xl font-semibold text-slate-900">
                    {{ $ticket->subject ?? 'Tiket tanpa subjek' }}
                </h1>
                <p class="mt-1 text-sm text-slate-500 max-w-2xl">
                    Detail tiket support dari pengguna terkait kategori
                    <span class="font-semibold">
                        @switch($ticket->category)
                            @case('payout') Penarikan dana @break
                            @case('order') Order & pembayaran @break
                            @case('technical') Teknis / bug @break
                            @case('account') Akun & login @break
                            @default Lainnya
                        @endswitch
                    </span>.
                </p>

                <div class="mt-3 flex flex-wrap gap-3 text-[11px] text-slate-500">
                    <span>Dibuat: <span class="font-semibold text-slate-700">{{ optional($ticket->created_at)->format('d M Y H:i') }}</span></span>
                    <span>Update: <span class="font-semibold text-slate-700">{{ optional($ticket->updated_at)->diffForHumans() }}</span></span>

                    @if($ticket->user)
                        <span>
                            Dari:
                            <span class="font-semibold text-slate-700">{{ $ticket->user->name }}</span>
                            <span class="text-slate-400">({{ $ticket->user->email }})</span>
                        </span>
                    @endif
                </div>
            </div>

            <div class="flex flex-col items-end gap-2">
                @if (session('status_admin_tickets'))
                    <div class="rounded-full bg-emerald-50 border border-emerald-100 px-3 py-1 text-[11px] text-emerald-700">
                        {{ session('status_admin_tickets') }}
                    </div>
                @endif

                <a
                    href="{{ route('admin.tickets.index') }}"
                    class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-[11px] font-medium text-slate-700 hover:bg-slate-50 cursor-pointer"
                >
                    Kembali ke daftar tiket
                </a>
            </div>
        </header>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(0,1.2fr)]">
            {{-- KONTEN TIKET --}}
            <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-semibold text-slate-900 mb-3">
                    Isi pesan
                </h2>

                <div class="rounded-xl bg-slate-50 border border-slate-100 px-4 py-3 text-sm text-slate-700 whitespace-pre-line">
                    {{ $ticket->message ?? '-' }}
                </div>
            </section>

            {{-- PANEL STATUS & AKSI --}}
            <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm flex flex-col gap-4">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900 mb-2">
                        Status tiket
                    </h2>

                    <div class="space-y-2 text-[11px]">
                        <p class="text-slate-500 mb-1">
                            Status saat ini:
                        </p>

                        <div class="inline-flex items-center gap-2 rounded-full bg-slate-50 px-3 py-1.5 border border-slate-200">
                            @if($ticket->status === 'open')
                                <span class="inline-flex h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                <span class="text-[11px] font-semibold text-emerald-700">Open</span>
                            @elseif($ticket->status === 'in_progress')
                                <span class="inline-flex h-1.5 w-1.5 rounded-full bg-sky-500"></span>
                                <span class="text-[11px] font-semibold text-sky-700">Sedang diproses</span>
                            @elseif($ticket->status === 'resolved')
                                <span class="inline-flex h-1.5 w-1.5 rounded-full bg-indigo-500"></span>
                                <span class="text-[11px] font-semibold text-indigo-700">Terselesaikan</span>
                            @elseif($ticket->status === 'closed')
                                <span class="inline-flex h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                <span class="text-[11px] font-semibold text-slate-700">Ditutup</span>
                            @else
                                <span class="inline-flex h-1.5 w-1.5 rounded-full bg-slate-300"></span>
                                <span class="text-[11px] font-semibold text-slate-700">{{ $ticket->status }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div>
                    <p class="text-[11px] text-slate-500 mb-2">
                        Ubah status tiket:
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <button
                            type="button"
                            wire:click="updateStatus('open')"
                            class="cursor-pointer inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1.5 text-[11px] font-medium text-slate-700 hover:bg-slate-50"
                        >
                            Tandai open
                        </button>
                        <button
                            type="button"
                            wire:click="updateStatus('in_progress')"
                            class="cursor-pointer inline-flex items-center rounded-full border border-sky-200 bg-sky-50 px-3 py-1.5 text-[11px] font-medium text-sky-700 hover:bg-sky-100"
                        >
                            Sedang diproses
                        </button>
                        <button
                            type="button"
                            wire:click="updateStatus('resolved')"
                            class="cursor-pointer inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-[11px] font-medium text-emerald-700 hover:bg-emerald-100"
                        >
                            Tandai selesai
                        </button>
                        <button
                            type="button"
                            wire:click="updateStatus('closed')"
                            class="cursor-pointer inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-[11px] font-medium text-slate-700 hover:bg-slate-100"
                        >
                            Tutup tiket
                        </button>
                    </div>
                </div>

                <div class="mt-2 pt-3 border-t border-slate-100">
                    <p class="text-[11px] text-slate-500 mb-2">
                        Aksi lain:
                    </p>
                    <button
                        type="button"
                        onclick="if(confirm('Yakin ingin menghapus tiket ini secara permanen?')) { @this.call('deleteTicket'); }"
                        class="cursor-pointer inline-flex items-center gap-1 rounded-full bg-white px-4 py-1.5 text-[11px] font-semibold text-rose-600 border border-rose-200 hover:bg-rose-50"
                    >
                        Hapus tiket ini
                    </button>
                </div>
            </section>
        </div>
    </main>
</div>
