<?php

namespace App\Support;

use App\Models\User;

class Notifier
{
    public static function notify(User $user, string $type, string $title, string $body): void
    {
        $user->notifications()->create([
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'data' => ['type' => $type],
        ]);
    }
}