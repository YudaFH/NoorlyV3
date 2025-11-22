
@php
    use Illuminate\Support\Facades\Route;

    /** @var bool $sidebarOpen */
    $user = auth()->user();

    // URL menu (fallback ke # kalau route belum ada)
    $dashboardUrl = Route::has('creator.dashboard')
        ? route('creator.dashboard')
        : '#';

    $contentsUrl = Route::has('creator.contents.index')
        ? route('creator.contents.index')
        : '#';

    $audienceUrl = Route::has('creator.audience.index')
        ? route('creator.audience.index')
        : '#';

    $balanceUrl = Route::has('creator.balance.index')
        ? route('creator.balance.index')
        : '#';

    $supportUrl = Route::has('creator.support.index')
        ? route('creator.support.index')
        : '#';

    $notificationsUrl = Route::has('creator.notifications.index')
        ? route('creator.notifications.index')
        : '#';

    $profileUrl = Route::has('creator.profile')
        ? route('creator.profile')
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
                    Panel kreator
                </p>
                <p class="text-sm font-semibold text-slate-900">
                    Navigasi
                </p>
            </div>
        @endif

        <button
            type="button"
            wire:click="toggleSidebar"
            class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-[#fbc926] text-white shadow-sm hover:shadow-md hover:bg-[#e6b522] transition cursor-pointer"
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
        {{-- Dashboard --}}
        @php
            $isDashboardActive = request()->routeIs('creator.dashboard');
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
                {{-- Icon: Home --}}
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 10.5L12 4l9 6.5M5 10.5V20h5v-5h4v5h5v-9.5" />
                </svg>
            </span>

            @if($sidebarOpen)
                <span>Dashboard</span>
            @endif
        </a>

        {{-- Konten saya --}}
        @php
            $isContentsActive = request()->routeIs('creator.contents.*');
        @endphp
        <a
            href="{{ $contentsUrl }}"
            class="relative group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                   {{ $isContentsActive ? 'bg-[#fff7d6] text-[#1d428a]' : 'text-slate-700 hover:bg-[#fff7d6] hover:text-[#1d428a]' }}"
        >
            @if($isContentsActive)
                <span class="absolute -left-2 h-7 w-1 rounded-r-full bg-[#1d428a]"></span>
            @endif

            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[#fef6e0] text-[#d4a116] group-hover:bg-white">
                {{-- Heroicon: Rectangle Stack --}}
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M4.5 9.75l7.5-4.5 7.5 4.5M4.5 14.25l7.5 4.5 7.5-4.5M4.5 9.75v4.5l7.5 4.5 7.5-4.5v-4.5" />
                </svg>
            </span>

            @if($sidebarOpen)
                <span>Konten saya</span>
            @endif
        </a>

        {{-- Audience & pembeli --}}
        @php
            $isAudienceActive = request()->routeIs('creator.audience.*');
        @endphp
        <a
            href="{{ $audienceUrl }}"
            class="relative group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                   {{ $isAudienceActive ? 'bg-[#fff7d6] text-[#1d428a]' : 'text-slate-700 hover:bg-[#fff7d6] hover:text-[#1d428a]' }}"
        >
            @if($isAudienceActive)
                <span class="absolute -left-2 h-7 w-1 rounded-r-full bg-[#1d428a]"></span>
            @endif

            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[#fef6e0] text-[#d4a116] group-hover:bg-white">
                {{-- Heroicon: Users --}}
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15 19.127a3.001 3.001 0 012.25-2.877 3.001 3.001 0 00-2.25-2.873M9 19.127a3.001 3.001 0 00-2.25-2.877 3.001 3.001 0 012.25-2.873M9 10.5a3 3 0 100-6 3 3 0 000 6zm6 0a3 3 0 100-6 3 3 0 000 6z" />
                </svg>
            </span>

            @if($sidebarOpen)
                <span>Audience &amp; pembeli</span>
            @endif
        </a>

        {{-- Saldo & penarikan --}}
        @php
            $isBalanceActive = request()->routeIs('creator.balance.*');
        @endphp
        <a
            href="{{ $balanceUrl }}"
            class="relative group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                   {{ $isBalanceActive ? 'bg-[#fff7d6] text-[#1d428a]' : 'text-slate-700 hover:bg-[#fff7d6] hover:text-[#1d428a]' }}"
        >
            @if($isBalanceActive)
                <span class="absolute -left-2 h-7 w-1 rounded-r-full bg-[#1d428a]"></span>
            @endif

            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[#fef6e0] text-[#d4a116] group-hover:bg-white">
                {{-- Heroicon: Banknotes --}}
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3.75 6.75h16.5v10.5H3.75z" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M7.5 9.75a2.25 2.25 0 01-2.25-2.25M18.75 9.75a2.25 2.25 0 002.25-2.25M7.5 17.25a2.25 2.25 0 01-2.25 2.25M18.75 17.25a2.25 2.25 0 002.25 2.25" />
                    <circle cx="12" cy="12" r="2.25" />
                </svg>
            </span>

            @if($sidebarOpen)
                <span>Saldo &amp; penarikan</span>
            @endif
        </a>

        {{-- Support kreator --}}
        @php
            $isSupportActive = request()->routeIs('creator.support.*');
        @endphp
        <a
            href="{{ $supportUrl }}"
            class="relative group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                   {{ $isSupportActive ? 'bg-[#fff7d6] text-[#1d428a]' : 'text-slate-700 hover:bg-[#fff7d6] hover:text-[#1d428a]' }}"
        >
            @if($isSupportActive)
                <span class="absolute -left-2 h-7 w-1 rounded-r-full bg-[#1d428a]"></span>
            @endif

            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[#fef6e0] text-[#d4a116] group-hover:bg-white">
                {{-- Heroicon: Lifebuoy --}}
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <circle cx="12" cy="12" r="4.5" />
                    <circle cx="12" cy="12" r="7.5" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 4.5V3M19.5 12H21M12 21v-1.5M3 12h1.5" />
                </svg>
            </span>

            @if($sidebarOpen)
                <span>Support kreator</span>
            @endif
        </a>

        {{-- Notifikasi --}}
        @php
            $isNotifActive = request()->routeIs('creator.notifications.*');
        @endphp
        <a
            href="{{ $notificationsUrl }}"
            class="relative group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                   {{ $isNotifActive ? 'bg-[#fff7d6] text-[#1d428a]' : 'text-slate-700 hover:bg-[#fff7d6] hover:text-[#1d428a]' }}"
        >
            @if($isNotifActive)
                <span class="absolute -left-2 h-7 w-1 rounded-r-full bg-[#1d428a]"></span>
            @endif

            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[#fef6e0] text-[#d4a116] group-hover:bg-white">
                {{-- Heroicon: Bell --}}
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M14.25 18.75a2.25 2.25 0 01-4.5 0M6.75 9A5.25 5.25 0 1117.25 9c0 3.003.879 4.44 1.492 5.174.346.415.519.623.507.827a.75.75 0 01-.311.572c-.16.112-.412.112-.917.112H6.979c-.505 0-.757 0-.917-.112a.75.75 0 01-.311-.572c-.012-.204.161-.412.507-.827C5.871 13.44 6.75 12.003 6.75 9z" />
                </svg>
            </span>

            @if($sidebarOpen)
                <span>Notifikasi</span>
            @endif
        </a>
    </nav>

    {{-- Footer user / profil kreator --}}
    <div class="px-3 py-4 border-t border-slate-100 bg-slate-50/70">
        <a href="{{ $profileUrl }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 hover:bg-white transition">
            {{-- Avatar: pakai foto kalau ada, kalau tidak pakai inisial --}}
            @if(!empty($user->avatar_path))
                <img
                    src="{{ asset('storage/'.$user->avatar_path) }}"
                    alt="{{ $user->name }}"
                    class="h-9 w-9 rounded-full object-cover border border-slate-200"
                >
            @else
                <div class="h-9 w-9 rounded-full bg-[#fbc926] text-white flex items-center justify-center text-xs font-semibold">
                    {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                </div>
            @endif

            @if($sidebarOpen)
                <div class="flex-1">
                    <p class="text-sm font-medium text-slate-900 truncate">
                        {{ $user->name }}
                    </p>
                    <p class="text-[11px] text-slate-500 truncate">
                        {{ $user->email }}
                    </p>

                    <div class="mt-1 inline-flex items-center gap-1 rounded-full bg-[#1d428a]/5 px-2 py-0.5">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        <span class="text-[10px] font-medium text-[#1d428a]">
                            Lihat &amp; edit profil
                        </span>
                    </div>
                </div>
            @endif
        </a>
    </div>
</aside>
