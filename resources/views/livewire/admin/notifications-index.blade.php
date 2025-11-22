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
                    Notifikasi admin
                </h1>
                <p class="mt-1 text-sm text-slate-500 max-w-2xl">
                    Semua notifikasi sistem yang terkait dengan aktivitas kreator, order, penarikan saldo, dan tiket support.
                </p>

                <div class="mt-3 flex flex-wrap gap-3 text-[11px] text-slate-500">
                    <span>Semua: <span class="font-semibold text-slate-700">{{ $totalAll }}</span></span>
                    <span>Belum dibaca: <span class="font-semibold text-amber-600">{{ $totalUnread }}</span></span>
                    <span>Sudah dibaca: <span class="font-semibold text-emerald-600">{{ $totalRead }}</span></span>
                </div>
            </div>

            <div class="flex flex-col items-end gap-2">
                @if (session('status_admin_notifications'))
                    <div class="rounded-full bg-emerald-50 border border-emerald-100 px-3 py-1 text-[11px] text-emerald-700">
                        {{ session('status_admin_notifications') }}
                    </div>
                @endif

                <div class="flex flex-wrap gap-2 justify-end text-[11px]">
                    <button
                        type="button"
                        wire:click="markAllAsRead"
                        class="cursor-pointer inline-flex items-center gap-1 rounded-full border border-slate-200 bg-white px-3 py-1 font-medium text-slate-700 hover:bg-slate-50"
                    >
                        Tandai semua sudah dibaca
                    </button>

                    <button
                        type="button"
                        onclick="if(confirm('Yakin ingin menghapus semua notifikasi? Tindakan ini tidak bisa dibatalkan.')) { Livewire.dispatch('callMethod', { method: 'clearAll' }); }"
                        class="cursor-pointer inline-flex items-center gap-1 rounded-full border border-rose-200 bg-white px-3 py-1 font-medium text-rose-600 hover:bg-rose-50"
                    >
                        Hapus semua
                    </button>
                </div>
            </div>
        </header>

        {{-- FILTER BAR --}}
        <section class="mb-4 rounded-2xl border border-slate-100 bg-white px-4 py-3 shadow-sm">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                {{-- Search --}}
                <div class="w-full md:max-w-sm">
                    <label class="sr-only" for="search-notif">Cari notifikasi</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 103.473 9.8l3.613 3.614a.75.75 0 101.06-1.06l-3.614-3.613A5.5 5.5 0 009 3.5zm-4 5.5a4 4 0 118 0 4 4 0 01-8 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <input
                            id="search-notif"
                            type="text"
                            wire:model.debounce.400ms="search"
                            class="block w-full rounded-full border border-slate-200 bg-slate-50 pl-8 pr-3 py-1.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#1d428a] focus:ring-[#1d428a]"
                            placeholder="Cari isi notifikasi..."
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
                            <option value="unread">Belum dibaca</option>
                            <option value="read">Sudah dibaca</option>
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
                        </select>
                    </div>
                </div>
            </div>
        </section>

        {{-- LIST NOTIFIKASI --}}
        @if($notifications->count() > 0)
            <section class="space-y-3">
                @foreach($notifications as $notification)
                    @php
                        $data    = $notification->data ?? [];
                        $title   = $data['title']   ?? ($data['subject'] ?? 'Notifikasi sistem');
                        $message = $data['message'] ?? ($data['body'] ?? null);
                        $url     = $data['url']     ?? null;
                        $category = $data['category'] ?? null;
                        $isUnread = is_null($notification->read_at);
                    @endphp

                    <article
                        class="rounded-2xl border border-slate-100 bg-white px-4 py-3 shadow-sm flex flex-col gap-2
                               {{ $isUnread ? 'border-amber-100 bg-amber-50/40' : '' }}"
                    >
                        <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
                            <div class="flex items-start gap-3">
                                {{-- Icon --}}
                                <div class="mt-0.5 h-9 w-9 flex items-center justify-center rounded-full
                                    {{ $isUnread ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-500' }}
                                    text-xs font-semibold"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 2a6 6 0 00-6 6v2.586l-.707.707A1 1 0 004 13h12a1 1 0 00.707-1.707L16 10.586V8a6 6 0 00-6-6z" />
                                        <path d="M10 18a3 3 0 01-2.83-2h5.66A3 3 0 0110 18z" />
                                    </svg>
                                </div>

                                <div class="space-y-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h2 class="text-sm font-semibold text-slate-900">
                                            {{ $title }}
                                        </h2>

                                        @if($isUnread)
                                            <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] text-amber-700">
                                                Belum dibaca
                                            </span>
                                        @else
                                            <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] text-emerald-700">
                                                Sudah dibaca
                                            </span>
                                        @endif

                                        @if($category)
                                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] text-slate-600">
                                                {{ $category }}
                                            </span>
                                        @endif
                                    </div>

                                    @if($message)
                                        <p class="text-[11px] text-slate-600">
                                            {{ Str::limit($message, 160) }}
                                        </p>
                                    @endif

                                    <p class="text-[11px] text-slate-400">
                                        Dibuat {{ optional($notification->created_at)->diffForHumans() ?? '-' }}
                                        @if($notification->read_at)
                                            • Dibaca {{ optional($notification->read_at)->diffForHumans() ?? '-' }}
                                        @endif
                                    </p>

                                    @if($url)
                                        <a
                                            href="{{ $url }}"
                                            target="_blank"
                                            class="inline-flex items-center gap-1 text-[11px] font-medium text-[#1d428a] hover:underline"
                                        >
                                            Buka detail
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M11 3a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 11-2 0V5.414l-5.293 5.293a1 1 0 01-1.414-1.414L13.586 4H12a1 1 0 01-1-1z" clip-rule="evenodd" />
                                                <path fill-rule="evenodd" d="M5 4a3 3 0 00-3 3v8a3 3 0 003 3h8a3 3 0 003-3v-3a1 1 0 10-2 0v3a1 1 0 01-1 1H5a1 1 0 01-1-1V7a1 1 0 011-1h3a1 1 0 000-2H5z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </div>

                            {{-- Aksi cepat --}}
                            <div class="flex flex-col items-end gap-2 text-[11px]">
                                <div class="flex flex-wrap gap-2 justify-end">
                                    @if($isUnread)
                                        <button
                                            type="button"
                                            wire:click="markAsRead('{{ $notification->id }}')"
                                            class="cursor-pointer inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 font-medium text-slate-700 hover:bg-slate-50"
                                        >
                                            Tandai sudah dibaca
                                        </button>
                                    @else
                                        <button
                                            type="button"
                                            wire:click="markAsUnread('{{ $notification->id }}')"
                                            class="cursor-pointer inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 font-medium text-slate-700 hover:bg-slate-50"
                                        >
                                            Tandai belum dibaca
                                        </button>
                                    @endif

                                    <button
                                        type="button"
                                        onclick="if(confirm('Hapus notifikasi ini?')) { Livewire.dispatch('callMethod', { method: 'delete', params: ['{{ $notification->id }}'] }); }"
                                        class="cursor-pointer inline-flex items-center rounded-full border border-rose-200 bg-white px-3 py-1 font-medium text-rose-600 hover:bg-rose-50"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </section>

            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
        @else
            {{-- EMPTY STATE --}}
            <section class="mt-8 flex flex-col items-center justify-center text-center">
                <div class="mb-4 h-16 w-16 rounded-full bg-slate-100 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 2a6 6 0 00-6 6v2.586l-.707.707A1 1 0 004 13h12a1 1 0 00.707-1.707L16 10.586V8a6 6 0 00-6-6z" />
                        <path d="M10 18a3 3 0 01-2.83-2h5.66A3 3 0 0110 18z" />
                    </svg>
                </div>
                <h2 class="text-sm font-semibold text-slate-900 mb-1">
                    Belum ada notifikasi
                </h2>
                <p class="text-xs text-slate-500 max-w-sm">
                    Notifikasi dari aktivitas kreator, order, dan support akan muncul di sini.
                    Untuk sementara, tidak ada notifikasi yang perlu kamu cek.
                </p>
            </section>
        @endif
    </main>
</div>
