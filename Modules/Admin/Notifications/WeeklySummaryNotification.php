<?php
// modules/Finance/Notifications/WeeklySummaryNotification.php

namespace Modules\Admin\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Admin\Enum\NotificationType;

class WeeklySummaryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private array $summaryData
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('📊 Your Weekly Financial Summary')
            ->greeting("Hello {$notifiable->first_name}!")
            ->line('Here is your weekly financial summary:')
            ->line("**Total Income:** {$this->summaryData['total_income']}")
            ->line("**Total Expenses:** {$this->summaryData['total_expense']}")
            ->line("**Net Flow:** {$this->summaryData['net_flow']}")
            ->line("**Top Spending Category:** {$this->summaryData['top_category']}")
            ->action('View Detailed Report', url('/reports'))
            ->line('Thank you for using Finance Manager!');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => NotificationType::WEEKLY_SUMMARY->value,
            'title' => 'Weekly Summary',
            'message' => "Weekly summary: Income {$this->summaryData['total_income']}, Expenses {$this->summaryData['total_expense']}",
            'data' => $this->summaryData,
            'action_url' => '/reports',
            'action_text' => 'View Report',
        ];
    }
}
