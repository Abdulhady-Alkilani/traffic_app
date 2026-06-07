<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session()->get('locale', config('app.locale'));

        if (in_array($locale, array_keys(config('app.available_locales')))) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
