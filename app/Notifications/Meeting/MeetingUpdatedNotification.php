<?php

namespace App\Notifications\Meeting;

use App\Models\Meeting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;

class MeetingUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Meeting
     */
    public Meeting $meeting;

    /**
     * MeetingUpdatedNotification constructor.
     * @param Meeting $meeting Meeting.
     */
    public function __construct(Meeting $meeting)
    {
        $this->meeting = $meeting;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['mail', FcmChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Meeting has been updated')
            ->view(
                'emails.update_meeting_notification',
                [
                    'meeting' => $this->meeting,
                    'user' => $notifiable,
                ]
            );
    }
}
