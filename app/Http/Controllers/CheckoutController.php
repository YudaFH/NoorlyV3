<?php

namespace App\Http\Controllers;

use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * List produk contoh (hard-coded) – samakan dengan /konten.
     */
    protected function products(): array
    {
        return [
            [
                'title' => 'Ebook: Personal Branding untuk Karier Gen Z',
                'slug' => 'ebook-personal-branding-genz',
                'type' => 'E-book (PDF)',
                'level' => 'Pemula – Menengah',
                'price' => 59000,
                'image' => asset('assets/produk/ebook-personal-branding.png'),
                'description' => 'Panduan praktis membangun personal branding di LinkedIn, Instagram, dan dunia kerja modern.'
            ],
            [
                'title' => 'Kelas Rekaman: Produktif tanpa Burnout',
                'slug' => 'kelas-rekaman-produktif-tanpa-burnout',
                'type' => 'Kelas rekaman (Video)',
                'level' => 'Umum',
                'price' => 89000,
                'image' => asset('assets/produk/kelas-produktif-tanpa-burnout.png'),
                'description' => 'Belajar mengatur energi, fokus, dan waktu agar tetap produktif tanpa mengorbankan kesehatan mental.'
            ],
            [
                'title' => 'Template Notion: Dashboard Life & Career OS',
                'slug' => 'template-notion-life-career-os',
                'type' => 'Template Notion',
                'level' => 'Pemula – Menengah',
                'price' => 49000,
                'image' => asset('assets/produk/template-notion-life-career-os.png'),
                'description' => 'Template Notion untuk mengatur hidup, karier, habit, dan project dalam satu dashboard rapi.'
            ],
            [
                'title' => 'Audio Guide: Self-Reflection sebelum Tidur',
                'slug' => 'audio-guide-self-reflection',
                'type' => 'Audio (MP3)',
                'level' => 'Umum',
                'price' => 39000,
                'image' => asset('assets/produk/audio-self-reflection.png'),
                'description' => 'Seri audio singkat untuk refleksi harian, membantu tidur lebih tenang dan mindful.'
            ],
        ];
    }

    public function show(string $slug)
    {
        $product = collect($this->products())->firstWhere('slug', $slug);

        if (! $product) {
            abort(404);
        }

        $checkoutData = [
            'product'  => $product,
            'quantity' => 1,
            'subtotal' => $product['price'],
            'currency' => 'IDR',
        ];

        return view('checkout.show', $checkoutData);
    }

    public function pay(Request $request, MidtransService $midtrans)
    {
        // Validasi basic data pembeli
        $data = $request->validate([
            'buyer_name'   => ['required', 'string', 'max:255'],
            'buyer_email'  => ['required', 'email', 'max:255'],
            'buyer_phone'  => ['required', 'string', 'max:30'],
            'product_slug' => ['required', 'string'],
        ]);

        // Cari produk
        $product = collect($this->products())->firstWhere('slug', $data['product_slug']);

        if (! $product) {
            return response()->json([
                'message' => 'Produk tidak ditemukan.',
            ], 404);
        }

        $amount = (int) $product['price'];

        // Generate order_id unik
        $orderId = 'NOORLY-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(5));

        // Parameter ke Midtrans (Snap)
        $params = [
            'transaction_details' => [
                'order_id'      => $orderId,
                'gross_amount'  => $amount, // dalam IDR (integer)
            ],
            'item_details' => [
                [
                    'id'       => $product['slug'],
                    'price'    => $amount,
                    'quantity' => 1,
                    'name'     => substr($product['title'], 0, 50),
                ],
            ],
            'customer_details' => [
                'first_name' => $data['buyer_name'],
                'email'      => $data['buyer_email'],
                'phone'      => $data['buyer_phone'],
            ],
        ];

        try {
            $snapToken = $midtrans->createTransaction($params);

            return response()->json([
                'snap_token' => $snapToken,
                'order_id'   => $orderId,
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Gagal membuat transaksi pembayaran.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
