<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    // Kalau tabelmu namanya 'orders', tidak perlu $table.
    // Kalau ternyata pakai nama lain (mis: 'transactions'), tambahkan:
    // protected $table = 'transactions';

    protected $fillable = [
        'user_id',
        'creator_id',
        'content_id',
        'amount',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'amount'  => 'float',
        'paid_at' => 'datetime',
    ];

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class, 'content_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
