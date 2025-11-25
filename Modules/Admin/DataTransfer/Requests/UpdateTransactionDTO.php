<?php
// modules/Finance/DataTransfer/Requests/UpdateTransactionDTO.php

namespace Modules\Admin\DataTransfer\Requests;

use Modules\Core\DataTransfer\DTO;
use Modules\Admin\Enum\RecurringFrequency;
use Modules\Admin\Enum\TransactionType;

final class UpdateTransactionDTO implements DTO
{
    public function __construct(
        private readonly ?string $wallet_id = null,
        private readonly ?string $category_id = null,
        private readonly ?TransactionType $type = null,
        private readonly ?string $title = null,
        private readonly ?float $amount = null,
        private readonly ?string $transaction_date = null,
        private readonly ?string $description = null,
        private readonly ?string $from_wallet_id = null,
        private readonly ?string $to_wallet_id = null,
        private readonly ?array $tags = null,
        private readonly ?bool $is_recurring = null,
        private readonly ?RecurringFrequency $recurring_frequency = null,
        private readonly ?string $recurring_end_date = null,
        private readonly ?string $reference_number = null,
    ) { }

    public static function create(
        ?string $wallet_id = null,
        ?string $category_id = null,
        ?TransactionType $type = null,
        ?string $title = null,
        ?float $amount = null,
        ?string $transaction_date = null,
        ?string $description = null,
        ?string $from_wallet_id = null,
        ?string $to_wallet_id = null,
        ?array $tags = null,
        ?bool $is_recurring = null,
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

    // Getters for all properties
    public function getWalletId(): ?string { return $this->wallet_id; }
    public function getCategoryId(): ?string { return $this->category_id; }
    public function getType(): ?TransactionType { return $this->type; }
    public function getTitle(): ?string { return $this->title; }
    public function getAmount(): ?float { return $this->amount; }
    public function getTransactionDate(): ?string { return $this->transaction_date; }
    public function getDescription(): ?string { return $this->description; }
    public function getFromWalletId(): ?string { return $this->from_wallet_id; }
    public function getToWalletId(): ?string { return $this->to_wallet_id; }
    public function getTags(): ?array { return $this->tags; }
    public function getIsRecurring(): ?bool { return $this->is_recurring; }
    public function getRecurringFrequency(): ?RecurringFrequency { return $this->recurring_frequency; }
    public function getRecurringEndDate(): ?string { return $this->recurring_end_date; }
    public function getReferenceNumber(): ?string { return $this->reference_number; }

    // Check methods for optional fields
    public function hasWalletId(): bool { return !is_null($this->wallet_id); }
    public function hasCategoryId(): bool { return !is_null($this->category_id); }
    public function hasType(): bool { return !is_null($this->type); }
    public function hasTitle(): bool { return !is_null($this->title); }
    public function hasAmount(): bool { return !is_null($this->amount); }
    public function hasTransactionDate(): bool { return !is_null($this->transaction_date); }
    public function hasDescription(): bool { return !is_null($this->description); }
    public function hasFromWalletId(): bool { return !is_null($this->from_wallet_id); }
    public function hasToWalletId(): bool { return !is_null($this->to_wallet_id); }
    public function hasTags(): bool { return !is_null($this->tags); }
    public function hasIsRecurring(): bool { return !is_null($this->is_recurring); }
    public function hasRecurringFrequency(): bool { return !is_null($this->recurring_frequency); }
    public function hasRecurringEndDate(): bool { return !is_null($this->recurring_end_date); }
    public function hasReferenceNumber(): bool { return !is_null($this->reference_number); }
}