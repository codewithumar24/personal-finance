<?php
// modules/Finance/Http/Controllers/WalletController.php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Http\Requests\FilterRequest;
use Modules\Admin\Contracts\Services\WalletServiceContract;
use Modules\Admin\Http\Requests\WalletRequest;
use Modules\Admin\Transformers\WalletTransformer;

class WalletController extends Controller
{
    public function __construct(
        private readonly WalletServiceContract $walletService
    ) {}

    public function index(FilterRequest $request): JsonResponse
    {
        $userId = Auth::id();
        $wallets = $this->walletService->getUserWallets($userId, $request->getPerPage());

        return apiResponse()
            ->pagination($wallets)
            ->success(WalletTransformer::collection($wallets));
    }

    public function store(WalletRequest $request): JsonResponse
    {
        $userId = Auth::id();
        $wallet = $this->walletService->createWallet($request->getDTO(), $userId);

        return apiResponse()
            ->success(new WalletTransformer($wallet), 'Wallet created successfully')
            ->setStatusCode(201);
    }

    public function show(string $uuid): JsonResponse
    {
        $userId = Auth::id();
        $wallet = $this->walletService->getUserWalletByUuid($userId, $uuid);

        if (!$wallet) {
            return apiResponse()->notFound('Wallet not found');
        }

        return apiResponse()->success(new WalletTransformer($wallet));
    }

    public function update(WalletRequest $request, string $uuid): JsonResponse
    {
        $userId = Auth::id();
        $wallet = $this->walletService->getUserWalletByUuid($userId, $uuid);

        if (!$wallet) {
            return apiResponse()->notFound('Wallet not found');
        }

        $updatedWallet = $this->walletService->updateWallet($wallet, $request->getDTO());

        return apiResponse()
            ->success(new WalletTransformer($updatedWallet), 'Wallet updated successfully');
    }

    public function destroy(string $uuid): JsonResponse
    {
        $userId = Auth::id();
        $wallet = $this->walletService->getUserWalletByUuid($userId, $uuid);

        if (!$wallet) {
            return apiResponse()->notFound('Wallet not found');
        }

        try {
            $this->walletService->deleteWallet($wallet);
            
            return apiResponse()->success(null, 'Wallet deleted successfully');
        } catch (\Exception $e) {
            return apiResponse()->error($e->getMessage(), 400);
        }
    }

    public function stats(string $uuid): JsonResponse
    {
        $userId = Auth::id();
        $wallet = $this->walletService->getUserWalletByUuid($userId, $uuid);

        if (!$wallet) {
            return apiResponse()->notFound('Wallet not found');
        }

        $stats = $this->walletService->getWalletStats($wallet->id);

        return apiResponse()->success([
            'wallet' => new WalletTransformer($wallet),
            'stats' => $stats,
        ]);
    }

    public function totalBalance(): JsonResponse
    {
        $userId = Auth::id();
        $totalBalance = $this->walletService->getTotalBalance($userId);

        return apiResponse()->success([
            'total_balance' => $totalBalance,
            'formatted_balance' => number_format($totalBalance, 2),
        ]);
    }
}