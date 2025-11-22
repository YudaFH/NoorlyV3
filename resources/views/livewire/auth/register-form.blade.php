{{-- resources/views/livewire/auth/register-form.blade.php --}}
<div
    class="relative w-full max-w-4xl bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden grid md:grid-cols-2">

    {{-- Panel kiri --}}
    <div class="hidden md:flex flex-col justify-between bg-[#1d428a] text-white p-8 relative">
        <div>
            <img src="{{ asset('images/icon/logo_header.png') }}" alt="Noorly" class="h-8 mb-6">
            <h1 class="text-2xl font-bold leading-snug">
                Mulai berbagi dan menemukan
                <span class="text-[#fbc926]">cahaya kebaikan</span> di Noorly.
            </h1>
            <p class="mt-4 text-sm text-white/80">
                Daftar sebagai pengguna untuk menikmati konten, atau sebagai kreator untuk
                membagikan blog, e-book, dan video kepada audiens Anda.
            </p>
        </div>

        <div class="mt-8 text-xs text-white/70">
            <p>Akun dapat diatur sebagai konten gratis atau berbayar, publik atau private.</p>
        </div>
    </div>

    {{-- Panel kanan --}}
    <div class="p-6 sm:p-8">
        {{-- Header --}}
        <div class="mb-4 text-center md:text-left">
            <p class="text-xs uppercase tracking-wide text-slate-400 mb-1">Daftar ke Noorly</p>
            <h2 class="text-xl sm:text-2xl font-semibold text-slate-900">
                Buat akun baru
            </h2>
        </div>

        {{-- Switch role --}}
        <div class="mb-5 flex gap-2 bg-slate-100 rounded-full p-1 text-xs font-medium">
            <button type="button"
                    @click="$wire.set('role','user')"
                    class="flex-1 rounded-full px-3 py-2 transition"
                    :class="$wire.get('role') === 'user'
                        ? 'bg-white text-slate-900 shadow'
                        : 'text-slate-500'">
                Pengguna
            </button>
            <button type="button"
                    @click="$wire.set('role','creator')"
                    class="flex-1 rounded-full px-3 py-2 transition"
                    :class="$wire.get('role') === 'creator'
                        ? 'bg-white text-slate-900 shadow'
                        : 'text-slate-500'">
                Kreator
            </button>
        </div>

        {{-- ===================== STEP 1: FORM REGISTRASI ===================== --}}
        @if (! $otpSent)
            <div class="mb-4">
                <a href="{{ route('auth.google.redirect') }}"
                   class="w-full inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                    <img src="https://www.svgrepo.com/show/475656/google-color.svg"
                         alt="Google" class="h-4 w-4">
                    <span>Daftar dengan Google</span>
                </a>
            </div>

            <div class="flex items-center gap-3 my-4">
                <div class="h-px flex-1 bg-slate-200"></div>
                <span class="text-[11px] uppercase tracking-wide text-slate-400">
                    atau isi data manual
                </span>
                <div class="h-px flex-1 bg-slate-200"></div>
            </div>

            {{-- FORM STEP 1 --}}
            <form wire:submit.prevent="register" class="space-y-4">
                {{-- Nama --}}
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">
                        Nama lengkap
                    </label>
                    <input type="text" wire:model.defer="name"
                           class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#fbc926] focus:border-transparent"
                           placeholder="Nama sesuai identitas">
                    @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">
                        Email
                    </label>
                    <input type="email" wire:model.defer="email"
                           class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#fbc926] focus:border-transparent"
                           placeholder="nama@email.com">
                    @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Telepon --}}
                <div class="space-y-1">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-medium text-slate-600">
                            Nomor telepon (WhatsApp)
                        </label>
                        <span class="text-[11px] text-slate-400">
                            Kode OTP akan dikirim ke nomor ini
                        </span>
                    </div>
                    <div class="flex">
                        <div class="inline-flex items-center px-3 border border-r-0 border-slate-200 rounded-l-lg bg-slate-50 text-sm text-slate-500">
                            +62
                        </div>
                        <input type="tel" wire:model.defer="phone"
                               class="flex-1 rounded-r-lg border border-l-0 border-slate-200 px-3 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#fbc926] focus:border-transparent"
                               placeholder="85712345678">
                    </div>
                    <p class="text-[11px] text-slate-400">
                        Masukkan nomor tanpa angka 0 di depan. Contoh:
                        <span class="font-semibold">81234567890</span>.
                    </p>
                    @error('phone')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Field kreator --}}
                <div x-show="$wire.get('role') === 'creator'" x-transition.opacity x-cloak
                     class="space-y-3 pt-2 border-t border-slate-100">
                    <p class="text-xs font-semibold text-slate-600">Informasi Kreator</p>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">
                            Nama kreator / brand
                        </label>
                        <input type="text" wire:model.defer="creator_name"
                               class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#fbc926] focus:border-transparent"
                               placeholder="Contoh: Noorly Stories">
                        @error('creator_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">
                            Jenis konten utama
                        </label>
                        <select wire:model.defer="main_content_type"
                                class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#fbc926] focus:border-transparent">
                            <option value="">Pilih jenis konten</option>
                            <option value="blog">Blog / Artikel</option>
                            <option value="video">Video</option>
                            <option value="ebook">E-Book</option>
                            <option value="mix">Campuran (Blog, Video, E-Book)</option>
                        </select>
                        @error('main_content_type')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Password --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">
                            Kata sandi
                        </label>
                        <input type="password" wire:model.defer="password"
                               class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#fbc926] focus:border-transparent"
                               placeholder="Minimal 8 karakter">
                        @error('password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">
                            Konfirmasi kata sandi
                        </label>
                        <input type="password" wire:model.defer="password_confirmation"
                               class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#fbc926] focus:border-transparent"
                               placeholder="Ulangi kata sandi">
                    </div>
                </div>

                {{-- Terms --}}
                <div class="flex items-start gap-2 pt-1">
                    <input id="terms" type="checkbox" wire:model="accept_terms"
                           class="mt-0.5 h-4 w-4 rounded border-slate-300 text-[#fbc926] focus:ring-[#fbc926]">
                    <label for="terms" class="text-xs text-slate-500">
                        Saya menyetujui <a href="#" class="text-[#1d428a] hover:underline">Syarat & Ketentuan</a>
                        dan <a href="#" class="text-[#1d428a] hover:underline">Kebijakan Privasi</a> Noorly.
                    </label>
                </div>
                @error('accept_terms')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror

                {{-- Submit --}}
                <div class="pt-2">
                    <button type="submit"
                        wire:target="register"
                        wire:loading.attr="disabled"
                        class="w-full inline-flex items-center justify-center rounded-lg bg-[#1d428a] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#163268] transition cursor-pointer disabled:opacity-70 disabled:cursor-not-allowed">

                    {{-- Teks normal (saat tidak loading) --}}
                    <span wire:loading.remove wire:target="register">
                        Buat akun
                    </span>

                    {{-- Teks + spinner saat loading --}}
                    <span wire:loading wire:target="register" class="inline-flex items-center gap-2">
                        <span class="h-4 w-4 border-2 border-white/60 border-t-transparent rounded-full animate-spin"></span>
                        Memproses...
                    </span>
                </button>


                </div>
            </form>

        {{-- ===================== STEP 2: VERIFIKASI OTP ===================== --}}
        @else
            <div x-data="otpBoxes(@js($otpExpiresAt))" x-init="init()" class="space-y-6 text-center">
                <h3 class="text-lg font-semibold text-slate-900">
                    Verifikasi nomor WhatsApp
                </h3>

                <div class="text-sm text-slate-600">
                    <p>Kami telah mengirimkan kode verifikasi 6 digit ke WhatsApp:</p>
                    <p class="mt-1 font-semibold text-slate-900">
                        {{ $maskedPhone }}
                    </p>
                </div>

                {{-- Hidden input yang terhubung ke Livewire --}}
                <input type="hidden" x-ref="otpHidden" wire:model.defer="otp_code">

                {{-- 6 kotak OTP --}}
                <div class="flex justify-center gap-2 mt-2">
                    @for ($i = 0; $i < 6; $i++)
                        <input
                            x-ref="d{{ $i }}"
                            x-model="boxes[{{ $i }}]"
                            @input="handleInput($event, {{ $i }})"
                            @keydown.backspace.prevent="handleBackspace($event, {{ $i }})"
                            type="text"
                            maxlength="1"
                            inputmode="numeric"
                            class="w-10 h-10 text-center rounded-lg border border-slate-300 text-base font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#fbc926] focus:border-transparent"
                        >
                    @endfor
                </div>
                @error('otp_code')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                @error('otp_general')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror

                {{-- Info timer --}}
                <div class="text-[11px] text-slate-500 space-y-1">
                    <p x-show="remainingSeconds > 0">
                        Kode berlaku selama
                        <span x-text="formattedTime()"></span>.
                    </p>
                    <p>Maksimal 3 kali percobaan verifikasi per kode.</p>
                </div>

                {{-- Tombol konfirmasi (panggil confirmOtp di Livewire) --}}
               <form wire:submit.prevent="confirmOtp" class="space-y-3">
                    <form wire:submit.prevent="confirmOtp" class="space-y-3">
                        <button type="submit"
                                wire:target="confirmOtp"
                                wire:loading.attr="disabled"
                                class="w-full inline-flex items-center justify-center rounded-lg bg-[#1d428a] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#163268] transition cursor-pointer disabled:opacity-70 disabled:cursor-not-allowed">

                            {{-- Normal --}}
                            <span wire:loading.remove wire:target="confirmOtp">
                                Konfirmasi kode
                            </span>

                            {{-- Loading --}}
                            <span wire:loading wire:target="confirmOtp" class="inline-flex items-center gap-2">
                                <span class="h-4 w-4 border-2 border-white/60 border-t-transparent rounded-full animate-spin"></span>
                                Memproses...
                            </span>
                        </button>
                    </form>

                </form>


                {{-- Ubah nomor / kirim ulang --}}
                <div class="text-xs text-slate-500 space-y-1 mt-2">
                    <button type="button"
                            wire:click="editPhone"
                            class="text-[#1d428a] hover:underline">
                        Ubah nomor WhatsApp
                    </button>
                    <br>
                    <button type="button"
                            wire:click="resendOtp"
                            class="text-[#1d428a] hover:underline">
                        Kirim ulang kode
                    </button>
                </div>
            </div>
        @endif

        <p class="mt-4 text-xs text-slate-500 text-center md:text-left">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-[#1d428a] font-medium hover:underline">
                Masuk di sini
            </a>
        </p>
    </div>
</div>

{{-- ===================== SCRIPT OTP (Alpine) ===================== --}}
<script>
    function otpBoxes(otpExpiresAt = null) {
        return {
            boxes: ['', '', '', '', '', ''],

            // jika backend sudah kirim timestamp, pakai itu, kalau tidak default 10 menit
            remainingSeconds: otpExpiresAt
                ? Math.max(otpExpiresAt - Math.floor(Date.now() / 1000), 0)
                : 600,

            timer: null,

            init() {
                // fokus ke kotak pertama
                this.$nextTick(() => {
                    if (this.$refs.d0) {
                        this.$refs.d0.focus();
                    }
                });

                // mulai timer
                this.startTimer();

                // sinkron awal (semisal boxes sudah terisi dari server)
                this.updateHidden();
            },

            startTimer() {
                this.timer = setInterval(() => {
                    if (this.remainingSeconds > 0) {
                        this.remainingSeconds--;
                    } else {
                        clearInterval(this.timer);
                    }
                }, 1000);
            },

            formattedTime() {
                const m = Math.floor(this.remainingSeconds / 60);
                const s = this.remainingSeconds % 60;
                return `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
            },

            updateHidden() {
                if (this.$refs.otpHidden) {
                    this.$refs.otpHidden.value = this.boxes.join('');
                    this.$refs.otpHidden.dispatchEvent(new Event('input', { bubbles: true }));
                }
            },

            handleInput(event, index) {
                let value = event.target.value.replace(/\D/g, '');
                value = value.charAt(0) ?? '';

                this.boxes[index] = value;
                event.target.value = value;

                this.updateHidden();

                // kalau ada angka, pindah ke kotak berikutnya
                if (value && index < this.boxes.length - 1) {
                    const nextRef = this.$refs['d' + (index + 1)];
                    if (nextRef) nextRef.focus();
                }
            },

            handleBackspace(event, index) {
                // kalau kotak sekarang berisi angka → hapus dulu
                if (this.boxes[index]) {
                    this.boxes[index] = '';
                    event.target.value = '';
                    this.updateHidden();
                    return;
                }

                // kalau kosong → mundur ke kotak sebelumnya
                if (index > 0) {
                    const prevRef = this.$refs['d' + (index - 1)];
                    if (prevRef) {
                        this.boxes[index - 1] = '';
                        prevRef.value = '';
                        prevRef.focus();
                        this.updateHidden();
                    }
                }
            },

            syncToLivewire() {
                this.updateHidden();
            }
        }
    }
</script>
