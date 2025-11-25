<?php
// modules/Finance/Entities/Wallet.php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Admin\Enum\WalletType;
use Modules\User\Entities\User;

class Wallet extends Model
{
    use HasFactory;

    protected $table = 'wallets';
    protected $guarded = [];

    protected $casts = [
        'type' => WalletType::class,
        'balance' => 'decimal:2',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function incomeTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class)->where('type', 'income');
    }

    public function expenseTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class)->where('type', 'expense');
    }

    public function transferFromTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'from_wallet_id');
    }

    public function transferToTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'to_wallet_id');
    }

    public function getFormattedBalanceAttribute(): string
    {
        return number_format($this->balance, 2);
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type->label();
    }

    protected static function newFactory()
    {
        return \Modules\Finance\Database\factories\WalletFactory::new();
    }
}