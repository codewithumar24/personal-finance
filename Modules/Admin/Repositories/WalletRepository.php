<?php
// modules/Finance/Repositories/WalletRepository.php

namespace Modules\Admin\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Admin\Contracts\Repositories\WalletRepositoryContract;
use Modules\Admin\Entities\Wallet;
use Modules\Admin\Enum\WalletType;

class WalletRepository implements WalletRepositoryContract
{
    public function __construct(private readonly Wallet $model) {}

    public function create(string $userId,
                           string $name,
                           WalletType $type,
                           string $currency,
                           float $balance,
                           ?string $accountNumber,
                           ?string $bankName,
                           bool $isDefault,
                           ?string $description): Wallet
    {
        $objQuery = $this->model->newQuery();

        return $objQuery->create([
            'wallet_uuid' => Str::uuid(),
            'user_id' => $userId,
            'name' => $name,
            'type' => $type,
            'currency' => $currency,
            'balance' => $balance,
            'account_number' => $accountNumber,
            'bank_name' => $bankName,
            'is_default' => $isDefault,
            'description' => $description,
        ]);
    }

    public function findById(string $id): ?Wallet
    {
        return $this->model->find($id);
    }

    public function findByUuid(string $uuid): ?Wallet
    {
        return $this->model->where('wallet_uuid', $uuid)->first();
    }

    public function getUserWallets(string $userId, int|null $perPage): Collection|LengthAwarePaginator
    {
        $query = $this->model->where('user_id', $userId)
            ->where('is_active', true)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc');

        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    public function getUserWalletByUuid(string $userId, string $uuid): ?Wallet
    {
        return $this->model->where('user_id', $userId)
            ->where('wallet_uuid', $uuid)
            ->first();
    }

    public function update( Wallet $wallet,
                            ?string $name,
                            ?string $type,
                            ?string $currency,
                            ?string $accountNumber,
                            ?string $bankName,
                            ?bool $isDefault,
                            ?string $description): Wallet
    {
        if (is_string($name) && $wallet->name !== $name) {
            $wallet->name = $name;
        }
        if (is_string($type) && $wallet->type !== $type) {
            $wallet->type = $type;
        }
        if (is_string($currency) && $wallet->currency !== $currency) {
            $wallet->currency = $currency;
        }
        if (!is_null($accountNumber) && $wallet->account_number !== $accountNumber) {
            $wallet->account_number = $accountNumber;
        }
        if (!is_null($bankName) && $wallet->bank_name !== $bankName) {
            $wallet->bank_name = $bankName;
        }
        if (is_bool($isDefault) && $wallet->is_default !== $isDefault) {
            $wallet->is_default = $isDefault;
        }
        if (!is_null($description) && $wallet->description !== $description) {
            $wallet->description = $description;
        }

        $wallet->save();
        return $wallet->fresh();
    }

    public function delete(Wallet $wallet): bool
    {
        return $wallet->delete();
    }

    public function updateBalance(Wallet $wallet, float $amount): Wallet
    {
        $wallet->increment('balance', $amount);
        return $wallet->fresh();
    }

    public function getUserDefaultWallet(string $userId): ?Wallet
    {
        return $this->model->where('user_id', $userId)
            ->where('is_default', true)
            ->where('is_active', true)
            ->first();
    }

    public function setDefaultWallet(Wallet $wallet): void
    {
        // Remove default from other wallets
        $this->model->where('user_id', $wallet->user_id)
            ->where('id', '!=', $wallet->id)
            ->update(['is_default' => false]);

        // Set this wallet as default
        $wallet->update(['is_default' => true]);
    }

    public function getWalletStats(string $walletId): array
    {
        $wallet = $this->findById($walletId);

        if (!$wallet) {
            return [];
        }

        $stats = DB::table('transactions')
            ->select(
                DB::raw('COUNT(*) as total_transactions'),
                DB::raw('SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as total_income'),
                DB::raw('SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as total_expense'),
                DB::raw('MAX(created_at) as last_transaction_date')
            )
            ->where('wallet_id', $walletId)
            ->first();

        return [
            'total_transactions' => $stats->total_transactions ?? 0,
            'total_income' => $stats->total_income ?? 0,
            'total_expense' => $stats->total_expense ?? 0,
            'last_transaction_date' => $stats->last_transaction_date,
            'current_balance' => $wallet->balance,
        ];
    }
}
