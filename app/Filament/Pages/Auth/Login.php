<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth;

use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();

        $credentials = [
            'email' => $data['email'],
            'password' => $data['password'],
        ];

        $throttleKey = strtolower($credentials['email']) . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            event(new Lockout(request()));

            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => __('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        }

        if (!auth()->attempt($credentials, $data['remember'] ?? false)) {
            RateLimiter::hit($throttleKey);

            throw ValidationException::withMessages([
                'email' => __('messages.invalid_credentials'),
            ]);
        }

        RateLimiter::clear($throttleKey);

        $user = auth()->user();

        if (!$user->is_active) {
            auth()->logout();

            throw ValidationException::withMessages([
                'email' => __('messages.invalid_credentials'),
            ]);
        }

        session()->regenerate();

        if ($user->isAdmin()) {
            redirect()->to('/admin')->send();
        } elseif ($user->isPolice()) {
            redirect()->to('/police')->send();
        } else {
            auth()->logout();

            throw ValidationException::withMessages([
                'email' => __('messages.invalid_credentials'),
            ]);
        }

        return app(LoginResponse::class);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'email' => $data['email'],
            'password' => $data['password'],
        ];
    }
}
