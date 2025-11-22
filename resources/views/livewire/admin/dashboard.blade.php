<div class="flex h-screen bg-slate-50">
    {{-- SIDEBAR --}}
    @include('livewire.admin.sidebar', ['sidebarOpen' => $sidebarOpen])

    {{-- MAIN --}}
    <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8 overflow-y-auto">
        {{-- HEADER --}}
        <header class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-slate-900">
                    Dashboard admin
                </h1>
                <p class="mt-1 text-sm text-slate-500 max-w-2xl">
                    Pantau performa platform, kreator, dan transaksi Noorly dalam satu tempat.
                </p>
                <p class="mt-2 text-[11px] text-slate-400">
                    Data per {{ now('Asia/Jakarta')->format('d M Y H:i') }} WIB
                </p>
            </div>

            <div class="flex items-center gap-2">
                <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-[11px] font-medium text-emerald-700">
                    Mode admin aktif
                </span>
            </div>
        </header>

        {{-- STAT CARDS --}}
        <section class="grid gap-4 md:grid-cols-2 lg:grid-cols-4 mb-6">
            <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                <p class="text-[11px] text-slate-500 mb-1">Total pengguna</p>
                <p class="text-2xl font-semibold text-slate-900">
                    {{ number_format($totalUsers, 0, ',', '.') }}
                </p>
                <p class="mt-1 text-[11px] text-slate-400">
                    {{ number_format($totalCreators, 0, ',', '.') }} di antaranya kreator.
                </p>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                <p class="text-[11px] text-slate-500 mb-1">Konten</p>
                <p class="text-2xl font-semibold text-slate-900">
                    {{ number_format($totalContents, 0, ',', '.') }}
                </p>
                <p class="mt-1 text-[11px] text-amber-600">
                    {{ number_format($pendingContents, 0, ',', '.') }} konten menunggu review.
                </p>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                <p class="text-[11px] text-slate-500 mb-1">Total omzet (paid)</p>
                <p class="text-2xl font-semibold text-emerald-600">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </p>
                <p class="mt-1 text-[11px] text-slate-400">
                    Akumulasi semua order berstatus paid.
                </p>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                <p class="text-[11px] text-slate-500 mb-1">Penarikan pending</p>
                <p class="text-2xl font-semibold text-slate-900">
                    {{ $pendingWithdrawCount }} permintaan
                </p>
                <p class="mt-1 text-[11px] text-slate-500">
                    Total:
                    <span class="font-semibold text-emerald-600">
                        Rp {{ number_format($pendingWithdrawAmount, 0, ',', '.') }}
                    </span>
                </p>
            </div>
        </section>

        {{-- 3 KOLOM: pending konten, withdraw, kreator baru --}}
        <section class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            {{-- KONTEN MENUNGGU REVIEW --}}
            <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900">Konten menunggu review</h2>
                        <p class="text-[11px] text-slate-500">
                            Konten baru dari kreator yang perlu dicek.
                        </p>
                    </div>
                    <a
                        href="{{ Route::has('admin.contents.moderation') ? route('admin.contents.moderation') : '#' }}"
                        class="cursor-pointer text-[11px] text-[#1d428a] hover:underline"
                    >
                        Lihat semua
                    </a>
                </div>

                @if($recentPendingContents->count())
                    <ul class="space-y-2">
                        @foreach($recentPendingContents as $content)
                            <li class="rounded-xl border border-slate-100 px-3 py-2 text-[11px] hover:bg-slate-50">
                                <p class="font-semibold text-slate-900 truncate">
                                    {{ $content->title }}
                                </p>
                                <p class="text-slate-500">
                                    {{ $content->type ?? 'konten' }} • oleh {{ $content->user->name ?? '—' }}
                                </p>
                                <p class="text-[10px] text-slate-400">
                                    Diajukan {{ optional($content->created_at)->diffForHumans() }}
                                </p>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-[12px] text-slate-500 mt-2">
                        Belum ada konten yang menunggu review.
                    </p>
                @endif
            </div>

            {{-- PENARIKAN SALDO PENDING --}}
            <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900">Penarikan saldo pending</h2>
                        <p class="text-[11px] text-slate-500">
                            Permintaan withdraw yang menunggu persetujuan admin.
                        </p>
                    </div>
                    <a
                        href="{{ Route::has('admin.withdraws.index') ? route('admin.withdraws.index') : '#' }}"
                        class="cursor-pointer text-[11px] text-[#1d428a] hover:underline"
                    >
                        Kelola
                    </a>
                </div>

                @if($recentWithdraws->count())
                    <ul class="space-y-2">
                        @foreach($recentWithdraws as $withdraw)
                            <li class="rounded-xl border border-slate-100 px-3 py-2 text-[11px] hover:bg-slate-50">
                                <p class="font-semibold text-slate-900">
                                    Rp {{ number_format($withdraw->amount, 0, ',', '.') }}
                                </p>
                                <p class="text-slate-500">
                                    {{ $withdraw->user->name ?? '—' }} • {{ $withdraw->method_label ?? '-' }}
                                </p>
                                <p class="text-[10px] text-slate-400">
                                    Diajukan {{ optional($withdraw->created_at)->diffForHumans() }}
                                </p>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-[12px] text-slate-500 mt-2">
                        Tidak ada permintaan penarikan yang pending saat ini.
                    </p>
                @endif
            </div>

            {{-- KREATOR BARU --}}
            <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900">Kreator baru bergabung</h2>
                        <p class="text-[11px] text-slate-500">
                            Pantau kreator yang baru daftar dan mulai upload konten.
                        </p>
                    </div>
                    <a
                        href="{{ Route::has('admin.creators.index') ? route('admin.creators.index') : '#' }}"
                        class="cursor-pointer text-[11px] text-[#1d428a] hover:underline"
                    >
                        Lihat daftar kreator
                    </a>
                </div>

                @if($recentCreators->count())
                    <ul class="space-y-2">
                        @foreach($recentCreators as $creator)
                            <li class="rounded-xl border border-slate-100 px-3 py-2 text-[11px] hover:bg-slate-50">
                                <p class="font-semibold text-slate-900">
                                    {{ $creator->name }}
                                </p>
                                <p class="text-slate-500 truncate">
                                    {{ $creator->email }}
                                </p>
                                <p class="text-[10px] text-slate-400">
                                    Bergabung {{ optional($creator->created_at)->diffForHumans() }}
                                </p>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-[12px] text-slate-500 mt-2">
                        Belum ada kreator baru dalam beberapa waktu terakhir.
                    </p>
                @endif
            </div>
        </section>
    </main>
</div>
