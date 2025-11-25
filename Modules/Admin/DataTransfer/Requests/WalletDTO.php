<?php
// modules/Finance/DataTransfer/Requests/WalletDTO.php

namespace Modules\Admin\DataTransfer\Requests;

use Modules\Core\DataTransfer\DTO;
use Modules\Admin\Enum\WalletType;

final class WalletDTO implements DTO
{
    public function __construct(
        private readonly string $name,
        private readonly WalletType $type,
        private readonly ?string $currency,
        private ?float $balance = 0.0, // Make sure this is here
        private readonly ?string $account_number,
        private readonly ?string $bank_name,
        private readonly ?bool $is_default,
        private readonly ?string $description,
    ) { }

    public static function create(
        string $name,
        WalletType $type,
        ?string $currency = 'USD',
        ?float $balance = 0.0,
        ?string $account_number = null,
        ?string $bank_name = null,
        ?bool $is_default = false,
        ?string $description = null,
    ): self {
        return new self($name, $type, $currency,$balance, $account_number, $bank_name, $is_default, $description);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): WalletType
    {
        return $this->type;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }
    public function getBalance(): ?float
    {
        return $this->balance;
    }

    public function getAccountNumber(): ?string
    {
        return $this->account_number;
    }

    public function getBankName(): ?string
    {
        return $this->bank_name;
    }

    public function getIsDefault(): ?bool
    {
        return $this->is_default;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
