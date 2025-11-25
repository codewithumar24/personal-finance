<?php
// modules/Finance/Http/Controllers/ReportController.php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Contracts\Services\ReportServiceContract;
use Modules\Admin\Enum\ReportPeriod;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportServiceContract $reportService
    ) {}

    public function incomeVsExpense(string $period): JsonResponse
    {
        $userId = Auth::id();
        $startDate = request('start_date');
        $endDate = request('end_date');

        try {
            $report = $this->reportService->getIncomeVsExpense($userId, $period, $startDate, $endDate);
            return apiResponse()->success($report);
        } catch (\Exception $e) {
            return apiResponse()->error($e->getMessage(), 400);
        }
    }

    public function categorySpending(string $period): JsonResponse
    {
        $userId = Auth::id();
        $startDate = request('start_date');
        $endDate = request('end_date');

        try {
            $report = $this->reportService->getCategoryWiseSpending($userId, $period, $startDate, $endDate);
            return apiResponse()->success($report);
        } catch (\Exception $e) {
            return apiResponse()->error($e->getMessage(), 400);
        }
    }

    public function walletSummary(string $period): JsonResponse
    {
        $userId = Auth::id();
        $startDate = request('start_date');
        $endDate = request('end_date');

        try {
            $report = $this->reportService->getWalletSummary($userId, $period, $startDate, $endDate);
            return apiResponse()->success($report);
        } catch (\Exception $e) {
            return apiResponse()->error($e->getMessage(), 400);
        }
    }

    public function monthlySummary(int $year): JsonResponse
    {
        $userId = Auth::id();

        try {
            $report = $this->reportService->getMonthlySummary($userId, $year);
            return apiResponse()->success($report);
        } catch (\Exception $e) {
            return apiResponse()->error($e->getMessage(), 400);
        }
    }

    public function yearlySummary(int $startYear, int $endYear): JsonResponse
    {
        $userId = Auth::id();

        try {
            $report = $this->reportService->getYearlySummary($userId, $startYear, $endYear);
            return apiResponse()->success($report);
        } catch (\Exception $e) {
            return apiResponse()->error($e->getMessage(), 400);
        }
    }

    public function trendAnalysis(string $type, string $period): JsonResponse
    {
        $userId = Auth::id();
        $months = request('months', 12);

        try {
            $report = $this->reportService->getTrendAnalysis($userId, $type, $period, $months);
            return apiResponse()->success($report);
        } catch (\Exception $e) {
            return apiResponse()->error($e->getMessage(), 400);
        }
    }

    public function topCategories(string $period): JsonResponse
    {
        $userId = Auth::id();
        $limit = request('limit', 5);

        try {
            $report = $this->reportService->getTopCategories($userId, $period, $limit);
            return apiResponse()->success($report);
        } catch (\Exception $e) {
            return apiResponse()->error($e->getMessage(), 400);
        }
    }

    public function cashFlow(): JsonResponse
    {
        $userId = Auth::id();
        $startDate = request('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = request('end_date', now()->format('Y-m-d'));

        try {
            $report = $this->reportService->getCashFlow($userId, $startDate, $endDate);
            return apiResponse()->success($report);
        } catch (\Exception $e) {
            return apiResponse()->error($e->getMessage(), 400);
        }
    }

    public function pdfReport(): JsonResponse
    {
        $userId = Auth::id();
        $filters = request()->all();

        try {
            $pdfPath = $this->reportService->generatePdfReport($userId, $filters);
            return apiResponse()->success(['pdf_url' => $pdfPath]);
        } catch (\Exception $e) {
            return apiResponse()->error($e->getMessage(), 400);
        }
    }

    public function reportPeriods(): JsonResponse
    {
        $periods = collect(ReportPeriod::cases())->map(function ($period) {
            return [
                'value' => $period->value,
                'label' => $period->label(),
            ];
        });

        return apiResponse()->success($periods);
    }
}
