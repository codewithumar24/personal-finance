<?php
// modules/Finance/Services/ReportService.php

namespace Modules\Admin\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Contracts\Services\ReportServiceContract;
use Modules\Admin\Enum\ReportPeriod;
use Modules\Admin\Enum\TransactionType;

class ReportService implements ReportServiceContract
{
    public function getIncomeVsExpense(string $userId, string $period, ?string $startDate = null, ?string $endDate = null): array
    {
        $dateRange = $this->getDateRange($period, $startDate, $endDate);

        $data = DB::table('transactions')
            ->where('user_id', $userId)
            ->whereBetween('transaction_date', [$dateRange['start'], $dateRange['end']])
            ->whereIn('type', [TransactionType::INCOME->value, TransactionType::EXPENSE->value])
            ->select(
                'type',
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('COUNT(*) as transaction_count')
            )
            ->groupBy('type')
            ->get()
            ->keyBy('type');

        return [
            'period' => $period,
            'start_date' => $dateRange['start']->format('Y-m-d'),
            'end_date' => $dateRange['end']->format('Y-m-d'),
            'income' => [
                'amount' => (float) ($data['income']->total_amount ?? 0),
                'count' => (int) ($data['income']->transaction_count ?? 0),
            ],
            'expense' => [
                'amount' => (float) ($data['expense']->total_amount ?? 0),
                'count' => (int) ($data['expense']->transaction_count ?? 0),
            ],
            'net_flow' => (float) (($data['income']->total_amount ?? 0) - ($data['expense']->total_amount ?? 0)),
        ];
    }

    public function getCategoryWiseSpending(string $userId, string $period, ?string $startDate = null, ?string $endDate = null): array
    {
        $dateRange = $this->getDateRange($period, $startDate, $endDate);

        $categories = DB::table('transactions')
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.user_id', $userId)
            ->where('transactions.type', TransactionType::EXPENSE->value)
            ->whereBetween('transactions.transaction_date', [$dateRange['start'], $dateRange['end']])
            ->select(
                'categories.id',
                'categories.name',
                'categories.color',
                'categories.icon',
                DB::raw('SUM(transactions.amount) as total_amount'),
                DB::raw('COUNT(*) as transaction_count')
            )
            ->groupBy('categories.id', 'categories.name', 'categories.color', 'categories.icon')
            ->orderBy('total_amount', 'desc')
            ->get();

        $totalExpense = $categories->sum('total_amount');

        return [
            'period' => $period,
            'start_date' => $dateRange['start']->format('Y-m-d'),
            'end_date' => $dateRange['end']->format('Y-m-d'),
            'total_expense' => (float) $totalExpense,
            'categories' => $categories->map(function ($category) use ($totalExpense) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'color' => $category->color,
                    'icon' => $category->icon,
                    'amount' => (float) $category->total_amount,
                    'count' => (int) $category->transaction_count,
                    'percentage' => $totalExpense > 0 ? round(($category->total_amount / $totalExpense) * 100, 2) : 0,
                ];
            }),
        ];
    }

    public function getWalletSummary(string $userId, string $period, ?string $startDate = null, ?string $endDate = null): array
    {
        $dateRange = $this->getDateRange($period, $startDate, $endDate);

        // Get wallet balances and transactions
        $wallets = DB::table('wallets')
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->select('id', 'name', 'type', 'balance', 'currency')
            ->get();

        $walletTransactions = DB::table('transactions')
            ->where('user_id', $userId)
            ->whereBetween('transaction_date', [$dateRange['start'], $dateRange['end']])
            ->select(
                'wallet_id',
                'type',
                DB::raw('SUM(amount) as total_amount')
            )
            ->groupBy('wallet_id', 'type')
            ->get()
            ->groupBy('wallet_id');

        return [
            'period' => $period,
            'start_date' => $dateRange['start']->format('Y-m-d'),
            'end_date' => $dateRange['end']->format('Y-m-d'),
            'wallets' => $wallets->map(function ($wallet) use ($walletTransactions) {
                $transactions = $walletTransactions->get($wallet->id, collect());

                $income = $transactions->where('type', TransactionType::INCOME->value)->sum('total_amount');
                $expense = $transactions->where('type', TransactionType::EXPENSE->value)->sum('total_amount');
                $transferIn = $transactions->where('type', TransactionType::TRANSFER->value)
                    ->filter(fn($t) => $t->wallet_id == $wallet->id) // This needs refinement for transfer logic
                    ->sum('total_amount');

                return [
                    'id' => $wallet->id,
                    'name' => $wallet->name,
                    'type' => $wallet->type,
                    'current_balance' => (float) $wallet->balance,
                    'currency' => $wallet->currency,
                    'period_income' => (float) $income,
                    'period_expense' => (float) $expense,
                    'net_flow' => (float) ($income - $expense),
                ];
            }),
            'total_balance' => $wallets->sum('balance'),
            'total_income' => $walletTransactions->flatten()->where('type', TransactionType::INCOME->value)->sum('total_amount'),
            'total_expense' => $walletTransactions->flatten()->where('type', TransactionType::EXPENSE->value)->sum('total_amount'),
        ];
    }

    public function getMonthlySummary(string $userId, int $year): array
    {
        $monthlyData = DB::table('transactions')
            ->where('user_id', $userId)
            ->whereYear('transaction_date', $year)
            ->whereIn('type', [TransactionType::INCOME->value, TransactionType::EXPENSE->value])
            ->select(
                DB::raw('MONTH(transaction_date) as month'),
                'type',
                DB::raw('SUM(amount) as total_amount')
            )
            ->groupBy('month', 'type')
            ->get();

        $summary = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthData = $monthlyData->where('month', $month);

            $income = $monthData->where('type', TransactionType::INCOME->value)->sum('total_amount');
            $expense = $monthData->where('type', TransactionType::EXPENSE->value)->sum('total_amount');

            $summary[] = [
                'month' => $month,
                'month_name' => Carbon::create()->month($month)->format('F'),
                'income' => (float) $income,
                'expense' => (float) $expense,
                'net_flow' => (float) ($income - $expense),
            ];
        }

        return [
            'year' => $year,
            'summary' => $summary,
            'total_income' => $monthlyData->where('type', TransactionType::INCOME->value)->sum('total_amount'),
            'total_expense' => $monthlyData->where('type', TransactionType::EXPENSE->value)->sum('total_amount'),
            'annual_net_flow' => $monthlyData->where('type', TransactionType::INCOME->value)->sum('total_amount')
                               - $monthlyData->where('type', TransactionType::EXPENSE->value)->sum('total_amount'),
        ];
    }

    public function getYearlySummary(string $userId, int $startYear, int $endYear): array
    {
        $yearlyData = DB::table('transactions')
            ->where('user_id', $userId)
            ->whereBetween(DB::raw('YEAR(transaction_date)'), [$startYear, $endYear])
            ->whereIn('type', [TransactionType::INCOME->value, TransactionType::EXPENSE->value])
            ->select(
                DB::raw('YEAR(transaction_date) as year'),
                'type',
                DB::raw('SUM(amount) as total_amount')
            )
            ->groupBy('year', 'type')
            ->get();

        $summary = [];
        for ($year = $startYear; $year <= $endYear; $year++) {
            $yearData = $yearlyData->where('year', $year);

            $income = $yearData->where('type', TransactionType::INCOME->value)->sum('total_amount');
            $expense = $yearData->where('type', TransactionType::EXPENSE->value)->sum('total_amount');

            $summary[] = [
                'year' => $year,
                'income' => (float) $income,
                'expense' => (float) $expense,
                'net_flow' => (float) ($income - $expense),
            ];
        }

        return [
            'period' => "{$startYear}-{$endYear}",
            'summary' => $summary,
        ];
    }

    public function getTrendAnalysis(string $userId, string $type, string $period, int $months = 12): array
    {
        $endDate = Carbon::now()->endOfMonth();
        $startDate = Carbon::now()->subMonths($months - 1)->startOfMonth();

        $trendData = DB::table('transactions')
            ->where('user_id', $userId)
            ->where('type', $type)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->select(
                DB::raw('YEAR(transaction_date) as year'),
                DB::raw('MONTH(transaction_date) as month'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('COUNT(*) as transaction_count')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $labels = [];
        $data = [];
        $counts = [];

        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $label = $currentDate->format('M Y');
            $monthData = $trendData->firstWhere('month', $currentDate->month);

            $labels[] = $label;
            $data[] = (float) ($monthData->total_amount ?? 0);
            $counts[] = (int) ($monthData->transaction_count ?? 0);

            $currentDate->addMonth();
        }

        return [
            'type' => $type,
            'period' => $period,
            'months' => $months,
            'labels' => $labels,
            'data' => $data,
            'counts' => $counts,
            'total_amount' => array_sum($data),
            'average_amount' => count($data) > 0 ? array_sum($data) / count($data) : 0,
        ];
    }

    public function getTopCategories(string $userId, string $period, int $limit = 5): array
    {
        $dateRange = $this->getDateRange($period);

        $topCategories = DB::table('transactions')
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.user_id', $userId)
            ->where('transactions.type', TransactionType::EXPENSE->value)
            ->whereBetween('transactions.transaction_date', [$dateRange['start'], $dateRange['end']])
            ->select(
                'categories.id',
                'categories.name',
                'categories.color',
                'categories.icon',
                DB::raw('SUM(transactions.amount) as total_amount')
            )
            ->groupBy('categories.id', 'categories.name', 'categories.color', 'categories.icon')
            ->orderBy('total_amount', 'desc')
            ->limit($limit)
            ->get();

        return [
            'period' => $period,
            'categories' => $topCategories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'color' => $category->color,
                    'icon' => $category->icon,
                    'amount' => (float) $category->total_amount,
                ];
            }),
        ];
    }

    public function getCashFlow(string $userId, string $startDate, string $endDate): array
    {
        $cashFlow = DB::table('transactions')
            ->where('user_id', $userId)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->whereIn('type', [TransactionType::INCOME->value, TransactionType::EXPENSE->value])
            ->select(
                'transaction_date',
                'type',
                'amount',
                'title',
                'wallet_id'
            )
            ->orderBy('transaction_date')
            ->get()
            ->groupBy('transaction_date');

        $dailyFlow = [];
        $runningBalance = 0;

        $currentDate = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        while ($currentDate <= $end) {
            $dateStr = $currentDate->format('Y-m-d');
            $dayTransactions = $cashFlow->get($dateStr, collect());

            $dayIncome = $dayTransactions->where('type', TransactionType::INCOME->value)->sum('amount');
            $dayExpense = $dayTransactions->where('type', TransactionType::EXPENSE->value)->sum('amount');
            $dayNet = $dayIncome - $dayExpense;

            $runningBalance += $dayNet;

            $dailyFlow[] = [
                'date' => $dateStr,
                'income' => (float) $dayIncome,
                'expense' => (float) $dayExpense,
                'net_flow' => (float) $dayNet,
                'running_balance' => (float) $runningBalance,
                'transactions' => $dayTransactions->count(),
            ];

            $currentDate->addDay();
        }

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'cash_flow' => $dailyFlow,
            'total_income' => collect($dailyFlow)->sum('income'),
            'total_expense' => collect($dailyFlow)->sum('expense'),
            'final_balance' => $runningBalance,
        ];
    }

    public function generatePdfReport(string $userId, array $filters): string
    {
        // This would integrate with a PDF generation library like DomPDF
        // For now, return a placeholder
        return "PDF report would be generated here with filters: " . json_encode($filters);
    }

    private function getDateRange(string $period, ?string $startDate = null, ?string $endDate = null): array
    {
        if ($period === ReportPeriod::CUSTOM->value && $startDate && $endDate) {
            return [
                'start' => Carbon::parse($startDate)->startOfDay(),
                'end' => Carbon::parse($endDate)->endOfDay(),
            ];
        }

        return ReportPeriod::from($period)->getDateRange($startDate);
    }
}
