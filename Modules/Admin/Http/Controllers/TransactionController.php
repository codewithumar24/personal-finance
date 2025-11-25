<?php
// modules/Finance/Http/Controllers/TransactionController.php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Category;
use Modules\Admin\Entities\Wallet;
use Modules\Core\Http\Requests\FilterRequest;
use Modules\Admin\Contracts\Services\TransactionServiceContract;
use Modules\Admin\Http\Requests\TransactionFilterRequest;
use Modules\Admin\Http\Requests\TransactionRequest;
use Modules\Admin\Http\Requests\UpdateTransactionRequest;
use Modules\Admin\Transformers\TransactionTransformer;

class TransactionController extends Controller
{
    public function __construct(
        private readonly TransactionServiceContract $transactionService
    ) {}

    public function index(TransactionFilterRequest $request): JsonResponse
    {
        $userId = Auth::id();
        $transactions = $this->transactionService->getUserTransactions(
            $userId,
            $request->getFilters(),
            $request->getPerPage()
        );

        return apiResponse()
            ->pagination($transactions)
            ->success(TransactionTransformer::collection($transactions));
    }

    public function store(TransactionRequest $request): JsonResponse
    {
        $userId = Auth::id();

        try {
            $transaction = $this->transactionService->createTransaction($request->getDTO(), $userId);

            return apiResponse()
                ->success(new TransactionTransformer($transaction), 'Transaction created successfully')
                ->setStatusCode(201);
        } catch (\Exception $e) {
            return apiResponse()->error($e->getMessage(), 400);
        }
    }

    public function show(string $uuid): JsonResponse
    {
        $userId = Auth::id();
        $transaction = $this->transactionService->getUserTransactionByUuid($userId, $uuid);

        if (!$transaction) {
            return apiResponse()->notFound('Transaction not found');
        }

        return apiResponse()->success(new TransactionTransformer($transaction));
    }

    public function update(UpdateTransactionRequest $request, string $uuid): JsonResponse
    {
        $userId = Auth::id();
        $transaction = $this->transactionService->getUserTransactionByUuid($userId, $uuid);

        if (!$transaction) {
            return apiResponse()->notFound('Transaction not found');
        }

        try {
            $updatedTransaction = $this->transactionService->updateTransaction($transaction, $request->getDTO());

            return apiResponse()
                ->success(new TransactionTransformer($updatedTransaction), 'Transaction updated successfully');
        } catch (\Exception $e) {
            return apiResponse()->error($e->getMessage(), 400);
        }
    }

    public function destroy(string $uuid): JsonResponse
    {
        $userId = Auth::id();
        $transaction = $this->transactionService->getUserTransactionByUuid($userId, $uuid);

        if (!$transaction) {
            return apiResponse()->notFound('Transaction not found');
        }

        try {
            $this->transactionService->deleteTransaction($transaction);

            return apiResponse()->success(null, 'Transaction deleted successfully');
        } catch (\Exception $e) {
            return apiResponse()->error($e->getMessage(), 400);
        }
    }

    public function stats(TransactionFilterRequest $request): JsonResponse
    {
        $userId = Auth::id();
        $stats = $this->transactionService->getTransactionStats($userId, $request->getFilters());

        return apiResponse()->success($stats);
    }

    public function walletTransactions(string $walletUuid, TransactionFilterRequest $request): JsonResponse
    {
        $userId = Auth::id();
        $wallet = Wallet::where('wallet_uuid', $walletUuid)
            ->where('user_id', $userId)
            ->first();

        if (!$wallet) {
            return apiResponse()->notFound('Wallet not found');
        }

        $transactions = $this->transactionService->getUserTransactions(
            $userId,
            array_merge($request->getFilters(), ['wallet_id' => $walletUuid]),
            $request->getPerPage()
        );

        return apiResponse()
            ->pagination($transactions)
            ->success(TransactionTransformer::collection($transactions));
    }

    public function categoryTransactions(string $categoryUuid, TransactionFilterRequest $request): JsonResponse
    {
        $userId = Auth::id();
        $category = Category::where('category_uuid', $categoryUuid)
            ->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)->orWhere('is_default', true);
            })
            ->first();

        if (!$category) {
            return apiResponse()->notFound('Category not found');
        }

        $transactions = $this->transactionService->getUserTransactions(
            $userId,
            array_merge($request->getFilters(), ['category_id' => $categoryUuid]),
            $request->getPerPage()
        );

        return apiResponse()
            ->pagination($transactions)
            ->success(TransactionTransformer::collection($transactions));
    }
}
