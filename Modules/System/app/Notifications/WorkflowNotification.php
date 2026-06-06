<?php

namespace Modules\System\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkflowNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $model;

    protected $message;

    protected $subject;

    /**
     * Create a new notification instance.
     */
    public function __construct($model, string $message, ?string $subject = null)
    {
        $this->model = $model;
        $this->message = $message;
        $this->subject = $subject ?? 'Workflow Update';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        if ($notifiable instanceof AnonymousNotifiable) {
            return ['mail'];
        }

        $channels = ['database'];

        if (method_exists($notifiable, 'routeNotificationFor')) {
            $mail = $notifiable->routeNotificationFor('mail');
            if (! empty($mail)) {
                $channels[] = 'mail';
            }
        }

        return array_values(array_unique($channels));
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->subject)
            ->line($this->message)
            ->action('View Details', url('/')) // Logic for specific model URL could be added
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->message,
            'model_type' => get_class($this->model),
            'model_id' => $this->model->id,
            'reference' => $this->model->reference ?? $this->model->id,
        ];
    }
}
