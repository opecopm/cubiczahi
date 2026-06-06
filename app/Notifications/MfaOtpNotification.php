<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MfaOtpNotification extends Notification
{
    use Queueable;

    public function __construct(public string $otp) {}

    public function via(): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Login Verification Code')
            ->greeting('Hello ' . $notifiable->first_name . '!')
            ->line('Use the code below to complete your login. It expires in **10 minutes**.')
            ->line('')
            ->line('## ' . $this->otp)
            ->line('')
            ->line('If you did not attempt to log in, please secure your account immediately.')
            ->salutation('— ' . config('app.name'));
    }
}
