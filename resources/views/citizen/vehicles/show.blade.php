@extends('layouts.app')

@section('title', __('تفاصيل المركبة') . ' ' . $vehicle->plate_number)

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-8 animate-fade-in-up">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                <svg class="w-8 h-8 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                </svg>
                {{ __('تفاصيل المركبة') }}
                <span class="text-indigo-600 font-mono">{{ $vehicle->plate_number }}</span>
            </h1>
        </div>
        <a href="{{ route('citizen.vehicles.index') }}" class="flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition-all shadow-sm">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
            </svg>
            {{ __('رجوع للمركبات') }}
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Vehicle Info --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="glass-card rounded-2xl p-6 animate-fade-in-up stagger-1">
                <div class="text-center mb-6">
                    <div class="w-24 h-24 mx-auto bg-indigo-50 dark:bg-indigo-900/20 rounded-full flex items-center justify-center border-4 border-indigo-100 dark:border-indigo-800 mb-3">
                        <svg class="w-12 h-12 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $vehicle->make }}</h2>
                    <p class="text-gray-500 dark:text-gray-400">{{ $vehicle->model_year }} • {{ __('messages.' . $vehicle->vehicle_type) }}</p>
                </div>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-800">
                        <span class="text-gray-500 font-semibold text-sm">{{ __('رقم اللوحة') }}</span>
                        <span class="font-mono font-bold text-gray-900 dark:text-white">{{ $vehicle->plate_number }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-800">
                        <span class="text-gray-500 font-semibold text-sm">{{ __('اللون') }}</span>
                        <div class="flex items-center gap-2" dir="ltr">
                            <span class="w-5 h-5 rounded-full shadow-sm border border-gray-200 dark:border-gray-700" style="background-color: {{ $vehicle->color ?? 'transparent' }};"></span>
                            <span class="font-bold text-gray-900 dark:text-white uppercase font-mono">{{ $vehicle->color ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-800">
                        <span class="text-gray-500 font-semibold text-sm">{{ __('المحافظة') }}</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ $vehicle->registration_city ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-500 font-semibold text-sm">{{ __('تاريخ التسجيل') }}</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ $vehicle->created_at->format('Y/m/d') }}</span>
                    </div>
                </div>
            </div>
            
            {{-- Stats --}}
            <div class="grid grid-cols-2 gap-4 animate-fade-in-up stagger-2">
                <div class="glass-card rounded-2xl p-4 text-center">
                    <p class="text-xs font-bold text-gray-500 uppercase mb-1">{{ __('إجمالي المخالفات') }}</p>
                    <p class="text-2xl font-bold text-rose-600">{{ $vehicle->violations->count() }}</p>
                </div>
                <div class="glass-card rounded-2xl p-4 text-center">
                    <p class="text-xs font-bold text-gray-500 uppercase mb-1">{{ __('المخالفات غير المدفوعة') }}</p>
                    <p class="text-2xl font-bold text-amber-600">{{ $vehicle->violations->where('status', \App\Enums\ViolationStatus::Unpaid)->count() }}</p>
                </div>
            </div>
        </div>

        {{-- Details --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Violations --}}
            <div class="glass-card rounded-2xl p-6 animate-fade-in-up stagger-3">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100 dark:border-gray-800">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('آخر المخالفات المسجلة') }}</h2>
                    <a href="{{ route('citizen.violations.index', ['search' => $vehicle->plate_number]) }}" class="text-sm font-semibold text-rose-600 hover:text-rose-700">{{ __('عرض الكل') }}</a>
                </div>
                
                @if($vehicle->violations->isEmpty())
                    <div class="text-center py-8">
                        <div class="w-16 h-16 mx-auto bg-emerald-50 dark:bg-emerald-900/20 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-8 h-8 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('لا يوجد مخالفات') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('سجلك نظيف لهذه المركبة') }}</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($vehicle->violations as $violation)
                        <a href="{{ route('citizen.violations.show', $violation) }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 py-4 hover:bg-gray-50 dark:hover:bg-slate-800/50 rounded-xl px-2 transition-colors">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-bold {{ $violation->status->value === 'unpaid' ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/50 dark:text-rose-400' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-400' }}">
                                        {{ __('messages.' . $violation->status->value) }}
                                    </span>
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ __('messages.' . $violation->violation_type) }}</span>
                                </div>
                                <p class="text-xs text-gray-500">{{ $violation->issued_at->format('Y/m/d h:i A') }} • {{ $violation->location_text }}</p>
                            </div>
                            <div class="flex items-center justify-between sm:justify-end gap-4 w-full sm:w-auto">
                                <span class="font-bold text-gray-900 dark:text-white bg-gray-100 dark:bg-slate-800 px-3 py-1.5 rounded-lg">{{ number_format($violation->fine_amount, 0) }} ل.س</span>
                                <svg class="w-5 h-5 text-gray-400 rtl:-scale-x-100" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </div>
                        </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Reports --}}
            <div class="glass-card rounded-2xl p-6 animate-fade-in-up stagger-4">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100 dark:border-gray-800">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('بلاغات مرتبطة بالمركبة') }}</h2>
                </div>
                
                @if($vehicle->reports->isEmpty())
                    <div class="text-center py-6 text-gray-500 dark:text-gray-400">
                        {{ __('لا يوجد بلاغات مرتبطة بهذه المركبة.') }}
                    </div>
                @else
                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($vehicle->reports as $report)
                        <a href="{{ route('citizen.reports.show', $report) }}" class="flex items-center justify-between gap-4 py-3 hover:bg-gray-50 dark:hover:bg-slate-800/50 rounded-xl px-2 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-purple-50 dark:bg-purple-900/20 text-purple-600 rounded-lg flex items-center justify-center shrink-0">
                                    @if($report->report_type === 'accident') 🚗 
                                    @elseif($report->report_type === 'hazard') ⚠️
                                    @elseif($report->report_type === 'traffic_jam') 🚦
                                    @else 🛡️ @endif
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ __('messages.' . $report->report_type) }}</p>
                                    <p class="text-xs text-gray-500">{{ $report->created_at->format('Y/m/d') }}</p>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 rtl:-scale-x-100" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </a>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
