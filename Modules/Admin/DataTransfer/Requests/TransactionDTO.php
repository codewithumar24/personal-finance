<?php
// modules/Finance/DataTransfer/Requests/TransactionDTO.php

namespace Modules\Admin\DataTransfer\Requests;

use Modules\Core\DataTransfer\DTO;
use Modules\Admin\Enum\RecurringFrequency;
use Modules\Admin\Enum\TransactionType;

final class TransactionDTO implements DTO
{
    public function __construct(
        private readonly string $wallet_id,
        private readonly string $category_id,
        private readonly TransactionType $type,
        private readonly string $title,
        private readonly float $amount,
        private readonly string $transaction_date,
        private readonly ?string $description = null,
        private readonly ?string $from_wallet_id = null,
        private readonly ?string $to_wallet_id = null,
        private readonly ?array $tags = null,
        private readonly ?bool $is_recurring = false,
        private readonly ?RecurringFrequency $recurring_frequency = null,
        private readonly ?string $recurring_end_date = null,
        private readonly ?string $reference_number = null,
    ) { }

    public static function create(
        string $wallet_id,
        string $category_id,
        TransactionType $type,
        string $title,
        float $amount,
        string $transaction_date,
        ?string $description = null,
        ?string $from_wallet_id = null,
        ?string $to_wallet_id = null,
        ?array $tags = null,
        ?bool $is_recurring = false,
        ?RecurringFrequency $recurring_frequency = null,
        ?string $recurring_end_date = null,
        ?string $reference_number = null,
    ): self {
        return new self(
            $wallet_id, 
            $category_id, 
            $type, 
            $title, 
            $amount, 
            $transaction_date, 
            $description, 
            $from_wallet_id, 
            $to_wallet_id, 
            $tags, 
            $is_recurring, 
            $recurring_frequency, 
            $recurring_end_date,
            $reference_number
        );
    }

    public function getWalletId(): string
    {
        return $this->wallet_id;
    }

    public function getCategoryId(): string
    {
        return $this->category_id;
    }

    public function getType(): TransactionType
    {
        return $this->type;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getTransactionDate(): string
    {
        return $this->transaction_date;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getFromWalletId(): ?string
    {
        return $this->from_wallet_id;
    }

    public function getToWalletId(): ?string
    {
        return $this->to_wallet_id;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function getIsRecurring(): ?bool
    {
        return $this->is_recurring;
    }

    public function getRecurringFrequency(): ?RecurringFrequency
    {
        return $this->recurring_frequency;
    }

    public function getRecurringEndDate(): ?string
    {
        return $this->recurring_end_date;
    }

    public function getReferenceNumber(): ?string
    {
        return $this->reference_number;
    }
}