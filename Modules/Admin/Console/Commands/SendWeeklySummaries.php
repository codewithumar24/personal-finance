<?php
namespace Modules\Admin\Console\Commands;

use Illuminate\Console\Command;
use Modules\Admin\Contracts\Services\NotificationServiceContract;
use Modules\Admin\Contracts\Services\ReportServiceContract;
use Modules\User\Entities\User;

class SendWeeklySummaries extends Command
{
    protected $signature = 'finance:send-weekly-summaries';
    protected $description = 'Send weekly financial summaries to users';

    public function handle(
        NotificationServiceContract $notificationService,
        ReportServiceContract $reportService
    ): int {
        $this->info('Sending weekly summaries...');

        $users = User::where('is_active', true)->get();
        $sentCount = 0;

        foreach ($users as $user) {
            try {
                // Get weekly summary data
                $summary = $reportService->getIncomeVsExpense($user->id, 'weekly');
                $topCategories = $reportService->getTopCategories($user->id, 'weekly', 1);

                $summaryData = [
                    'total_income' => $summary['income']['amount'] ?? 0,
                    'total_expense' => $summary['expense']['amount'] ?? 0,
                    'net_flow' => $summary['net_flow'] ?? 0,
                    'top_category' => $topCategories['categories'][0]['name'] ?? 'N/A',
                ];

                $notificationService->sendSystemNotification(
                    $user->id,
                    'weekly_summary',
                    $summaryData
                );

                $sentCount++;
                $this->info("Sent weekly summary to {$user->email}");
            } catch (\Exception $e) {
                $this->error("Error sending weekly summary to {$user->email}: " . $e->getMessage());
            }
        }

        $this->info("Sent {$sentCount} weekly summaries total.");
        return Command::SUCCESS;
    }
}
