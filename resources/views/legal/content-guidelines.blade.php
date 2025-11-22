@extends('layouts.noorly')

@section('title', 'Pedoman Konten Noorly')
@section('content')
<div class="bg-slate-50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-16">
        <h1 class="text-2xl md:text-3xl font-semibold text-slate-900 mb-4">
            Pedoman Konten Kreator Noorly
        </h1>
        <p class="text-sm text-slate-600 mb-6">
            Pedoman ini dibuat untuk menjaga agar Noorly tetap aman, bermanfaat, dan nyaman bagi semua pengguna.
            Dengan mengunggah konten, kamu menyetujui aturan berikut.
        </p>

        <div class="space-y-4 text-sm text-slate-700">
            <section>
                <h2 class="text-sm font-semibold text-slate-900 mb-1">
                    1. Konten yang dilarang
                </h2>
                <ul class="list-disc list-inside text-sm text-slate-700 space-y-1">
                    <li>Konten pornografi, eksplisit seksual, atau fetisis.</li>
                    <li>Konten yang mempromosikan judi online, narkoba, atau aktivitas ilegal.</li>
                    <li>Ujaran kebencian, SARA, bullying, dan ancaman kekerasan.</li>
                    <li>Penipuan investasi atau klaim keuntungan tanpa dasar yang jelas.</li>
                    <li>Konten yang melanggar hak cipta / plagiarisme.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-sm font-semibold text-slate-900 mb-1">
                    2. Kualitas dan kejujuran konten
                </h2>
                <ul class="list-disc list-inside space-y-1">
                    <li>Judul, deskripsi, dan materi konten harus sesuai (tidak menyesatkan).</li>
                    <li>Jika konten berbayar, kreator wajib memberikan materi yang bisa diakses pembeli.</li>
                    <li>Dilarang menggunakan testimoni palsu atau statistik yang dimanipulasi.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-sm font-semibold text-slate-900 mb-1">
                    3. Proses review & sanksi
                </h2>
                <ul class="list-disc list-inside space-y-1">
                    <li>Konten kreator baru biasanya akan dicek terlebih dahulu oleh tim Noorly.</li>
                    <li>Konten yang melanggar pedoman dapat diturunkan tanpa pemberitahuan sebelumnya.</li>
                    <li>Akun yang berulang kali melanggar dapat dibatasi, dihentikan sementara, atau ditutup.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-sm font-semibold text-slate-900 mb-1">
                    4. Laporkan pelanggaran
                </h2>
                <p>
                    Jika kamu menemukan konten yang menurutmu melanggar pedoman, kamu bisa menggunakan tombol
                    <span class="font-medium">“Laporkan konten ini”</span> di halaman konten,
                    atau menghubungi kami melalui halaman <a href="{{ route('contact.show') }}" class="text-[#1d428a] hover:underline">Kontak</a>.
                </p>
            </section>
        </div>
    </div>
</div>
@endsection
