<?php
// modules/Finance/Services/WalletService.php

namespace Modules\Admin\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Contracts\Repositories\WalletRepositoryContract;
use Modules\Admin\Contracts\Services\WalletServiceContract;
use Modules\Admin\DataTransfer\Requests\WalletDTO;
use Modules\Admin\Entities\Wallet;

class WalletService implements WalletServiceContract
{
    public function __construct(
        private readonly WalletRepositoryContract $walletRepository
    ) {}

    public function createWallet(WalletDTO $dto, string $userId): Wallet
    {
        return DB::transaction(function () use ($dto, $userId) {
            $wallet = $this->walletRepository->create(
            $userId,
            $dto->getName(),
            $dto->getType(),
            $dto->getCurrency(),
            $dto->getBalance(),
            $dto->getAccountNumber(),
            $dto->getBankName(),
            $dto->getIsDefault(),
            $dto->getDescription()
        );

        // If this is set as default, update other wallets
        if ($dto->getIsDefault()) {
            $this->walletRepository->setDefaultWallet($wallet);
        }

        // If no default wallet exists and this is the first wallet, set as default
        $defaultWallet = $this->walletRepository->getUserDefaultWallet($userId);
        if (!$defaultWallet) {
            $this->walletRepository->setDefaultWallet($wallet);
        }

        return $wallet;
    });
    }

    public function getUserWallets(string $userId, int|null $perPage): Collection|LengthAwarePaginator
    {
        return $this->walletRepository->getUserWallets($userId, $perPage);
    }

    public function getUserWalletByUuid(string $userId, string $uuid): ?Wallet
    {
        return $this->walletRepository->getUserWalletByUuid($userId, $uuid);
    }

    public function updateWallet(Wallet $wallet, WalletDTO $dto): Wallet
    {
        return DB::transaction(function () use ($wallet, $dto) {
            $wallet = $this->walletRepository->update(
                $wallet,
                $dto->getName(),
                $dto->getType(),
                $dto->getCurrency(),
                $dto->getAccountNumber(),
                $dto->getBankName(),
                $dto->getColor(),
                $dto->getIsDefault(),
                $dto->getDescription()
            );

            // Handle default wallet setting
            if ($dto->getIsDefault()) {
                $this->walletRepository->setDefaultWallet($wallet);
            }

            return $wallet;
        });
    }

    public function deleteWallet(Wallet $wallet): bool
    {
        // Check if wallet has transactions
        if ($wallet->transactions()->exists()) {
            throw new \Exception('Cannot delete wallet that has transactions. Please delete transactions first.');
        }

        return $this->walletRepository->delete($wallet);
    }

    public function getWalletStats(string $walletId): array
    {
        return $this->walletRepository->getWalletStats($walletId);
    }

    public function getTotalBalance(string $userId): float
    {
        $wallets = $this->walletRepository->getUserWallets($userId, null);

        return $wallets->sum('balance');
    }
}
