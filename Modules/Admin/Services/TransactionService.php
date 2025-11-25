<?php
// modules/Finance/Services/TransactionService.php

namespace Modules\Admin\Services;

use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Contracts\Repositories\TransactionRepositoryContract;
use Modules\Admin\Contracts\Repositories\WalletRepositoryContract;
use Modules\Admin\Contracts\Services\TransactionServiceContract;
use Modules\Admin\DataTransfer\Requests\TransactionDTO;
use Modules\Admin\DataTransfer\Requests\UpdateTransactionDTO;
use Modules\Admin\Entities\Category;
use Modules\Admin\Entities\Transaction;
use Modules\Admin\Entities\Wallet;
use Modules\Admin\Enum\TransactionType;

class TransactionService implements TransactionServiceContract
{
    public function __construct(
        private readonly TransactionRepositoryContract $transactionRepository,
        private readonly WalletRepositoryContract $walletRepository
    ) {}

    public function createTransaction(TransactionDTO $dto, string $userId): Transaction
    {
        return DB::transaction(function () use ($dto, $userId) {
            // Get wallet and category IDs from UUIDs
            $wallet = $this->walletRepository->findByUuid($dto->getWalletId());
            $category = Category::where('category_uuid', $dto->getCategoryId())->first();

            if (!$wallet || $wallet->user_id != $userId) {
                throw new \Exception('Invalid wallet');
            }

            if (!$category) {
                throw new \Exception('Invalid category');
            }

            $fromWalletId = null;
            $toWalletId = null;

            // Handle transfer transactions
            if ($dto->getType() === TransactionType::TRANSFER) {
                $fromWallet = $this->walletRepository->findByUuid($dto->getFromWalletId());
                $toWallet = $this->walletRepository->findByUuid($dto->getToWalletId());

                if (!$fromWallet || !$toWallet || $fromWallet->user_id != $userId || $toWallet->user_id != $userId) {
                    throw new \Exception('Invalid transfer wallets');
                }

                $fromWalletId = $fromWallet->id;
                $toWalletId = $toWallet->id;
            }

            // Create transaction using individual parameters
            $transaction = $this->transactionRepository->create(
                $userId,
                $wallet->id,
                $category->id,
                $dto->getType(),
                $dto->getTitle(),
                $dto->getAmount(),
                $dto->getTransactionDate(),
                $dto->getDescription(),
                $fromWalletId,
                $toWalletId,
                $dto->getTags(),
                $dto->getIsRecurring() ?? false,
                $dto->getRecurringFrequency(),
                $dto->getRecurringEndDate(),
                $dto->getReferenceNumber()
            );

            // Update wallet balances
            $this->updateWalletBalances($transaction, 'create');

            // Handle attachment upload
            if (request()->hasFile('attachment')) {
                $this->uploadAttachment($transaction, request()->file('attachment'));
            }

            return $transaction->load(['wallet', 'category', 'fromWallet', 'toWallet']);
        });
    }

    public function getUserTransactions(string $userId, array $filters = [], int|null $perPage = null): Collection|LengthAwarePaginator
    {
        return $this->transactionRepository->getUserTransactions($userId, $filters, $perPage);
    }

    public function getUserTransactionByUuid(string $userId, string $uuid): ?Transaction
    {
        return $this->transactionRepository->getUserTransactionByUuid($userId, $uuid);
    }

    public function updateTransaction(Transaction $transaction, UpdateTransactionDTO $dto): Transaction
    {
        return DB::transaction(function () use ($transaction, $dto) {
            // Store old values for balance reversal
            $this->reverseWalletBalances($transaction);

            // Prepare update parameters
            $walletId = null;
            $categoryId = null;
            $type = null;
            $title = null;
            $amount = null;
            $transactionDate = null;
            $description = null;
            $fromWalletId = null;
            $toWalletId = null;
            $tags = null;
            $isRecurring = null;
            $recurringFrequency = null;
            $recurringEndDate = null;
            $referenceNumber = null;

            if ($dto->hasWalletId()) {
                $wallet = $this->walletRepository->findByUuid($dto->getWalletId());
                if ($wallet && $wallet->user_id == $transaction->user_id) {
                    $walletId = $wallet->id;
                }
            }

            if ($dto->hasCategoryId()) {
                $category = Category::where('category_uuid', $dto->getCategoryId())->first();
                if ($category) {
                    $categoryId = $category->id;
                }
            }

            if ($dto->hasType()) {
                $type = $dto->getType();
            }

            if ($dto->hasTitle()) {
                $title = $dto->getTitle();
            }

            if ($dto->hasAmount()) {
                $amount = $dto->getAmount();
            }

            if ($dto->hasTransactionDate()) {
                $transactionDate = $dto->getTransactionDate();
            }

            if ($dto->hasDescription()) {
                $description = $dto->getDescription();
            }

            if ($dto->hasTags()) {
                $tags = $dto->getTags();
            }

            if ($dto->hasIsRecurring()) {
                $isRecurring = $dto->getIsRecurring();
            }

            if ($dto->hasRecurringFrequency()) {
                $recurringFrequency = $dto->getRecurringFrequency();
            }

            if ($dto->hasRecurringEndDate()) {
                $recurringEndDate = $dto->getRecurringEndDate();
            }

            if ($dto->hasReferenceNumber()) {
                $referenceNumber = $dto->getReferenceNumber();
            }

            // Handle transfer wallets update
            if ($dto->hasFromWalletId() || $dto->hasToWalletId()) {
                $fromWallet = $dto->hasFromWalletId() ?
                    $this->walletRepository->findByUuid($dto->getFromWalletId()) : null;
                $toWallet = $dto->hasToWalletId() ?
                    $this->walletRepository->findByUuid($dto->getToWalletId()) : null;

                if ($fromWallet && $fromWallet->user_id == $transaction->user_id) {
                    $fromWalletId = $fromWallet->id;
                }
                if ($toWallet && $toWallet->user_id == $transaction->user_id) {
                    $toWalletId = $toWallet->id;
                }
            }

            // Update transaction using individual parameters
            $transaction = $this->transactionRepository->update(
                $transaction,
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
                $isRecurring,
                $recurringFrequency,
                $recurringEndDate,
                $referenceNumber
            );

            // Apply new transaction impact on wallets
            $this->updateWalletBalances($transaction, 'update');

            // Handle attachment upload
            if (request()->hasFile('attachment')) {
                $this->uploadAttachment($transaction, request()->file('attachment'));
            }

            return $transaction;
        });
    }

    public function deleteTransaction(Transaction $transaction): bool
    {
        return DB::transaction(function () use ($transaction) {
            // Reverse wallet balances before deletion
            $this->reverseWalletBalances($transaction);

            // Delete child recurring transactions if this is a parent
            if ($transaction->is_recurring && !$transaction->parent_transaction_id) {
                $transaction->childTransactions()->delete();
            }

            return $this->transactionRepository->delete($transaction);
        });
    }

    public function getTransactionStats(string $userId, array $filters = []): array
    {
        return $this->transactionRepository->getTransactionStats($userId, $filters);
    }

    public function processRecurringTransactions(): void
    {
        $recurringTransactions = $this->transactionRepository->getRecurringTransactions();
        $today = Carbon::today();

        foreach ($recurringTransactions as $transaction) {
            $lastInstance = $transaction->childTransactions()
                ->orderBy('transaction_date', 'desc')
                ->first();

            $nextDate = $lastInstance
                ? $transaction->recurring_frequency->getNextDate($lastInstance->transaction_date)
                : $transaction->recurring_frequency->getNextDate($transaction->transaction_date);

            // Check if we should create a new instance
            if ($nextDate->lte($today) &&
                (!$transaction->recurring_end_date || $nextDate->lte($transaction->recurring_end_date))) {

                $this->createRecurringInstance($transaction, $nextDate);
            }
        }
    }

    private function createRecurringInstance(Transaction $parent, Carbon $transactionDate): void
    {
        $instanceData = [
            'user_id' => $parent->user_id,
            'wallet_id' => $parent->wallet_id,
            'category_id' => $parent->category_id,
            'type' => $parent->type,
            'title' => $parent->title,
            'amount' => $parent->amount,
            'transaction_date' => $transactionDate,
            'description' => $parent->description,
            'tags' => $parent->tags,
            'from_wallet_id' => $parent->from_wallet_id,
            'to_wallet_id' => $parent->to_wallet_id,
            'reference_number' => $parent->reference_number . '-' . $transactionDate->format('Ym'),
        ];

        $instance = $this->transactionRepository->createRecurringInstance($parent, $instanceData);
        $this->updateWalletBalances($instance, 'create');
    }

    private function updateWalletBalances(Transaction $transaction, string $action): void
    {
        $amount = $transaction->amount;
        $multiplier = $action === 'create' ? 1 : -1;

        switch ($transaction->type) {
            case TransactionType::INCOME:
                $this->walletRepository->updateBalance($transaction->wallet, $amount * $multiplier);
                break;

            case TransactionType::EXPENSE:
                $this->walletRepository->updateBalance($transaction->wallet, -$amount * $multiplier);
                break;

            case TransactionType::TRANSFER:
                if ($transaction->fromWallet && $transaction->toWallet) {
                    $this->walletRepository->updateBalance($transaction->fromWallet, -$amount * $multiplier);
                    $this->walletRepository->updateBalance($transaction->toWallet, $amount * $multiplier);
                }
                break;
        }
    }

    private function reverseWalletBalances(Transaction $transaction): void
    {
        $this->updateWalletBalances($transaction, 'reverse');
    }

    private function uploadAttachment(Transaction $transaction, $file): void
    {
        $path = $file->store('transaction-attachments', 'public');
        $transaction->update(['attachment' => $path]);
    }
}
