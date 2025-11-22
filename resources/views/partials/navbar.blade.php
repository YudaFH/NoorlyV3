<nav id="site-navbar" class="w-full fixed top-0 left-0 z-50 transition-colors duration-300">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    @php
        use Illuminate\Support\Facades\Route;
    @endphp

    {{-- Top bar --}}
    <div class="flex items-center justify-between py-[18px]">
      {{-- Left: Logo --}}
      <div class="flex items-center">
        <a href="{{ route('home') }}" class="flex items-center">
          <img src="{{ asset('images/icon/logo_header.png') }}" alt="Logo Noorly" class="h-8 w-auto mr-3">
        </a>
      </div>

      {{-- Center: Links (desktop) --}}
      <div class="hidden md:flex justify-center">
        <ul class="flex items-center gap-6 lg:gap-8 text-sm font-medium">
          <li>
            <a href="{{ route('home') }}"
               class="nav-link {{ Request::routeIs('home') ? 'active' : '' }}">
              Beranda
            </a>
          </li>
          <li>
            <a href="{{ route('contents.index') }}"
               class="nav-link {{ Request::routeIs('konten') ? 'active' : '' }}">
              Konten
            </a>
          </li>
          {{-- <li>
            <a href="{{ url('komunitas') }}"
               class="nav-link {{ Request::is('komunitas') ? 'active' : '' }}">
              Komunitas
            </a>
          </li>
          <li>
            <a href="{{ url('acara') }}"
               class="nav-link {{ Request::is('acara') ? 'active' : '' }}">
              Acara
            </a>
          </li> --}}
          <li>
            <a href="{{ route('contact.show') }}"
               class="nav-link {{ Request::routeIs('contact.show') ? 'active' : '' }}">
              Kontak
            </a>
          </li>
        </ul>
      </div>

      {{-- Right: Auth (desktop) --}}
      <div class="hidden md:flex items-center gap-3">
        @guest
          <a href="{{ route('login') }}"
             class="text-sm font-medium text-[#b8c0cc] hover:text-[#fbc926] transition">
            Login
          </a>
          <a href="{{ route('register') }}"
             class="inline-flex items-center rounded-full bg-[#fbc926] px-4 py-2 text-sm font-semibold text-white border border-transparent hover:bg-white hover:text-[#fbc926] hover:border-[#fbc926] transform hover:scale-105 transition">
            Daftar
          </a>
        @else
          @php
            /** @var \App\Models\User $user */
            $user = auth()->user();

            // role & dashboard
            if ($user->role === 'admin') {
                $dashboardRoute = route('admin.dashboard');
                $roleLabel      = 'Admin';
            } elseif (method_exists($user, 'isCreator') && $user->isCreator()) {
                $dashboardRoute = route('creator.dashboard');
                $roleLabel      = 'Kreator';
            } else {
                $dashboardRoute = null; // user biasa tidak punya dashboard
                $roleLabel      = 'Pengguna';
            }

            // boleh jadi kreator kalau bukan admin & belum kreator
            $canBecomeCreator = $user->role !== 'admin'
                && !(method_exists($user, 'isCreator') && $user->isCreator());

            // URL fitur tambahan (fallback ke '#' kalau route belum ada)
            $profileUrl      = Route::has('user.profile')          ? route('user.profile')          : '#';
            $purchasesUrl    = Route::has('user.purchases')        ? route('user.purchases')        : '#';
            $supportUrl      = Route::has('support.tickets.index') ? route('support.tickets.index') : '#';
            $creatorApplyUrl = Route::has('creator.onboarding')    ? route('creator.onboarding')    : '#';
          @endphp

          <div x-data="{ open: false }" class="relative">
            {{-- Trigger: avatar + nama --}}
            <button
              type="button"
              @click="open = !open"
              @click.outside="open = false"
              @keydown.escape.window="open = false"
              class="flex items-center gap-2 rounded-full border border-slate-200 bg-white/80 px-3 py-1.5 text-sm shadow-sm hover:shadow-md hover:bg-white cursor-pointer transition"
            >
              {{-- Avatar / inisial --}}
              @if(!empty($user->avatar_url ?? null))
                <img
                  src="{{ $user->avatar_url }}"
                  alt="{{ $user->name }}"
                  class="h-8 w-8 rounded-full object-cover"
                >
              @else
                <div class="h-8 w-8 rounded-full bg-[#1d428a] text-white flex items-center justify-center text-xs font-semibold">
                  {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                </div>
              @endif

              <span class="max-w-[120px] truncate text-[#0e141b] font-medium">
                {{ $user->name }}
              </span>

              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
              </svg>
            </button>

            {{-- Dropdown --}}
            <div
              x-show="open"
              x-transition.opacity
              x-cloak
              class="absolute right-0 mt-2 w-56 rounded-xl border border-slate-100 bg-white shadow-lg py-2 z-50"
            >
              {{-- Header kecil --}}
              <div class="px-3 pb-2 border-b border-slate-100 mb-1">
                <p class="text-xs text-slate-400">Masuk sebagai</p>
                <p class="text-sm font-semibold text-slate-800 truncate">
                  {{ $user->name }}
                </p>
                <p class="text-[11px] text-slate-400">
                  {{ $roleLabel }}
                </p>
              </div>

              {{-- Profil akun --}}
              <a
                href="{{ $profileUrl }}"
                class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50 cursor-pointer"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                  <path d="M10 2a4 4 0 100 8 4 4 0 000-8zM4 14a4 4 0 014-4h4a4 4 0 014 4v1a1 1 0 01-1 1H5a1 1 0 01-1-1v-1z" />
                </svg>
                <span>Profil &amp; pengaturan</span>
              </a>

              {{-- Konten yang dibeli --}}
              <a
                href="{{ $purchasesUrl }}"
                class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50 cursor-pointer"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                  <path d="M3 3.75A.75.75 0 013.75 3h12.5a.75.75 0 01.737.94l-2.25 8.25A1.75 1.75 0 0113.047 14H7.203a1.75 1.75 0 01-1.69-1.31L3.26 3.94A.75.75 0 013 3.75z" />
                  <path d="M7.5 16a1.25 1.25 0 112.5 0 1.25 1.25 0 01-2.5 0zm5.75-1.25a1.25 1.25 0 100 2.5 1.25 1.25 0 000-2.5z" />
                </svg>
                <span>Konten yang saya beli</span>
              </a>

              {{-- Dashboard (admin/kreator) --}}
              @if($dashboardRoute)
                <a
                  href="{{ $dashboardRoute }}"
                  class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50 cursor-pointer"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.75 2.75a.75.75 0 00-1.5 0v1.86A5.501 5.501 0 004.22 9.25H2.75a.75.75 0 000 1.5h1.47a5.501 5.501 0 005.03 4.64v1.86a.75.75 0 001.5 0v-1.86a5.501 5.501 0 005.03-4.64h1.47a.75.75 0 000-1.5h-1.47a5.501 5.501 0 00-5.03-4.64V2.75z" />
                  </svg>
                  <span>Dashboard</span>
                </a>
              @endif

              {{-- Jadi kreator (hanya user biasa) --}}
              @if($canBecomeCreator)
                <div class="my-1 border-t border-slate-100"></div>
                <a
                  href="{{ $creatorApplyUrl }}"
                  class="flex items-center gap-2 px-3 py-2 text-sm font-semibold text-[#1d428a] hover:bg-slate-50 cursor-pointer"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#fbc926]" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 2a2 2 0 00-2 2v1H6a2 2 0 00-2 2v1.5a6 6 0 1012 0V7a2 2 0 00-2-2h-2V4a2 2 0 00-2-2z" />
                  </svg>
                  <span>Jadi kreator di Noorly</span>
                </a>
              @endif

              {{-- Support / tiket --}}
              <a
                href="{{ $supportUrl }}"
                class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50 cursor-pointer"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                  <path d="M18 10a8 8 0 11-9.25-7.88.75.75 0 01.87.73V5.5A.5.5 0 0110 6h3.65a.75.75 0 01.73.87A8.01 8.01 0 0118 10z" />
                  <path d="M11 9a1 1 0 10-2 0v4a1 1 0 102 0V9zM10 15a1.25 1.25 0 100 2.5A1.25 1.25 0 0010 15z" />
                </svg>
                <span>Tiket &amp; bantuan</span>
              </a>

              <div class="my-1 border-t border-slate-100"></div>

              {{-- Logout --}}
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                  type="submit"
                  class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-red-50 cursor-pointer"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 4.75A1.75 1.75 0 014.75 3h5.5A1.75 1.75 0 0112 4.75v1a.75.75 0 01-1.5 0v-1a.25.25 0 00-.25-.25h-5.5a.25.25 0 00-.25.25v10.5c0 .138.112.25.25.25h5.5a.25.25 0 00.25-.25v-1a.75.75 0 011.5 0v1A1.75 1.75 0 0110.25 17h-5.5A1.75 1.75 0 013 15.25V4.75zm9.22 2.03a.75.75 0 011.06 0l2.97 2.97a.75.75 0 010 1.06l-2.97 2.97a.75.75 0 11-1.06-1.06l1.69-1.69H9.25a.75.75 0 010-1.5h4.66l-1.69-1.69a.75.75 0 010-1.06z" clip-rule="evenodd" />
                  </svg>
                  <span>Logout</span>
                </button>
              </form>
            </div>
          </div>
        @endguest
      </div>

      {{-- Mobile: burger --}}
      <div class="md:hidden">
        <button id="nav-toggle" type="button" aria-controls="mobile-menu" aria-expanded="false" aria-label="Toggle navigation" class="p-2 rounded focus:outline-none focus:ring-2 focus:ring-[#fbc926] text-[#b8c0cc] hover:text-[#fbc926]">
          {{-- icon: menu --}}
          <svg id="icon-open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
          {{-- icon: close --}}
          <svg id="icon-close" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>

    {{-- Mobile panel --}}
    <div id="mobile-menu" class="md:hidden hidden pb-4">
      <ul class="flex flex-col gap-3">
        <li>
          <a href="{{ route('home') }}"
             class="nav-link {{ Request::routeIs('home') ? 'active' : '' }}">
            Beranda
          </a>
        </li>
        <li>
          <a href="{{ route('contents.index') }}"
             class="nav-link {{ Request::routeIs('konten') ? 'active' : '' }}">
            Konten
          </a>
        </li>
         {{--  <li>
          <a href="{{ url('komunitas') }}"
             class="nav-link {{ Request::is('komunitas') ? 'active' : '' }}">
            Komunitas
          </a>
        </li>
        <li>
          <a href="{{ url('acara') }}"
             class="nav-link {{ Request::is('acara') ? 'active' : '' }}">
            Acara
          </a>
        </li> --}}
        <li>
          <a href="{{ route('contact.show') }}"
             class="nav-link {{ Request::routeIs('contact.show') ? 'active' : '' }}">
            Kontak
          </a>
        </li>
      </ul>

      <div class="mt-4">
        @guest
          <a href="{{ route('login') }}"
             class="inline-flex w-full items-center justify-center rounded-md bg-[#fbc926] px-4 py-2 text-sm font-medium text-white border border-transparent hover:bg-white hover:text-[#fbc926] hover:border-[#fbc926] transform hover:scale-105 transition">
            Login
          </a>
          <a href="{{ route('register') }}"
             class="mt-3 inline-flex w-full items-center justify-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-[#fbc926] border border-[#fbc926] hover:bg-[#fbc926] hover:text-white transition">
            Daftar
          </a>
        @else
          @php
            /** @var \App\Models\User $user */
            $user = auth()->user();

            if ($user->role === 'admin') {
                $dashboardRoute = route('admin.dashboard');
                $roleLabel      = 'Admin';
            } elseif (method_exists($user, 'isCreator') && $user->isCreator()) {
                $dashboardRoute = route('creator.dashboard');
                $roleLabel      = 'Kreator';
            } else {
                $dashboardRoute = null;
                $roleLabel      = 'Pengguna';
            }

            $canBecomeCreator = $user->role !== 'admin'
                && !(method_exists($user, 'isCreator') && $user->isCreator());

            $profileUrl      = Route::has('user.profile')          ? route('user.profile')          : '#';
            $purchasesUrl    = Route::has('user.purchases')        ? route('user.purchases')        : '#';
            $supportUrl      = Route::has('support.tickets.index') ? route('support.tickets.index') : '#';
            $creatorApplyUrl = Route::has('creator.onboarding')    ? route('creator.onboarding')    : '#';
          @endphp

          <div class="mt-4 border-t border-slate-100 pt-3 space-y-2">
            <div class="flex items-center gap-3 mb-2">
              @if(!empty($user->avatar_url ?? null))
                <img
                  src="{{ $user->avatar_url }}"
                  alt="{{ $user->name }}"
                  class="h-9 w-9 rounded-full object-cover"
                >
              @else
                <div class="h-9 w-9 rounded-full bg-[#1d428a] text-white flex items-center justify-center text-xs font-semibold">
                  {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                </div>
              @endif
              <div class="flex flex-col">
                <span class="text-sm font-medium text-[#0e141b]">
                  {{ $user->name }}
                </span>
                <span class="text-[11px] text-slate-500">
                  {{ $roleLabel }}
                </span>
              </div>
            </div>

            <a href="{{ $profileUrl }}"
               class="inline-flex w-full items-center justify-center rounded-md bg-slate-100 px-4 py-2 text-sm font-medium text-slate-800 hover:bg-slate-200 transition">
              Profil &amp; pengaturan
            </a>

            <a href="{{ $purchasesUrl }}"
               class="inline-flex w-full items-center justify-center rounded-md bg-slate-100 px-4 py-2 text-sm font-medium text-slate-800 hover:bg-slate-200 transition">
              Konten yang saya beli
            </a>

            @if($dashboardRoute)
              <a href="{{ $dashboardRoute }}"
                 class="inline-flex w-full items-center justify-center rounded-md bg-slate-100 px-4 py-2 text-sm font-medium text-slate-800 hover:bg-slate-200 transition">
                Dashboard
              </a>
            @endif

            @if($canBecomeCreator)
              <a href="{{ $creatorApplyUrl }}"
                 class="inline-flex w-full items-center justify-center rounded-md bg-[#1d428a] px-4 py-2 text-sm font-semibold text-white hover:bg-[#163268] transition">
                Jadi kreator di Noorly
              </a>
            @endif

            <a href="{{ $supportUrl }}"
               class="inline-flex w-full items-center justify-center rounded-md bg-slate-100 px-4 py-2 text-sm font-medium text-slate-800 hover:bg-slate-200 transition">
              Tiket &amp; bantuan
            </a>

            <form method="POST" action="{{ route('logout') }}" class="pt-1">
              @csrf
              <button type="submit"
                      class="inline-flex w-full items-center justify-center rounded-md bg-red-500 px-4 py-2 text-sm font-medium text-white hover:bg-red-600 cursor-pointer">
                Logout
              </button>
            </form>
          </div>
        @endguest
      </div>
    </div>
  </div>

  {{-- CSS & JS sama seperti sebelumnya --}}
  <style>
    #site-navbar {
      background: transparent;
      backdrop-filter: blur(4px);
    }
    #site-navbar.scrolled {
      background: #ffffff;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .nav-link {
      color: #b8c0cc;
      position: relative;
      display: inline-block;
      padding: 4px 0;
      transition: color .18s ease, transform .18s ease;
    }
    #site-navbar.scrolled .nav-link {
      color: #0e141b;
    }
    #site-navbar.scrolled .nav-link.active {
      color: #fbc926;
    }
    .nav-link::after {
      content: '';
      position: absolute;
      left: 0;
      bottom: -8px;
      height: 2px;
      width: 100%;
      background: #fbc926;
      transform-origin: left;
      transform: scaleX(0);
      transition: transform .28s cubic-bezier(.2,.8,.2,1);
    }
    .nav-link:hover {
      transform: scale(1.08);
      color: #fbc926;
    }
    .nav-link:hover::after {
      transform: scaleX(1);
    }
    .nav-link.active {
      color: #fbc926;
    }
    .nav-link.active::after {
      transform: scaleX(1);
    }
    #site-navbar.scrolled #mobile-menu {
      background: #ffffff;
      border-top: 1px solid rgba(0,0,0,0.05);
    }
  </style>

  <script>
    (function(){
      const btn     = document.getElementById('nav-toggle');
      const navbar  = document.getElementById('site-navbar');
      const menu    = document.getElementById('mobile-menu');
      const icoOpen = document.getElementById('icon-open');
      const icoClose= document.getElementById('icon-close');

      if (btn) {
        btn.addEventListener('click', function () {
          const expanded = this.getAttribute('aria-expanded') === 'true';
          this.setAttribute('aria-expanded', String(!expanded));
          menu.classList.toggle('hidden');
          icoOpen.classList.toggle('hidden');
          icoClose.classList.toggle('hidden');
        });
      }

      function onScroll() {
        if (window.scrollY > 10) {
          navbar.classList.add('scrolled');
        } else {
          navbar.classList.remove('scrolled');
        }
      }

      window.addEventListener('scroll', onScroll, { passive: true });
      onScroll();
    })();
  </script>
</nav>
