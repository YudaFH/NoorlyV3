<div class="flex h-screen bg-slate-50">
    {{-- Sidebar admin --}}
    @include('livewire.admin.sidebar', ['sidebarOpen' => $sidebarOpen])

    {{-- Main --}}
    <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8 overflow-y-auto">
        {{-- Header --}}
        <header class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-slate-900">
                    Profil admin
                </h1>
                <p class="mt-1 text-sm text-slate-500 max-w-2xl">
                    Kelola data profil, email login, dan password akun admin Noorly Studio.
                </p>

                @if($user)
                    <div class="mt-3 flex flex-wrap gap-3 text-[11px] text-slate-500">
                        <span>Role: <span class="font-semibold text-slate-800">{{ strtoupper($user->role ?? 'admin') }}</span></span>
                        <span>Bergabung: <span class="font-semibold text-slate-800">
                            {{ optional($user->created_at)->format('d M Y') ?? '-' }}
                        </span></span>
                    </div>
                @endif
            </div>

            <div class="flex flex-col items-end gap-2">
                @if (session('status_profile'))
                    <div class="rounded-full bg-emerald-50 border border-emerald-100 px-3 py-1 text-[11px] text-emerald-700">
                        {{ session('status_profile') }}
                    </div>
                @endif

                @if (session('status_password'))
                    <div class="rounded-full bg-sky-50 border border-sky-100 px-3 py-1 text-[11px] text-sky-700">
                        {{ session('status_password') }}
                    </div>
                @endif
            </div>
        </header>

        <section class="grid gap-6 lg:grid-cols-3">
            {{-- Kolom kiri: data profil --}}
            <article class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                <div class="flex items-center gap-4 mb-5">
                    {{-- Avatar --}}
                    <div class="h-12 w-12 rounded-full bg-[#1d428a] text-white flex items-center justify-center text-sm font-semibold">
                        {{ strtoupper(mb_substr($user->name ?? 'A', 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">
                            {{ $user->name ?? 'Admin' }}
                        </p>
                        <p class="text-xs text-slate-500">
                            {{ $user->email ?? '-' }}
                        </p>
                    </div>
                </div>

                <form wire:submit.prevent="updateProfile" class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">
                            Nama lengkap
                        </label>
                        <input
                            type="text"
                            wire:model.defer="name"
                            class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                        >
                        @error('name')
                            <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">
                            Email login
                        </label>
                        <input
                            type="email"
                            wire:model.defer="email"
                            class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                        >
                        @error('email')
                            <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button
                            type="submit"
                            class="cursor-pointer inline-flex items-center gap-2 rounded-full bg-[#1d428a] px-5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-[#163268]"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M5 10.5l3 3 7-7-1.5-1.5L8 11 6.5 9.5 5 10.5z" />
                            </svg>
                            Simpan perubahan profil
                        </button>
                    </div>
                </form>
            </article>

            {{-- Kolom kanan: ganti password --}}
            <article class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                <h2 class="text-sm font-semibold text-slate-900 mb-3">
                    Ganti password
                </h2>
                <p class="text-[11px] text-slate-500 mb-4">
                    Demi keamanan, gunakan password yang kuat dan jangan dibagikan ke orang lain.
                </p>

                <form wire:submit.prevent="updatePassword" class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">
                            Password saat ini
                        </label>
                        <input
                            type="password"
                            wire:model.defer="current_password"
                            class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                        >
                        @error('current_password')
                            <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">
                            Password baru
                        </label>
                        <input
                            type="password"
                            wire:model.defer="new_password"
                            class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                        >
                        @error('new_password')
                            <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">
                            Konfirmasi password baru
                        </label>
                        <input
                            type="password"
                            wire:model.defer="new_password_confirmation"
                            class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                        >
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button
                            type="submit"
                            class="cursor-pointer inline-flex items-center gap-2 rounded-full bg-slate-900 px-5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-slate-800"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 2a4 4 0 00-4 4v2H5a1 1 0 00-1 1v7a1 1 0 001 1h10a1 1 0 001-1v-7a1 1 0 00-1-1h-1V6a4 4 0 00-4-4zM8 8V6a2 2 0 114 0v2H8z" />
                            </svg>
                            Update password
                        </button>
                    </div>
                </form>
            </article>
        </section>
    </main>
</div>
