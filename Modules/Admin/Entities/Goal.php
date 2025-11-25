<?php
// modules/Finance/Entities/Goal.php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Admin\Enum\GoalType;
use Modules\User\Entities\User;

class Goal extends Model
{
    use HasFactory;

    protected $table = 'goals';
    protected $guarded = [];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'target_date' => 'date',
        'type' => GoalType::class,
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getProgressPercentageAttribute(): float
    {
        if ($this->target_amount <= 0) return 0;
        return min(100, ($this->current_amount / $this->target_amount) * 100);
    }

    public function getDaysRemainingAttribute(): int
    {
        return max(0, now()->diffInDays($this->target_date, false));
    }

    public function getFormattedTargetAmountAttribute(): string
    {
        return number_format($this->target_amount, 2);
    }

    public function getFormattedCurrentAmountAttribute(): string
    {
        return number_format($this->current_amount, 2);
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type->label();
    }

    public function addAmount(float $amount): void
    {
        $this->increment('current_amount', $amount);

        // Check if goal is completed
        if ($this->current_amount >= $this->target_amount && !$this->is_completed) {
            $this->update([
                'is_completed' => true,
                'completed_at' => now(),
            ]);
        }
    }

    protected static function newFactory()
    {
        return \Modules\Finance\Database\factories\GoalFactory::new();
    }
}
