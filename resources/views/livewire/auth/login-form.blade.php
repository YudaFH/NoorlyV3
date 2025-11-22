{{-- resources/views/livewire/auth/login-form.blade.php --}}
<div
    class="relative w-full max-w-4xl bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden grid md:grid-cols-2"
    x-data="loginPageState()">

    {{-- Panel kiri --}}
    <div class="hidden md:flex flex-col justify-between bg-[#1d428a] text-white p-8">
        <div>
            <img src="{{ asset('images/icon/logo_header.png') }}" alt="Noorly"
                 class="h-8 mb-6">
            <h1 class="text-2xl font-bold leading-snug">
                Selamat datang kembali di
                <span class="text-[#fbc926]">Noorly</span>.
            </h1>
            <p class="mt-4 text-sm text-white/80">
                Masuk untuk melanjutkan perjalanan Anda, mengakses konten yang disimpan,
                atau mengelola karya sebagai kreator.
            </p>
        </div>

        <div class="mt-8 text-xs text-white/70">
            <p>Gunakan email & kata sandi, atau login cepat melalui Google.</p>
        </div>
    </div>

    {{-- Panel kanan --}}
    <div class="p-6 sm:p-8">
        <div class="mb-4 text-center md:text-left">
            <p class="text-xs uppercase tracking-wide text-slate-400 mb-1">Masuk ke Noorly</p>
            <h2 class="text-xl sm:text-2xl font-semibold text-slate-900">Masuk ke akun Anda</h2>
        </div>

        {{-- Google --}}
        <div class="mb-4">
            <a href="{{ route('auth.google.redirect') }}"
               class="cursor-pointer w-full inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                <img src="https://www.svgrepo.com/show/475656/google-color.svg"
                     alt="Google" class="h-4 w-4">
                <span>Masuk dengan Google</span>
            </a>
        </div>

        <div class="flex items-center gap-3 my-4">
            <div class="h-px flex-1 bg-slate-200"></div>
            <span class="text-[11px] uppercase tracking-wide text-slate-400">
                atau gunakan akun Noorly
            </span>
            <div class="h-px flex-1 bg-slate-200"></div>
        </div>

        {{-- Switch mode: password / OTP --}}
        <div class="mb-4 flex gap-2 bg-slate-100 rounded-full p-1 text-xs font-medium">
            <button type="button"
                    @click="mode = 'password'"
                    :class="mode === 'password'
                            ? 'bg-white text-slate-900 shadow'
                            : 'text-slate-500'"
                    class="flex-1 rounded-full px-3 py-2 transition">
                Email & Kata Sandi
            </button>
            <button type="button"
                    @click="mode = 'otp'"
                    :class="mode === 'otp'
                            ? 'bg-white text-slate-900 shadow'
                            : 'text-slate-500'"
                    class="flex-1 rounded-full px-3 py-2 transition">
                Nomor & Kode OTP
            </button>
        </div>

        {{-- Form login --}}
        <form wire:submit.prevent="login" class="space-y-4">
            {{-- MODE: PASSWORD --}}
            <div x-show="mode === 'password'" x-transition.opacity>
                <div class="space-y-3">
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

                    {{-- Password --}}
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-medium text-slate-600">
                                Kata sandi
                            </label>
                            <a href="#" class="text-[11px] text-[#1d428a] hover:underline">
                                Lupa kata sandi?
                            </a>
                        </div>
                        <input type="password" wire:model.defer="password"
                               class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#fbc926] focus:border-transparent"
                               placeholder="Masukkan kata sandi">
                        @error('password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Remember --}}
                    <div class="flex items-center justify-between pt-1">
                        <label class="inline-flex items-center gap-2 text-xs text-slate-500">
                            <input type="checkbox" wire:model="remember"
                                   class="h-4 w-4 rounded border-slate-300 text-[#fbc926] focus:ring-[#fbc926]">
                            <span>Ingat saya di perangkat ini</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- MODE: OTP --}}
            <div x-show="mode === 'otp'" x-transition.opacity x-cloak class="space-y-3">
                {{-- Phone --}}
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">
                        Nomor telepon (WhatsApp)
                    </label>
                    <div class="flex">
                        <div class="inline-flex items-center px-3 border border-r-0 border-slate-200 rounded-l-lg bg-slate-50 text-sm text-slate-500">
                            +62
                        </div>
                        <input type="tel" wire:model.defer="phone"
                               class="flex-1 rounded-r-lg border border-l-0 border-slate-200 px-3 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#fbc926] focus:border-transparent"
                               placeholder="81234567890">
                    </div>
                    <p class="text-[11px] text-slate-400">
                        Masukkan nomor tanpa angka 0 di depan. Contoh:
                        <span class="font-semibold">81234567890</span>.
                    </p>
                    @error('phone')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror

                    <div class="mt-2 flex justify-end">
                        <button type="button"
                                @click="sendOtp()"
                                class="cursor-pointer inline-flex items-center rounded-lg bg-[#fbc926] px-3 py-2.5 text-xs font-semibold text-white hover:bg-[#e0ae1f] transition">
                            Kirim kode
                        </button>
                    </div>
                </div>

                {{-- OTP boxes --}}
                <div x-show="otpSent" x-transition.opacity class="space-y-2">
                    <label class="block text-xs font-medium text-slate-600">
                        Masukkan kode OTP
                    </label>

                    <div class="flex justify-center gap-2 mt-1">
                        @for ($i = 0; $i < 6; $i++)
                            <input
                                x-ref="d{{ $i }}"
                                x-model="boxes[{{ $i }}]"
                                @input="handleInput($event, {{ $i }})"
                                @keydown.backspace.prevent="handleBackspace($event, {{ $i }})"
                                type="text"
                                maxlength="1"
                                inputmode="numeric"
                                class="w-9 h-9 text-center rounded-md border border-slate-300 text-sm font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#fbc926] focus:border-transparent"
                            >
                        @endfor
                    </div>

                    @error('otp')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror

                    <p class="text-[11px] text-slate-400">
                        Kode dikirim ke WhatsApp dan berlaku selama
                        <span x-text="formattedTime()"></span>.
                    </p>
                </div>
            </div>

            {{-- Submit --}}
            <div class="pt-2">
                <button type="submit"
                        wire:target="login"
                        wire:loading.attr="disabled"
                        class="w-full inline-flex items-center justify-center rounded-lg bg-[#1d428a] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#163268] transition cursor-pointer disabled:opacity-70 disabled:cursor-not-allowed">

                    {{-- Normal --}}
                    <span wire:loading.remove wire:target="login">
                        Masuk
                    </span>

                    {{-- Loading --}}
                    <span wire:loading wire:target="login" class="inline-flex items-center gap-2">
                        <span class="h-4 w-4 border-2 border-white/60 border-t-transparent rounded-full animate-spin"></span>
                        Memeriksa akun...
                    </span>
                </button>

            </div>
        </form>

        <p class="mt-4 text-xs text-slate-500 text-center md:text-left">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-[#1d428a] font-medium hover:underline">
                Daftar sekarang
            </a>
        </p>
    </div>
</div>

{{-- SCRIPT: state login (mode + OTP) --}}
<script>
    function loginPageState() {
        return {
            mode: 'password',
            otpSent: false,
            boxes: ['', '', '', '', '', ''],
            remainingSeconds: 0,
            timer: null,

            sendOtp() {
                // reset state OTP
                this.boxes = ['', '', '', '', '', ''];
                this.remainingSeconds = 600; // 10 menit
                this.otpSent = true;

                if (this.timer) {
                    clearInterval(this.timer);
                }
                this.startTimer();

                this.$nextTick(() => {
                    if (this.$refs.d0) {
                        this.$refs.d0.focus();
                    }
                });

                // panggil Livewire kirim OTP
                this.$wire.sendOtpForLogin();
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
                if (this.remainingSeconds <= 0) {
                    return '00:00';
                }
                const m = Math.floor(this.remainingSeconds / 60);
                const s = this.remainingSeconds % 60;
                return `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
            },

            handleInput(event, index) {
                let value = event.target.value.replace(/\D/g, '');
                value = value.charAt(0) ?? '';

                this.boxes[index] = value;
                event.target.value = value;

                // sync ke Livewire → $otpDigits[index]
                this.$wire.set(`otpDigits.${index}`, value);

                // auto-pindah ke kotak berikutnya
                if (value && index < this.boxes.length - 1) {
                    const nextRef = this.$refs['d' + (index + 1)];
                    if (nextRef) nextRef.focus();
                }
            },

            handleBackspace(event, index) {
                // kalau masih ada angka → hapus dulu
                if (this.boxes[index]) {
                    this.boxes[index] = '';
                    event.target.value = '';
                    this.$wire.set(`otpDigits.${index}`, '');
                    return;
                }

                // kalau sudah kosong → mundur ke kotak sebelumnya
                if (index > 0) {
                    const prevRef = this.$refs['d' + (index - 1)];
                    if (prevRef) {
                        this.boxes[index - 1] = '';
                        prevRef.value = '';
                        prevRef.focus();
                        this.$wire.set(`otpDigits.${index - 1}`, '');
                    }
                }
            },
        };
    }
</script>
