<section
  class="relative overflow-hidden w-screen min-h-[75vh] md:min-h-[85vh] lg:min-h-screen flex items-center justify-center -mx-4 sm:-mx-6 lg:-mx-8 bg-slate-900"
>
  {{-- Desktop: background video --}}
  <div class="hidden md:block absolute inset-0 w-full h-full">
    <video
      id="heroVideo"
      class="w-full h-full object-cover"
      muted
      loop
      playsinline
      preload="none"
      poster="{{ asset('images/banner.jpg') }}"
    >
      <source src="{{ asset('video/header/home.mp4') }}" type="video/mp4">
      Your browser does not support the video tag.
    </video>

    {{-- overlay gelap --}}
    <div class="absolute inset-0 bg-black/50"></div>
  </div>

  {{-- Mobile: foto + overlay gelap + gradient coklat (seperti footer) --}}
  <div class="block md:hidden absolute inset-0 w-full h-full">
    <img
      src="{{ asset('images/banner.jpg') }}"
      alt="Banner background"
      class="w-full h-full object-cover"
    >

    {{-- overlay: atas lebih gelap, bawah coklat --}}
    <div class="absolute inset-0 bg-gradient-to-b from-black/70 via-black/40 to-[#2b2a26]/85"></div>
  </div>

  {{-- Konten hero --}}
  <div class="relative w-full flex items-center justify-center py-16 md:py-24 lg:py-32">
    <div class="text-center px-4 sm:px-6 max-w-7xl mx-auto">
      <h1 class="text-3xl md:text-5xl lg:text-6xl font-extrabold text-white drop-shadow-md">
        Cahaya Inspirasi untuk jiwa muda
      </h1>
      <p class="mt-4 text-sm md:text-lg text-white/90 max-w-2xl mx-auto">
        Temukan kisah dan semangat spiritual yang menyentuh hati generasi Z
      </p>
    </div>
  </div>
</section>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const video = document.getElementById('heroVideo');
    if (!video) return;

    // kalau browser tidak support IntersectionObserver, langsung play saja
    if (!('IntersectionObserver' in window)) {
      video.play().catch(() => {});
      return;
    }

    const observer = new IntersectionObserver(function (entries, obs) {
      const entry = entries[0];
      if (entry.isIntersecting) {
        video.play().catch(() => {});
        obs.disconnect();
      }
    }, {
      threshold: 0.4
    });

    observer.observe(video);
  });
</script>
