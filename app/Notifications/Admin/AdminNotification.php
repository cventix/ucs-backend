<?php

namespace App\Notifications\Admin;

use App\Components\ShortMessageNotificationChannel\ShortMessage;
use App\Models\User;
use App\Transformers\UserPartialTransformer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class AdminNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /** @var User */
    private User $sender;

    /** @var array */
    protected array $via = ['database', FcmChannel::class, WebPushChannel::class];

    /** @var string */
    protected string $message;

    /** @var string|null */
    protected $subject;

    /** @var string|null */
    protected $smsPattern;

    /**
     * Create a new notification instance.
     *
     * @param array|null $via Via.
     * @param string $message Message.
     * @param string|null $subject Email Subject.
     * @param string|null $smsPattern SMS Pattern.
     */
    public function __construct(User $sender, array $via, string $message, ?string $subject, ?string $smsPattern = "")
    {
        $this->sender = $sender;
        $this->via = array_unique(array_merge($this->via, $via));
        $this->message = $message;
        $this->subject = $subject;
        $this->smsPattern = $smsPattern;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable Notifiable.
     *
     * @return array
     */
    public function via($notifiable): array
    {
        return $this->via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable Notifiable.
     *
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->subject)
            ->greeting(__("Hi :name,", ['name' => $notifiable->full_name]))
            ->line($this->message)
            ->line(__("Thank you for using our application!"));
    }

    /**
     * @param mixed $notifiable Notifiable.
     *
     * @return ShortMessage
     */
    public function toShortMessage($notifiable): ShortMessage
    {
        return new ShortMessage($this->message);
    }

    /**
     * @param mixed $notifiable Notifiable.
     *
     * @return string[]
     */
    public function toDatabase($notifiable): array
    {
        return [
            'message' => $this->message,
            'subject' => $this->subject,
            'via' => $this->via,
            'sender' => $this->sender->transformIt(new UserPartialTransformer()),
        ];
    }

    /**
     * @param $notifiable
     * @return FcmMessage
     */
    public function toFcm($notifiable)
    {
        $title = !empty($this->subject) ? $this->subject : "New Notification";
        return FcmMessage::create()
            //            ->setData([])
            ->setNotification(\NotificationChannels\Fcm\Resources\Notification::create()
                ->setTitle($title)
                ->setBody($this->message));
    }

    public function toWebPush($notifiable, $notification)
    {
        $title = !empty($this->subject) ? $this->subject : "New Notification";
        return (new WebPushMessage)
            ->title($title)
            ->body($this->message);
        // ->icon('/notification-icon.png')
        // ->action('View App', 'notification_action')
    }
}
