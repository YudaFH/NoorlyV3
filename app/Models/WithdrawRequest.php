<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WithdrawRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'status',
        'method_type',
        'method_label',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
