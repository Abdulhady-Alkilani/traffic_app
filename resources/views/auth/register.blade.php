@extends('layouts.app')

@section('title', __('messages.register'))

@section('content')
<div class="min-h-[85vh] flex items-center justify-center py-12 px-4">
    {{-- Decorative --}}
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-40 end-20 w-80 h-80 bg-purple-400/10 dark:bg-purple-400/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 -start-40 w-80 h-80 bg-indigo-400/10 dark:bg-indigo-400/5 rounded-full blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-2xl animate-fade-in-up">
        <div class="glass-card rounded-2xl shadow-xl shadow-gray-200/50 dark:shadow-black/20 p-8">
            {{-- Header --}}
            <div class="text-center mb-8">
                <div class="w-16 h-16 mx-auto bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-purple-500/25 mb-4">
                    <svg class="w-8 h-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('messages.register') }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('messages.dashboard_subtitle') }}</p>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Username --}}
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('messages.username') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                            </div>
                            <input type="text" id="username" name="username" value="{{ old('username') }}"
                                class="w-full ps-11 pe-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl bg-white/50 dark:bg-slate-800/50 text-gray-900 dark:text-white placeholder-gray-400 form-input-pro focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all duration-200"
                                required>
                        </div>
                        @error('username') <p class="mt-1.5 text-sm text-rose-500 flex items-center gap-1"><svg class="w-4 h-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>{{ $message }}</p> @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('messages.email') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                </svg>
                            </div>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                class="w-full ps-11 pe-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl bg-white/50 dark:bg-slate-800/50 text-gray-900 dark:text-white placeholder-gray-400 form-input-pro focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all duration-200"
                                required>
                        </div>
                        @error('email') <p class="mt-1.5 text-sm text-rose-500 flex items-center gap-1"><svg class="w-4 h-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>{{ $message }}</p> @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('messages.password') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                </svg>
                            </div>
                            <input type="password" id="password" name="password"
                                class="w-full ps-11 pe-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl bg-white/50 dark:bg-slate-800/50 text-gray-900 dark:text-white placeholder-gray-400 form-input-pro focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all duration-200"
                                required>
                        </div>
                        @error('password') <p class="mt-1.5 text-sm text-rose-500 flex items-center gap-1"><svg class="w-4 h-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>{{ $message }}</p> @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('messages.confirm_password') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="w-full ps-11 pe-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl bg-white/50 dark:bg-slate-800/50 text-gray-900 dark:text-white placeholder-gray-400 form-input-pro focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all duration-200"
                                required>
                        </div>
                    </div>

                    {{-- Role Selection --}}
                    <div class="col-span-1 md:col-span-2">
                        <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">طبيعة الحساب (مواطن أم شرطي)</label>
                        <div class="relative">
                            <select id="role" name="role"
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl bg-white/50 dark:bg-slate-800/50 text-gray-900 dark:text-white form-input-pro focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all duration-200"
                                required onchange="toggleRoleFields()">
                                <option value="citizen" {{ old('role') === 'citizen' ? 'selected' : '' }}>مواطن (Citizen)</option>
                                <option value="police" {{ old('role') === 'police' ? 'selected' : '' }}>شرطي (Police)</option>
                            </select>
                        </div>
                        @error('role') <p class="mt-1.5 text-sm text-rose-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Citizen Fields --}}
                    <div id="citizen_fields" class="contents">
                        {{-- Full Name --}}
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('messages.full_name') }}</label>
                            <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}"
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl bg-white/50 dark:bg-slate-800/50 text-gray-900 dark:text-white form-input-pro focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                            @error('full_name') <p class="mt-1.5 text-sm text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- National ID --}}
                        <div>
                            <label for="national_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('messages.national_id') }}</label>
                            <input type="text" id="national_id" name="national_id" value="{{ old('national_id') }}"
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl bg-white/50 dark:bg-slate-800/50 text-gray-900 dark:text-white form-input-pro focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                            @error('national_id') <p class="mt-1.5 text-sm text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- Phone --}}
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('messages.phone') }}</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl bg-white/50 dark:bg-slate-800/50 text-gray-900 dark:text-white form-input-pro focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                            @error('phone') <p class="mt-1.5 text-sm text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- Blood Type --}}
                        <div>
                            <label for="blood_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('messages.blood_type') }}</label>
                            <select id="blood_type" name="blood_type"
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl bg-white/50 dark:bg-slate-800/50 text-gray-900 dark:text-white form-input-pro focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none appearance-none">
                                <option value="">{{ __('messages.select_blood_type') }}</option>
                                @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bt)
                                    <option value="{{ $bt }}" {{ old('blood_type') === $bt ? 'selected' : '' }}>{{ $bt }}</option>
                                @endforeach
                            </select>
                            @error('blood_type') <p class="mt-1.5 text-sm text-rose-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Police Fields --}}
                    <div id="police_fields" class="contents" style="display: none;">
                        {{-- Badge Number --}}
                        <div>
                            <label for="police_badge_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">الرقم العسكري</label>
                            <input type="text" id="police_badge_number" name="police_badge_number" value="{{ old('police_badge_number') }}"
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl bg-white/50 dark:bg-slate-800/50 text-gray-900 dark:text-white form-input-pro focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                            @error('police_badge_number') <p class="mt-1.5 text-sm text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- Full Name --}}
                        <div>
                            <label for="police_full_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">الاسم الكامل</label>
                            <input type="text" id="police_full_name" name="police_full_name" value="{{ old('police_full_name') }}"
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl bg-white/50 dark:bg-slate-800/50 text-gray-900 dark:text-white form-input-pro focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                            @error('police_full_name') <p class="mt-1.5 text-sm text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- Rank --}}
                        <div>
                            <label for="police_rank" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">الرتبة</label>
                            <select id="police_rank" name="police_rank"
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl bg-white/50 dark:bg-slate-800/50 text-gray-900 dark:text-white form-input-pro focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none appearance-none">
                                <option value="">اختر الرتبة</option>
                                @foreach(['شرطي', 'عريف', 'رقيب', 'رقيب أول', 'مساعد', 'مساعد أول', 'ملازم', 'ملازم أول', 'نقيب', 'رائد', 'مقدم', 'عقيد', 'عميد', 'لواء'] as $rank)
                                    <option value="{{ $rank }}" {{ old('police_rank') === $rank ? 'selected' : '' }}>{{ $rank }}</option>
                                @endforeach
                            </select>
                            @error('police_rank') <p class="mt-1.5 text-sm text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- Department --}}
                        <div>
                            <label for="police_department" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">القسم</label>
                            <select id="police_department" name="police_department"
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl bg-white/50 dark:bg-slate-800/50 text-gray-900 dark:text-white form-input-pro focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none appearance-none">
                                <option value="">اختر القسم</option>
                                @foreach(\App\Enums\Department::cases() as $dept)
                                    <option value="{{ $dept->value }}" {{ old('police_department') === $dept->value ? 'selected' : '' }}>{{ $dept->label() }}</option>
                                @endforeach
                            </select>
                            @error('police_department') <p class="mt-1.5 text-sm text-rose-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <script>
                    function toggleRoleFields() {
                        const role = document.getElementById('role').value;
                        const citizenFields = document.getElementById('citizen_fields');
                        const policeFields = document.getElementById('police_fields');
                        
                        if (role === 'citizen') {
                            citizenFields.style.display = 'contents';
                            policeFields.style.display = 'none';
                            
                            // Make citizen required, police optional
                            ['full_name', 'national_id', 'phone', 'blood_type'].forEach(id => document.getElementById(id).required = true);
                            ['police_badge_number', 'police_full_name', 'police_rank', 'police_department'].forEach(id => {
                                const el = document.getElementById(id);
                                if (el) el.required = false;
                            });
                        } else {
                            citizenFields.style.display = 'none';
                            policeFields.style.display = 'contents';
                            
                            // Make police required, citizen optional
                            ['full_name', 'national_id', 'phone', 'blood_type'].forEach(id => {
                                const el = document.getElementById(id);
                                if (el) el.required = false;
                            });
                            ['police_badge_number', 'police_full_name', 'police_rank', 'police_department'].forEach(id => document.getElementById(id).required = true);
                        }
                    }
                    // Run on load
                    document.addEventListener('DOMContentLoaded', toggleRoleFields);
                </script>

                {{-- Submit --}}
                <button type="submit"
                    class="w-full mt-8 flex items-center justify-center gap-2 py-3 px-4 rounded-xl font-semibold text-white bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 shadow-lg shadow-purple-500/25 hover:shadow-xl hover:shadow-purple-500/30 transition-all duration-300 btn-shine">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                    </svg>
                    {{ __('messages.register') }}
                </button>
            </form>

            {{-- Login Link --}}
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('messages.has_account') }}
                    <a href="{{ route('login') }}" class="font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">{{ __('messages.login') }}</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
