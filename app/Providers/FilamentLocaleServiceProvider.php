<?php

declare(strict_types=1);

namespace App\Providers;

use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Illuminate\Support\ServiceProvider;

class FilamentLocaleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['en', 'ar'])
                ->visible(outsidePanels: true)
                ->circular(false)
                ->labels([
                    'en' => 'English',
                    'ar' => 'العربية',
                ])
                ->outsidePanelRoutes([
                    'auth.login',
                    'auth.profile',
                ]);
        });
    }
}
