<?php
// modules/Finance/Notifications/GoalMilestoneNotification.php

namespace Modules\Admin\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Admin\Enum\NotificationType;

class GoalMilestoneNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private array $goalData
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🎉 Goal Milestone Achieved!')
            ->greeting("Congratulations {$notifiable->first_name}!")
            ->line("You've reached a milestone on your goal: **{$this->goalData['goal_name']}**")
            ->line("**Current Progress:** {$this->goalData['progress_percentage']}%")
            ->line("**Amount Saved:** {$this->goalData['current_amount']}")
            ->line("**Target Amount:** {$this->goalData['target_amount']}")
            ->action('View Goal Progress', url('/goals'))
            ->line('Keep up the great work towards your financial goal!');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => NotificationType::GOAL_MILESTONE->value,
            'title' => 'Goal Milestone Reached',
            'message' => "You've reached {$this->goalData['progress_percentage']}% of your {$this->goalData['goal_name']} goal",
            'data' => $this->goalData,
            'action_url' => '/goals',
            'action_text' => 'View Goal',
        ];
    }
}
