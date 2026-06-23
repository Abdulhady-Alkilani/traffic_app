<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ViolationStatus: string implements HasLabel, HasColor
{
    case Unpaid = 'unpaid';
    case Paid = 'paid';
    case PendingVerification = 'pending_verification';
    case Canceled = 'canceled';

    public function getLabel(): ?string
    {
        return __('filament.enums.violation_status.' . $this->value);
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Unpaid => 'danger',
            self::Paid => 'success',
            self::PendingVerification => 'warning',
            self::Canceled => 'gray',
        };
    }

    public function isUnpaid(): bool
    {
        return $this === self::Unpaid;
    }

    /**
     * Get options for select inputs, excluding canceled.
     */
    public static function getSelectOptions(): array
    {
        return collect(self::cases())
            ->filter(fn ($case) => $case !== self::Canceled)
            ->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}
