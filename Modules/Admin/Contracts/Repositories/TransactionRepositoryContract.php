<?php
// modules/Finance/Contracts/Repositories/TransactionRepositoryContract.php

namespace Modules\Admin\Contracts\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Admin\Entities\Transaction;
use Modules\Admin\Enum\RecurringFrequency;
use Modules\Admin\Enum\TransactionType;

interface TransactionRepositoryContract
{
    public function create( string $userId,
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
                            ?string $referenceNumber): Transaction;
    public function findById(string $id): ?Transaction;
    public function findByUuid(string $uuid): ?Transaction;
    public function getUserTransactions(string $userId, array $filters = [], int|null $perPage = null): Collection|LengthAwarePaginator;
    public function getUserTransactionByUuid(string $userId, string $uuid): ?Transaction;
    public function update(Transaction $transaction,
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
                           ?string $referenceNumber): Transaction;
    public function delete(Transaction $transaction): bool;
    public function getWalletTransactions(string $walletId, array $filters = [], int|null $perPage = null): Collection|LengthAwarePaginator;
    public function getCategoryTransactions(string $categoryId, array $filters = [], int|null $perPage = null): Collection|LengthAwarePaginator;
    public function getRecurringTransactions(): Collection;
    public function createRecurringInstance( Transaction $parent,
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
                                             ?string $referenceNumber): Transaction;
    public function getTransactionStats(string $userId, array $filters = []): array;
}
