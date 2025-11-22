<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Content;
use Illuminate\Http\Request;

class CreatorPublicController extends Controller
{
    public function show(Request $request, $creatorId)
    {
        // Ambil data kreator
        $creator = User::query()
            ->where('id', $creatorId)
            ->firstOrFail(); // 404 kalau tidak ada

        // Ambil user yang lagi login (kalau ada)
        $authUser = $request->user();

        // Mode preview: hanya untuk kreator itu sendiri
        $isOwnerPreview = $request->boolean('preview')
            && $authUser
            && (int) $authUser->id === (int) $creator->id;

        $contentsQuery = Content::query()
            ->where('user_id', $creator->id);

        if ($isOwnerPreview) {
            // Owner bisa lihat draft + pending + published (kalau pakai preview=1)
            $contentsQuery->whereIn('status', ['draft', 'pending_review', 'published']);
        } else {
            // Publik cuma lihat konten yang sudah terbit
            $contentsQuery->where('status', 'published');
        }

        $contents = $contentsQuery
            ->orderByDesc('created_at')
            ->paginate(9);

        return view('creators.public-show', [
            'creator'        => $creator,
            'contents'       => $contents,
            'isOwnerPreview' => $isOwnerPreview,
        ]);
    }
}
