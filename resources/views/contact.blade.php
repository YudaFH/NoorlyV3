@extends('layouts.noorly')

@section('title', 'Kontak | Noorly')

@section('content')
  <main class="relative min-h-[calc(100vh-5rem)] bg-slate-50 text-slate-900">
    {{-- Background dekor tipis --}}
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
      <div class="absolute -top-32 -right-20 h-64 w-64 rounded-full bg-gradient-to-br from-indigo-200 via-fuchsia-100 to-sky-200 blur-3xl opacity-60"></div>
      <div class="absolute -bottom-40 -left-10 h-72 w-72 rounded-full bg-gradient-to-tr from-emerald-100 via-sky-100 to-indigo-200 blur-3xl opacity-60"></div>
    </div>

    <section class="relative z-10 py-16 md:py-24">
      <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
       

        <div class="mt-10 grid gap-8 md:mt-12 md:grid-cols-[1.1fr,0.9fr] items-start">
          {{-- Form Kontak --}}
          <div class="rounded-2xl border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.06)]">
            <div class="border-b border-slate-200 px-5 py-4 md:px-6">
              <h2 class="text-sm font-semibold text-slate-900">
                Kirim pesan ke Noorly
              </h2>
              <p class="mt-1 text-[12px] text-slate-500">
                Isi data di bawah dengan jelas agar kami bisa merespons dengan tepat.
              </p>
            </div>

            <div class="px-5 py-5 md:px-6 md:py-6">
              {{-- Alert sukses --}}
              @if (session('status'))
                <div class="mb-4 rounded-xl border border-emerald-500/40 bg-emerald-50 px-4 py-3 text-xs md:text-sm text-emerald-700">
                  {{ session('status') }}
                </div>
              @endif

              {{-- Error validasi --}}
              @if ($errors->any())
                <div class="mb-4 rounded-xl border border-red-500/40 bg-red-50 px-4 py-3 text-xs md:text-sm text-red-700">
                  <div class="font-semibold mb-1">Ada beberapa yang perlu dicek lagi:</div>
                  <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif

              <form action="{{ route('contact.send') }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid gap-4 md:grid-cols-2">
                  <div class="space-y-1.5">
                    <label for="name" class="block text-xs font-medium text-slate-800">
                      Nama lengkap
                    </label>
                    <input
                      type="text"
                      id="name"
                      name="name"
                      value="{{ old('name') }}"
                      required
                      class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200"
                      placeholder="Nama kamu"
                    >
                  </div>

                  <div class="space-y-1.5">
                    <label for="email" class="block text-xs font-medium text-slate-800">
                      Email
                    </label>
                    <input
                      type="email"
                      id="email"
                      name="email"
                      value="{{ old('email') }}"
                      required
                      class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200"
                      placeholder="email@contoh.com"
                    >
                  </div>
                </div>

                <div class="space-y-1.5">
                  <label for="subject" class="block text-xs font-medium text-slate-800">
                    Subjek
                  </label>
                  <input
                    type="text"
                    id="subject"
                    name="subject"
                    value="{{ old('subject') }}"
                    required
                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200"
                    placeholder="Misal: Kerja sama, pertanyaan fitur, feedback, dsb."
                  >
                </div>

                <div class="space-y-1.5">
                  <label for="message" class="block text-xs font-medium text-slate-800">
                    Pesan
                  </label>
                  <textarea
                    id="message"
                    name="message"
                    rows="5"
                    required
                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200 resize-y"
                    placeholder="Ceritakan kebutuhan atau pertanyaanmu dengan detail..."
                  >{{ old('message') }}</textarea>
                </div>

                <div class="flex flex-col gap-3 pt-1 md:flex-row md:items-center md:justify-between">
                  <p class="text-[11px] leading-relaxed text-slate-500 max-w-sm">
                    Dengan mengirim pesan ini, kamu mengizinkan Noorly menghubungi kamu kembali melalui email
                    yang kamu cantumkan.
                  </p>

                  <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#fbc926] px-5 py-2.5 text-xs md:text-sm font-semibold text-white shadow-[0_18px_45px_rgba(79,70,229,0.45)] transition hover:brightness-110 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-50 focus-visible:ring-indigo-400"
                  >
                    <span>Kirim pesan</span>
                    <svg  class="h-4 w-4 bg-[#fbc926]" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                      <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 4.5l16.5 7.5-16.5 7.5L9 12 3.75 4.5z" />
                    </svg>
                  </button>
                </div>
              </form>
            </div>
          </div>

          {{-- Panel info kontak --}}
          <aside class="space-y-4 md:space-y-5">
            <div class="rounded-2xl border border-slate-200 bg-white px-5 py-5 md:px-6 md:py-6 shadow-sm">
              <h2 class="text-sm font-semibold text-slate-900">
                Cara lain menghubungi Noorly
              </h2>
              <p class="mt-2 text-xs md:text-sm text-slate-600">
                Untuk kebutuhan profesional seperti partnership, sponsorship, atau kolaborasi,
                kamu juga bisa hubungi kami melalui email berikut.
              </p>

              <div class="mt-4 space-y-3 text-xs md:text-sm">
                <div class="flex items-start gap-3">
                  <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class=" h-4 w-4 text-indigo-500" fill="none"
                         viewBox="0 0 24 24" stroke="#fbc926" stroke-width="1.7">
                      <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 8l9 6 9-6M4.5 6h15a1.5 1.5 0 011.5 1.5v9A1.5 1.5 0 0119.5 18h-15A1.5 1.5 0 013 16.5v-9A1.5 1.5 0 014.5 6z" />
                    </svg>
                  </div>
                  <div>
                    <div class="text-slate-800 font-medium">Email resmi</div>
                    <div class="text-slate-600">
                      {{ config('mail.contact_address') ?? 'contact@noorly.digital' }}
                    </div>
                  </div>
                </div>

                {{-- Tambah channel lain kalau mau (IG, X, dsb) --}}
                {{--
                <div class="flex items-start gap-3">
                  ...
                </div>
                --}}
              </div>
            </div>

            
          </aside>
        </div>
      </div>
    </section>
  </main>
@endsection
