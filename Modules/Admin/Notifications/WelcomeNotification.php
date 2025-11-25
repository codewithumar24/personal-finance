<?php
// modules/Admin/Notifications/WelcomeNotification.php

namespace Modules\Admin\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class WelcomeNotification extends Notification
{
    use Queueable;

    public function __construct(
        private string $temporaryPassword
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        \Log::info('WelcomeNotification: Preparing to send email to ' . $notifiable->email);

        return (new MailMessage)
            ->subject('Welcome to Finance Manager - Your Account Has Been Created')
            ->view('emails.registration', [
                'user' => $notifiable,
                'temporaryPassword' => $this->temporaryPassword
            ]);
    }
}
