<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicketMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'support_ticket_id',
        'user_id',
        'sender_type',
        'message',
    ];

    /**
     * Relasi ke tiket utama.
     */
    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'support_ticket_id');
    }

    /**
     * Relasi ke user (bisa user biasa atau admin).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
