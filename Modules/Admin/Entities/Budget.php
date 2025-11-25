<?php
// modules/Finance/Entities/Budget.php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\User\Entities\User;

class Budget extends Model
{
    use HasFactory;

    protected $table = 'budgets';
    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'notifications' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getSpentAmountAttribute(): float
    {
        return (float) Transaction::where('user_id', $this->user_id)
            ->where('category_id', $this->category_id)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$this->start_date, $this->end_date])
            ->sum('amount');
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->amount - $this->spent_amount);
    }

    public function getProgressPercentageAttribute(): float
    {
        if ($this->amount <= 0) return 0;
        return min(100, ($this->spent_amount / $this->amount) * 100);
    }

    public function getIsExceededAttribute(): bool
    {
        return $this->spent_amount > $this->amount;
    }

    public function getDaysRemainingAttribute(): int
    {
        return max(0, now()->diffInDays($this->end_date, false));
    }

    protected static function newFactory()
    {
        return \Modules\Finance\Database\factories\BudgetFactory::new();
    }
}
