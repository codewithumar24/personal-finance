<?php
// modules/Finance/Entities/Transaction.php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Admin\Enum\RecurringFrequency;
use Modules\Admin\Enum\TransactionType;
use Modules\User\Entities\User;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';
    protected $guarded = [];

    protected $casts = [
        'type' => TransactionType::class,
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
        'tags' => 'array',
        'is_recurring' => 'boolean',
        'recurring_frequency' => RecurringFrequency::class,
        'recurring_end_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function fromWallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'from_wallet_id');
    }

    public function toWallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'to_wallet_id');
    }

    public function parentTransaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'parent_transaction_id');
    }

    public function childTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'parent_transaction_id');
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2);
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type->label();
    }

    public function getSignedAmountAttribute(): float
    {
        return match($this->type) {
            TransactionType::INCOME => $this->amount,
            TransactionType::EXPENSE => -$this->amount,
            TransactionType::TRANSFER => 0, // Transfers don't affect net balance
        };
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->attachment ? asset('storage/' . $this->attachment) : null;
    }

    public function isTransfer(): bool
    {
        return $this->type === TransactionType::TRANSFER;
    }

    public function isRecurring(): bool
    {
        return $this->is_recurring;
    }

    protected static function newFactory()
    {
        return \Modules\Finance\Database\factories\TransactionFactory::new();
    }
}