<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\CitizenData;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm(): \Illuminate\View\View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $roleSlug = $request->input('role', 'citizen');
        $role = Role::where('slug', $roleSlug)->firstOrFail();

        DB::transaction(function () use ($request, $role, $roleSlug): void {
            $user = User::create([
                'username' => $request->validated('username'),
                'email' => $request->validated('email'),
                'password' => Hash::make($request->validated('password')),
                'role_id' => $role->id,
                'is_active' => $roleSlug === 'citizen',
            ]);

            if ($roleSlug === 'citizen') {
                CitizenData::create([
                    'user_id' => $user->id,
                    'national_id' => $request->validated('national_id'),
                    'full_name' => $request->validated('full_name'),
                    'phone' => $request->validated('phone'),
                    'blood_type' => $request->validated('blood_type'),
                ]);
            } else {
                \App\Models\PoliceData::create([
                    'user_id' => $user->id,
                    'badge_number' => $request->validated('police_badge_number'),
                    'full_name' => $request->validated('police_full_name'),
                    'rank' => $request->validated('police_rank'),
                    'department' => $request->validated('police_department'),
                ]);
            }

            Auth::login($user);
        });

        if ($roleSlug === 'police') {
            // They are suspended, so redirect them maybe to login with a message, or just let them get 403.
            return redirect()->route('citizen.dashboard'); // We will just redirect. Middleware will block if needed.
        }

        return redirect()->route('citizen.dashboard');
    }
}
