<?php
namespace Modules\Admin\Console\Commands;

use Illuminate\Console\Command;
use Modules\Admin\Contracts\Services\TransactionServiceContract;

class ProcessRecurringTransactions extends Command
{
    protected $signature = 'finance:process-recurring-transactions';
    protected $description = 'Process recurring transactions and create new instances';

    public function handle(TransactionServiceContract $transactionService): int
    {
        $this->info('Processing recurring transactions...');

        try {
            $transactionService->processRecurringTransactions();
            $this->info('Recurring transactions processed successfully.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error processing recurring transactions: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
