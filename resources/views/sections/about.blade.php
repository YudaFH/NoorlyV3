<section id="tentang" class="py-12 md:py-16 bg-[#fbc926] w-screen -mx-4 sm:-mx-6 lg:-mx-8">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">
    {{-- Mobile: flex row, Desktop: grid 2 kolom --}}
    <div class="flex items-center gap-4 md:grid md:grid-cols-2 md:items-center md:gap-8">
      <!-- Left: Text -->
      <div class="text-white flex-1">
        <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-white">
          Tentang Noorly
        </h2>
        <p class="mt-3 text-sm sm:text-base text-white/95 text-justify">
          Noorly adalah platform digital yang didedikasikan untuk memberdayakan jiwa muda melalui
          konten Islami yang inspiratif dan relevan. Kami percaya bahwa setiap generasi memiliki
          potensi untuk membawa perubahan positif, dan kami berkomitmen untuk menjadi sumber
          cahaya yang membimbing mereka dalam perjalanan spiritual mereka.
        </p>
        <div class="mt-5">
          <a href="{{ route('register') }}"
             class="inline-block bg-[#2b2a26] text-white font-semibold px-5 py-2.5 rounded-md shadow border-2 border-transparent hover:bg-transparent hover:border-[#1d428a] hover:text-white transition-colors duration-200 text-sm">
            Gabung
          </a>
        </div>
      </div>

      <!-- Right: Image (margin kanan supaya agak ke kiri) -->
      <div class="flex justify-start md:justify-end flex-shrink-0 mr-4 sm:mr-8 lg:mr-16">
        <div class="w-20 sm:w-28 md:w-full md:max-w-xs lg:max-w-sm">
          <img
            src="{{ asset('images/hero/hero-phone2.png') }}"
            alt="Preview Noorly"
            class="w-full h-auto rounded-lg object-contain"
          >
        </div>
      </div>
    </div>
  </div>
</section>
