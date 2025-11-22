<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
    'name',
    'email',
    'password',
    'role',
    'phone',
    'phone_verified',
    'phone_verified_at',
    'creator_name',
    'main_content_type',
    'google_id', 
];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

     /** Cek apakah user ini kreator */
    public function isCreator(): bool
    {
        return $this->role === 'creator';
    }

    /** Cek apakah user ini user biasa */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function payoutMethods()
    {
        return $this->hasMany(\App\Models\PayoutMethod::class);
    }

    public function hasVerifiedPayoutMethod(): bool
    {
        return $this->payoutMethods()
            ->where('status', 'verified')
            ->exists();
    }


    

}
