<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;

class ContentPublicController extends Controller
{
    public function show(string $slug)
    {
        // Ambil konten dengan slug, hanya yang sudah terbit
        $content = Content::with('user')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        // Konten lain dari kreator yang sama
        $otherContents = Content::where('user_id', $content->user_id)
            ->where('status', 'published')
            ->where('id', '!=', $content->id)
            ->orderByDesc('buyers_count')
            ->limit(4)
            ->get();

        return view('contents.show', [
            'content'       => $content,
            'otherContents' => $otherContents,
        ]);
    }
}
