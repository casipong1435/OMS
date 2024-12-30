<?php

namespace App\Services;

use Illuminate\Support\Facades\Notification;
use App\Notifications\SystemNotification;

 class SystemNotificationServices
{
/**
     * Send a notification to one or multiple users.
     *
     * @param  \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|array  $users
     * @param  array  $details
     * @return void
     */
    public function sendNotification($users, $message)
    {
        $notification = new SystemNotification($message);
        Notification::send($users, $notification);
    }

}
