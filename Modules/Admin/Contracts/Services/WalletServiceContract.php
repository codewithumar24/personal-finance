<?php
// modules/Finance/Contracts/Services/WalletServiceContract.php

namespace Modules\Admin\Contracts\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Admin\DataTransfer\Requests\WalletDTO;
use Modules\Admin\Entities\Wallet;

interface WalletServiceContract
{
    public function createWallet(WalletDTO $dto, string $userId): Wallet;
    public function getUserWallets(string $userId, int|null $perPage): Collection|LengthAwarePaginator;
    public function getUserWalletByUuid(string $userId, string $uuid): ?Wallet;
    public function updateWallet(Wallet $wallet, WalletDTO $dto): Wallet;
    public function deleteWallet(Wallet $wallet): bool;
    public function getWalletStats(string $walletId): array;
    public function getTotalBalance(string $userId): float;
}