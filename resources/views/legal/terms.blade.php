{{-- resources/views/legal/terms.blade.php --}}
@extends('layouts.noorly')

@section('title', 'Syarat dan Ketentuan Noorly')

@section('content')
<div class="min-h-screen bg-slate-50 py-10">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-semibold text-slate-900 mb-2">
                Syarat dan Ketentuan Noorly
            </h1>
            <p class="text-sm text-slate-500">
                Terakhir diperbarui: {{ now()->format('d F Y') }}
            </p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sm:p-8 prose prose-sm max-w-none">
            <p>
                Selamat datang di <strong>Noorly</strong> (“<strong>Noorly</strong>”, “<strong>Kami</strong>”).
                Dengan mengakses dan/atau menggunakan situs web <strong>noorly.digital</strong>
                maupun layanan kami (“<strong>Layanan</strong>”), Anda (“<strong>Pengguna</strong>”) menyatakan
                telah membaca, memahami, dan menyetujui Syarat dan Ketentuan ini.
            </p>

            <p>Jika Anda tidak menyetujui sebagian atau seluruh ketentuan di bawah ini, harap untuk tidak menggunakan Layanan Noorly.</p>

            <h2>1. Definisi</h2>
            <ul>
                <li><strong>Noorly</strong>: Platform digital yang menyediakan konten, kelas, dan/atau materi edukasi berbasis daring yang dibuat oleh Noorly maupun para kreator terdaftar.</li>
                <li><strong>Pengguna</strong>: Setiap orang yang mengakses dan/atau menggunakan Layanan Noorly, baik yang sudah memiliki akun maupun belum.</li>
                <li><strong>Kreator</strong>: Pengguna yang terdaftar dan disetujui Noorly untuk membuat, mengunggah, dan/atau menjual konten di Noorly.</li>
                <li><strong>Akun</strong>: Data dan kredensial Pengguna yang terdaftar di sistem Noorly (mis. nama, email, nomor telepon, kata sandi, dsb.).</li>
                <li><strong>Konten</strong>: Seluruh materi digital yang tersedia di Noorly, termasuk namun tidak terbatas pada video, audio, teks, gambar, modul, kelas, dan materi edukasi lain.</li>
                <li><strong>Transaksi</strong>: Proses pembelian konten/kelas/layanan lain melalui sistem pembayaran Noorly.</li>
                <li><strong>Payment Gateway</strong>: Pihak ketiga penyedia layanan pemrosesan pembayaran yang bekerja sama dengan Noorly (saat ini Midtrans).</li>
                <li><strong>Mitra Pembayaran</strong>: Bank, lembaga keuangan, dan/atau penyedia uang elektronik yang terhubung dengan Payment Gateway.</li>
            </ul>

            <h2>2. Lingkup Layanan</h2>
            <ol>
                <li>Noorly menyediakan platform untuk:
                    <ul>
                        <li>Mengakses dan mengonsumsi konten/kelas digital.</li>
                        <li>Memungkinkan Kreator yang disetujui untuk membagikan dan/atau menjual konten.</li>
                    </ul>
                </li>
                <li>Noorly dapat menambah, mengubah, atau menghentikan fitur tertentu sewaktu-waktu dengan tetap mengacu pada ketentuan hukum yang berlaku.</li>
            </ol>

            <h2>3. Pendaftaran Akun</h2>
            <ol>
                <li>Pengguna wajib memberikan data yang benar, lengkap, dan terkini saat membuat akun.</li>
                <li>Pengguna bertanggung jawab penuh atas kerahasiaan kredensial login dan seluruh aktivitas yang terjadi pada akunnya.</li>
                <li>Noorly berhak menangguhkan atau menutup akun jika terdapat pelanggaran, indikasi penyalahgunaan, atau tindakan yang merugikan.</li>
            </ol>

            <h2>4. Pembelian dan Pembayaran</h2>
            <ol>
                <li>Pengguna dapat melakukan pembelian konten/kelas melalui metode pembayaran yang tersedia, antara lain kartu kredit/debit, transfer bank/virtual account, e-wallet, dan metode lain yang disediakan.</li>
                <li>Noorly bekerja sama dengan <strong>Midtrans</strong> sebagai Payment Gateway. Data transaksi akan diproses melalui sistem Midtrans dan/atau mitra pembayaran yang bekerjasama dengan Midtrans.</li>
                <li>Noorly tidak menyimpan data lengkap kartu pembayaran (seperti nomor kartu dan CVV) di server Noorly.</li>
                <li>Transaksi dianggap berhasil jika pembayaran telah dikonfirmasi oleh Payment Gateway dan sistem Noorly mencatat status “berhasil/paid”.</li>
                <li>Jika terjadi kegagalan transaksi, Pengguna dapat menghubungi layanan pelanggan Noorly dan/atau pihak bank/penyedia e-wallet terkait.</li>
            </ol>

            <h2>5. Akses Konten dan Pengiriman Layanan</h2>
            <ol>
                <li>Produk utama Noorly berupa konten digital. Setelah pembayaran berhasil, Pengguna akan mendapatkan akses ke konten/kelas sesuai ketentuan (permanen atau terbatas waktu).</li>
                <li>Noorly tidak mengirimkan produk fisik kecuali dinyatakan lain secara tegas pada deskripsi produk.</li>
            </ol>

            <h2>6. Pembatalan dan Pengembalian Dana (Refund)</h2>
            <ol>
                <li>Karena sifat produk berupa konten digital yang dapat langsung diakses, secara umum pembelian yang sudah berhasil <strong>tidak dapat dibatalkan maupun direfund</strong>.</li>
                <li>Refund hanya dapat dipertimbangkan dalam kondisi tertentu, misalnya:
                    <ul>
                        <li>Terjadi penagihan ganda (double charge).</li>
                        <li>Pengguna tidak mendapatkan akses konten setelah pembayaran dan terbukti masalah berasal dari sisi Noorly.</li>
                    </ul>
                </li>
                <li>Permohonan refund harus diajukan melalui kanal resmi Noorly dengan melampirkan bukti pembayaran dan data pendukung lain.</li>
                <li>Proses refund (jika disetujui) mengikuti prosedur internal Noorly serta ketentuan Payment Gateway dan mitra pembayaran.</li>
            </ol>

            <h2>7. Saldo Pendapatan Kreator dan Penarikan Dana</h2>
            <ol>
                <li>Kreator berhak atas bagian pendapatan sesuai skema bagi hasil yang disepakati antara Noorly dan Kreator.</li>
                <li>Pendapatan akan tercatat sebagai saldo pendapatan setelah transaksi berhasil dan tidak terdapat indikasi kecurangan.</li>
                <li>Kreator dapat mengajukan penarikan dana ke rekening bank dan/atau metode lain (mis. e-wallet) yang disediakan.</li>
                <li>Noorly dapat menetapkan jumlah minimum penarikan, biaya administrasi, serta jadwal proses penarikan.</li>
                <li>Noorly berhak menunda atau menolak penarikan jika terjadi sengketa, indikasi penipuan, atau pelanggaran hukum/hak cipta.</li>
            </ol>

            <h2>8. Hak Kekayaan Intelektual</h2>
            <ol>
                <li>Seluruh logo, merek, desain, dan sistem Noorly merupakan hak milik Noorly dan dilindungi oleh hukum.</li>
                <li>Konten di Noorly dilindungi hak cipta dan perjanjian terkait. Kepemilikan dapat berada pada Noorly, Kreator, atau pihak ketiga lain.</li>
                <li>Pengguna dilarang menggandakan, menyebarkan, menjual kembali, atau memanfaatkan konten di luar platform Noorly tanpa izin tertulis.</li>
            </ol>

            <h2>9. Perilaku Pengguna dan Larangan</h2>
            <p>Pengguna setuju untuk tidak menggunakan Noorly untuk:</p>
            <ul>
                <li>Melanggar hukum, peraturan, atau norma kesusilaan.</li>
                <li>Menyebarkan konten SARA, pornografi, kekerasan, perjudian, atau konten ilegal lainnya.</li>
                <li>Melakukan penipuan, spam, hacking, atau upaya mengakses sistem tanpa izin.</li>
                <li>Menggunakan identitas palsu atau mengambil alih akun pihak lain.</li>
            </ul>
            <p>Noorly berhak mengambil tindakan, termasuk penghentian akses dan penghapusan akun/konten, jika terjadi pelanggaran.</p>

            <h2>10. Data Pribadi dan Keamanan</h2>
            <ol>
                <li>Noorly mengumpulkan dan memproses data pribadi untuk keperluan pendaftaran akun, pemrosesan transaksi, pengembangan layanan, dan dukungan pelanggan.</li>
                <li>Noorly berupaya menjaga keamanan data dengan metode yang wajar dan bekerja sama dengan Payment Gateway yang memiliki standar keamanan.</li>
                <li>Noorly tidak menjual data pribadi Pengguna kepada pihak ketiga di luar kepentingan penyediaan Layanan, kecuali diwajibkan oleh hukum.</li>
            </ol>

            <h2>11. Batasan Tanggung Jawab</h2>
            <ol>
                <li>Noorly tidak menjamin Layanan selalu bebas gangguan, kesalahan, atau downtime.</li>
                <li>Noorly tidak bertanggung jawab atas kerugian tidak langsung, kehilangan keuntungan, atau kerugian konsekuensial akibat penggunaan Layanan.</li>
                <li>Tanggung jawab maksimum (jika ada) dibatasi sebesar nilai transaksi terakhir Pengguna, sejauh diizinkan oleh hukum.</li>
            </ol>

            <h2>12. Force Majeure</h2>
            <p>Noorly dibebaskan dari tanggung jawab atas keterlambatan atau kegagalan layanan yang disebabkan oleh peristiwa di luar kendali wajar (bencana alam, gangguan listrik, kebijakan pemerintah, dan sejenisnya).</p>

            <h2>13. Perubahan Syarat dan Ketentuan</h2>
            <ol>
                <li>Noorly dapat mengubah Syarat dan Ketentuan ini sewaktu-waktu.</li>
                <li>Perubahan akan diinformasikan melalui situs atau media lain yang wajar. Dengan tetap menggunakan Layanan, Pengguna dianggap menyetujui perubahan tersebut.</li>
            </ol>

            <h2>14. Hukum yang Berlaku dan Penyelesaian Sengketa</h2>
            <ol>
                <li>Syarat dan Ketentuan ini diatur oleh hukum Republik Indonesia.</li>
                <li>Sengketa akan diupayakan penyelesaiannya secara musyawarah. Jika tidak tercapai, sengketa akan diselesaikan melalui mekanisme hukum yang berlaku.</li>
            </ol>

            <h2>15. Kontak</h2>
            <p>Untuk pertanyaan, keluhan, atau permintaan terkait layanan Noorly, hubungi:</p>
            <ul>
                <li>Email: <strong>support@noorly.digital</strong> (sesuaikan)</li>
                <li>Telepon/WhatsApp: <strong>[isi nomor]</strong></li>
                <li>Alamat kantor: <strong>[isi alamat legal Noorly]</strong></li>
            </ul>
        </div>
    </div>
</div>
@endsection
