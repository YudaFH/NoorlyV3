@php
    use Illuminate\Support\Facades\Route;
@endphp

<div class="flex h-screen bg-slate-50">
    {{-- SIDEBAR KIRI --}}
    @include('livewire.creator.sidebar', ['sidebarOpen' => $sidebarOpen])

    {{-- KONTEN KANAN --}}
    <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8 overflow-y-auto">
        {{-- Header --}}
        <header class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-slate-900">
                    Profil kreator
                </h1>
                <p class="mt-1 text-sm text-slate-500 max-w-2xl">
                    Kelola identitas publik dan keamanan akun kreator Noorly kamu.
                </p>
            </div>
        </header>

        {{-- Flash message --}}
        @if (session('status_profile'))
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('status_profile') }}
            </div>
        @endif

        @if (session('status_password'))
            <div class="mb-4 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-800">
                {{ session('status_password') }}
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">
            {{-- FORM PROFIL (kiri) --}}
            <form wire:submit.prevent="saveProfile" class="lg:col-span-2 space-y-6">
                {{-- Informasi dasar --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <h2 class="text-sm font-semibold text-slate-900 mb-1">
                        Informasi dasar
                    </h2>
                    <p class="text-xs text-slate-500 mb-4">
                        Nama dan email utama akun kreator kamu.
                    </p>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1.5">
                                Nama lengkap
                            </label>
                            <input
                                type="text"
                                wire:model.defer="name"
                                class="block w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#1d428a] focus:ring-[#1d428a]"
                                placeholder="Nama lengkap"
                            >
                            @error('name')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1.5">
                                Email
                            </label>
                            <input
                                type="email"
                                value="{{ $email }}"
                                disabled
                                class="block w-full rounded-xl border-slate-200 bg-slate-100 px-3 py-2.5 text-sm text-slate-500 cursor-not-allowed"
                            >
                            <p class="mt-1 text-[11px] text-slate-400">
                                Email digunakan untuk login dan notifikasi penting.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Profil publik: avatar, banner, bio --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 space-y-5">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900 mb-1">
                            Profil publik
                        </h2>
                        <p class="text-xs text-slate-500">
                            Informasi ini akan terlihat oleh pengunjung ketika mereka melihat profil kreator kamu.
                        </p>
                    </div>

                    {{-- Banner --}}
                    <div class="space-y-2">
                        <p class="text-xs font-medium text-slate-700">
                            Banner profil
                        </p>
                        <div class="relative w-full rounded-2xl border border-dashed border-slate-200 bg-slate-50/60 overflow-hidden">
                            @if ($banner)
                                <img
                                    src="{{ $banner->temporaryUrl() }}"
                                    class="h-40 w-full object-cover"
                                    alt="Banner baru"
                                >
                            @elseif ($banner_path)
                                <img
                                    src="{{ asset('storage/' . $banner_path) }}"
                                    class="h-40 w-full object-cover"
                                    alt="Banner kreator"
                                >
                            @else
                                <div class="h-32 w-full flex flex-col items-center justify-center text-xs text-slate-400">
                                    <span>Belum ada banner profil.</span>
                                    <span>Unggah gambar untuk membuat tampilan profil lebih menarik.</span>
                                </div>
                            @endif

                            <div class="absolute inset-x-0 bottom-0 flex items-center justify-between gap-2 bg-gradient-to-t from-black/50 to-transparent px-3 py-2 text-xs text-white">
                                <label class="inline-flex items-center justify-center rounded-full bg-white/90 px-3 py-1.5 text-xs font-medium text-slate-800 cursor-pointer hover:bg-white">
                                    <input type="file" class="hidden" wire:model="banner" accept="image/*">
                                    Ubah banner
                                </label>

                                @if ($banner_path)
                                    <button
                                        type="button"
                                        wire:click="removeBanner"
                                        class="inline-flex items-center justify-center rounded-full bg-red-500/90 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-600"
                                    >
                                        Hapus banner
                                    </button>
                                @endif
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-400">
                            Disarankan rasio 16:9, format JPG/PNG, maksimal 4MB.
                        </p>
                        @error('banner')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Avatar --}}
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            @if ($avatar)
                                <img
                                    src="{{ $avatar->temporaryUrl() }}"
                                    class="h-16 w-16 rounded-full object-cover border border-slate-200"
                                    alt="Avatar kreator"
                                >
                            @elseif ($avatar_path)
                                <img
                                    src="{{ asset('storage/' . $avatar_path) }}"
                                    class="h-16 w-16 rounded-full object-cover border border-slate-200"
                                    alt="Avatar kreator"
                                >
                            @else
                                <div class="h-16 w-16 rounded-full bg-[#fbc926] text-white flex items-center justify-center text-xl font-semibold">
                                    {{ strtoupper(mb_substr($name, 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        <div class="space-y-2">
                            <div class="flex flex-wrap items-center gap-2">
                                <label class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 cursor-pointer hover:bg-slate-50">
                                    <input type="file" class="hidden" wire:model="avatar" accept="image/*">
                                    Ubah foto profil
                                </label>

                                @if ($avatar_path)
                                    <button
                                        type="button"
                                        wire:click="removeAvatar"
                                        class="inline-flex items-center justify-center rounded-full border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-100"
                                    >
                                        Hapus foto
                                    </button>
                                @endif
                            </div>

                            <p class="text-[11px] text-slate-400">
                                Format JPG/PNG, maksimal 2MB.
                            </p>

                            @error('avatar')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Bio --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">
                            Bio singkat
                        </label>
                        <textarea
                            rows="4"
                            wire:model.defer="bio"
                            class="block w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#1d428a] focus:ring-[#1d428a]"
                            placeholder="Ceritakan sedikit tentang dirimu, niche, dan jenis konten yang kamu buat di Noorly."
                        ></textarea>
                        @error('bio')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Tombol simpan profil --}}
                <div>
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-full bg-[#1d428a] px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#163268] focus:outline-none focus:ring-2 focus:ring-[#1d428a] focus:ring-offset-1 focus:ring-offset-slate-50 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer"
                        wire:loading.attr="disabled"
                        wire:target="saveProfile,avatar,banner"
                    >
                        <span wire:loading.remove wire:target="saveProfile,avatar,banner">
                            Simpan perubahan profil
                        </span>
                        <span wire:loading wire:target="saveProfile,avatar,banner">
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </form>

            {{-- KANAN: keamanan & akun --}}
            <div class="space-y-6">
                {{-- Form ubah password --}}
                <form wire:submit.prevent="updatePassword" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 space-y-4">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900 mb-1">
                            Keamanan & password
                        </h2>
                        <p class="text-xs text-slate-500">
                            Ubah password secara berkala untuk menjaga keamanan akunmu.
                        </p>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1.5">
                                Password saat ini
                            </label>
                            <input
                                type="password"
                                wire:model.defer="current_password"
                                class="block w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                                autocomplete="current-password"
                            >
                            @error('current_password')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1.5">
                                Password baru
                            </label>
                            <input
                                type="password"
                                wire:model.defer="new_password"
                                class="block w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                                autocomplete="new-password"
                            >
                            @error('new_password')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1.5">
                                Konfirmasi password baru
                            </label>
                            <input
                                type="password"
                                wire:model.defer="new_password_confirmation"
                                class="block w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                                autocomplete="new-password"
                            >
                            @error('new_password_confirmation')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="mt-2 inline-flex w-full items-center justify-center gap-2 rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-1 focus:ring-offset-slate-50 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer"
                        wire:loading.attr="disabled"
                        wire:target="updatePassword"
                    >
                        <span wire:loading.remove wire:target="updatePassword">Update password</span>
                        <span wire:loading wire:target="updatePassword">Memproses...</span>
                    </button>
                </form>

                {{-- Lupa password & switch account --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 space-y-4">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900 mb-1">
                            Akses akun
                        </h2>
                        <p class="text-xs text-slate-500">
                            Kelola akses akunmu atau keluar untuk berganti akun.
                        </p>
                    </div>

                    <div class="space-y-3">
                        @if (Route::has('password.request'))
                            <a
                                href="{{ route('password.request') }}"
                                class="inline-flex w-full items-center justify-center rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-xs font-medium text-slate-700 hover:bg-slate-100"
                            >
                                Lupa password? Kirim link reset
                            </a>
                        @else
                            <p class="text-[11px] text-slate-500">
                                Jika kamu lupa password, hubungi tim Noorly atau admin untuk bantuan reset password.
                            </p>
                        @endif

                        <button
                            type="button"
                            wire:click="switchAccount"
                            class="inline-flex w-full items-center justify-center rounded-full border border-red-200 bg-red-50 px-4 py-2 text-xs font-semibold text-red-600 hover:bg-red-100 cursor-pointer"
                        >
                            Keluar & ganti akun
                        </button>
                    </div>

                    <p class="text-[11px] text-slate-400">
                        Setelah keluar, kamu bisa login kembali menggunakan akun kreator lain atau akun biasa.
                    </p>
                </div>
            </div>
        </div>
    </main>
</div>
