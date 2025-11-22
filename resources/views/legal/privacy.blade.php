{{-- resources/views/legal/privacy.blade.php --}}
@extends('layouts.noorly')

@section('title', 'Kebijakan Privasi Noorly')

@section('content')
<div class="min-h-screen bg-slate-50 py-10">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-semibold text-slate-900 mb-2">
                Kebijakan Privasi Noorly
            </h1>
            <p class="text-sm text-slate-500">
                Terakhir diperbarui: {{ now()->format('d F Y') }}
            </p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sm:p-8 prose prose-sm max-w-none">
            <p>
                Kebijakan Privasi ini menjelaskan bagaimana <strong>Noorly</strong> (“Kami”) mengumpulkan, menggunakan,
                menyimpan, dan melindungi data pribadi Pengguna (“Anda”) ketika menggunakan situs dan Layanan kami.
            </p>

            <p>Dengan menggunakan Noorly, Anda menyatakan telah membaca dan menyetujui Kebijakan Privasi ini.</p>

            <h2>1. Data yang Kami Kumpulkan</h2>

            <h3>a. Data yang Anda berikan secara langsung</h3>
            <ul>
                <li>Nama lengkap</li>
                <li>Alamat email</li>
                <li>Nomor telepon / WhatsApp</li>
                <li>Kata sandi (disimpan dalam bentuk terenkripsi)</li>
                <li>Data profil lain yang Anda isi (mis. nama kreator/brand, jenis konten utama, dsb.)</li>
            </ul>

            <h3>b. Data transaksi dan pembayaran</h3>
            <ul>
                <li>Riwayat pembelian konten/kelas</li>
                <li>Metode pembayaran yang digunakan (mis. jenis e-wallet, bank, dsb.)</li>
                <li>Status pembayaran (berhasil/gagal/pending)</li>
            </ul>
            <p>
                Untuk keamanan, data sensitif seperti nomor kartu kredit, CVV, dan detail teknis pembayaran
                diproses oleh Payment Gateway pihak ketiga (saat ini <strong>Midtrans</strong>) dan
                <strong>tidak disimpan secara penuh</strong> di server Noorly.
            </p>

            <h3>c. Data penggunaan (usage data)</h3>
            <ul>
                <li>Log aktivitas seperti waktu akses, halaman yang dikunjungi, fitur yang digunakan.</li>
                <li>Informasi perangkat dan browser (jenis perangkat, sistem operasi, tipe browser).</li>
                <li>Alamat IP, negara/kota berdasarkan IP, dan informasi jaringan lainnya.</li>
            </ul>

            <h3>d. Data dari pihak ketiga (mis. Google)</h3>
            <p>
                Jika Anda login/daftar dengan <strong>Google</strong>, Kami dapat menerima data dasar dari akun Google Anda,
                seperti nama, alamat email, dan foto profil, sesuai dengan izin yang Anda berikan saat proses login.
            </p>

            <h2>2. Tujuan Penggunaan Data</h2>
            <p>Kami menggunakan data yang dikumpulkan untuk:</p>
            <ul>
                <li>Memproses pendaftaran akun dan proses login (termasuk login dengan Google atau OTP).</li>
                <li>Memberikan akses ke konten/kelas yang Anda beli.</li>
                <li>Memproses dan memverifikasi pembayaran melalui Payment Gateway (Midtrans) dan mitra pembayaran.</li>
                <li>Mengelola saldo pendapatan dan penarikan dana bagi Kreator.</li>
                <li>Mengirimkan notifikasi yang berkaitan dengan akun, transaksi, dan pembaruan layanan.</li>
                <li>Meningkatkan kualitas dan keamanan Layanan Noorly.</li>
                <li>Menangani pertanyaan, keluhan, atau dukungan pelanggan.</li>
                <li>Memenuhi kewajiban hukum dan regulasi yang berlaku.</li>
            </ul>

            <h2>3. Dasar Hukum Pemrosesan Data</h2>
            <p>Kami memproses data pribadi berdasarkan beberapa dasar hukum, antara lain:</p>
            <ul>
                <li>Persetujuan yang Anda berikan saat mendaftar/menggunakan Layanan.</li>
                <li>Kebutuhan kontraktual untuk menyediakan Layanan (mis. pemrosesan transaksi dan akses konten).</li>
                <li>Kepatuhan terhadap kewajiban hukum.</li>
                <li>Kepentingan sah Noorly untuk pengembangan dan keamanan layanan.</li>
            </ul>

            <h2>4. Berbagi Data dengan Pihak Ketiga</h2>
            <p>Kami dapat membagikan data Anda kepada:</p>
            <ul>
                <li><strong>Payment Gateway (Midtrans)</strong> dan mitra pembayaran terkait, untuk keperluan pemrosesan pembayaran.</li>
                <li>Penyedia layanan infrastruktur (hosting, email service, analitik) yang membantu operasional Noorly.</li>
                <li>Otoritas hukum/pemerintah jika diwajibkan oleh peraturan perundang-undangan.</li>
            </ul>
            <p>
                Kami <strong>tidak menjual</strong> data pribadi Anda kepada pihak ketiga untuk kepentingan komersial di luar penyediaan Layanan Noorly.
            </p>

            <h2>5. Cookies dan Teknologi Pelacakan</h2>
            <p>
                Noorly dapat menggunakan cookies dan teknologi serupa untuk:
            </p>
            <ul>
                <li>Mengingat preferensi dan sesi login Anda.</li>
                <li>Menganalisis penggunaan situs untuk peningkatan layanan.</li>
                <li>Menampilkan konten yang lebih relevan bagi Anda.</li>
            </ul>
            <p>
                Anda dapat mengatur browser untuk menolak cookies, namun beberapa bagian situs mungkin tidak berfungsi dengan optimal.
            </p>

            <h2>6. Penyimpanan dan Keamanan Data</h2>
            <ul>
                <li>Data Anda disimpan di server yang Kami kelola atau milik penyedia layanan pihak ketiga yang bekerja sama dengan Kami.</li>
                <li>Kami menggunakan langkah-langkah keamanan yang wajar (enkripsi, kontrol akses, dll.) untuk melindungi data dari akses tidak sah.</li>
                <li>Meskipun demikian, tidak ada sistem yang sepenuhnya bebas risiko. Kami menganjurkan Anda menjaga kerahasiaan kata sandi dan OTP.</li>
            </ul>

            <h2>7. Hak-Hak Anda atas Data Pribadi</h2>
            <p>Anda memiliki hak untuk:</p>
            <ul>
                <li>Mengakses dan melihat data pribadi tertentu yang Kami simpan.</li>
                <li>Memperbarui atau mengoreksi data pribadi yang tidak akurat.</li>
                <li>Meminta penghapusan akun sesuai prosedur yang ditetapkan Noorly (dengan mempertimbangkan kewajiban hukum dan arsip).*</li>
                <li>Menarik kembali persetujuan tertentu (mis. penerimaan email promosi), dengan konsekuensi terbatasnya fungsi tertentu Layanan.</li>
            </ul>
            <p>
                Permintaan dapat diajukan melalui kontak resmi Noorly dan akan Kami proses sesuai dengan kebijakan internal dan ketentuan hukum.
            </p>

            <h2>8. Penyimpanan Data (Retention)</h2>
            <ul>
                <li>Kami menyimpan data pribadi selama akun Anda aktif dan/atau selama diperlukan untuk tujuan yang dijelaskan dalam Kebijakan Privasi ini.</li>
                <li>Data tertentu mungkin tetap disimpan dalam bentuk arsip untuk memenuhi kewajiban hukum, akuntansi, atau pelaporan.</li>
            </ul>

            <h2>9. Anak di Bawah Umur</h2>
            <p>
                Noorly tidak secara sengaja menargetkan atau mengumpulkan data pribadi dari anak di bawah usia yang diatur oleh hukum setempat tanpa persetujuan orang tua/wali.
                Jika Anda adalah orang tua/wali dan mengetahui bahwa anak Anda menggunakan Noorly tanpa izin, silakan hubungi Kami untuk penanganan lebih lanjut.
            </p>

            <h2>10. Perubahan Kebijakan Privasi</h2>
            <ul>
                <li>Kami dapat memperbarui Kebijakan Privasi ini dari waktu ke waktu.</li>
                <li>Perubahan akan diumumkan melalui situs Noorly dan tanggal pembaruan akan disesuaikan.</li>
                <li>Dengan tetap menggunakan Layanan setelah perubahan tersebut berlaku, Anda dianggap menyetujui Kebijakan Privasi yang telah diperbarui.</li>
            </ul>

            <h2>11. Kontak</h2>
            <p>Jika Anda memiliki pertanyaan terkait Kebijakan Privasi ini, silakan hubungi:</p>
            <ul>
                <li>Email: <strong>support@noorly.digital</strong> (sesuaikan)</li>
                <li>Telepon/WhatsApp: <strong>[isi nomor]</strong></li>
                <li>Alamat kantor: <strong>[isi alamat legal Noorly]</strong></li>
            </ul>
        </div>
    </div>
</div>
@endsection
