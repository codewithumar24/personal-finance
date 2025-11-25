<?php
// modules/Finance/Entities/Category.php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Admin\Enum\CategoryType;
use Modules\User\Entities\User;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';
    protected $guarded = [];

    protected $casts = [
        'type' => CategoryType::class,
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

    public function getTypeLabelAttribute(): string
    {
        return $this->type->label();
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->icon . ' ' . $this->name;
    }

    protected static function newFactory()
    {
        return \Modules\Finance\Database\factories\CategoryFactory::new();
    }
}