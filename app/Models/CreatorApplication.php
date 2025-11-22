<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CreatorApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'tagline',
        'niche',
        'experience_level',
        'content_types',
        'social_instagram',
        'social_tiktok',
        'social_youtube',
        'portfolio_url',
        'phone',
        'about',
        'status',
        'admin_notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
