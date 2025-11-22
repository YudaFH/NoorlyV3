<footer class="bg-[#2b2a26] text-white py-10 px-4">
	<div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8 items-start">
		<!-- Logo & Social -->
		<div>
			<img src="{{ asset('images/icon/logo_footer.png') }}" alt="Noorly Logo" class="h-14 mb-4">
			<p class="mb-4">Hubungi kami untuk berbagi inspirasi dan cerita</p>
			<div class="flex space-x-4 mb-6">
				<a href="#" aria-label="Facebook"><i class="fab fa-facebook-f text-2xl"></i></a>
				<a href="#" aria-label="Instagram"><i class="fab fa-instagram text-2xl"></i></a>
				<a href="#" aria-label="TikTok"><i class="fab fa-tiktok text-2xl"></i></a>
				<a href="#" aria-label="X"><i class="fab fa-x-twitter text-2xl"></i></a>
			</div>
			<nav class="flex flex-wrap gap-4 text-sm">
				<a href="{{ route('home') }}" class="hover:underline">Home</a>
				<a href="{{ route('tentang') }}" class="hover:underline">Tentang</a>
				<a href="{{ route('contact.show') }}" class="hover:underline">Kontak</a>
				<a href="{{ route('login') }}" class="hover:underline">Login</a>
				<a href="{{ route('register') }}" class="hover:underline">Register</a>
			</nav>
		</div>
		<!-- Email & Telepon -->
		<div>
			<div class="mb-6">
				<h4 class="font-bold mb-2">EMAIL</h4>
				<p class="mb-1">+6281234567890</p>
				<p>noorlydigital.official@gmail.com</p>
			</div>
		</div>
		<!-- Form -->
		<div>
			<h4 class="font-bold mb-2">TELEPON</h4>
			<form class="space-y-4">
				<label for="nama" class="block">Nama Lengkap</label>
				<input type="text" id="nama" name="nama" placeholder="Masukkan nama Anda" class="w-full rounded-md px-4 py-3 text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-yellow-400">
				<button type="submit" class="bg-[#c09a3c] text-white font-semibold px-8 py-3 rounded-full shadow hover:bg-yellow-600 transition">Kirim</button>
			</form>
		</div>
	</div>
	<div class="max-w-7xl mx-auto mt-8 text-center text-sm text-white/80">
		&copy; 2025. All rights reserved.
	</div>
</footer>
