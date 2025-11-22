<?php

namespace App\Mail;

use App\Models\UserNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CreatorNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public UserNotification $notification;

    /**
     * Create a new message instance.
     */
    public function __construct(UserNotification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('[Noorly] '.$this->notification->title)
            ->view('emails.creator-notification');
    }
}
