@extends('layouts.noorly')

@section('title', 'Checkout | Noorly')

@section('content')
  <main class="relative min-h-[calc(100vh-5rem)] bg-slate-50 text-slate-900">
    {{-- Background dekor halus --}}
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
      <div class="absolute -top-32 -right-24 h-64 w-64 rounded-full bg-gradient-to-br from-indigo-200 via-fuchsia-100 to-sky-200 blur-3xl opacity-50"></div>
      <div class="absolute -bottom-40 -left-10 h-72 w-72 rounded-full bg-gradient-to-tr from-emerald-100 via-sky-100 to-indigo-200 blur-3xl opacity-50"></div>
    </div>

    <section class="relative z-10 py-16 md:py-24">
      <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <div class="mb-6 text-xs text-slate-500 flex items-center gap-2">
          <a href="{{ url('/') }}" class="hover:text-slate-800">Beranda</a>
          <span>/</span>
          <a href="{{ url('/konten') }}" class="hover:text-slate-800">Konten</a>
          <span>/</span>
          <span class="text-slate-700">Checkout</span>
        </div>

        <div class="max-w-2xl">
          

          <h1 class="font-sans text-3xl md:text-4xl font-semibold tracking-tight text-slate-900">
            Selesaikan pembayaran konten digitalmu.
          </h1>
          <p class="mt-3 text-sm md:text-base text-slate-600 max-w-xl">
            Cek kembali detail produk dan data dirimu. Pembayaran akan diproses menggunakan mata uang Rupiah (IDR).
          </p>
        </div>

        {{-- Layout 2 kolom: ringkasan + form --}}
        <div class="mt-10 grid gap-6 lg:grid-cols-[1.1fr,0.9fr] items-start">
          {{-- Ringkasan Produk --}}
          <section class="rounded-2xl border border-slate-200 bg-white shadow-[0_16px_40px_rgba(15,23,42,0.06)]">
            <div class="border-b border-slate-200 px-5 py-4 md:px-6">
              <h2 class="text-sm font-semibold text-slate-900">Ringkasan Pesanan</h2>
              <p class="mt-1 text-[12px] text-slate-500">
                Pastikan produk dan jumlah sudah sesuai sebelum melanjutkan ke pembayaran.
              </p>
            </div>

            <div class="px-5 py-5 md:px-6 md:py-6 space-y-4">
              <div class="flex items-start gap-4">
                <div class="relative h-20 w-28 overflow-hidden rounded-xl bg-slate-100">
                  <img
                    src="{{ $product['image'] }}"
                    alt="{{ $product['title'] }}"
                    class="h-full w-full object-cover"
                  >
                </div>

                <div class="flex-1">
                  <h3 class="text-sm font-semibold text-slate-900">
                    {{ $product['title'] }}
                  </h3>
                  <p class="mt-0.5 text-xs text-slate-500">
                    {{ $product['type'] }} • Level: {{ $product['level'] }}
                  </p>
                  <p class="mt-2 text-xs text-slate-600">
                    {{ $product['description'] }}
                  </p>
                </div>
              </div>

              <div class="border-t border-slate-200 pt-4 space-y-2 text-sm">
                <div class="flex items-center justify-between">
                  <span class="text-slate-600">Harga</span>
                  <span class="font-medium text-slate-900">
                    Rp {{ number_format($subtotal, 0, ',', '.') }}
                  </span>
                </div>

                <div class="flex items-center justify-between text-xs">
                  <span class="text-slate-500">Jumlah</span>
                  <span class="text-slate-700">1 (konten digital)</span>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-dashed border-slate-200 mt-1">
                  <span class="text-[13px] font-semibold text-slate-800">Total Pembayaran</span>
                  <span class="text-lg font-semibold text-slate-900">
                    Rp {{ number_format($subtotal, 0, ',', '.') }}
                  </span>
                </div>

                <p class="mt-2 text-[11px] text-slate-500">
                  Semua harga sudah termasuk pajak jika berlaku. Tidak ada biaya pengiriman karena produk sepenuhnya digital.
                </p>
              </div>
            </div>
          </section>

          {{-- Form Data Pembeli --}}
          <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4 md:px-6">
              <h2 class="text-sm font-semibold text-slate-900">Data Pembeli</h2>
              <p class="mt-1 text-[12px] text-slate-500">
                Data ini akan digunakan untuk mengirimkan akses konten dan bukti pembayaran.
              </p>
            </div>

            <div class="px-5 py-5 md:px-6 md:py-6">
              <form id="checkout-form" class="space-y-4">
                <div class="space-y-1.5">
                  <label for="buyer_name" class="block text-xs font-medium text-slate-800">
                    Nama lengkap
                  </label>
                  <input
                    type="text"
                    id="buyer_name"
                    name="buyer_name"
                    required
                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200"
                    placeholder="Nama sesuai identitas"
                  >
                </div>

                <div class="space-y-1.5">
                  <label for="buyer_email" class="block text-xs font-medium text-slate-800">
                    Email
                  </label>
                  <input
                    type="email"
                    id="buyer_email"
                    name="buyer_email"
                    required
                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200"
                    placeholder="email@kamu.com"
                  >
                </div>

                <div class="space-y-1.5">
                  <label for="buyer_phone" class="block text-xs font-medium text-slate-800">
                    Nomor WhatsApp / HP
                  </label>
                  <input
                    type="tel"
                    id="buyer_phone"
                    name="buyer_phone"
                    required
                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200"
                    placeholder="08xxxxxxxxxx"
                  >
                </div>

                {{-- Hidden fields --}}
                <input type="hidden" name="product_slug" value="{{ $product['slug'] }}">
                <input type="hidden" name="amount" value="{{ $subtotal }}">
                <input type="hidden" name="currency" value="{{ $currency }}">

                <div class="space-y-3 pt-2">
                  <p class="text-[11px] text-slate-500 leading-relaxed">
                    Dengan melanjutkan pembayaran, kamu menyetujui ketentuan penggunaan dan kebijakan privasi Noorly.
                    Setelah pembayaran berhasil, akses konten digital akan dikirim melalui email.
                  </p>

                  <button
                    type="button"
                    id="pay-button"
                    class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-[#fbc926] to-sky-500 px-5 py-2.75 text-sm font-semibold text-white shadow-[0_18px_45px_rgba(79,70,229,0.45)] transition hover:brightness-110 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-50 focus-visible:ring-indigo-400"
                  >
                    <span>Lanjut ke Pembayaran</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                         viewBox="0 0 24 24" stroke="#fbc926" stroke-width="1.8">
                      <path stroke-linecap="round" stroke-linejoin="round"
                            d="M5 12h14M13 6l6 6-6 6" />
                    </svg>
                  </button>

                  <p class="text-[11px] text-slate-500 text-center">
                    Saat ini menggunakan Midtrans Sandbox. Setelah akun production aktif, mode pembayaran bisa diganti ke production.
                  </p>
                </div>
              </form>
            </div>
          </section>
        </div>
      </div>
    </section>
  </main>

  @php
    $snapUrl = config('midtrans.is_production')
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js';
  @endphp

  {{-- Snap JS --}}
  <script type="text/javascript"
          src="{{ $snapUrl }}"
          data-client-key="{{ config('midtrans.client_key') }}"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const payButton = document.getElementById('pay-button');
      const form = document.getElementById('checkout-form');

      payButton.addEventListener('click', function () {
        const payload = {
          buyer_name: form.buyer_name.value.trim(),
          buyer_email: form.buyer_email.value.trim(),
          buyer_phone: form.buyer_phone.value.trim(),
          product_slug: form.product_slug.value,
        };

        if (!payload.buyer_name || !payload.buyer_email || !payload.buyer_phone) {
          alert('Lengkapi data pembeli terlebih dahulu.');
          return;
        }

        fetch('{{ route('checkout.pay') }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
          },
          body: JSON.stringify(payload),
        })
          .then(res => res.json())
          .then(res => {
            if (res.snap_token) {
              window.snap.pay(res.snap_token, {
                onSuccess: function (result) {
                  console.log('success', result);
                  alert('Pembayaran berhasil (sandbox). Implementasi update status akan ditambahkan di backend.');
                  window.location.href = '/';
                },
                onPending: function (result) {
                  console.log('pending', result);
                  alert('Transaksi masih pending. Cek status di dashboard Midtrans sandbox.');
                },
                onError: function (result) {
                  console.error('error', result);
                  alert('Terjadi kesalahan saat memproses pembayaran.');
                },
                onClose: function () {
                  console.log('Snap popup closed');
                }
              });
            } else {
              console.error(res);
              alert(res.message || 'Gagal membuat transaksi pembayaran.');
            }
          })
          .catch(err => {
            console.error(err);
            alert('Tidak dapat terhubung ke server pembayaran.');
          });
      });
    });
  </script>
@endsection
