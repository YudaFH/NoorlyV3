<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserNotification;
use App\Mail\CreatorNotificationMail;
use Illuminate\Support\Facades\Mail;

class CreatorNotifier
{
    /**
     * Buat notifikasi ke kreator + (opsional) kirim email.
     *
     * @param  \App\Models\User  $user
     * @param  string  $type   balance|content|buyer|support|system
     * @param  string  $title
     * @param  string|null  $body
     * @param  array  $data   misal: ['url' => route(...), 'withdraw_id' => 1]
     * @param  bool   $sendEmail
     * @return \App\Models\UserNotification
     */
    public static function notify(
        User $user,
        string $type,
        string $title,
        ?string $body = null,
        array $data = [],
        bool $sendEmail = true
    ): UserNotification {
        // Simpan ke tabel user_notifications
        $notification = UserNotification::create([
            'user_id' => $user->id,
            'type'    => $type,
            'title'   => $title,
            'body'    => $body,
            'data'    => $data,
        ]);

        // Kirim email kalau diizinkan & user punya email
        if ($sendEmail && ! empty($user->email)) {
            try {
                Mail::to($user->email)->send(
                    new CreatorNotificationMail($notification)
                );
            } catch (\Throwable $e) {
                // Jangan bikin error kalau email gagal, cukup silent log
                // logger()->error('Failed to send creator notification mail', [
                //     'notification_id' => $notification->id,
                //     'error' => $e->getMessage(),
                // ]);
            }
        }

        return $notification;
    }
}
