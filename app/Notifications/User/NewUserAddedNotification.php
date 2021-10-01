<?php

namespace App\Notifications\User;

use App\Components\ShortMessageNotificationChannel\ShortMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUserAddedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var string
     */
    public string $password;

    /**
     * Create a new notification instance.
     *
     * @param string $password Password.
     */
    public function __construct(string $password)
    {
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'short_message'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->view(
                'emails.new_user_added',
                [
                    'user' => $notifiable,
                    'password' => $this->password,
                ]
            );
    }
}
