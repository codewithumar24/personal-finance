<?php
// modules/Finance/Notifications/BudgetExceededNotification.php

namespace Modules\Admin\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Admin\Enum\NotificationType;

class BudgetExceededNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private array $budgetData
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🚨 Budget Exceeded Alert')
            ->greeting("Hello {$notifiable->first_name}!")
            ->line("Your budget for **{$this->budgetData['category_name']}** has been exceeded.")
            ->line("**Budget Amount:** {$this->budgetData['budget_amount']}")
            ->line("**Amount Spent:** {$this->budgetData['spent_amount']}")
            ->line("**Overspent By:** {$this->budgetData['overspent_amount']}")
            ->action('View Budget Details', url('/budgets'))
            ->line('Consider reviewing your spending in this category.');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => NotificationType::BUDGET_EXCEEDED->value,
            'title' => 'Budget Exceeded',
            'message' => "Budget for {$this->budgetData['category_name']} has been exceeded by {$this->budgetData['overspent_amount']}",
            'data' => $this->budgetData,
            'action_url' => '/budgets',
            'action_text' => 'View Budget',
        ];
    }
}
