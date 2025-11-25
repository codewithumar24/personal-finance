<?php
// modules/Finance/Contracts/Repositories/WalletRepositoryContract.php

namespace Modules\Admin\Contracts\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Admin\Entities\Wallet;
use Modules\Admin\Enum\WalletType;

interface WalletRepositoryContract
{
    public function create(string $userId,
                           string $name,
                           WalletType $type,
                           string $currency,
                           float $balance,
                           ?string $accountNumber,
                           ?string $bankName,
                           bool $isDefault,
                           ?string $description): Wallet;
    public function findById(string $id): ?Wallet;
    public function findByUuid(string $uuid): ?Wallet;
    public function getUserWallets(string $userId, int|null $perPage): Collection|LengthAwarePaginator;
    public function getUserWalletByUuid(string $userId, string $uuid): ?Wallet;
    public function update( Wallet $wallet,
                            ?string $name,
                            ?string $type,
                            ?string $currency,
                            ?string $accountNumber,
                            ?string $bankName,
                            ?bool $isDefault,
                            ?string $description): Wallet;
    public function delete(Wallet $wallet): bool;
    public function updateBalance(Wallet $wallet, float $amount): Wallet;
    public function getUserDefaultWallet(string $userId): ?Wallet;
    public function setDefaultWallet(Wallet $wallet): void;
    public function getWalletStats(string $walletId): array;
}
