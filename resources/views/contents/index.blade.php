@extends('layouts.noorly')

@section('title', 'Konten di Noorly')

@section('content')
<div class="bg-slate-50 min-h-screen pt-20 pb-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- HEADER + SEARCH + FILTER --}}
        <header class="mb-6">
            <h1 class="text-2xl md:text-3xl font-semibold text-slate-900">
                Jelajahi konten di Noorly
            </h1>
            <p class="mt-1 text-sm text-slate-500 max-w-2xl">
                Temukan e-book, kelas video, rekaman webinar, dan konten digital lain dari para kreator.
            </p>

            {{-- Form search + filter --}}
            <form
                method="GET"
                action="{{ route('contents.index') }}"
                class="mt-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between"
            >
                {{-- Search --}}
                <div class="w-full md:max-w-md">
                    <label class="sr-only" for="q">Cari konten</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 103.473 9.8l3.613 3.614a.75.75 0 101.06-1.06l-3.614-3.613A5.5 5.5 0 009 3.5zm-4 5.5a4 4 0 118 0 4 4 0 01-8 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <input
                            id="q"
                            name="q"
                            type="text"
                            value="{{ $search }}"
                            class="block w-full rounded-full border border-slate-200 bg-white pl-8 pr-3 py-1.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#1d428a] focus:ring-[#1d428a]"
                            placeholder="Cari judul konten..."
                        >
                    </div>
                </div>

                {{-- Filters --}}
                <div class="flex flex-wrap gap-2 justify-start md:justify-end">
                    {{-- Filter jenis konten --}}
                    <select
                        name="type"
                        class="cursor-pointer rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                    >
                        @foreach($typeOptions as $value => $label)
                            <option value="{{ $value }}" @selected($type === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Filter harga --}}
                    <select
                        name="price"
                        class="cursor-pointer rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                    >
                        @foreach($priceOptions as $value => $label)
                            <option value="{{ $value }}" @selected($price === $value || ($price === null && $value === 'all'))>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>

                    <button
                        type="submit"
                        class="cursor-pointer inline-flex items-center gap-1 rounded-full bg-[#1d428a] px-4 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-[#163268]"
                    >
                        Terapkan
                    </button>
                </div>
            </form>

            {{-- Chips kecil penjelasan filter aktif (opsional) --}}
            @if($search || $type || ($price && $price !== 'all'))
                <div class="mt-2 flex flex-wrap gap-2 text-[11px] text-slate-500">
                    <span>Filter aktif:</span>
                    @if($search)
                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5">
                            Cari: <span class="ml-1 font-medium text-slate-700">"{{ $search }}"</span>
                        </span>
                    @endif
                    @if($type)
                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5">
                            Jenis: <span class="ml-1 font-medium text-slate-700">{{ $typeOptions[$type] ?? $type }}</span>
                        </span>
                    @endif
                    @if($price && $price !== 'all')
                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5">
                            Harga:
                            <span class="ml-1 font-medium text-slate-700">
                                {{ $price === 'free' ? 'Gratis' : 'Berbayar' }}
                            </span>
                        </span>
                    @endif
                    <a
                        href="{{ route('konten') }}"
                        class="inline-flex items-center rounded-full bg-transparent px-2 py-0.5 text-[11px] text-[#1d428a] hover:underline"
                    >
                        Reset
                    </a>
                </div>
            @endif
        </header>

        {{-- LIST KONTEN --}}
        @if($contents->count())
            <section>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($contents as $content)
                        <a
                            href="{{ route('contents.show', $content->slug) }}"
                            class="group cursor-pointer rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden hover:shadow-md transition flex flex-col"
                        >
                            {{-- Cover --}}
                            <div class="h-40 bg-slate-100 overflow-hidden">
                                @if($content->cover_path)
                                    <img
                                        src="{{ asset('storage/'.$content->cover_path) }}"
                                        alt="{{ $content->title }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                    >
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-[11px] text-slate-400">
                                        Tidak ada cover
                                    </div>
                                @endif
                            </div>

                            {{-- Info --}}
                            <div class="p-3 flex-1 flex flex-col">
                                <p class="text-[11px] text-slate-400 mb-1 flex items-center gap-2">
                                    <span class="uppercase tracking-wide">
                                        {{ strtoupper($content->type ?? 'Produk digital') }}
                                    </span>
                                    <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                                    <span>{{ $content->buyers_count ?? 0 }} pembeli</span>
                                </p>

                                <h2 class="text-sm font-semibold text-slate-900 line-clamp-2">
                                    {{ $content->title }}
                                </h2>

                                @if(!empty($content->description))
                                    <p class="mt-1 text-[11px] text-slate-500 line-clamp-2">
                                        {{ $content->description }}
                                    </p>
                                @endif

                                <div class="mt-2 flex items-center justify-between">
                                    <p class="text-[12px] text-slate-700">
                                        @if(($content->price ?? 0) > 0)
                                            <span class="font-semibold text-slate-900">
                                                Rp {{ number_format($content->price, 0, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="font-semibold text-emerald-600">Gratis</span>
                                        @endif
                                    </p>
                                    <p class="text-[10px] text-slate-400">
                                        {{ optional($content->updated_at)->diffForHumans() ?? '' }}
                                    </p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $contents->links() }}
                </div>
            </section>
        @else
            <section class="mt-8 text-center">
                <p class="text-sm text-slate-500">
                    Belum ada konten yang cocok dengan pencarian / filter kamu.
                </p>
            </section>
        @endif
    </div>
</div>
@endsection
