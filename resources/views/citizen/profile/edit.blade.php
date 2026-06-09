@extends('layouts.app')

@section('title', __('تعديل الملف الشخصي'))

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    
    <div class="mb-8 animate-fade-in-up">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
            <svg class="w-8 h-8 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>
            {{ __('الملف الشخصي والإعدادات') }}
        </h1>
        <p class="text-gray-500 dark:text-gray-400 mt-2">{{ __('يمكنك تحديث معلوماتك الشخصية أو تغيير كلمة المرور لحسابك من هنا.') }}</p>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-xl flex items-center gap-3 animate-fade-in-up">
        <svg class="w-6 h-6 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>
        <span class="text-emerald-700 dark:text-emerald-400 font-semibold">{{ session('success') }}</span>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        {{-- Profile Information --}}
        <div class="glass-card rounded-2xl p-6 md:p-8 animate-fade-in-up stagger-1">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 pb-2 border-b border-gray-100 dark:border-gray-800">
                {{ __('المعلومات الأساسية') }}
            </h2>
            
            <form method="POST" action="{{ route('citizen.profile.update-info') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ __('الاسم الكامل') }}</label>
                    <input type="text" name="full_name" value="{{ old('full_name', $user->citizenData->full_name ?? '') }}" required
                        class="w-full bg-gray-50 dark:bg-slate-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-2.5">
                    @error('full_name') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ __('البريد الإلكتروني') }}</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full bg-gray-50 dark:bg-slate-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-2.5" dir="ltr">
                    @error('email') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ __('رقم الهاتف') }}</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->citizenData->phone ?? '') }}"
                        class="w-full bg-gray-50 dark:bg-slate-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-2.5" dir="ltr">
                    @error('phone') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ __('الرقم الوطني') }} <span class="text-xs text-gray-400">({{ __('للقراءة فقط') }})</span></label>
                    <input type="text" value="{{ $user->citizenData->national_id ?? '' }}" disabled
                        class="w-full bg-gray-100 dark:bg-slate-900 border border-gray-200 dark:border-gray-800 text-gray-500 dark:text-gray-500 cursor-not-allowed rounded-xl block p-2.5" dir="ltr">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-sm transition-all shadow-md shadow-indigo-500/20">
                        {{ __('حفظ المعلومات') }}
                    </button>
                </div>
            </form>
        </div>

        {{-- Update Password --}}
        <div class="glass-card rounded-2xl p-6 md:p-8 animate-fade-in-up stagger-2">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 pb-2 border-b border-gray-100 dark:border-gray-800">
                {{ __('تغيير كلمة المرور') }}
            </h2>
            
            <form method="POST" action="{{ route('citizen.profile.update-password') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ __('كلمة المرور الحالية') }}</label>
                    <input type="password" name="current_password" required
                        class="w-full bg-gray-50 dark:bg-slate-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-2.5">
                    @error('current_password') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ __('كلمة المرور الجديدة') }}</label>
                    <input type="password" name="password" required
                        class="w-full bg-gray-50 dark:bg-slate-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-2.5">
                    @error('password') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ __('تأكيد كلمة المرور الجديدة') }}</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full bg-gray-50 dark:bg-slate-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-2.5">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white dark:text-gray-900 font-semibold rounded-xl text-sm transition-all shadow-md">
                        {{ __('تغيير كلمة المرور') }}
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
