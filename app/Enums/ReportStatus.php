<?php

declare(strict_types=1);

namespace App\Enums;

enum ReportStatus: string
{
    case New = 'new';
    case InProgress = 'in_progress';
    case Resolved = 'resolved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return __('filament.enums.report_status.' . $this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::New => 'gray',
            self::InProgress => 'warning',
            self::Resolved => 'success',
            self::Rejected => 'danger',
        };
    }
}
