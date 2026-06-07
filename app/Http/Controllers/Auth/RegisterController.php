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
        $citizenRole = Role::where('slug', 'citizen')->firstOrFail();

        DB::transaction(function () use ($request, $citizenRole): void {
            $user = User::create([
                'username' => $request->validated('username'),
                'email' => $request->validated('email'),
                'password' => Hash::make($request->validated('password')),
                'role_id' => $citizenRole->id,
                'is_active' => true,
            ]);

            CitizenData::create([
                'user_id' => $user->id,
                'national_id' => $request->validated('national_id'),
                'full_name' => $request->validated('full_name'),
                'phone' => $request->validated('phone'),
                'blood_type' => $request->validated('blood_type'),
            ]);

            Auth::login($user);
        });

        return redirect()->route('citizen.dashboard');
    }
}
