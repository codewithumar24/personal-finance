<?php
// modules/Finance/Contracts/Services/TransactionServiceContract.php

namespace Modules\Admin\Contracts\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Admin\DataTransfer\Requests\TransactionDTO;
use Modules\Admin\DataTransfer\Requests\UpdateTransactionDTO;
use Modules\Admin\Entities\Transaction;

interface TransactionServiceContract
{
    public function createTransaction(TransactionDTO $dto, string $userId): Transaction;
    public function getUserTransactions(string $userId, array $filters = [], int|null $perPage = null): Collection|LengthAwarePaginator;
    public function getUserTransactionByUuid(string $userId, string $uuid): ?Transaction;
    public function updateTransaction(Transaction $transaction, UpdateTransactionDTO $dto): Transaction;
    public function deleteTransaction(Transaction $transaction): bool;
    public function getTransactionStats(string $userId, array $filters = []): array;
    public function processRecurringTransactions(): void;
}
