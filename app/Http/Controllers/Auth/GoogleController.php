<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeGoogleUser;   // <-- tambahkan ini

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')
                ->with('error', 'Gagal login dengan Google. Silakan coba lagi.');
        }

        $email = $googleUser->getEmail();

        if (! $email) {
            return redirect()->route('login')
                ->with('error', 'Akun Google tidak memiliki email yang bisa digunakan.');
        }

        // flag untuk cek user baru
        $isNewUser = false;

        // cari user berdasarkan google_id / email
        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $email)
            ->first();

        if (! $user) {
            // user baru -> buat akun
            $user = User::create([
                'name'     => $googleUser->getName() ?: $email,
                'email'    => $email,
                'google_id'=> $googleUser->getId(),
                'password' => bcrypt(Str::random(32)),
                'role'     => 'user',
                'email_verified_at' => now(),
            ]);

            $isNewUser = true;
        } else {
            // update google_id kalau sebelumnya login biasa
            if (! $user->google_id) {
                $user->google_id = $googleUser->getId();
                $user->save();
            }
        }

        // login user
        Auth::login($user, true);

        // kalau user baru -> kirim email welcome
        if ($isNewUser) {
            Mail::to($user->email)->send(new WelcomeGoogleUser($user));
        }

        // redirect sesuai role
        if ($user->isCreator()) {
            return redirect()->route('creator.dashboard');
        }

        return redirect()->route('home'); 
    }
}
