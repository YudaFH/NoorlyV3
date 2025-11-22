<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicket extends Model
{
    protected $fillable = [
        'user_id',
        'category',
        'subject',
        'message',
        'status',
        'attachment_path',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function messages()
    {
        return $this->hasMany(SupportTicketMessage::class, 'support_ticket_id');
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
