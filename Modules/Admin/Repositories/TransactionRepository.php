<?php
// modules/Finance/Repositories/TransactionRepository.php

namespace Modules\Admin\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Admin\Contracts\Repositories\TransactionRepositoryContract;
use Modules\Admin\Entities\Transaction;
use Modules\Admin\Enum\RecurringFrequency;
use Modules\Admin\Enum\TransactionType;

class TransactionRepository implements TransactionRepositoryContract
{
    public function __construct(private readonly Transaction $model) {}

    public function create(
        string $userId,
        string $walletId,
        string $categoryId,
        TransactionType $type,
        string $title,
        float $amount,
        string $transactionDate,
        ?string $description,
        ?string $fromWalletId,
        ?string $toWalletId,
        ?array $tags,
        bool $isRecurring,
        ?RecurringFrequency $recurringFrequency,
        ?string $recurringEndDate,
        ?string $referenceNumber
    ): Transaction {
        $objQuery = $this->model->newQuery();

        return $objQuery->create([
            'transaction_uuid' => Str::uuid(),
            'user_id' => $userId,
            'wallet_id' => $walletId,
            'category_id' => $categoryId,
            'type' => $type->value,
            'title' => $title,
            'amount' => $amount,
            'transaction_date' => $transactionDate,
            'description' => $description,
            'from_wallet_id' => $fromWalletId,
            'to_wallet_id' => $toWalletId,
            'tags' => $tags,
            'is_recurring' => $isRecurring,
            'recurring_frequency' => $recurringFrequency?->value,
            'recurring_end_date' => $recurringEndDate,
            'reference_number' => $referenceNumber,
        ]);
    }

    public function findById(string $id): ?Transaction
    {
        return $this->model->find($id);
    }

    public function findByUuid(string $uuid): ?Transaction
    {
        return $this->model->where('transaction_uuid', $uuid)->first();
    }

    public function getUserTransactions(string $userId, array $filters = [], int|null $perPage = null): Collection|LengthAwarePaginator
    {
        $query = $this->model->with(['wallet', 'category', 'fromWallet', 'toWallet'])
            ->where('user_id', $userId);

        $this->applyFilters($query, $filters);

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'transaction_date';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    public function getUserTransactionByUuid(string $userId, string $uuid): ?Transaction
    {
        return $this->model->with(['wallet', 'category', 'fromWallet', 'toWallet'])
            ->where('user_id', $userId)
            ->where('transaction_uuid', $uuid)
            ->first();
    }

    public function update(
        Transaction $transaction,
        ?string $walletId,
        ?string $categoryId,
        ?TransactionType $type,
        ?string $title,
        ?float $amount,
        ?string $transactionDate,
        ?string $description,
        ?string $fromWalletId,
        ?string $toWalletId,
        ?array $tags,
        ?bool $isRecurring,
        ?RecurringFrequency $recurringFrequency,
        ?string $recurringEndDate,
        ?string $referenceNumber
    ): Transaction {
        if (is_string($walletId) && $transaction->wallet_id !== $walletId) {
            $transaction->wallet_id = $walletId;
        }
        if (is_string($categoryId) && $transaction->category_id !== $categoryId) {
            $transaction->category_id = $categoryId;
        }
        if ($type instanceof TransactionType && $transaction->type !== $type->value) {
            $transaction->type = $type->value;
        }
        if (is_string($title) && $transaction->title !== $title) {
            $transaction->title = $title;
        }
        if (!is_null($amount) && $transaction->amount !== $amount) {
            $transaction->amount = $amount;
        }
        if (is_string($transactionDate) && $transaction->transaction_date !== $transactionDate) {
            $transaction->transaction_date = $transactionDate;
        }
        if (!is_null($description) && $transaction->description !== $description) {
            $transaction->description = $description;
        }
        if (!is_null($fromWalletId) && $transaction->from_wallet_id !== $fromWalletId) {
            $transaction->from_wallet_id = $fromWalletId;
        }
        if (!is_null($toWalletId) && $transaction->to_wallet_id !== $toWalletId) {
            $transaction->to_wallet_id = $toWalletId;
        }
        if (!is_null($tags) && $transaction->tags !== $tags) {
            $transaction->tags = $tags;
        }
        if (is_bool($isRecurring) && $transaction->is_recurring !== $isRecurring) {
            $transaction->is_recurring = $isRecurring;
        }
        if ($recurringFrequency instanceof RecurringFrequency && $transaction->recurring_frequency !== $recurringFrequency->value) {
            $transaction->recurring_frequency = $recurringFrequency->value;
        }
        if (!is_null($recurringEndDate) && $transaction->recurring_end_date !== $recurringEndDate) {
            $transaction->recurring_end_date = $recurringEndDate;
        }
        if (!is_null($referenceNumber) && $transaction->reference_number !== $referenceNumber) {
            $transaction->reference_number = $referenceNumber;
        }

        $transaction->save();
        return $transaction->fresh(['wallet', 'category', 'fromWallet', 'toWallet']);
    }

    public function delete(Transaction $transaction): bool
    {
        return $transaction->delete();
    }

    public function getWalletTransactions(string $walletId, array $filters = [], int|null $perPage = null): Collection|LengthAwarePaginator
    {
        $query = $this->model->with(['wallet', 'category', 'fromWallet', 'toWallet'])
            ->where(function ($q) use ($walletId) {
                $q->where('wallet_id', $walletId)
                  ->orWhere('from_wallet_id', $walletId)
                  ->orWhere('to_wallet_id', $walletId);
            });

        $this->applyFilters($query, $filters);
        $query->orderBy('transaction_date', 'desc');

        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    public function getCategoryTransactions(string $categoryId, array $filters = [], int|null $perPage = null): Collection|LengthAwarePaginator
    {
        $query = $this->model->with(['wallet', 'category', 'fromWallet', 'toWallet'])
            ->where('category_id', $categoryId);

        $this->applyFilters($query, $filters);
        $query->orderBy('transaction_date', 'desc');

        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    public function getRecurringTransactions(): Collection
    {
        return $this->model->with(['user', 'wallet', 'category'])
            ->where('is_recurring', true)
            ->whereNull('parent_transaction_id') // Only parent recurring transactions
            ->where(function ($q) {
                $q->whereNull('recurring_end_date')
                  ->orWhere('recurring_end_date', '>=', now());
            })
            ->get();
    }

    public function createRecurringInstance(
        Transaction $parent,
        string $userId,
        string $walletId,
        string $categoryId,
        TransactionType $type,
        string $title,
        float $amount,
        string $transactionDate,
        ?string $description,
        ?string $fromWalletId,
        ?string $toWalletId,
        ?array $tags,
        ?string $referenceNumber
    ): Transaction {
        return $this->create(
            $userId,
            $walletId,
            $categoryId,
            $type,
            $title,
            $amount,
            $transactionDate,
            $description,
            $fromWalletId,
            $toWalletId,
            $tags,
            false, // Child instances are not recurring
            null,  // No recurring frequency for child instances
            null,  // No recurring end date for child instances
            $referenceNumber
        );
    }

    public function getTransactionStats(string $userId, array $filters = []): array
    {
        $query = $this->model->where('user_id', $userId);
        $this->applyFilters($query, $filters);

        $stats = $query->select(
            DB::raw('COUNT(*) as total_transactions'),
            DB::raw('SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as total_income'),
            DB::raw('SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as total_expense'),
            DB::raw('MAX(transaction_date) as last_transaction_date'),
            DB::raw('AVG(amount) as average_amount')
        )->first();

        $categoryStats = $this->model
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.user_id', $userId)
            ->where('transactions.type', TransactionType::EXPENSE->value)
            ->when(!empty($filters['date_from']), function ($q) use ($filters) {
                $q->where('transaction_date', '>=', $filters['date_from']);
            })
            ->when(!empty($filters['date_to']), function ($q) use ($filters) {
                $q->where('transaction_date', '<=', $filters['date_to']);
            })
            ->groupBy('categories.id', 'categories.name')
            ->select(
                'categories.id',
                'categories.name',
                DB::raw('SUM(transactions.amount) as total_amount'),
                DB::raw('COUNT(*) as transaction_count')
            )
            ->orderBy('total_amount', 'desc')
            ->limit(10)
            ->get();

        return [
            'total_transactions' => $stats->total_transactions ?? 0,
            'total_income' => $stats->total_income ?? 0,
            'total_expense' => $stats->total_expense ?? 0,
            'net_flow' => ($stats->total_income ?? 0) - ($stats->total_expense ?? 0),
            'last_transaction_date' => $stats->last_transaction_date,
            'average_amount' => $stats->average_amount ?? 0,
            'top_expense_categories' => $categoryStats,
        ];
    }

    private function applyFilters($query, array $filters): void
    {
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['wallet_id'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('wallet_id', $filters['wallet_id'])
                  ->orWhere('from_wallet_id', $filters['wallet_id'])
                  ->orWhere('to_wallet_id', $filters['wallet_id']);
            });
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->where('transaction_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('transaction_date', '<=', $filters['date_to']);
        }

        if (!empty($filters['amount_min'])) {
            $query->where('amount', '>=', $filters['amount_min']);
        }

        if (!empty($filters['amount_max'])) {
            $query->where('amount', '<=', $filters['amount_max']);
        }

        if (!empty($filters['tags'])) {
            $query->whereJsonContains('tags', $filters['tags']);
        }

        if (isset($filters['is_recurring'])) {
            $query->where('is_recurring', $filters['is_recurring']);
        }
    }
}
