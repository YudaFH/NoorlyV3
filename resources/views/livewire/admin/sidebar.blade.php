@php 
    use Illuminate\Support\Facades\Route;

    /** @var bool $sidebarOpen */
    $user = auth()->user();

    $homeUrl = Route::has('home')
        ? route('home')
        : '/';

    // URL menu (fallback ke # kalau route belum ada)
    $dashboardUrl = Route::has('admin.dashboard')
        ? route('admin.dashboard')
        : '#';

    $usersUrl = Route::has('admin.users.index')
        ? route('admin.users.index')
        : '#';

    $creatorsUrl = Route::has('admin.creators.index')
        ? route('admin.creators.index')
        : '#';

    // Antrian review konten
    $contentsModerationUrl = Route::has('admin.contents.review')
        ? route('admin.contents.review')
        : '#';

    // Semua konten
    $contentsUrl = Route::has('admin.contents.index')
        ? route('admin.contents.index')
        : '#';

    // Order & pembayaran
    $ordersUrl = Route::has('admin.orders.index')
        ? route('admin.orders.index')
        : '#';

    // Penarikan saldo
    $withdrawsUrl = Route::has('admin.withdraws.index')
        ? route('admin.withdraws.index')
        : '#';

    // Metode penarikan kreator (verifikasi rekening / e-wallet)
    $payoutMethodsUrl = Route::has('admin.payout-methods.index')
        ? route('admin.payout-methods.index')
        : '#';

    // Support & notifikasi
    $ticketsUrl = Route::has('admin.tickets.index')
        ? route('admin.tickets.index')
        : '#';

    $notificationsUrl = Route::has('admin.notifications.index')
        ? route('admin.notifications.index')
        : '#';

    // Pengaturan
    $settingsUrl = Route::has('admin.settings.index')
        ? route('admin.settings.index')
        : '#';

    $profileAdminUrl = Route::has('admin.profile')
        ? route('admin.profile')
        : '#';

@endphp

<aside
    class="flex flex-col bg-white border-r border-slate-100
           h-full
           transition-all duration-300 ease-in-out
           {{ $sidebarOpen ? 'w-64' : 'w-20' }}"
>
    {{-- Header + tombol toggle --}}
    <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
        @if($sidebarOpen)
            <div>
                <p class="text-[11px] font-semibold tracking-[0.16em] uppercase text-slate-400">
                    Panel admin
                </p>
                <p class="text-sm font-semibold text-slate-900">
                    Noorly Studio
                </p>
            </div>
        @endif

        <button
            type="button"
            wire:click="toggleSidebar"
            class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-[#1d428a] text-white shadow-sm hover:shadow-md hover:bg-[#163268] transition cursor-pointer"
        >
            @if($sidebarOpen)
                {{-- chevron-left --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 010 1.06L9.06 10l3.73 3.71a.75.75 0 11-1.06 1.06l-4.25-4.24a.75.75 0 010-1.06l4.25-4.24a.75.75 0 011.06 0z" clip-rule="evenodd" />
                </svg>
            @else
                {{-- chevron-right --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.21 4.23a.75.75 0 011.06 0l4.25 4.24a.75.75 0 010 1.06l-4.25 4.24a.75.75 0 11-1.06-1.06L10.94 10 7.21 6.29a.75.75 0 010-1.06z" clip-rule="evenodd" />
                </svg>
            @endif
        </button>
    </div>

    {{-- Menu utama --}}
    <nav class="flex-1 px-3 py-4 space-y-1">
        {{-- DASHBOARD --}}
        @php
            $isDashboardActive = request()->routeIs('admin.dashboard');
        @endphp
        <a
            href="{{ $dashboardUrl }}"
            class="relative group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                   {{ $isDashboardActive ? 'bg-[#fff7d6] text-[#1d428a]' : 'text-slate-700 hover:bg-[#fff7d6] hover:text-[#1d428a]' }}"
        >
            @if($isDashboardActive)
                <span class="absolute -left-2 h-7 w-1 rounded-r-full bg-[#1d428a]"></span>
            @endif

            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[#fef6e0] text-[#d4a116] group-hover:bg-white">
                {{-- icon: home --}}
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 10.5L12 4l9 6.5M5 10.5V20h5v-5h4v5h5v-9.5" />
                </svg>
            </span>

            @if($sidebarOpen)
                <span>Dashboard</span>
            @endif
        </a>

        {{-- KREATOR & PENGGUNA --}}
        @php
            $isUsersActive    = request()->routeIs('admin.users.*');
            $isCreatorsActive = request()->routeIs('admin.creators.*');
        @endphp
        <div>
            <p class="mb-1 text-[10px] font-semibold uppercase tracking-wide text-slate-400 px-3">
                Kreator &amp; pengguna
            </p>

            <a
                href="{{ $usersUrl }}"
                class="relative group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition
                       {{ $isUsersActive ? 'bg-[#fff7d6] text-[#1d428a]' : 'text-slate-700 hover:bg-[#fff7d6] hover:text-[#1d428a]' }}"
            >
                @if($isUsersActive)
                    <span class="absolute -left-2 h-7 w-1 rounded-r-full bg-[#1d428a]"></span>
                @endif
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[#fef6e0] text-[#d4a116] group-hover:bg-white">
                    {{-- icon: users --}}
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M15 19.127a3.001 3.001 0 012.25-2.877 3.001 3.001 0 00-2.25-2.873M9 19.127a3.001 3.001 0 00-2.25-2.877 3.001 3.001 0 012.25-2.873M9 10.5a3 3 0 100-6 3 3 0 000 6zm6 0a3 3 0 100-6 3 3 0 000 6z" />
                    </svg>
                </span>
                @if($sidebarOpen)
                    <span>Semua pengguna</span>
                @endif
            </a>

            <a
                href="{{ $creatorsUrl }}"
                class="relative mt-1 group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition
                       {{ $isCreatorsActive ? 'bg-[#fff7d6] text-[#1d428a]' : 'text-slate-700 hover:bg-[#fff7d6] hover:text-[#1d428a]' }}"
            >
                @if($isCreatorsActive)
                    <span class="absolute -left-2 h-7 w-1 rounded-r-full bg-[#1d428a]"></span>
                @endif
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[#fef6e0] text-[#d4a116] group-hover:bg-white">
                    {{-- icon: star / badge --}}
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 3l2.09 4.24 4.68.68-3.39 3.3.8 4.63L12 13.77l-4.18 2.18.8-4.63-3.39-3.3 4.68-.68z" />
                    </svg>
                </span>
                @if($sidebarOpen)
                    <span>Daftar kreator</span>
                @endif
            </a>
        </div>

        {{-- KONTEN & MODERASI --}}
        @php
            $isModerationActive = request()->routeIs('admin.contents.review');
            $isContentsActive   = request()->routeIs('admin.contents.index');
        @endphp
        <div class="mt-3">
            <p class="mb-1 text-[10px] font-semibold uppercase tracking-wide text-slate-400 px-3">
                Konten &amp; moderasi
            </p>

            <a
                href="{{ $contentsModerationUrl }}"
                class="relative group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition
                       {{ $isModerationActive ? 'bg-[#fff7d6] text-[#1d428a]' : 'text-slate-700 hover:bg-[#fff7d6] hover:text-[#1d428a]' }}"
            >
                @if($isModerationActive)
                    <span class="absolute -left-2 h-7 w-1 rounded-r-full bg-[#1d428a]"></span>
                @endif
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[#fef6e0] text-[#d4a116] group-hover:bg-white">
                    {{-- icon: shield-check --}}
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 3l7 3v5a10 10 0 01-7 9 10 10 0 01-7-9V6z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9.5 11.75L11 13.25l3-3.5" />
                    </svg>
                </span>
                @if($sidebarOpen)
                    <span>Antrian review konten</span>
                @endif
            </a>

            <a
                href="{{ $contentsUrl }}"
                class="relative mt-1 group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition
                       {{ $isContentsActive ? 'bg-[#fff7d6] text-[#1d428a]' : 'text-slate-700 hover:bg-[#fff7d6] hover:text-[#1d428a]' }}"
            >
                @if($isContentsActive)
                    <span class="absolute -left-2 h-7 w-1 rounded-r-full bg-[#1d428a]"></span>
                @endif
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[#fef6e0] text-[#d4a116] group-hover:bg-white">
                    {{-- icon: stack --}}
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M4 8l8-4 8 4-8 4z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M4 12l8 4 8-4M4 16l8 4 8-4" />
                    </svg>
                </span>
                @if($sidebarOpen)
                    <span>Semua konten</span>
                @endif
            </a>
        </div>

        {{-- TRANSAKSI & SALDO --}}
        @php
            $isOrdersActive        = request()->routeIs('admin.orders.*');
            $isWithdrawsActive     = request()->routeIs('admin.withdraws.*');
            $isPayoutMethodsActive = request()->routeIs('admin.payout-methods.*');
        @endphp
        <div class="mt-3">
            <p class="mb-1 text-[10px] font-semibold uppercase tracking-wide text-slate-400 px-3">
                Transaksi &amp; saldo
            </p>

            <a
                href="{{ $ordersUrl }}"
                class="relative group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition
                       {{ $isOrdersActive ? 'bg-[#fff7d6] text-[#1d428a]' : 'text-slate-700 hover:bg-[#fff7d6] hover:text-[#1d428a]' }}"
            >
                @if($isOrdersActive)
                    <span class="absolute -left-2 h-7 w-1 rounded-r-full bg-[#1d428a]"></span>
                @endif
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[#fef6e0] text-[#d4a116] group-hover:bg-white">
                    {{-- icon: receipt --}}
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M7 5h10v14l-2-1-2 1-2-1-2 1-2-1-2 1V5z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 9h6M9 12h4" />
                    </svg>
                </span>
                @if($sidebarOpen)
                    <span>Order &amp; pembayaran</span>
                @endif
            </a>

            <a
                href="{{ $withdrawsUrl }}"
                class="relative mt-1 group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition
                       {{ $isWithdrawsActive ? 'bg-[#fff7d6] text-[#1d428a]' : 'text-slate-700 hover:bg-[#fff7d6] hover:text-[#1d428a]' }}"
            >
                @if($isWithdrawsActive)
                    <span class="absolute -left-2 h-7 w-1 rounded-r-full bg-[#1d428a]"></span>
                @endif
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[#fef6e0] text-[#d4a116] group-hover:bg-white">
                    {{-- icon: banknotes --}}
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                        <rect x="3" y="6" width="18" height="12" rx="2" />
                        <circle cx="12" cy="12" r="2.5" />
                    </svg>
                </span>
                @if($sidebarOpen)
                    <span>Penarikan saldo</span>
                @endif
            </a>

            <a
                href="{{ $payoutMethodsUrl }}"
                class="relative mt-1 group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition
                       {{ $isPayoutMethodsActive ? 'bg-[#fff7d6] text-[#1d428a]' : 'text-slate-700 hover:bg-[#fff7d6] hover:text-[#1d428a]' }}"
            >
                @if($isPayoutMethodsActive)
                    <span class="absolute -left-2 h-7 w-1 rounded-r-full bg-[#1d428a]"></span>
                @endif
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[#fef6e0] text-[#d4a116] group-hover:bg-white">
                    {{-- icon: credit-card --}}
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                        <rect x="3" y="5" width="18" height="14" rx="2" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 15h3" />
                    </svg>
                </span>
                @if($sidebarOpen)
                    <span>Metode penarikan kreator</span>
                @endif
            </a>
        </div>

        {{-- SUPPORT & NOTIFIKASI --}}
        @php
            $isTicketsActive    = request()->routeIs('admin.tickets.*');
            $isAdminNotifActive = request()->routeIs('admin.notifications.*');
        @endphp
        <div class="mt-3">
            <p class="mb-1 text-[10px] font-semibold uppercase tracking-wide text-slate-400 px-3">
                Support &amp; notifikasi
            </p>

            <a
                href="{{ $ticketsUrl }}"
                class="relative group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition
                       {{ $isTicketsActive ? 'bg-[#fff7d6] text-[#1d428a]' : 'text-slate-700 hover:bg-[#fff7d6] hover:text-[#1d428a]' }}"
            >
                @if($isTicketsActive)
                    <span class="absolute -left-2 h-7 w-1 rounded-r-full bg-[#1d428a]"></span>
                @endif
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[#fef6e0] text-[#d4a116] group-hover:bg-white">
                    {{-- icon: lifebuoy --}}
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                        <circle cx="12" cy="12" r="4.5" />
                        <circle cx="12" cy="12" r="7.5" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 4.5V3M19.5 12H21M12 21v-1.5M3 12h1.5" />
                    </svg>
                </span>
                @if($sidebarOpen)
                    <span>Tiket support</span>
                @endif
            </a>

            <a
                href="{{ $notificationsUrl }}"
                class="relative mt-1 group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition
                       {{ $isAdminNotifActive ? 'bg-[#fff7d6] text-[#1d428a]' : 'text-slate-700 hover:bg-[#fff7d6] hover:text-[#1d428a]' }}"
            >
                @if($isAdminNotifActive)
                    <span class="absolute -left-2 h-7 w-1 rounded-r-full bg-[#1d428a]"></span>
                @endif
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[#fef6e0] text-[#d4a116] group-hover:bg-white">
                    {{-- icon: bell --}}
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M14.25 18.75a2.25 2.25 0 01-4.5 0M6.75 9A5.25 5.25 0 1117.25 9c0 3.003.879 4.44 1.492 5.174.346.415.519.623.507.827a.75.75 0 01-.311.572c-.16.112-.412.112-.917.112H6.979c-.505 0-.757 0-.917-.112a.75.75 0 01-.311-.572c-.012-.204.161-.412.507-.827C5.871 13.44 6.75 12.003 6.75 9z" />
                    </svg>
                </span>
                @if($sidebarOpen)
                    <span>Notifikasi admin</span>
                @endif
            </a>
        </div>

        {{-- PENGATURAN --}}
        @php
            $isProfileAdminActive = request()->routeIs('admin.profile');
            $isSettingsActive = request()->routeIs('admin.settings.*');
        @endphp
        <div class="mt-3">
            <p class="mb-1 text-[10px] font-semibold uppercase tracking-wide text-slate-400 px-3">
                Pengaturan
            </p>

            <a
                href="{{ $profileAdminUrl }}"
                class="relative group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition
                    {{ $isProfileAdminActive ? 'bg-[#fff7d6] text-[#1d428a]' : 'text-slate-700 hover:bg-[#fff7d6] hover:text-[#1d428a]' }}"
            >
                @if($isProfileAdminActive)
                    <span class="absolute -left-2 h-7 w-1 rounded-r-full bg-[#1d428a]"></span>
                @endif
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[#fef6e0] text-[#d4a116] group-hover:bg-white">
                    {{-- icon: user circle --}}
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                        <circle cx="12" cy="8" r="3.25" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6 19.25C6.8 16.8 9.2 15 12 15s5.2 1.8 6 4.25" />
                    </svg>
                </span>
                @if($sidebarOpen)
                    <span>Profil admin</span>
                @endif
            </a>


            <a
                href="{{ $settingsUrl }}"
                class="relative group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition
                       {{ $isSettingsActive ? 'bg-[#fff7d6] text-[#1d428a]' : 'text-slate-700 hover:bg-[#fff7d6] hover:text-[#1d428a]' }}"
            >
                @if($isSettingsActive)
                    <span class="absolute -left-2 h-7 w-1 rounded-r-full bg-[#1d428a]"></span>
                @endif
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[#fef6e0] text-[#d4a116] group-hover:bg-white">
                    {{-- icon: cog --}}
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M10.325 4.317L9.6 3h4.8l-.725 1.317M6.5 8.25L5 7l2.5-2.5 1.5 1.25M17.5 8.25L19 7l-2.5-2.5-1.5 1.25M9.6 21l.725-1.317M13.675 19.683 14.4 21M6.5 15.75 5 17l2.5 2.5 1.5-1.25M17.5 15.75 19 17l-2.5 2.5-1.5-1.25" />
                        <circle cx="12" cy="12" r="3.25" />
                    </svg>
                </span>
                @if($sidebarOpen)
                    <span>Pengaturan platform</span>
                @endif
            </a>
        </div>
    </nav>

    

        {{-- Footer: info admin + menu akun --}}
    <div x-data="{ open: false }" class="px-3 py-4 border-t border-slate-100 bg-slate-50/70">
        {{-- area profil, diklik untuk toggle menu --}}
        <button
            type="button"
            @click="open = !open"
            class="w-full flex items-center gap-3 rounded-xl px-3 py-2.5 hover:bg-white transition cursor-pointer"
        >
            <div class="h-9 w-9 rounded-full bg-[#1d428a] text-white flex items-center justify-center text-xs font-semibold">
                {{ strtoupper(mb_substr($user->name ?? 'A', 0, 1)) }}
            </div>

            @if($sidebarOpen)
                <div class="flex-1 text-left">
                    <p class="text-sm font-medium text-slate-900 truncate">
                        {{ $user->name ?? 'Admin' }}
                    </p>
                    <p class="text-[11px] text-slate-500 truncate">
                        {{ $user->email ?? '-' }}
                    </p>
                    <p class="mt-0.5 text-[10px] text-slate-400">
                        Role: Admin
                    </p>
                </div>

                {{-- icon caret --}}
                <svg xmlns="http://www.w3.org/2000/svg"
                     :class="open ? 'rotate-180' : ''"
                     class="h-4 w-4 text-slate-400 transition-transform"
                     viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                          d="M5.23 7.21a.75.75 0 011.06.02L10 11.06l3.71-3.83a.75.75 0 111.08 1.04l-4.25 4.39a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z"
                          clip-rule="evenodd" />
                </svg>
            @endif
        </button>

        {{-- dropdown menu --}}
        <div
            x-show="open"
            x-transition
            @click.outside="open = false"
            class="mt-2 space-y-1 text-[11px]"
        >
            {{-- Kembali ke landing page --}}
            <a
                href="{{ $homeUrl }}"
                class="flex items-center gap-2 rounded-xl px-3 py-2 text-slate-700 hover:bg-white hover:text-[#1d428a] border border-transparent hover:border-slate-200"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 3.22l-7 5.5V17a1 1 0 001 1h5v-4h2v4h5a1 1 0 001-1V8.72l-7-5.5z" />
                </svg>
                <span>Ke halaman landing</span>
            </a>

            {{-- Logout --}}
            @if(Route::has('logout'))
                <form method="POST" action="{{ route('logout') }}" class="m-0" @click.stop>
                    @csrf
                    <button
                        type="submit"
                        class="w-full flex items-center gap-2 rounded-xl px-3 py-2 text-rose-600 hover:bg-white hover:text-rose-700 border border-transparent hover:border-rose-100 cursor-pointer"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                  d="M3.5 4.75A1.75 1.75 0 015.25 3h4a.75.75 0 010 1.5h-4a.25.25 0 00-.25.25v11.5c0 .138.112.25.25.25h4a.75.75 0 010 1.5h-4A1.75 1.75 0 013.5 16.75V4.75zm9.47-.53a.75.75 0 011.06 0l3.25 3.25a.75.75 0 01-1.06 1.06L14.75 7.56v4.69a.75.75 0 01-1.5 0V7.56l-1.47 1.47a.75.75 0 11-1.06-1.06l3.25-3.25z"
                                  clip-rule="evenodd" />
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

</aside>
