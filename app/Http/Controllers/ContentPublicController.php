<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Content;

class ContentPublicController extends Controller
{
    public function index(Request $request)
    {
        // Ambil filter dari query string
        $search   = $request->input('q');
        $type     = $request->input('type');   // ebook, video, dll
        $price    = $request->input('price');  // all | free | paid

        // Base query: hanya konten yang sudah TERBIT
        $query = Content::query()
            ->where('status', 'published');

        // Filter: search judul
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%'.$search.'%');
            });
        }

        // Filter: tipe konten
        if ($type) {
            $query->where('type', $type);
        }

        // Filter: harga
        if ($price === 'free') {
            $query->where(function ($q) {
                $q->whereNull('price')
                  ->orWhere('price', 0);
            });
        } elseif ($price === 'paid') {
            $query->where('price', '>', 0);
        }

        $contents = $query
            ->latest()
            ->paginate(12)
            ->withQueryString(); // supaya pagination tetap bawa filter & search

        // Opsi jenis konten untuk dropdown
        $typeOptions = [
            ''         => 'Semua jenis',
            'ebook'    => 'E-book / PDF',
            'video'    => 'Kelas video',
            'webinar'  => 'Rekaman webinar',
            'template' => 'Template / file',
            'bundle'   => 'Bundle konten',
            'other'    => 'Lainnya',
        ];

        $priceOptions = [
            'all'  => 'Semua harga',
            'free' => 'Gratis',
            'paid' => 'Berbayar',
        ];

        return view('contents.index', [
            'contents'     => $contents,
            'search'       => $search,
            'type'         => $type,
            'price'        => $price ?? 'all',
            'typeOptions'  => $typeOptions,
            'priceOptions' => $priceOptions,
        ]);
    }

    public function show(string $slug)
    {
        // Ambil konten tanpa filter status dulu
        $content = Content::where('slug', $slug)->firstOrFail();

        $user = Auth::user();

        // Kalau kontennya BELUM published:
        if ($content->status !== 'published') {
            // Tidak login -> anggap tidak ada
            if (! $user) {
                abort(404);
            }

            // Login tapi:
            // - bukan pemilik konten DAN
            // - bukan admin
            if (
                $user->id !== $content->user_id &&
                ($user->role ?? null) !== 'admin'
            ) {
                abort(404);
            }
        }

        // Di titik ini:
        // - visitor umum: hanya bisa akses published
        // - kreator pemilik: boleh akses kontennya sendiri (draft/pending/published)
        // - admin: boleh akses semua

        return view('contents.show', [
            'content' => $content,
        ]);
    }
}
