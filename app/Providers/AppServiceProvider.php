<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\ReportCreated;
use App\Listeners\LogReportCreation;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        Event::listen(
            ReportCreated::class,
            LogReportCreation::class,
        );
    }
}
