<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\CitizenData;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $citizenRole = Role::where('slug', 'citizen')->first();

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $citizenRole->id,
            'is_active' => true,
        ]);

        CitizenData::create([
            'user_id' => $user->id,
            'national_id' => $request->national_id,
            'full_name' => $request->full_name,
            'phone' => $request->phone,
            'blood_type' => $request->blood_type,
        ]);

        Auth::login($user);

        return redirect()->route('citizen.dashboard');
    }
}
