{{-- resources/views/user/profile.blade.php --}}
@extends('layouts.noorly')

@section('title', 'Profil & Pengaturan — Noorly')

@section('content')
<div class="pt-24 pb-12 bg-slate-50 min-h-screen">
  <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
    {{-- Heading --}}
    <div class="mb-6">
      <h1 class="text-2xl md:text-3xl font-semibold text-slate-900">
        Profil & pengaturan
      </h1>
      <p class="mt-1 text-sm text-slate-500 max-w-2xl">
        Kelola informasi akun Noorly kamu, foto profil, dan pengaturan keamanan.
      </p>
    </div>

    {{-- Notif sukses --}}
    @if(session('profile_updated'))
      <div class="mb-4 rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 flex items-start gap-3">
        <svg class="h-5 w-5 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293A1 1 0 006.293 10.707l2 2a1 1 0 001.414 0l4-4z"
                clip-rule="evenodd" />
        </svg>
        <div>{{ session('profile_updated') }}</div>
      </div>
    @endif

    @if(session('password_updated'))
      <div class="mb-4 rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 flex items-start gap-3">
        <svg class="h-5 w-5 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293A1 1 0 006.293 10.707l2 2a1 1 0 001.414 0l4-4z"
                clip-rule="evenodd" />
        </svg>
        <div>{{ session('password_updated') }}</div>
      </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      {{-- Kiri: info profil --}}
      <div class="lg:col-span-2">
        <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-5 sm:p-6">
          <h2 class="text-sm font-semibold text-slate-900 mb-1">
            Informasi profil
          </h2>
          <p class="text-xs text-slate-500 mb-4">
            Data ini akan digunakan di beberapa bagian di Noorly, misalnya avatar, nama di komentar, dan email notifikasi.
          </p>

          <form method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PATCH')

            {{-- Avatar --}}
            <div class="flex items-center gap-4">
              <div class="flex-shrink-0">
                @if(!empty($user->avatar_url))
                  <img
                    src="{{ $user->avatar_url }}"
                    alt="{{ $user->name }}"
                    class="h-14 w-14 rounded-full object-cover border border-slate-200"
                  >
                @else
                  <div class="h-14 w-14 rounded-full bg-[#1d428a] text-white flex items-center justify-center text-lg font-semibold">
                    {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                  </div>
                @endif
              </div>
              <div class="flex-1">
                <label class="block text-xs font-medium text-slate-700 mb-1">
                  Foto profil
                </label>
                <input
                  type="file"
                  name="avatar"
                  class="block w-full text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-medium file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200"
                  accept="image/*"
                >
                <p class="mt-1 text-[11px] text-slate-400">
                  Format: JPG/PNG, maks. 2MB.
                </p>
                @error('avatar')
                  <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                @enderror
              </div>
            </div>

            {{-- Nama --}}
            <div>
              <label for="name" class="block text-xs font-medium text-slate-700 mb-1">
                Nama lengkap
              </label>
              <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $user->name) }}"
                class="block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#1d428a] focus:ring-[#1d428a]"
                required
              >
              @error('name')
                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
              @enderror
            </div>

            {{-- Email (readonly / optional) --}}
            <div>
              <label for="email" class="block text-xs font-medium text-slate-700 mb-1">
                Email
              </label>
              <input
                type="email"
                id="email"
                value="{{ $user->email }}"
                class="block w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-500"
                disabled
              >
              <p class="mt-1 text-[11px] text-slate-400">
                Email digunakan untuk login dan notifikasi. Hubungi support jika ingin mengganti email.
              </p>
              {{-- Kalau nanti mau izinkan ganti email, ubah input jadi name="email" dan izinkan di controller --}}
            </div>

            <div class="pt-2 flex items-center justify-end gap-2">
              <button
                type="submit"
                class="inline-flex items-center rounded-full bg-[#1d428a] px-4 py-2 text-xs sm:text-sm font-semibold text-white shadow-sm hover:bg-[#163268] cursor-pointer"
              >
                Simpan perubahan
              </button>
            </div>
          </form>
        </div>
      </div>

      {{-- Kanan: ubah password --}}
      <div>
        <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-5 sm:p-6">
          <h2 class="text-sm font-semibold text-slate-900 mb-1">
            Keamanan & password
          </h2>
          <p class="text-xs text-slate-500 mb-4">
            Gunakan password yang kuat untuk menjaga keamanan akun Noorly kamu.
          </p>

          <form method="POST" action="{{ route('user.profile.password') }}" class="space-y-3">
            @csrf
            @method('PATCH')

            <div>
              <label for="current_password" class="block text-xs font-medium text-slate-700 mb-1">
                Password saat ini
              </label>
              <input
                type="password"
                id="current_password"
                name="current_password"
                class="block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#1d428a] focus:ring-[#1d428a]"
                required
              >
              @error('current_password')
                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label for="password" class="block text-xs font-medium text-slate-700 mb-1">
                Password baru
              </label>
              <input
                type="password"
                id="password"
                name="password"
                class="block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#1d428a] focus:ring-[#1d428a]"
                required
              >
              @error('password')
                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label for="password_confirmation" class="block text-xs font-medium text-slate-700 mb-1">
                Konfirmasi password baru
              </label>
              <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                class="block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#1d428a] focus:ring-[#1d428a]"
                required
              >
            </div>

            <div class="pt-2 flex items-center justify-end">
              <button
                type="submit"
                class="inline-flex items-center rounded-full bg-white border border-slate-200 px-4 py-2 text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-50 cursor-pointer"
              >
                Ubah password
              </button>
            </div>
          </form>

          <p class="mt-4 text-[11px] text-slate-400 leading-relaxed">
            Tips: jangan gunakan password yang sama dengan akun lain. Kombinasikan huruf besar, huruf kecil, angka, dan simbol.
          </p>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
