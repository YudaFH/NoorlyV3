<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayoutMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'provider_code',
        'provider_name',
        'account_name',
        'account_number',
        'is_default',
        'status',
        'status_note',
        'verified_by',
        'verified_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
