<?php

namespace App\Http\Controllers;

use App\Models\CreatorApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreatorOnboardingController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Kalau sudah kreator, langsung arahkan ke dashboard kreator (kalau ada)
        if (method_exists($user, 'isCreator') && $user->isCreator()) {
            if (route_has('creator.dashboard')) {
                return redirect()
                    ->route('creator.dashboard')
                    ->with('status_creator', 'Kamu sudah menjadi kreator di Noorly.');
            }

            return redirect('/')
                ->with('status_creator', 'Kamu sudah menjadi kreator di Noorly.');
        }

        // Ambil pengajuan terakhir (kalau ada)
        $application = CreatorApplication::where('user_id', $user->id)
            ->latest()
            ->first();

        return view('creators.onboarding', [
            'user'        => $user,
            'application' => $application,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        // Kalau sudah kreator, tidak perlu daftar lagi
        if (method_exists($user, 'isCreator') && $user->isCreator()) {
            if (route_has('creator.dashboard')) {
                return redirect()
                    ->route('creator.dashboard')
                    ->with('status_creator', 'Kamu sudah menjadi kreator di Noorly.');
            }

            return redirect('/')
                ->with('status_creator', 'Kamu sudah menjadi kreator di Noorly.');
        }

        $data = $request->validate([
            'full_name'        => ['required', 'string', 'max:191'],
            'tagline'          => ['nullable', 'string', 'max:191'],
            'niche'            => ['nullable', 'string', 'max:191'],
            'experience_level' => ['required', 'in:pemula,menengah,berpengalaman'],
            'content_types'    => ['nullable', 'array'],
            'content_types.*'  => ['string'],
            'social_instagram' => ['nullable', 'string', 'max:191'],
            'social_tiktok'    => ['nullable', 'string', 'max:191'],
            'social_youtube'   => ['nullable', 'string', 'max:191'],
            'portfolio_url'    => ['nullable', 'url', 'max:191'],
            'phone'            => ['nullable', 'string', 'max:50'],
            'about'            => ['nullable', 'string', 'max:2000'],
        ]);

        // Gabung content_types (checkbox) jadi string
        $contentTypes = $data['content_types'] ?? [];
        unset($data['content_types']);

        $application = CreatorApplication::create([
            'user_id'        => $user->id,
            'full_name'      => $data['full_name'],
            'tagline'        => $data['tagline'] ?? null,
            'niche'          => $data['niche'] ?? null,
            'experience_level' => $data['experience_level'],
            'content_types'  => implode(', ', $contentTypes),
            'social_instagram' => $data['social_instagram'] ?? null,
            'social_tiktok'    => $data['social_tiktok'] ?? null,
            'social_youtube'   => $data['social_youtube'] ?? null,
            'portfolio_url'    => $data['portfolio_url'] ?? null,
            'phone'            => $data['phone'] ?? null,
            'about'            => $data['about'] ?? null,
            'status'           => 'pending',
        ]);

        // TODO (opsional): kirim notifikasi ke admin kalau mau

        return redirect()
            ->route('creators.onboarding')
            ->with('status_creator', 'Pengajuan kreator berhasil dikirim. Tim Noorly akan meninjau dalam ±1 hari kerja.');
    }
}

/**
 * Helper kecil untuk cek route exist tanpa melempar error
 */
if (! function_exists('route_has')) {
    function route_has(string $name): bool
    {
        return app('router')->has($name);
    }
}
