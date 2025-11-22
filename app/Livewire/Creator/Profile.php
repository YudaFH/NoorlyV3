<?php

namespace App\Livewire\Creator;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class Profile extends Component
{
    use WithFileUploads;

    public bool $sidebarOpen = true;

    // Profil dasar
    public string $name = '';
    public string $email = '';
    public ?string $bio = null;

    // Media profil
    public $avatar;                 // upload baru
    public ?string $avatar_path = null;

    public $banner;                 // upload baru
    public ?string $banner_path = null;

    // Password
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    protected string $layout = 'livewire.components.layouts.app';

    public function mount(): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $this->name  = $user->name;
        $this->email = $user->email;
        $this->bio   = $user->bio ?? null;

        $this->avatar_path = $user->avatar_path ?? null;
        $this->banner_path = $user->banner_path ?? null;
    }

    public function toggleSidebar(): void
    {
        $this->sidebarOpen = ! $this->sidebarOpen;
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDASI
    |--------------------------------------------------------------------------
    */
    protected function profileRules(): array
    {
        return [
            'name'   => ['required', 'string', 'max:191'],
            'bio'    => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'max:2048'],  // 2MB
            'banner' => ['nullable', 'image', 'max:4096'],  // 4MB
        ];
    }

    protected function passwordRules(): array
    {
        return [
            'current_password'          => ['required'],
            'new_password'              => ['required', 'string', 'min:8', 'different:current_password'],
            'new_password_confirmation' => ['required', 'same:new_password'],
        ];
    }

    public function updatedAvatar(): void
    {
        $this->validateOnly('avatar', $this->profileRules());
    }

    public function updatedBanner(): void
    {
        $this->validateOnly('banner', $this->profileRules());
    }

    /*
    |--------------------------------------------------------------------------
    | SIMPAN PROFIL (nama, bio, avatar, banner)
    |--------------------------------------------------------------------------
    */
    public function saveProfile(): void
    {
        $this->validate($this->profileRules());

        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            return;
        }

        // Profil dasar
        $user->name = $this->name;
        $user->bio  = $this->bio;

        // Avatar baru
        if ($this->avatar) {
            if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }

            $path = $this->avatar->store('avatars', 'public');
            $user->avatar_path = $path;
            $this->avatar_path = $path;
        }

        // Banner baru
        if ($this->banner) {
            if ($user->banner_path && Storage::disk('public')->exists($user->banner_path)) {
                Storage::disk('public')->delete($user->banner_path);
            }

            $path = $this->banner->store('banners', 'public');
            $user->banner_path = $path;
            $this->banner_path = $path;
        }

        $user->save();

        session()->flash('status_profile', 'Profil kreator berhasil diperbarui.');
    }

    public function removeAvatar(): void
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            return;
        }

        if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $user->avatar_path = null;
        $user->save();

        $this->avatar = null;
        $this->avatar_path = null;

        session()->flash('status_profile', 'Foto profil dihapus.');
    }

    public function removeBanner(): void
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            return;
        }

        if ($user->banner_path && Storage::disk('public')->exists($user->banner_path)) {
            Storage::disk('public')->delete($user->banner_path);
        }

        $user->banner_path = null;
        $user->save();

        $this->banner = null;
        $this->banner_path = null;

        session()->flash('status_profile', 'Banner profil dihapus.');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE PASSWORD
    |--------------------------------------------------------------------------
    */
    public function updatePassword(): void
    {
        $this->validate($this->passwordRules());

        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            return;
        }

        if (! Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Password saat ini tidak sesuai.');
            return;
        }

        $user->password = Hash::make($this->new_password);
        $user->save();

        // kosongkan field form
        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';

        session()->flash('status_password', 'Password berhasil diperbarui.');
    }

    /*
    |--------------------------------------------------------------------------
    | SWITCH ACCOUNT (logout & redirect ke login)
    |--------------------------------------------------------------------------
    */
    public function switchAccount()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return $this->redirectRoute('login');
    }

    public function render()
    {
        return view('livewire.creator.profile');
    }
}
