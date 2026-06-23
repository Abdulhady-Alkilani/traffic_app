<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ReportStatus: string implements HasLabel, HasColor
{
    case New = 'new';
    case InProgress = 'in_progress';
    case Resolved = 'resolved';
    case Rejected = 'rejected';

    public function getLabel(): ?string
    {
        return __('filament.enums.report_status.' . $this->value);
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::New => 'gray',
            self::InProgress => 'warning',
            self::Resolved => 'success',
            self::Rejected => 'danger',
        };
    }
}
