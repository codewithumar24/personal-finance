<?php
namespace Modules\Admin\Contracts\Services;

use Illuminate\Support\Collection;

interface ReportServiceContract
{
    public function getIncomeVsExpense(string $userId, string $period, ?string $startDate = null, ?string $endDate = null): array;
    public function getCategoryWiseSpending(string $userId, string $period, ?string $startDate = null, ?string $endDate = null): array;
    public function getWalletSummary(string $userId, string $period, ?string $startDate = null, ?string $endDate = null): array;
    public function getMonthlySummary(string $userId, int $year): array;
    public function getYearlySummary(string $userId, int $startYear, int $endYear): array;
    public function getTrendAnalysis(string $userId, string $type, string $period, int $months = 12): array;
    public function getTopCategories(string $userId, string $period, int $limit = 5): array;
    public function getCashFlow(string $userId, string $startDate, string $endDate): array;
    public function generatePdfReport(string $userId, array $filters): string;
}
