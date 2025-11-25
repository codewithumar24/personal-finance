<?php
namespace Modules\Admin\Console\Commands;

use Illuminate\Console\Command;
use Modules\Admin\Contracts\Services\BudgetServiceContract;
use Modules\User\Entities\User;

class CheckBudgetAlerts extends Command
{
    protected $signature = 'finance:check-budget-alerts';
    protected $description = 'Check and send budget alerts to users';

    public function handle(BudgetServiceContract $budgetService): int
    {
        $this->info('Checking budget alerts...');

        $users = User::whereHas('budgets', function ($query) {
            $query->where('is_active', true);
        })->get();

        $totalAlerts = 0;

        foreach ($users as $user) {
            try {
                $alerts = $budgetService->checkAndSendBudgetAlerts($user->id);
                $totalAlerts += count($alerts);

                if (count($alerts) > 0) {
                    $this->info("Sent " . count($alerts) . " budget alerts to user {$user->email}");
                }
            } catch (\Exception $e) {
                $this->error("Error sending budget alerts to user {$user->email}: " . $e->getMessage());
            }
        }

        $this->info("Sent {$totalAlerts} budget alerts total.");
        return Command::SUCCESS;
    }
}
