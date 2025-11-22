{{-- resources/views/creators/public-show.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $creator->name }} — Halaman kreator Noorly</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Pakai Tailwind dari CDN kalau belum ada setup asset, sementara saja --}}
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-900">

<header class="border-b border-slate-200 bg-white">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between gap-3">
        <a href="{{ url('/') }}" class="text-sm font-semibold text-slate-800">
            Noorly
        </a>

        <div class="flex items-center gap-3">
            @if($isOwnerPreview)
                <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-[11px] font-medium text-amber-700">
                    Mode preview halaman kreator
                </span>
            @endif

            @auth
                @if(auth()->id() === $creator->id)
                    <a href="{{ route('creator.dashboard') }}"
                       class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">
                        Masuk ke panel kreator
                    </a>
                @endif
            @endauth
        </div>
    </div>
</header>

<main class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-8">
    {{-- Profil kreator --}}
    <section class="mb-8 flex flex-col sm:flex-row sm:items-center gap-4">
        <div class="flex items-center gap-3">
            {{-- Avatar --}}
            @if(!empty($creator->avatar_path))
                <img
                    src="{{ asset('storage/'.$creator->avatar_path) }}"
                    alt="{{ $creator->name }}"
                    class="h-16 w-16 rounded-full object-cover border border-slate-200"
                >
            @else
                <div class="h-16 w-16 rounded-full bg-[#fbc926] text-white flex items-center justify-center text-xl font-semibold">
                    {{ strtoupper(mb_substr($creator->name, 0, 1)) }}
                </div>
            @endif

            <div>
                <h1 class="text-xl sm:text-2xl font-semibold text-slate-900">
                    {{ $creator->name }}
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    Kreator di Noorly
                    @if($isOwnerPreview)
                        • <span class="text-amber-600 font-medium">Preview (hanya kamu yang lihat semua status)</span>
                    @endif
                </p>
            </div>
        </div>
    </section>

    {{-- Daftar konten --}}
    <section>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-slate-900">
                Konten dari {{ $creator->name }}
            </h2>

            @if($isOwnerPreview)
                <p class="text-[11px] text-slate-500">
                    Konten yang ditampilkan: <span class="font-medium">draft, menunggu review, dan terbit</span>.
                    Pengunjung umum hanya melihat konten yang sudah terbit.
                </p>
            @else
                <p class="text-[11px] text-slate-500">
                    Hanya konten yang sudah terbit yang ditampilkan di halaman publik.
                </p>
            @endif
        </div>

        @if($contents->count() === 0)
            <div class="rounded-2xl border border-dashed border-slate-200 bg-white/70 p-6 text-center">
                <p class="text-sm font-medium text-slate-800 mb-1">
                    Belum ada konten yang bisa ditampilkan
                </p>
                <p class="text-xs text-slate-500">
                    @if($isOwnerPreview)
                        Kamu belum menerbitkan konten apa pun. Buat konten baru dari panel kreator.
                    @else
                        Kreator ini belum memiliki konten yang terbit di Noorly.
                    @endif
                </p>
            </div>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($contents as $content)
                    <article class="rounded-2xl border border-slate-100 bg-white shadow-sm flex flex-col overflow-hidden">
                        {{-- Cover --}}
                        @if($content->cover_path)
                            <div class="h-32 w-full bg-slate-100 overflow-hidden">
                                <img
                                    src="{{ asset('storage/'.$content->cover_path) }}"
                                    alt="{{ $content->title }}"
                                    class="h-full w-full object-cover"
                                >
                            </div>
                        @else
                            <div class="h-32 w-full bg-slate-100 flex items-center justify-center text-[11px] text-slate-400">
                                Tidak ada cover
                            </div>
                        @endif

                        <div class="flex-1 p-4 flex flex-col gap-2">
                            <h3 class="text-sm font-semibold text-slate-900 line-clamp-2">
                                {{ $content->title }}
                            </h3>

                            <div class="flex flex-wrap items-center gap-1 text-[10px]">
                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-slate-600">
                                    {{ $content->type ?? 'Konten digital' }}
                                </span>

                                @if($isOwnerPreview)
                                    @if($content->status === 'draft')
                                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-slate-600">Draft</span>
                                    @elseif($content->status === 'pending_review')
                                        <span class="rounded-full bg-amber-50 px-2 py-0.5 text-amber-700">Menunggu review</span>
                                    @elseif($content->status === 'published')
                                        <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-emerald-700">Terbit</span>
                                    @endif
                                @endif
                            </div>

                            <p class="text-[11px] text-slate-500 mt-1">
                                @if(($content->price ?? 0) > 0)
                                    <span class="font-semibold text-slate-900">
                                        Rp {{ number_format($content->price, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="font-semibold text-emerald-600">Gratis</span>
                                @endif
                            </p>

                            <p class="mt-auto text-[10px] text-slate-400">
                                Terakhir diubah {{ optional($content->updated_at)->diffForHumans() ?? '-' }}
                            </p>

                            <div class="mt-2 flex items-center justify-between gap-2">
                                <a
                                    href="{{ route('contents.show', $content->slug) }}"
                                    class="inline-flex items-center justify-center rounded-full bg-[#1d428a] px-3 py-1.5 text-[11px] font-semibold text-white hover:bg-[#163268]"
                                >
                                    Lihat detail
                                </a>

                                @if($isOwnerPreview)
                                    <a
                                        href="{{ route('creator.contents.edit', $content->id) }}"
                                        class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-3 py-1.5 text-[11px] font-medium text-slate-700 hover:bg-slate-50"
                                    >
                                        Edit
                                    </a>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $contents->links() }}
            </div>
        @endif
    </section>
</main>

</body>
</html>
