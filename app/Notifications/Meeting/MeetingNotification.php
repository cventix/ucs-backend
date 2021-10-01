<?php

namespace App\Notifications\Meeting;

use App\Models\Meeting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class MeetingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Meeting
     */
    public Meeting $meeting;

    /**
     * MeetingNotification constructor.
     *
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
    public function via($notifiable)
    {
        return ['mail', 'database', WebPushChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        $joinLink = route('meetings.join', ['meeting' => $this->meeting->id, 'user' => $notifiable->id]);

        return (new MailMessage)
            ->subject(__("Meeting session"))
            ->greeting(__("Hi :name,", ['name' => $notifiable->full_name]))
            ->line(__("A meeting session has been set for you on :date", ['date' => $this->meeting->started_at->format('Y/m/d H:i')]))
            ->action(__("Join Link"), $joinLink)
            ->line(__("Thank you for using our application!"));
    }

    /**
     * @param mixed $notifiable Notifiable.
     *
     * @return string[]
     */
    public function toDatabase($notifiable): array
    {
        $message = __("A meeting session has been set for you on :date", ['date' => $this->meeting->started_at->format('Y/m/d H:i')]);

        return [
            'message' => $message,
            'subject' => __("Meeting session"),
            'via' => $this->via($notifiable),
        ];
    }

    public function toWebPush($notifiable, $notification)
    {
        $title = __("Meeting session");
        $message = __("A meeting session has been set for you on :date", ['date' => $this->meeting->started_at->format('Y/m/d H:i')]);

        return (new WebPushMessage)
            ->title($title)
            ->body($message);
        // ->icon('/notification-icon.png')
        // ->action('View App', 'notification_action')
    }
}
