<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        // Note: ReportCreated listeners (LogReportCreation, AnalyzeReportWithAi)
        // are auto-discovered by Laravel's event discovery from app/Listeners.
        // Do NOT register them manually here — it causes double execution.

        \App\Models\Report::observe(\App\Observers\ReportObserver::class);

        Event::listen(\BezhanSalleh\FilamentLanguageSwitch\Events\LocaleChanged::class, function ($event) {
            session()->save();
        });

        \Illuminate\Support\Facades\Gate::before(function (\App\Models\User $user, string $ability) {
            if ($user->isAdmin()) {
                return true;
            }
        });
    }
}
