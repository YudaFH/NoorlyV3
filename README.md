# E-Book Interactive Builder (React)

This project now includes a modular React-based E-Book builder similar to modern site editors (Canva / Hostinger Builder).

## 📁 File Struktur Utama

React source (mounted via Vite):

- `resources/js/pages/BookEditor.jsx` – Halaman utama builder.
- `resources/js/components/navbartop.jsx` – Navbar atas (Preview, Save, Mode Web/Mobile).
- `resources/js/components/SidebarTools.jsx` – Sidebar tools (Text, Gambar, Video, Audio, Bentuk, Background).
- `resources/js/components/editorcanvas.jsx` – Kanvas drag & drop (Konva, resize, rotate, delete).
- `resources/js/hooks/useEditorStore.js` – State management (mode, preview, elemen, save API).
- `resources/js/app.js` – Entry; akan mount `BookEditor` jika menemukan `<div id="book-editor-root"></div>`.

## ⚙️ Dependencies

Installed via `npm install` (lihat `package.json`):

- React + ReactDOM
- Framer Motion (animasi halus)
- Konva + React-Konva (kanvas interaktif)
- Lucide React (ikon)
- React DnD (opsional; saat ini drag sumber sederhana memakai HTML5 DnD)
- Axios (HTTP ke backend Laravel)
- jQuery (disiapkan untuk integrasi Turn.js via CDN)

Turn.js tidak tersedia di NPM stabil sekarang; gunakan CDN:

```html
<!-- Tambahkan di Blade untuk preview flipbook (opsional) -->
<script src="https://cdn.jsdelivr.net/npm/turn.js@4/turn.min.js"></script>
```

## 🚀 Cara Pakai di Laravel (Blade)

1. Pastikan menjalankan dev server:
	```powershell
	npm run dev
	```
2. Tambahkan container di Blade (contoh di `resources/views/welcome.blade.php` atau halaman admin):
	```blade
	<div id="book-editor-root" class="h-screen"></div>
	@vite(['resources/css/app.css','resources/js/app.js'])
	```
3. Buka halaman tersebut di browser – React builder akan otomatis mount.

## 💾 Menyimpan Layout

`useEditorStore.saveLayout()` mengirim POST ke `/api/book-layouts` dengan payload:

```json
{
  "mode": "web|mobile",
  "bgColor": "#ffffff",
  "elements": [ { "id": "...", "type": "text|image|rect", ... } ],
  "updated_at": "ISO8601"
}
```

Buat endpoint Laravel (contoh `routes/api.php`):
```php
Route::post('/book-layouts', function (\Illuminate\Http\Request $r) {
	 // Validasi & simpan ke database
	 // return response()->json(['status' => 'ok']);
});
```

## 🧩 Drag & Drop

Sidebar tombol bisa di-drag ke kanvas (HTML5 DnD). Drop posisi dihitung dan elemen akan muncul di koordinat tersebut.

## 🔍 Preview Flipbook

Saat klik "Preview" navbar, builder menampilkan representasi sederhana. Integrasikan Turn.js:
```js
// setelah elemen tampil
$('#flipbook').turn({ width: 800, height: 600 });
```

## 🎨 Desain

Warna utama: `#FFC72C`, putih, abu abu lembut. Komponen memakai rounded-2xl & shadow lembut.

## 🛠 Next Steps / Ide Pengembangan

- Simpan versi / history layout.
- Support multi page (array of pages -> flipbook).
- Integrasi upload media (image/video/audio) ke storage Laravel.
- Penggunaan React DnD penuh (custom drag previews, internal reordering).
- Panel properti elemen (font, warna, layer order, opacity).
- Ekspor ke PDF.

## ✅ Build Status

`npm run build` telah berhasil; bundle di `public/build/`.

## ❗ Catatan

Jika gunakan JSX dalam entry seperti `app.js`, pastikan plugin React aktif. Saat ini `app.js` memakai `React.createElement` untuk kompatibilitas cepat.

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
