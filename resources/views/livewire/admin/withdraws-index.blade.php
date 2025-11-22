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
                    Penarikan saldo
                </h1>
                <p class="mt-1 text-sm text-slate-500 max-w-2xl">
                    Pantau dan kelola semua permintaan penarikan saldo kreator di Noorly.
                </p>

                <div class="mt-3 flex flex-wrap gap-3 text-[11px] text-slate-500">
                    <span>Semua: <span class="font-semibold text-slate-700">{{ $totalAll }}</span></span>
                    <span>Pending: <span class="font-semibold text-amber-600">{{ $totalPending }}</span></span>
                    <span>Disetujui: <span class="font-semibold text-sky-700">{{ $totalApproved }}</span></span>
                    <span>Sudah dibayar: <span class="font-semibold text-emerald-600">{{ $totalPaid }}</span></span>
                    <span>Ditolak: <span class="font-semibold text-rose-600">{{ $totalRejected }}</span></span>
                </div>

                <div class="mt-1 flex flex-wrap gap-3 text-[11px] text-slate-500">
                    <span>Total diajukan:
                        <span class="font-semibold text-slate-900">
                            Rp {{ number_format($totalRequested, 0, ',', '.') }}
                        </span>
                    </span>
                    <span>Total sudah dibayar:
                        <span class="font-semibold text-emerald-600">
                            Rp {{ number_format($totalPaidAmount, 0, ',', '.') }}
                        </span>
                    </span>
                </div>
            </div>

            <div class="flex flex-col items-end gap-2">
                @if (session('status_admin_withdraws'))
                    <div class="rounded-full bg-emerald-50 border border-emerald-100 px-3 py-1 text-[11px] text-emerald-700">
                        {{ session('status_admin_withdraws') }}
                    </div>
                @endif
            </div>
        </header>

        {{-- FILTER BAR --}}
        <section class="mb-4 rounded-2xl border border-slate-100 bg-white px-4 py-3 shadow-sm">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                {{-- Search --}}
                <div class="w-full md:max-w-sm">
                    <label class="sr-only" for="search-withdraws">Cari penarikan</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 103.473 9.8l3.613 3.614a.75.75 0 101.06-1.06l-3.614-3.613A5.5 5.5 0 009 3.5zm-4 5.5a4 4 0 118 0 4 4 0 01-8 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <input
                            id="search-withdraws"
                            type="text"
                            wire:model.debounce.400ms="search"
                            class="block w-full rounded-full border border-slate-200 bg-slate-50 pl-8 pr-3 py-1.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#1d428a] focus:ring-[#1d428a]"
                            placeholder="Cari ID penarikan atau nama kreator..."
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
                            <option value="pending">Pending</option>
                            <option value="approved">Disetujui</option>
                            <option value="paid">Sudah dibayar</option>
                            <option value="rejected">Ditolak</option>
                        </select>
                    </div>

                    {{-- Tanggal --}}
                    <div class="flex items-center gap-1">
                        <span class="text-slate-500">Waktu:</span>
                        <select
                            wire:model="dateFilter"
                            class="cursor-pointer rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-[11px] text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                        >
                            <option value="all">Semua</option>
                            <option value="today">Hari ini</option>
                            <option value="7d">7 hari terakhir</option>
                            <option value="30d">30 hari terakhir</option>
                            <option value="90d">90 hari terakhir</option>
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
                            <option value="amount_high">Nominal tertinggi</option>
                            <option value="amount_low">Nominal terendah</option>
                        </select>
                    </div>

                    {{-- Tombol terapkan --}}
                    <button
                        type="button"
                        wire:click="applyFilters"
                        class="cursor-pointer inline-flex items-center gap-1 rounded-full bg-[#1d428a] px-4 py-1.5 text-[11px] font-semibold text-white shadow-sm hover:bg-[#163268]"
                    >
                        Terapkan
                    </button>
                </div>
            </div>
        </section>

        {{-- LIST PENARIKAN --}}
        @if($withdraws->count() > 0)
            <section class="space-y-3">
                @foreach($withdraws as $withdraw)
                    @php
                        $creator = $withdraw->user;
                        $status  = $withdraw->status;
                    @endphp

                    <article class="rounded-2xl border border-slate-100 bg-white px-4 py-3 shadow-sm flex flex-col gap-3">
                        {{-- Bar atas: kreator + status --}}
                        <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
                            <div class="flex items-start gap-3">
                                {{-- Avatar kreator (inisial) --}}
                                <div class="mt-0.5 h-10 w-10 flex items-center justify-center rounded-full bg-[#fef6e0] text-[#d4a116] text-xs font-semibold">
                                    @if($creator && $creator->name)
                                        {{ strtoupper(mb_substr($creator->name, 0, 1)) }}
                                    @else
                                        U
                                    @endif
                                </div>

                                <div class="space-y-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h2 class="text-sm font-semibold text-slate-900">
                                            Penarikan #{{ $withdraw->id }}
                                        </h2>

                                        @if($status === 'pending')
                                            <span class="rounded-full bg-amber-50 px-2 py-0.5 text-[11px] text-amber-700">
                                                Pending
                                            </span>
                                        @elseif($status === 'approved')
                                            <span class="rounded-full bg-sky-50 px-2 py-0.5 text-[11px] text-sky-700">
                                                Disetujui (menunggu pembayaran)
                                            </span>
                                        @elseif($status === 'paid')
                                            <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] text-emerald-700">
                                                Sudah dibayar
                                            </span>
                                        @elseif($status === 'rejected')
                                            <span class="rounded-full bg-rose-50 px-2 py-0.5 text-[11px] text-rose-700">
                                                Ditolak
                                            </span>
                                        @endif
                                    </div>

                                    <p class="text-[11px] text-slate-500">
                                        Nominal:
                                        <span class="font-semibold text-slate-900">
                                            Rp {{ number_format($withdraw->amount ?? 0, 0, ',', '.') }}
                                        </span>
                                    </p>

                                    <p class="text-[11px] text-slate-400">
                                        Diajukan {{ optional($withdraw->created_at)->diffForHumans() ?? '-' }}
                                        • Terakhir diubah {{ optional($withdraw->updated_at)->diffForHumans() ?? '-' }}
                                        @if($creator)
                                            • Kreator:
                                            <span class="font-medium text-slate-700">{{ $creator->name }}</span>
                                            <span class="text-slate-400">({{ $creator->email }})</span>
                                        @endif
                                    </p>

                                    @if(!empty($withdraw->notes))
                                        <p class="text-[11px] text-slate-500">
                                            Catatan: {{ Str::limit($withdraw->notes, 140) }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            {{-- Aksi cepat --}}
                            <div class="flex flex-col items-end gap-2 text-[11px]">
                                {{-- Lihat halaman publik kreator (tidak pakai middleware role) --}}
                                @if($creator)
                                    <a
                                        href="{{ route('creator.public.show', $creator->id) }}"
                                        target="_blank"
                                        class="cursor-pointer inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 font-medium text-slate-700 hover:bg-slate-50"
                                    >
                                        Lihat halaman publik kreator
                                    </a>
                                @endif

                                {{-- Ubah status --}}
                                <div class="flex items-center gap-1">
                                    <span class="text-slate-500">Ubah status:</span>
                                    <select
                                        wire:change="updateStatus({{ $withdraw->id }}, $event.target.value)"
                                        class="cursor-pointer rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-[11px] text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                                    >
                                        <option value="">Pilih</option>
                                        <option value="pending">Pending</option>
                                        <option value="approved">Disetujui</option>
                                        <option value="paid">Sudah dibayar</option>
                                        <option value="rejected">Ditolak</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </section>

            <div class="mt-4">
                {{ $withdraws->links() }}
            </div>
        @else
            {{-- EMPTY STATE --}}
            <section class="mt-8 flex flex-col items-center justify-center text-center">
                <div class="mb-4 h-16 w-16 rounded-full bg-slate-100 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M4 3a2 2 0 00-2 2v9.5A1.5 1.5 0 003.5 16h13a1.5 1.5 0 001.5-1.5V7a2 2 0 00-2-2h-5.586a2 2 0 01-1.414-.586L7.586 3H4z" />
                    </svg>
                </div>
                <h2 class="text-sm font-semibold text-slate-900 mb-1">
                    Belum ada permintaan penarikan saldo
                </h2>
                <p class="text-xs text-slate-500 max-w-sm">
                    Jika kreator mengajukan penarikan saldo, data tersebut akan muncul di halaman ini untuk kamu review dan proses.
                </p>
            </section>
        @endif
    </main>
</div>
