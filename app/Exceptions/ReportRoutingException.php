<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class ReportRoutingException extends Exception
{
    public static function unknownReportType(string $type): self
    {
        return new self("Cannot route report with unknown type: {$type}");
    }
}
