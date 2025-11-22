<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'type',
        'status',
        'price',
        'views_count',
        'buyers_count',
        'revenue_total',
        'cover_path',
        'primary_file_path',
        'primary_link_url',
        'ebook_chapters',
    ];
    protected $casts = [
        'published_at' => 'datetime',
        'price'        => 'integer',
        'views_count'  => 'integer',
        'buyers_count' => 'integer',
        'revenue_total'=> 'integer',
        'ebook_chapters' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
