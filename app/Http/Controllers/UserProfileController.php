<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserProfileController extends Controller
{
    public function edit(Request $request)
    {
        /** @var User $user */
        $user = $request->user(); // sama dengan Auth::user()

        return view('user.profile', [
            'user' => $user,
        ]);
    }

    public function updateProfile(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            // kalau mau izinkan ganti email:
            // 'email'  => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'avatar' => ['nullable', 'image', 'max:2048'], // maks 2MB
        ]);

        $user->name = $validated['name'];

        // kalau email boleh diganti, buka komentar ini:
        // if (isset($validated['email']) && $validated['email'] !== $user->email) {
        //     $user->email = $validated['email'];
        // }

        // upload avatar (opsional)
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $path = $file->store('avatars', 'public'); // storage/app/public/avatars/...

            // di navbar kamu pakai $user->avatar_url
            $user->avatar_url = '/storage/' . $path;
        }

        $user->save();

        return back()->with('profile_updated', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Password sekarang tidak sesuai.'])
                ->with('password_error', true);
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        return back()->with('password_updated', 'Password berhasil diubah.');
    }
}
