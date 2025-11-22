<div class="flex h-screen bg-slate-50">
    {{-- SIDEBAR --}}
    @include('livewire.creator.sidebar', ['sidebarOpen' => $sidebarOpen])

    {{-- MAIN --}}
    <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8 overflow-y-auto">
        {{-- HEADER --}}
        <header class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-slate-900">
                    Support kreator
                </h1>
                <p class="mt-1 text-sm text-slate-500 max-w-2xl">
                    Ajukan pertanyaan, laporkan masalah, atau sampaikan saran untuk tim Noorly. 
                    Kami akan berusaha merespon secepat mungkin.
                </p>
            </div>

            <div class="text-xs text-slate-500 text-right">
                <p class="font-semibold text-slate-700">Estimasi respon</p>
                <p>1 × 24 jam pada hari kerja</p>
            </div>
        </header>

        {{-- METRIK RINGKAS --}}
        <section class="grid gap-4 md:grid-cols-4 mb-6">
            <article class="bg-white rounded-2xl border border-slate-100 shadow-sm px-5 py-4">
                <p class="text-xs font-medium text-slate-500">Tiket aktif</p>
                <p class="mt-3 text-2xl font-semibold text-slate-900">{{ $openTickets }}</p>
                <p class="mt-1 text-[11px] text-slate-400">
                    Laporan / pertanyaan yang masih menunggu respon atau sedang diproses.
                </p>
            </article>

            <article class="bg-white rounded-2xl border border-slate-100 shadow-sm px-5 py-4">
                <p class="text-xs font-medium text-slate-500">Sedang diproses</p>
                <p class="mt-3 text-2xl font-semibold text-amber-600">{{ $inProgress }}</p>
                <p class="mt-1 text-[11px] text-slate-400">
                    Tiket yang sudah dibaca dan sedang ditangani tim Noorly.
                </p>
            </article>

            <article class="bg-white rounded-2xl border border-slate-100 shadow-sm px-5 py-4">
                <p class="text-xs font-medium text-slate-500">Selesai</p>
                <p class="mt-3 text-2xl font-semibold text-emerald-600">{{ $resolved }}</p>
                <p class="mt-1 text-[11px] text-slate-400">
                    Tiket yang sudah dijawab dan dinyatakan selesai / ditutup.
                </p>
            </article>

            <article class="bg-white rounded-2xl border border-slate-100 shadow-sm px-5 py-4">
                <p class="text-xs font-medium text-slate-500">Total tiket</p>
                <p class="mt-3 text-2xl font-semibold text-slate-900">{{ $totalTickets }}</p>
                <p class="mt-1 text-[11px] text-slate-400">
                    Total semua tiket yang pernah kamu kirim ke tim Noorly.
                </p>
            </article>
        </section>

        <div class="grid gap-6 lg:grid-cols-3">
            {{-- FORM TIKET BARU --}}
            <section class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-slate-900">
                        Buat tiket support baru
                    </h2>
                    <span class="text-[11px] text-slate-400">
                        Jelaskan masalahmu dengan jelas agar cepat ditangani
                    </span>
                </div>

                @if (session()->has('support_status'))
                    <div class="mb-4 rounded-xl border border-emerald-100 bg-emerald-50 px-3 py-2 text-[11px] text-emerald-700">
                        {{ session('support_status') }}
                    </div>
                @endif

                <form wire:submit.prevent="submitTicket" class="space-y-3">
                    {{-- Kategori --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">
                            Kategori
                        </label>
                        <select
                            wire:model="category"
                            class="cursor-pointer block w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                        >
                            <option value="">Pilih kategori...</option>
                            @foreach($this->categoryOptions as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('category')
                            <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Subjek --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">
                            Judul / subjek
                        </label>
                        <input
                            type="text"
                            wire:model.defer="subject"
                            class="block w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#1d428a] focus:ring-[#1d428a]"
                            placeholder="Contoh: Penarikan saldo saya belum diproses"
                        >
                        @error('subject')
                            <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Pesan --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">
                            Jelaskan masalahmu
                        </label>
                        <textarea
                            rows="4"
                            wire:model.defer="message"
                            class="block w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#1d428a] focus:ring-[#1d428a]"
                            placeholder="Ceritakan apa yang terjadi, kapan, di konten mana, dan jika ada kode transaksi / bukti pembayaran."
                        ></textarea>
                        @error('message')
                            <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Lampiran --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">
                            Lampiran (opsional)
                        </label>
                        <input
                            type="file"
                            wire:model="attachment"
                            class="block w-full text-xs text-slate-500 file:mr-3 file:cursor-pointer file:rounded-full file:border-0 file:bg-[#1d428a] file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:bg-[#163268]"
                        >
                        <p class="mt-1 text-[11px] text-slate-400">
                            Format yang didukung: JPG, PNG, PDF. Maksimal 2 MB.
                        </p>
                        @error('attachment')
                            <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button
                            type="submit"
                            class="cursor-pointer inline-flex items-center gap-2 rounded-full bg-[#1d428a] px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-[#163268]"
                        >
                            Kirim tiket
                        </button>
                    </div>
                </form>
            </section>

            {{-- FAQ / Pusat bantuan --}}
            <section class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                <h2 class="text-sm font-semibold text-slate-900 mb-3">
                    Pusat bantuan kreator
                </h2>

                <div class="space-y-3 text-[11px] text-slate-600">
                    <details class="group border border-slate-100 rounded-xl px-3 py-2">
                        <summary class="flex cursor-pointer list-none items-center justify-between text-xs font-semibold text-slate-800">
                            <span>Apa yang terjadi setelah saya mengirim tiket?</span>
                            <span class="text-slate-400 group-open:rotate-180 transition-transform">
                                ▾
                            </span>
                        </summary>
                        <p class="mt-1 text-[11px] text-slate-500">
                            Tim Noorly akan meninjau laporanmu dan merespon melalui tiket ini. 
                            Kamu akan mendapatkan update status (open, sedang diproses, selesai) di daftar tiket di bawah.
                        </p>
                    </details>

                    <details class="group border border-slate-100 rounded-xl px-3 py-2">
                        <summary class="flex cursor-pointer list-none items-center justify-between text-xs font-semibold text-slate-800">
                            <span>Berapa lama penarikan saldo diproses?</span>
                            <span class="text-slate-400 group-open:rotate-180 transition-transform">
                                ▾
                            </span>
                        </summary>
                        <p class="mt-1 text-[11px] text-slate-500">
                            Penarikan saldo biasanya diproses maksimal 1×24 jam kerja setelah pengajuan disetujui. 
                            Jika lebih lama, kirim tiket dengan kategori <span class="font-semibold">Penarikan saldo</span> dan sertakan detailnya.
                        </p>
                    </details>

                    <details class="group border border-slate-100 rounded-xl px-3 py-2">
                        <summary class="flex cursor-pointer list-none items-center justify-between text-xs font-semibold text-slate-800">
                            <span>Konten seperti apa yang dilarang di Noorly?</span>
                            <span class="text-slate-400 group-open:rotate-180 transition-transform">
                                ▾
                            </span>
                        </summary>
                        <p class="mt-1 text-[11px] text-slate-500">
                            Noorly melarang konten yang mengandung pornografi, kekerasan ekstrem, ujaran kebencian, perjudian, 
                            penipuan, dan hal lain yang melanggar hukum. Konten yang melanggar dapat diturunkan dan akun bisa dibatasi.
                        </p>
                    </details>

                    <details class="group border border-slate-100 rounded-xl px-3 py-2">
                        <summary class="flex cursor-pointer list-none items-center justify-between text-xs font-semibold text-slate-800">
                            <span>Saya punya ide fitur untuk Noorly</span>
                            <span class="text-slate-400 group-open:rotate-180 transition-transform">
                                ▾
                            </span>
                        </summary>
                        <p class="mt-1 text-[11px] text-slate-500">
                            Gunakan kategori <span class="font-semibold">Saran fitur / feedback</span> 
                            dan ceritakan kebutuhanmu sebagai kreator. Masukanmu sangat membantu prioritas pengembangan Noorly.
                        </p>
                    </details>
                </div>
            </section>
        </div>

        {{-- BAR FILTER TIKET --}}
        <section class="mt-8 mb-3 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-sm font-semibold text-slate-900">
                    Riwayat tiket support
                </h2>
                <p class="text-[11px] text-slate-500">
                    Lihat status dan update tiket yang pernah kamu kirim.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 text-[11px]">
                {{-- Status filter --}}
                <div class="flex items-center gap-1">
                    <span class="text-slate-500">Status:</span>
                    <select
                        wire:model="statusFilter"
                        class="cursor-pointer rounded-full border border-slate-200 bg-white px-3 py-1.5 text-[11px] text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                    >
                        <option value="all">Semua</option>
                        <option value="open">Open</option>
                        <option value="in_progress">Sedang diproses</option>
                        <option value="resolved">Selesai</option>
                        <option value="closed">Ditutup</option>
                    </select>
                </div>

                {{-- Search tiket --}}
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
                            placeholder="Cari subjek / isi tiket..."
                        >
                    </div>
                </div>
            </div>
        </section>

        {{-- TABEL TIKET --}}
        <section class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50">
                        <tr class="text-[11px] uppercase tracking-wide text-slate-500">
                            <th class="px-4 py-2 text-left">ID tiket</th>
                            <th class="px-4 py-2 text-left">Subjek</th>
                            <th class="px-4 py-2 text-left">Kategori</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Terakhir update</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($tickets as $ticket)
                            <tr class="hover:bg-slate-50/60">
                                <td class="px-4 py-3 align-top text-[11px] font-mono text-slate-500">
                                    #NRL-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}
                                </td>

                                <td class="px-4 py-3 align-top">
                                    <p class="text-[13px] font-medium text-slate-900 truncate max-w-xs">
                                        {{ $ticket->subject }}
                                    </p>
                                    <p class="text-[11px] text-slate-400 truncate max-w-xs">
                                        {{ Str::limit($ticket->message, 80) }}
                                    </p>
                                </td>

                                <td class="px-4 py-3 align-top text-[11px] text-slate-600">
                                    {{ $this->categoryOptions[$ticket->category] ?? ucfirst($ticket->category) }}
                                </td>

                                <td class="px-4 py-3 align-top">
                                    @php
                                        $status = $ticket->status;
                                        $statusLabel = match($status) {
                                            'open'        => 'Open',
                                            'in_progress' => 'Sedang diproses',
                                            'resolved'    => 'Selesai',
                                            'closed'      => 'Ditutup',
                                            default       => ucfirst($status),
                                        };

                                        [$bg, $text] = match($status) {
                                            'open'        => ['bg-amber-50',  'text-amber-700'],
                                            'in_progress' => ['bg-sky-50',    'text-sky-700'],
                                            'resolved'    => ['bg-emerald-50','text-emerald-700'],
                                            'closed'      => ['bg-slate-100', 'text-slate-700'],
                                            default       => ['bg-slate-100', 'text-slate-700'],
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-medium {{ $bg }} {{ $text }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 align-top text-[11px] text-slate-500">
                                    @if($ticket->updated_at)
                                        @php
                                            $updatedAtWib = $ticket->updated_at->timezone('Asia/Jakarta');
                                        @endphp

                                        <p>{{ $updatedAtWib->format('d M Y H:i') }} WIB</p>
                                        <p>{{ $updatedAtWib->diffForHumans() }}</p>
                                    @else
                                        <p>-</p>
                                        <p></p>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-4 py-6 text-center text-[13px] text-slate-500" colspan="5">
                                    Belum ada tiket support untuk ditampilkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($tickets->hasPages())
                <div class="border-t border-slate-100 px-4 py-3">
                    {{ $tickets->links() }}
                </div>
            @endif
        </section>

        <p class="mt-3 text-[11px] text-slate-400">
            Tiket support hanya bisa dilihat oleh kamu dan tim Noorly. 
            Jangan membagikan informasi sensitif seperti password atau kode OTP di dalam tiket.
        </p>
    </main>
</div>
