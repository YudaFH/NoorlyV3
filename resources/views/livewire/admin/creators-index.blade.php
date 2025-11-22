<div class="flex h-screen bg-slate-50">
    {{-- SIDEBAR ADMIN --}}
    @include('livewire.admin.sidebar', ['sidebarOpen' => $sidebarOpen])

    {{-- MAIN --}}
    <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8 overflow-y-auto">
        {{-- HEADER --}}
        <header class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-slate-900">
                    Daftar kreator
                </h1>
                <p class="mt-1 text-sm text-slate-500 max-w-2xl">
                    Pantau dan kelola semua kreator yang menjual konten di Noorly.
                </p>

                <div class="mt-3 flex flex-wrap gap-3 text-[11px] text-slate-500">
                    <span>
                        Total kreator:
                        <span class="font-semibold text-slate-800">{{ $totalCreators }}</span>
                    </span>
                    <span>
                        Kreator terpercaya:
                        <span class="font-semibold text-emerald-600">{{ $trustedCount }}</span>
                    </span>
                </div>
            </div>

            {{-- FILTER CEPAT & SEARCH --}}
            <div class="flex flex-col gap-2 md:items-end">
                <div class="w-full md:w-64">
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
                            placeholder="Cari kreator (nama / email)..."
                        >
                    </div>
                </div>

                <div class="flex flex-wrap gap-2 text-[11px]">
                    <div class="flex items-center gap-1">
                        <span class="text-slate-500">Kreator terpercaya:</span>
                        <select
                            wire:model="trustedFilter"
                            class="cursor-pointer rounded-full border border-slate-200 bg-white px-2.5 py-1 text-[11px] text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                        >
                            <option value="all">Semua</option>
                            <option value="trusted">Hanya trusted</option>
                            <option value="non_trusted">Belum trusted</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-1">
                        <span class="text-slate-500">Payout:</span>
                        <select
                            wire:model="payoutFilter"
                            class="cursor-pointer rounded-full border border-slate-200 bg-white px-2.5 py-1 text-[11px] text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                        >
                            <option value="all">Semua</option>
                            <option value="verified">Sudah verifikasi</option>
                            <option value="unverified">Belum verifikasi</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-1">
                        <span class="text-slate-500">Urutkan:</span>
                        <select
                            wire:model="sort"
                            class="cursor-pointer rounded-full border border-slate-200 bg-white px-2.5 py-1 text-[11px] text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                        >
                            <option value="newest">Terbaru bergabung</option>
                            <option value="oldest">Terlama</option>
                            <option value="most_revenue">Pendapatan tertinggi</option>
                            <option value="most_buyers">Pembeli terbanyak</option>
                            <option value="most_contents">Konten terbanyak</option>
                        </select>
                    </div>
                </div>
            </div>
        </header>

        {{-- LIST KREATOR --}}
        @if($creators->count() > 0)
            <section class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <table class="min-w-full border-collapse">
                    <thead class="bg-slate-50/80">
                        <tr class="text-[11px] uppercase tracking-wide text-slate-500">
                            <th class="px-4 py-3 text-left">Kreator</th>
                            <th class="px-4 py-3 text-left">Konten</th>
                            <th class="px-4 py-3 text-left">Performa</th>
                            <th class="px-4 py-3 text-left">Payout</th>
                            <th class="px-4 py-3 text-left">Info akun</th>
                            <th class="px-4 py-3 text-right"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($creators as $creator)
                            @php
                                $stats = $contentStats[$creator->id] ?? [
                                    'total_contents'     => 0,
                                    'published_contents' => 0,
                                    'draft_contents'     => 0,
                                    'total_buyers'       => 0,
                                    'total_revenue'      => 0,
                                ];

                                $payout = $payoutMap[$creator->id] ?? null;
                            @endphp

                            <tr class="text-xs text-slate-700 hover:bg-slate-50/70">
                                {{-- KREATOR --}}
                                <td class="px-4 py-3 align-top">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 rounded-full bg-[#fbc926]/90 text-white flex items-center justify-center text-xs font-semibold">
                                            {{ strtoupper(mb_substr($creator->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-900">
                                                {{ $creator->name }}
                                            </p>
                                            <p class="text-[11px] text-slate-500">
                                                {{ $creator->email }}
                                            </p>
                                            <div class="mt-1 flex flex-wrap items-center gap-1">
                                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[10px] text-slate-600">
                                                    ID: {{ $creator->id }}
                                                </span>

                                                @if($creator->is_trusted_creator)
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] text-emerald-700">
                                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                                        Kreator terpercaya
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-50 px-2 py-0.5 text-[10px] text-slate-500">
                                                        Kreator baru
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- KONTEN --}}
                                <td class="px-4 py-3 align-top">
                                    <p class="text-[11px] text-slate-500">
                                        Total konten:
                                        <span class="font-semibold text-slate-900">{{ $stats['total_contents'] }}</span>
                                    </p>
                                    <p class="text-[11px] text-slate-500">
                                        Terbit:
                                        <span class="font-semibold text-emerald-600">{{ $stats['published_contents'] }}</span>
                                        • Draft:
                                        <span class="font-semibold text-slate-700">{{ $stats['draft_contents'] }}</span>
                                    </p>
                                </td>

                                {{-- PERFORMA --}}
                                <td class="px-4 py-3 align-top">
                                    <p class="text-[11px] text-slate-500">
                                        Total pembeli:
                                        <span class="font-semibold text-slate-900">{{ $stats['total_buyers'] }}</span>
                                    </p>
                                    <p class="text-[11px] text-slate-500">
                                        Total pendapatan:
                                        <span class="font-semibold text-emerald-600">
                                            Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}
                                        </span>
                                    </p>
                                </td>

                                {{-- PAYOUT --}}
                                <td class="px-4 py-3 align-top">
                                    @if($payout)
                                        <p class="text-[11px] text-slate-500 mb-0.5">
                                            Metode default:
                                        </p>
                                        <p class="text-[11px] font-medium text-slate-900">
                                            {{ $payout['label'] }}
                                        </p>
                                        <span class="inline-flex items-center gap-1 mt-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] text-emerald-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            Terverifikasi
                                        </span>
                                    @else
                                        <p class="text-[11px] text-slate-500">
                                            Belum ada payout terverifikasi.
                                        </p>
                                    @endif
                                </td>

                                {{-- INFO AKUN --}}
                                <td class="px-4 py-3 align-top">
                                    <p class="text-[11px] text-slate-500">
                                        Bergabung:
                                        <span class="font-medium text-slate-900">
                                            {{ optional($creator->created_at)->format('d M Y') ?? '-' }}
                                        </span>
                                    </p>
                                    <p class="text-[11px] text-slate-400">
                                        {{ optional($creator->created_at)->diffForHumans() ?? '' }}
                                    </p>
                                </td>

                                {{-- ACTIONS --}}
                                <td class="px-4 py-3 align-top">
                                    <div class="flex items-center justify-end gap-1">
                                        <button
                                            type="button"
                                            wire:click="openDetail({{ $creator->id }})"
                                            class="cursor-pointer inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-medium text-slate-700 hover:bg-slate-50"
                                        >
                                            Detail
                                        </button>

                                        {{-- MENU TITIK 3 --}}
                                        <div x-data="{ open: false }" class="relative">
                                            <button
                                                type="button"
                                                @click="open = !open"
                                                class="cursor-pointer inline-flex h-7 w-7 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 hover:bg-slate-50"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 8.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 14a1.5 1.5 0 110 3 1.5 1.5 0 010-3z" />
                                                </svg>
                                            </button>

                                            <div
                                                x-show="open"
                                                x-transition
                                                @click.outside="open = false"
                                                class="absolute right-0 mt-2 w-44 rounded-xl border border-slate-100 bg-white shadow-xl z-30 p-1.5 text-[11px]"
                                            >
                                                <button
                                                    type="button"
                                                    class="w-full text-left px-2 py-1.5 rounded-lg hover:bg-slate-50 text-slate-700"
                                                    {{-- wire:click nanti bisa dihubungkan ke aksi jadikan trusted --}}
                                                >
                                                    Jadikan kreator terpercaya
                                                </button>
                                                <button
                                                    type="button"
                                                    class="w-full text-left px-2 py-1.5 rounded-lg hover:bg-slate-50 text-slate-700"
                                                    {{-- wire:click aksi suspend / unsuspend --}}
                                                >
                                                    Tangguhkan / aktifkan akun
                                                </button>
                                                <button
                                                    type="button"
                                                    class="w-full text-left px-2 py-1.5 rounded-lg hover:bg-slate-50 text-slate-700"
                                                >
                                                    Lihat konten kreator ini
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="px-4 py-3 border-t border-slate-100">
                    {{ $creators->links() }}
                </div>
            </section>
        @else
            <section class="mt-10 flex flex-col items-center justify-center text-center">
                <div class="mb-4 h-14 w-14 rounded-full bg-slate-100 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M4 3a2 2 0 00-2 2v9.5A1.5 1.5 0 003.5 16h13a1.5 1.5 0 001.5-1.5V7a2 2 0 00-2-2h-5.586a2 2 0 01-1.414-.586L7.586 3H4z" />
                    </svg>
                </div>
                <h2 class="text-sm font-semibold text-slate-900 mb-1">
                    Belum ada kreator
                </h2>
                <p class="text-xs text-slate-500 max-w-sm">
                    Saat kreator mendaftar dan membuat konten, mereka akan muncul di halaman ini untuk kamu kelola.
                </p>
            </section>
        @endif

        {{-- DETAIL MODAL --}}
        @if($showDetailModal && $detailUser)
            <div
                x-data="{ open: true }"
                x-show="open"
                x-transition
                class="fixed inset-0 z-40 flex items-center justify-center bg-black/40 px-4"
            >
                <div class="max-w-lg w-full rounded-2xl bg-white shadow-xl border border-slate-100 p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h2 class="text-sm font-semibold text-slate-900">
                                Detail kreator
                            </h2>
                            <p class="text-[11px] text-slate-500">
                                {{ $detailUser->name }} • {{ $detailUser->email }}
                            </p>
                        </div>
                        <button
                            type="button"
                            @click="open = false; $wire.call('closeDetail')"
                            class="cursor-pointer inline-flex h-7 w-7 items-center justify-center rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200"
                        >
                            ✕
                        </button>
                    </div>

                    <div class="space-y-3 text-[11px] text-slate-600">
                        <div class="rounded-xl bg-slate-50/80 p-3">
                            <p class="font-semibold text-slate-800 mb-1">Info dasar</p>
                            <p>ID: {{ $detailUser->id }}</p>
                            <p>Email: {{ $detailUser->email }}</p>
                            <p>
                                Bergabung:
                                {{ optional($detailUser->created_at)->format('d M Y H:i') ?? '-' }}
                                ({{ optional($detailUser->created_at)->diffForHumans() ?? '' }})
                            </p>
                            <p>
                                Status kreator:
                                @if($detailUser->is_trusted_creator)
                                    <span class="font-semibold text-emerald-600">Kreator terpercaya</span>
                                @else
                                    <span class="font-semibold text-slate-700">Kreator biasa</span>
                                @endif
                            </p>
                        </div>

                        @if($detailStat)
                            <div class="rounded-xl bg-slate-50/80 p-3">
                                <p class="font-semibold text-slate-800 mb-1">Statistik konten</p>
                                <p>Total konten: <span class="font-semibold text-slate-900">{{ $detailStat['total_contents'] }}</span></p>
                                <p>Terbit: <span class="font-semibold text-emerald-600">{{ $detailStat['published_contents'] }}</span></p>
                                <p>Draft: <span class="font-semibold text-slate-700">{{ $detailStat['draft_contents'] }}</span></p>
                                <p>Total pembeli: <span class="font-semibold text-slate-900">{{ $detailStat['total_buyers'] }}</span></p>
                                <p>
                                    Total pendapatan:
                                    <span class="font-semibold text-emerald-600">
                                        Rp {{ number_format($detailStat['total_revenue'], 0, ',', '.') }}
                                    </span>
                                </p>
                            </div>
                        @endif

                        <div class="rounded-xl bg-slate-50/80 p-3">
                            <p class="font-semibold text-slate-800 mb-1">Payout & kepatuhan</p>
                            @if($detailPayout)
                                <p>
                                    Metode default:
                                    <span class="font-semibold text-slate-900">
                                        {{ $detailPayout['provider'] }} • {{ $detailPayout['account'] }} a.n {{ $detailPayout['name'] }}
                                    </span>
                                </p>
                                <p>
                                    Status:
                                    <span class="font-semibold text-emerald-600">
                                        {{ ucfirst($detailPayout['status']) }}
                                    </span>
                                </p>
                            @else
                                <p>Belum ada metode penarikan yang disetel atau terverifikasi.</p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 flex justify-end">
                        <button
                            type="button"
                            @click="open = false; $wire.call('closeDetail')"
                            class="cursor-pointer inline-flex items-center rounded-full border border-slate-200 bg-white px-4 py-1.5 text-[11px] font-medium text-slate-700 hover:bg-slate-50"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </main>
</div>
