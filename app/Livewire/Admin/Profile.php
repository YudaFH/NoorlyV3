<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;

class Profile extends Component
{
    public bool $sidebarOpen = true;

    // form profil
    public string $name  = '';
    public string $email = '';

    // form password
    public ?string $current_password            = null;
    public ?string $new_password                = null;
    public ?string $new_password_confirmation   = null;

    public function mount(): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        if ($user) {
            $this->name  = (string) ($user->name  ?? '');
            $this->email = (string) ($user->email ?? '');
        }
    }

    public function toggleSidebar(): void
    {
        $this->sidebarOpen = ! $this->sidebarOpen;
    }

    public function updateProfile(): void
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $data = $this->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        $user->fill($data);
        $user->save();

        session()->flash('status_profile', 'Profil admin berhasil diperbarui.');
    }

    public function updatePassword(): void
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $this->validate(
            [
                'current_password'          => ['required'],
                'new_password'              => ['required', 'string', 'min:8', 'confirmed'],
                'new_password_confirmation' => ['required'],
            ],
            [
                'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            ]
        );

        if (! Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Password sekarang tidak sesuai.');
            return;
        }

        $user->password = Hash::make($this->new_password);
        $user->save();

        // reset field
        $this->current_password          = null;
        $this->new_password              = null;
        $this->new_password_confirmation = null;

        session()->flash('status_password', 'Password berhasil diperbarui.');
    }

    public function render()
    {
        /** @var User|null $user */
        $user = Auth::user();

        return view('livewire.admin.profile', [
            'user' => $user,
        ]);
    }
}
