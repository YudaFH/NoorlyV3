{{-- resources/views/livewire/admin/settings-index.blade.php --}}
<div class="flex h-screen bg-slate-50">
    {{-- SIDEBAR ADMIN --}}
    @include('livewire.admin.sidebar', ['sidebarOpen' => $sidebarOpen])

    {{-- MAIN --}}
    <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8 overflow-y-auto">
        {{-- HEADER --}}
        <header class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-slate-900">
                    Pengaturan platform
                </h1>
                <p class="mt-1 text-sm text-slate-500 max-w-2xl">
                    Atur nama platform, fee, batas minimal penarikan, dan mode maintenance Noorly Studio.
                </p>
            </div>

            <div class="flex flex-col items-end gap-2">
                @if (session('status_settings'))
                    <div
                        x-data="{ show: true }"
                        x-init="setTimeout(() => show = false, 3500)"
                        x-show="show"
                        x-transition
                        class="rounded-full bg-emerald-50 border border-emerald-100 px-3 py-1 text-[11px] text-emerald-700"
                    >
                        {{ session('status_settings') }}
                    </div>
                @endif
            </div>
        </header>

        {{-- CEK: kalau tabel belum ada --}}
        @if (! $hasSettingsTable)
            <section class="mt-4 rounded-2xl border border-amber-100 bg-amber-50 px-4 py-4 text-sm text-amber-800">
                <p class="font-semibold mb-1">Tabel pengaturan belum tersedia</p>
                <p class="text-xs">
                    Jalankan migrasi untuk tabel <code class="font-mono">platform_settings</code> terlebih dahulu,
                    kemudian refresh halaman ini.
                </p>
            </section>
        @else
            {{-- FORM PENGATURAN --}}
            <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                <form wire:submit.prevent="save" class="space-y-6">
                    {{-- Identitas platform --}}
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-xs font-medium text-slate-700 block mb-1.5">
                                Nama platform
                            </label>
                            <input
                                type="text"
                                wire:model.defer="platform_name"
                                class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                                placeholder="Misal: Noorly Studio"
                            >
                            @error('platform_name')
                                <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-xs font-medium text-slate-700 block mb-1.5">
                                Tagline singkat
                            </label>
                            <input
                                type="text"
                                wire:model.defer="platform_tagline"
                                class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                                placeholder="Misal: Bantu kreator menjual produk digital."
                            >
                            @error('platform_tagline')
                                <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-xs font-medium text-slate-700 block mb-1.5">
                                Domain / URL utama
                            </label>
                            <input
                                type="text"
                                wire:model.defer="platform_domain"
                                class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                                placeholder="https://noorly.com"
                            >
                            @error('platform_domain')
                                <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-xs font-medium text-slate-700 block mb-1.5">
                                Email support
                            </label>
                            <input
                                type="email"
                                wire:model.defer="support_email"
                                class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                                placeholder="support@noorly.com"
                            >
                            @error('support_email')
                                <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <hr class="border-slate-100 my-4">

                    {{-- Finansial & fee --}}
                    <div class="grid gap-4 md:grid-cols-3">
                        <div>
                            <label class="text-xs font-medium text-slate-700 block mb-1.5">
                                Fee platform (%)
                            </label>
                            <div class="relative">
                                <input
                                    type="number"
                                    wire:model.defer="platform_fee_percent"
                                    min="0"
                                    max="100"
                                    class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 pr-10 text-sm text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                                >
                                <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-xs text-slate-500">
                                    %
                                </span>
                            </div>
                            @error('platform_fee_percent')
                                <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-[11px] text-slate-400">
                                Persentase yang diambil platform dari setiap transaksi sukses.
                            </p>
                        </div>

                        <div>
                            <label class="text-xs font-medium text-slate-700 block mb-1.5">
                                Minimal penarikan (Rp)
                            </label>
                            <input
                                type="number"
                                wire:model.defer="min_withdraw_amount"
                                min="0"
                                step="1000"
                                class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 focus:border-[#1d428a] focus:ring-[#1d428a]"
                            >
                            @error('min_withdraw_amount')
                                <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-[11px] text-slate-400">
                                Batas saldo minimum sebelum kreator bisa mengajukan penarikan.
                            </p>
                        </div>

                        <div class="flex items-start gap-2 pt-1">
                            <div class="mt-1">
                                <button
                                    type="button"
                                    wire:click="$set('maintenance_mode', {{ $maintenance_mode ? 'false' : 'true' }})"
                                    class="cursor-pointer inline-flex h-6 w-10 items-center rounded-full border transition
                                           {{ $maintenance_mode ? 'bg-rose-500 border-rose-500' : 'bg-slate-200 border-slate-300' }}"
                                >
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition
                                                 {{ $maintenance_mode ? 'translate-x-4' : 'translate-x-1' }}"></span>
                                </button>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-slate-700">
                                    Mode maintenance
                                </p>
                                <p class="text-[11px] text-slate-400">
                                    Jika aktif, pengguna biasa akan melihat halaman pemeliharaan saat mengakses platform.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- ACTION BAR --}}
                    <div class="mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4">
                        <div class="text-[11px] text-slate-400">
                            Perubahan akan langsung mempengaruhi perilaku platform setelah disimpan.
                        </div>

                        <div class="flex flex-wrap gap-2">
                            {{-- tombol reset -> panggil resetForm() --}}
                            <button
                                type="button"
                                wire:click="resetForm"
                                class="cursor-pointer inline-flex items-center gap-1 rounded-full border border-slate-200 bg-white px-4 py-1.5 text-[11px] font-medium text-slate-700 hover:bg-slate-50"
                            >
                                Batalkan perubahan
                            </button>

                            {{-- tombol simpan -> submit form ke save() --}}
                            <button
                                type="submit"
                                class="cursor-pointer inline-flex items-center gap-2 rounded-full bg-[#1d428a] px-5 py-1.5 text-[11px] font-semibold text-white shadow-sm hover:bg-[#163268]"
                            >
                                <span wire:loading.remove wire:target="save">Simpan pengaturan</span>
                                <span wire:loading wire:target="save" class="flex items-center gap-1">
                                    <svg class="h-3 w-3 animate-spin" viewBox="0 0 24 24" fill="none">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3.5-3.5L12 0v4a8 8 0 100 16v-4l-3.5 3.5L12 24v-4a8 8 0 01-8-8z"></path>
                                    </svg>
                                    Menyimpan...
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </section>
        @endif
    </main>
</div>
