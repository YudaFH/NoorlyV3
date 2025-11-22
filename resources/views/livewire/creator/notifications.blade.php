@php
    use Illuminate\Support\Str;
@endphp

<div class="flex h-screen bg-slate-50">
    {{-- SIDEBAR --}}
    @include('livewire.creator.sidebar', ['sidebarOpen' => $sidebarOpen])

    {{-- MAIN --}}
    <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8 overflow-y-auto">
        {{-- HEADER --}}
        <header class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-slate-900">
                    Notifikasi
                </h1>
                <p class="mt-1 text-sm text-slate-500 max-w-2xl">
                    Semua update penting tentang saldo, konten, pembeli, dan support akan muncul di sini.
                </p>
            </div>

            <div class="flex flex-col items-end gap-2">
                <p class="text-[11px] text-slate-500">
                    Notifikasi hanya terlihat oleh kamu sebagai kreator.
                </p>
                <button
                    type="button"
                    wire:click="markAllAsRead"
                    class="cursor-pointer inline-flex items-center gap-1 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-[11px] font-medium text-slate-700 hover:bg-slate-50"
                >
                    Tandai semua sudah dibaca
                </button>
            </div>
        </header>

        {{-- METRIK RINGKAS --}}
        <section class="grid gap-4 md:grid-cols-4 mb-6">
            <article class="bg-white rounded-2xl border border-slate-100 shadow-sm px-5 py-4">
                <p class="text-xs font-medium text-slate-500">Belum dibaca</p>
                <p class="mt-3 text-2xl font-semibold text-[#1d428a]">{{ $totalUnread }}</p>
                <p class="mt-1 text-[11px] text-slate-400">
                    Notifikasi penting yang belum kamu buka.
                </p>
            </article>

            <article class="bg-white rounded-2xl border border-slate-100 shadow-sm px-5 py-4">
                <p class="text-xs font-medium text-slate-500">Semua notifikasi</p>
                <p class="mt-3 text-2xl font-semibold text-slate-900">{{ $totalAll }}</p>
                <p class="mt-1 text-[11px] text-slate-400">
                    Total semua notifikasi sejak kamu mulai di Noorly.
                </p>
            </article>

            <article class="bg-white rounded-2xl border border-slate-100 shadow-sm px-5 py-4">
                <p class="text-xs font-medium text-slate-500">Saldo &amp; penarikan</p>
                <p class="mt-3 text-2xl font-semibold text-emerald-600">{{ $balanceCount }}</p>
                <p class="mt-1 text-[11px] text-slate-400">
                    Update terkait penarikan saldo dan pembayaran ke rekeningmu.
                </p>
            </article>

            <article class="bg-white rounded-2xl border border-slate-100 shadow-sm px-5 py-4">
                <p class="text-xs font-medium text-slate-500">Konten &amp; pembeli</p>
                <p class="mt-3 text-2xl font-semibold text-slate-900">
                    {{ $contentCount + $buyerCount }}
                </p>
                <p class="mt-1 text-[11px] text-slate-400">
                    Notifikasi tentang status konten dan aktivitas pembeli.
                </p>
            </article>
        </section>

        {{-- FILTER BAR --}}
        <section class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div class="flex flex-wrap gap-2">
                @php
                    $tabs = [
                        'all'     => 'Semua',
                        'balance' => "Saldo ({$balanceCount})",
                        'content' => "Konten ({$contentCount})",
                        'buyer'   => "Pembeli ({$buyerCount})",
                        'support' => "Support ({$supportCount})",
                        'system'  => "Platform ({$systemCount})",
                    ];
                @endphp

                @foreach($tabs as $key => $label)
                    <button
                        type="button"
                        wire:click="$set('typeFilter', '{{ $key }}')"
                        class="cursor-pointer inline-flex items-center rounded-full px-3 py-1.5 text-[11px] font-medium
                            @if($typeFilter === $key)
                                bg-[#1d428a] text-white
                            @else
                                bg-slate-100 text-slate-600 hover:bg-slate-200
                            @endif"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <div class="flex flex-wrap items-center gap-3 justify-end">
                {{-- Hanya yang belum dibaca --}}
                <label class="inline-flex items-center gap-1 text-[11px] text-slate-600">
                    <input
                        type="checkbox"
                        wire:model="onlyUnread"
                        class="h-3.5 w-3.5 rounded border-slate-300 text-[#1d428a] focus:ring-[#1d428a]"
                    >
                    <span>Hanya yang belum dibaca</span>
                </label>

                {{-- Search --}}
                <div class="w-full md:w-56">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 103.473 9.8l3.613 3.614a.75.75 0 101.06-1.06l-3.614-3.613A5.5 5.5 0 009 3.5zm-4 5.5a4 4 0 118 0 4 4 0 01-8 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <input
                            type="text"
                            wire:model.debounce.400ms="search"
                            class="block w-full rounded-full border border-slate-200 bg-white pl-8 pr-3 py-1.5 text-[11px] text-slate-900 placeholder:text-slate-400 focus:border-[#1d428a] focus:ring-[#1d428a]"
                            placeholder="Cari judul / isi notif..."
                        >
                    </div>
                </div>
            </div>
        </section>

        {{-- LIST NOTIFIKASI --}}
        @if($notifications->count() > 0)
            <section class="space-y-2">
                @foreach($notifications as $notification)
                    @php
                        $isUnread = is_null($notification->read_at);

                        $type = $notification->type;
                        [$iconBg, $iconColor] = match($type) {
                            'balance' => ['bg-emerald-50', 'text-emerald-600'],
                            'content' => ['bg-indigo-50', 'text-indigo-600'],
                            'buyer'   => ['bg-sky-50', 'text-sky-600'],
                            'support' => ['bg-amber-50', 'text-amber-600'],
                            'system'  => ['bg-slate-100', 'text-slate-600'],
                            default   => ['bg-slate-100', 'text-slate-600'],
                        };

                        $typeLabel = $this->typeOptions[$type] ?? ucfirst($type);

                        $data = $notification->data ?? [];
                        $ctaUrl = $data['url'] ?? null;
                    @endphp

                    <article
                        class="rounded-2xl border border-slate-100 px-4 py-3 shadow-sm flex gap-3
                               {{ $isUnread ? 'bg-white' : 'bg-slate-50/60' }}"
                    >
                        {{-- ICON --}}
                        <div class="mt-1">
                            <div class="h-9 w-9 rounded-full flex items-center justify-center {{ $iconBg }} {{ $iconColor }}">
                                @switch($type)
                                    @case('balance')
                                        {{-- Icon: banknotes --}}
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M3.75 6.75h16.5v10.5H3.75z" />
                                            <circle cx="12" cy="12" r="2.25" />
                                        </svg>
                                        @break
                                    @case('content')
                                        {{-- Icon: document --}}
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M9 3.75h6.75L21 9v11.25H9z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M9 3.75v16.5H3.75V3.75z" />
                                        </svg>
                                        @break
                                    @case('buyer')
                                        {{-- Icon: user --}}
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                            <circle cx="12" cy="8" r="3" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M6 19.25a6 6 0 0112 0" />
                                        </svg>
                                        @break
                                    @case('support')
                                        {{-- Icon: lifebuoy --}}
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                            <circle cx="12" cy="12" r="4.5" />
                                            <circle cx="12" cy="12" r="7.5" />
                                        </svg>
                                        @break
                                    @case('system')
                                        {{-- Icon: megaphone --}}
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M3 10.5l12-4.5v12l-12-4.5z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M15 8.25V5.25M15 18.75v-3" />
                                        </svg>
                                        @break
                                    @default
                                        {{-- Icon default: bell --}}
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M14.25 18.75a2.25 2.25 0 01-4.5 0M6.75 9A5.25 5.25 0 1117.25 9" />
                                        </svg>
                                @endswitch
                            </div>
                        </div>

                        {{-- ISI --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-semibold text-slate-900 truncate max-w-xs">
                                            {{ $notification->title }}
                                        </p>

                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-600">
                                            {{ $typeLabel }}
                                        </span>

                                        @if($isUnread)
                                            <span class="inline-flex items-center rounded-full bg-[#1d428a]/10 px-2 py-0.5 text-[10px] font-semibold text-[#1d428a]">
                                                Baru
                                            </span>
                                        @endif
                                    </div>

                                    @if($notification->body)
                                        <p class="mt-0.5 text-[11px] text-slate-600">
                                            {{ Str::limit($notification->body, 160) }}
                                        </p>
                                    @endif
                                </div>

                                <div class="text-right text-[10px] text-slate-400 flex flex-col items-end">
                                    <span>{{ optional($notification->created_at)->format('d M Y H:i') ?? '-' }}</span>
                                    <span>{{ optional($notification->created_at)->diffForHumans() ?? '' }}</span>
                                </div>
                            </div>

                            <div class="mt-2 flex flex-wrap items-center gap-2">
                                @if($ctaUrl)
                                    <a
                                        href="{{ $ctaUrl }}"
                                        class="cursor-pointer inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-medium text-slate-700 hover:bg-slate-50"
                                        target="_blank"
                                    >
                                        Lihat detail
                                    </a>
                                @endif

                                @if($isUnread)
                                    <button
                                        type="button"
                                        wire:click="markAsRead({{ $notification->id }})"
                                        class="cursor-pointer inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-medium text-slate-700 hover:bg-slate-50"
                                    >
                                        Tandai sudah dibaca
                                    </button>
                                @endif

                                {{-- Menu titik tiga: hapus --}}
                                <div class="relative">
                                    <button
                                        type="button"
                                        wire:click="deleteNotification({{ $notification->id }})"
                                        class="cursor-pointer inline-flex items-center rounded-full border border-slate-200 bg-white px-2 py-1 text-[11px] font-medium text-slate-500 hover:bg-slate-50"
                                    >
                                        ⋯
                                        <span class="sr-only">Hapus notifikasi</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </section>

            @if($notifications->hasPages())
                <div class="mt-4">
                    {{ $notifications->links() }}
                </div>
            @endif
        @else
            {{-- EMPTY STATE --}}
            <section class="mt-8 flex flex-col items-center justify-center text-center">
                <div class="mb-4 h-16 w-16 rounded-full bg-[#fef6e0] flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#d4a116]" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6z" />
                        <path d="M10 18a2.5 2.5 0 002.45-2h-4.9A2.5 2.5 0 0010 18z" />
                    </svg>
                </div>
                <h2 class="text-sm font-semibold text-slate-900 mb-1">
                    Belum ada notifikasi
                </h2>
                <p class="text-xs text-slate-500 max-w-sm mb-3">
                    Saat ada update penting tentang saldo, konten, atau support, notifikasi akan muncul di sini.
                </p>
            </section>
        @endif

        <p class="mt-4 text-[11px] text-slate-400">
            Jangan pernah membagikan password atau kode OTP di dalam notifikasi atau pesan apapun. 
            Tim Noorly tidak akan meminta informasi tersebut.
        </p>
    </main>
</div>
