@component('mail::message')
# Selamat datang di Noorly, {{ $user->name }} 👋

Terima kasih sudah mendaftar menggunakan akun Google.

Sekarang kamu bisa:
- Menyimpan dan mengakses konten favoritmu
- Mengikuti kelas dan program dari kreator
- Mendapatkan update dari kreator yang kamu ikuti

@component('mail::button', ['url' => route('users.dashboard')])
Masuk ke Dashboard
@endcomponent

Salam hangat,  
**Tim Noorly**
@endcomponent
