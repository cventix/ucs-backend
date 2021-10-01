<?php


namespace App\Components;


use Illuminate\Notifications\Events\NotificationSending;

class NotificationValidityListener
{
    public function handle(NotificationSending $event)
    {
        if (method_exists($event->notification, 'isStillSendable')) {
            return !$event->notification->isStillSendable($event->notifiable);
        }

        return true;
    }
}
