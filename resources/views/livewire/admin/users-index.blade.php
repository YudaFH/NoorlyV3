<div class="flex h-screen bg-slate-50">
    {{-- SIDEBAR ADMIN --}}
    @include('livewire.admin.sidebar', ['sidebarOpen' => $sidebarOpen])

    {{-- MAIN --}}
    <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8 overflow-y-auto">
        {{-- HEADER --}}
        <header class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-slate-900">
                    Semua pengguna
                </h1>
                <p class="mt-1 text-sm text-slate-500 max-w-2xl">
                    Kelola akun pengguna, kreator, dan admin yang terdaftar di Noorly.
                </p>

                <div class="mt-3 flex flex-wrap gap-3 text-[11px] text-slate-500">
                    <span>Total: <span class="font-semibold text-slate-700">{{ $totalUsers }}</span></span>
                    <span>Kreator: <span class="font-semibold text-[#1d428a]">{{ $totalCreators }}</span></span>
                    <span>User biasa: <span class="font-semibold text-slate-700">{{ $totalMembers }}</span></span>
                    <span>Admin: <span class="font-semibold text-amber-600">{{ $totalAdmins }}</span></span>
                </div>
            </div>
        </header>

        {{-- ALERT / FLASH MESSAGE --}}
        @if(session('status_users') || session('status_error'))
            <div class="mb-4">
                @if(session('status_users'))
                    <div class="flex items-start gap-2 rounded-xl border border-emerald-100 bg-emerald-50 px-3 py-2 text-[11px] text-emerald-800">
                        <span class="mt-0.5 inline-flex h-4 w-4 items-center justify-center rounded-full bg-emerald-600 text-white text-[9px]">
                            ✓
                        </span>
                        <p>{{ session('status_users') }}</p>
                    </div>
                @endif

                @if(session('status_error'))
                    <div class="mt-2 flex items-start gap-2 rounded-xl border border-red-100 bg-red-50 px-3 py-2 text-[11px] text-red-800">
                        <span class="mt-0.5 inline-flex h-4 w-4 items-center justify-center rounded-full bg-red-600 text-white text-[9px]">
                            !
                        </span>
                        <p>{{ session('status_error') }}</p>
                    </div>
                @endif
            </div>
        @endif

        {{-- FILTER BAR --}}
        <section class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            {{-- Search --}}
            <div class="w-full md:max-w-sm">
                <label class="sr-only" for="search-users">Cari pengguna</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 103.473 9.8l3.613 3.614a.75.75 0 101.06-1.06l-3.614-3.613A5.5 5.5 0 009 3.5zm-4 5.5a4 4 0 118 0 4 4 0 01-8 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    <input
                        id="search-users"
                        type="text"
                        wire:model.defer="filterSearchInput"
                        class="block w-full rounded-full border border-slate-200 bg-white pl-8 pr-3 py-1.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#1d428a] focus:ring-[#1d428a]"
                        placeholder="Cari nama atau email..."
                    >
                </div>
            </div>

            {{-- Filters kanan --}}
            <div class="flex flex-wrap items-center gap-3 justify-end">
                {{-- Filter role --}}
                <div class="flex items-center gap-2">
                    <span class="text-[11px] text-slate-500">Role:</span>
                    <select
                        wire:model.defer="filterRoleInput"
                        class="cursor-pointer rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                    >
                        <option value="">Semua</option>
                        <option value="user">User biasa</option>
                        <option value="creator">Kreator</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                {{-- Filter status akun --}}
                <div class="flex items-center gap-2">
                    <span class="text-[11px] text-slate-500">Status akun:</span>
                    <select
                        wire:model.defer="filterStatusInput"
                        class="cursor-pointer rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                    >
                        <option value="">Semua</option>
                        <option value="active">Akun aktif</option>
                        <option value="suspended">Ditangguhkan</option>
                    </select>
                </div>

                {{-- Sort --}}
                <div class="flex items-center gap-2">
                    <span class="text-[11px] text-slate-500">Urutkan:</span>
                    <select
                        wire:model.defer="filterSortInput"
                        class="cursor-pointer rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                    >
                        <option value="latest">Terbaru daftar</option>
                        <option value="oldest">Terlama</option>
                        <option value="name_asc">Nama A-Z</option>
                        <option value="name_desc">Nama Z-A</option>
                    </select>
                </div>

                {{-- Tombol TERAPKAN --}}
                <button
                    type="button"
                    wire:click="applyFilters"
                    class="cursor-pointer inline-flex items-center gap-1 rounded-full bg-[#1d428a] px-4 py-1.5 text-[11px] font-semibold text-white shadow-sm hover:bg-[#163268]"
                >
                    Terapkan
                </button>
            </div>
        </section>

        {{-- TABEL USERS --}}
        <section class="bg-white rounded-2xl border border-slate-100 shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-[12px]">
                    <thead class="bg-slate-50/80">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-slate-500">Pengguna</th>
                            <th class="px-4 py-3 text-left font-semibold text-slate-500">Role</th>
                            <th class="px-4 py-3 text-left font-semibold text-slate-500">Email</th>
                            <th class="px-4 py-3 text-left font-semibold text-slate-500">Tanggal daftar</th>
                            <th class="px-4 py-3 text-left font-semibold text-slate-500">Status</th>
                            <th class="px-4 py-3 text-right font-semibold text-slate-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($users as $user)
                            @php
                                $role        = $user->role ?? 'user';
                                $isTrusted   = (bool) ($user->is_trusted_creator ?? false);
                                $isSuspended = (bool) ($user->is_suspended ?? false);
                            @endphp

                            <tr class="hover:bg-slate-50/70">
                                {{-- Pengguna --}}
                                <td class="px-4 py-3 align-top">
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 rounded-full bg-[#1d428a]/10 text-[#1d428a] flex items-center justify-center text-[11px] font-semibold">
                                            {{ strtoupper(mb_substr($user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-[12px] font-semibold text-slate-900">
                                                {{ $user->name ?? '—' }}
                                            </p>
                                            <p class="text-[11px] text-slate-500">
                                                ID: {{ $user->id }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Role + badge trusted --}}
                                <td class="px-4 py-3 align-top space-y-1">
                                    @if($role === 'admin')
                                        <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-[11px] font-medium text-amber-700">
                                            Admin
                                        </span>
                                    @elseif($role === 'creator')
                                        <span class="inline-flex items-center rounded-full bg-[#1d428a]/10 px-2 py-0.5 text-[11px] font-medium text-[#1d428a]">
                                            Kreator
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-600">
                                            User biasa
                                        </span>
                                    @endif

                                    @if($isTrusted)
                                        <div class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-medium text-emerald-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.28-9.78a.75.75 0 00-1.06-1.06L9 10.44 7.78 9.22a.75.75 0 10-1.06 1.06l1.75 1.75a.75.75 0 001.06 0l3.75-3.75z" clip-rule="evenodd" />
                                            </svg>
                                            Kreator terpercaya
                                        </div>
                                    @endif
                                </td>

                                {{-- Email --}}
                                <td class="px-4 py-3 align-top">
                                    <p class="text-[11px] text-slate-700">
                                        {{ $user->email ?? '—' }}
                                    </p>
                                </td>

                                {{-- Tanggal daftar --}}
                                <td class="px-4 py-3 align-top text-[11px] text-slate-500">
                                    <p>{{ optional($user->created_at)->format('d M Y H:i') ?? '-' }}</p>
                                    <p>{{ optional($user->created_at)->diffForHumans() ?? '' }}</p>
                                </td>

                                {{-- Status --}}
                                <td class="px-4 py-3 align-top text-[11px] text-slate-500 space-y-1">
                                    @if($user->email_verified_at)
                                        <div>
                                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-medium text-emerald-700">
                                                Email terverifikasi
                                            </span>
                                        </div>
                                    @else
                                        <div>
                                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-600">
                                                Email belum verifikasi
                                            </span>
                                        </div>
                                    @endif

                                    <div>
                                        @if($isSuspended)
                                            <span class="inline-flex items-center rounded-full bg-red-50 px-2 py-0.5 text-[10px] font-medium text-red-700">
                                                Akun ditangguhkan
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-medium text-emerald-700">
                                                Akun aktif
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-4 py-3 align-top text-right">
                                    <div class="inline-flex items-center gap-1" x-data="{ openMenu: false }">
                                        {{-- Tombol detail --}}
                                        <button
                                            type="button"
                                            wire:click="openDetail({{ $user->id }})"
                                            class="cursor-pointer inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-medium text-slate-700 hover:bg-slate-50"
                                        >
                                            Detail
                                        </button>

                                        {{-- Menu 3 titik --}}
                                        <div class="relative">
                                            <button
                                                type="button"
                                                @click="openMenu = !openMenu"
                                                class="cursor-pointer inline-flex items-center justify-center rounded-full border border-slate-200 bg-white h-7 w-7 text-slate-500 hover:bg-slate-50"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zm6 0a2 2 0 11-4 0 2 2 0 014 0zm4 2a2 2 0 100-4 2 2 0 000 4z" />
                                                </svg>
                                            </button>

                                            {{-- Dropdown menu (di depan list, tidak ke-clip) --}}
                                            <div
                                                x-show="openMenu"
                                                x-transition
                                                @click.outside="openMenu = false"
                                                class="absolute right-0 mt-2 w-52 rounded-2xl border border-slate-100 bg-white shadow-xl z-30 py-1 text-[11px] text-slate-700"
                                            >
                                                <p class="px-3 pt-1 pb-2 text-[10px] uppercase tracking-wide text-slate-400">
                                                    Ubah role
                                                </p>

                                                <button
                                                    type="button"
                                                    wire:click="setRole({{ $user->id }}, 'user')"
                                                    class="w-full text-left px-3 py-1.5 hover:bg-slate-50 cursor-pointer"
                                                >
                                                    Jadikan user biasa
                                                </button>
                                                <button
                                                    type="button"
                                                    wire:click="setRole({{ $user->id }}, 'creator')"
                                                    class="w-full text-left px-3 py-1.5 hover:bg-slate-50 cursor-pointer"
                                                >
                                                    Jadikan kreator
                                                </button>
                                                <button
                                                    type="button"
                                                    wire:click="setRole({{ $user->id }}, 'admin')"
                                                    class="w-full text-left px-3 py-1.5 hover:bg-slate-50 cursor-pointer"
                                                >
                                                    Jadikan admin
                                                </button>

                                                @if($role === 'creator')
                                                    <div class="border-t border-slate-100 my-1"></div>
                                                    <p class="px-3 pt-1 pb-2 text-[10px] uppercase tracking-wide text-slate-400">
                                                        Kreator terpercaya
                                                    </p>
                                                    <button
                                                        type="button"
                                                        wire:click="toggleTrustedCreator({{ $user->id }})"
                                                        class="w-full text-left px-3 py-1.5 hover:bg-slate-50 cursor-pointer"
                                                    >
                                                        {{ $isTrusted ? 'Cabut status kreator terpercaya' : 'Tandai sebagai kreator terpercaya' }}
                                                    </button>
                                                @endif

                                                <div class="border-t border-slate-100 my-1"></div>
                                                <p class="px-3 pt-1 pb-2 text-[10px] uppercase tracking-wide text-slate-400">
                                                    Keamanan
                                                </p>
                                                <button
                                                    type="button"
                                                    wire:click="toggleSuspend({{ $user->id }})"
                                                    class="w-full text-left px-3 py-1.5 hover:bg-slate-50 cursor-pointer text-red-600"
                                                >
                                                    {{ $isSuspended ? 'Aktifkan kembali akun' : 'Tangguhkan akun' }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-[12px] text-slate-500">
                                    Belum ada data pengguna yang cocok dengan filter / pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            <div class="px-4 py-3 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        </section>

        {{-- MODAL DETAIL PENGGUNA (tetap sama seperti versi sebelumnya) --}}
        @if($showDetailModal && $detailUser)
            <div class="fixed inset-0 z-40 flex items-center justify-center bg-slate-900/40">
                <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 w-full max-w-xl mx-4">
                    <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
                        <div>
                            <p class="text-xs font-semibold tracking-[0.16em] uppercase text-slate-400">
                                Detail pengguna
                            </p>
                            <p class="text-sm font-semibold text-slate-900">
                                {{ $detailUser->name }}
                            </p>
                        </div>
                        <button
                            type="button"
                            wire:click="closeDetail"
                            class="cursor-pointer inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200"
                        >
                            ✕
                        </button>
                    </div>

                    <div class="px-4 py-4 space-y-4 text-[12px]">
                        {{-- Info dasar --}}
                        <div class="grid md:grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <p class="text-[11px] font-semibold text-slate-700">Informasi akun</p>
                                <p class="text-slate-600">
                                    <span class="text-slate-400">Nama:</span>
                                    <br>{{ $detailUser->name }}
                                </p>
                                <p class="text-slate-600">
                                    <span class="text-slate-400">Email:</span>
                                    <br>{{ $detailUser->email }}
                                </p>
                                <p class="text-slate-600">
                                    <span class="text-slate-400">Role:</span>
                                    <br>{{ $detailUser->role ?? 'user' }}
                                </p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[11px] font-semibold text-slate-700">Status & aktivitas</p>
                                <p class="text-slate-600">
                                    <span class="text-slate-400">Status akun:</span>
                                    <br>
                                    @if($detailUser->is_suspended ?? false)
                                        Ditangguhkan
                                    @else
                                        Aktif
                                    @endif
                                </p>
                                <p class="text-slate-600">
                                    <span class="text-slate-400">Email verifikasi:</span>
                                    <br>
                                    @if($detailUser->email_verified_at)
                                        Sudah verifikasi ({{ $detailUser->email_verified_at->format('d M Y H:i') }})
                                    @else
                                        Belum verifikasi
                                    @endif
                                </p>
                                <p class="text-slate-600">
                                    <span class="text-slate-400">Bergabung:</span>
                                    <br>{{ optional($detailUser->created_at)->format('d M Y H:i') ?? '-' }}
                                    ({{ optional($detailUser->created_at)->diffForHumans() ?? '' }})
                                </p>
                            </div>
                        </div>

                        {{-- Statistik konten kreator --}}
                        <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-3">
                            <p class="text-[11px] font-semibold text-slate-700 mb-2">
                                Statistik konten kreator
                            </p>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-[11px]">
                                <div>
                                    <p class="text-slate-400 uppercase tracking-wide text-[10px]">Total konten</p>
                                    <p class="text-slate-900 font-semibold">
                                        {{ $detailStats['contents_total'] ?? 0 }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-slate-400 uppercase tracking-wide text-[10px]">Terbit</p>
                                    <p class="text-emerald-700 font-semibold">
                                        {{ $detailStats['contents_published'] ?? 0 }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-slate-400 uppercase tracking-wide text-[10px]">Draft</p>
                                    <p class="text-slate-900 font-semibold">
                                        {{ $detailStats['contents_draft'] ?? 0 }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-slate-400 uppercase tracking-wide text-[10px]">Menunggu review</p>
                                    <p class="text-amber-700 font-semibold">
                                        {{ $detailStats['contents_pending'] ?? 0 }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-3 grid grid-cols-2 gap-3 text-[11px]">
                                <div>
                                    <p class="text-slate-400 uppercase tracking-wide text-[10px]">Total pembeli</p>
                                    <p class="text-slate-900 font-semibold">
                                        {{ number_format($detailStats['buyers_total'] ?? 0, 0, ',', '.') }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-slate-400 uppercase tracking-wide text-[10px]">Perkiraan pendapatan</p>
                                    <p class="text-emerald-700 font-semibold">
                                        Rp {{ number_format($detailStats['revenue_total'] ?? 0, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-4 py-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button
                            type="button"
                            wire:click="closeDetail"
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
