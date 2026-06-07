<?php

declare(strict_types=1);

namespace App\Enums;

enum ViolationStatus: string
{
    case Unpaid = 'unpaid';
    case Paid = 'paid';
    case Canceled = 'canceled';

    public function label(): string
    {
        return __('filament.enums.violation_status.' . $this->value);
    }

    public function isUnpaid(): bool
    {
        return $this === self::Unpaid;
    }

    public function color(): string
    {
        return match ($this) {
            self::Unpaid => 'danger',
            self::Paid => 'success',
            self::Canceled => 'gray',
        };
    }
}
