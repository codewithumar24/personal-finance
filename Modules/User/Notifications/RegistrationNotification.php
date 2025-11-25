<?php
// modules/User/Notifications/RegistrationNotification.php

namespace Modules\User\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class RegistrationNotification extends Notification
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Account Registered Successfully')
            ->greeting('Hello ' . $notifiable->first_name . '!')
            ->line('Your account has been successfully registered.')
            ->line('Thank you for joining our application!')
            ->line('If you did not create an account, no further action is required.');
    }
}
