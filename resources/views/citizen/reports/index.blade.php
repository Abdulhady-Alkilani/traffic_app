@extends('layouts.app')

@section('title', __('messages.my_reports'))

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    {{-- Header --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-fade-in-up">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                <svg class="w-8 h-8 text-purple-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
                {{ __('messages.my_reports') }}
            </h1>
        </div>
        <a href="{{ route('citizen.reports.create') }}"
            class="flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-purple-600 hover:bg-purple-700 shadow-md shadow-purple-500/20 transition-all duration-200">
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            {{ __('messages.new_report') }}
        </a>
    </div>

    {{-- Search and Filter --}}
    <div class="glass-card rounded-2xl p-4 mb-6 animate-fade-in-up stagger-1">
        <form method="GET" action="{{ route('citizen.reports.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" class="bg-gray-50 dark:bg-slate-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-xl focus:ring-purple-500 focus:border-purple-500 block w-full pr-10 pl-4 py-2.5" placeholder="{{ __('بحث في الوصف أو الموقع') }}">
                </div>
            </div>
            <div class="md:w-40">
                <select name="type" class="bg-gray-50 dark:bg-slate-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-xl focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5">
                    <option value="">{{ __('الكل') }}</option>
                    <option value="accident" {{ request('type') == 'accident' ? 'selected' : '' }}>{{ __('messages.accident') }}</option>
                    <option value="hazard" {{ request('type') == 'hazard' ? 'selected' : '' }}>{{ __('messages.hazard') }}</option>
                    <option value="traffic_jam" {{ request('type') == 'traffic_jam' ? 'selected' : '' }}>{{ __('messages.traffic_jam') }}</option>
                    <option value="security_threat" {{ request('type') == 'security_threat' ? 'selected' : '' }}>{{ __('messages.security_threat') }}</option>
                </select>
            </div>
            <div class="md:w-40">
                <select name="status" class="bg-gray-50 dark:bg-slate-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-xl focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5">
                    <option value="">{{ __('جميع الحالات') }}</option>
                    <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>جديد</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد المعالجة</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>مكتمل</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                </select>
            </div>
            <div class="md:w-40">
                <select name="sort" class="bg-gray-50 dark:bg-slate-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-xl focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5">
                    <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>{{ __('الأحدث') }}</option>
                    <option value="status" {{ request('sort') == 'status' ? 'selected' : '' }}>{{ __('الحالة') }}</option>
                </select>
            </div>
            <div>
                <button type="submit" class="w-full md:w-auto px-6 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-xl text-sm transition-all shadow-md shadow-purple-500/20">
                    {{ __('بحث وفلترة') }}
                </button>
            </div>
        </form>
    </div>

    {{-- Reports List --}}
    <div class="glass-card rounded-2xl overflow-hidden animate-fade-in-up stagger-2">
        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @forelse($reports as $report)
            <a href="{{ route('citizen.reports.show', $report) }}" class="block p-5 hover:bg-purple-50/30 dark:hover:bg-purple-900/10 transition-colors duration-150">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0
                            {{ $report->status->value === 'new' ? 'bg-gray-100 dark:bg-gray-800 text-gray-500' : '' }}
                            {{ $report->status->value === 'in_progress' ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-600' : '' }}
                            {{ $report->status->value === 'resolved' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600' : '' }}
                            {{ $report->status->value === 'rejected' ? 'bg-rose-100 dark:bg-rose-900/30 text-rose-600' : '' }}">
                            @if($report->status->value === 'new')
                                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                            @elseif($report->status->value === 'in_progress')
                                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182" /></svg>
                            @elseif($report->status->value === 'resolved')
                                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                            @else
                                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                            @endif
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold rounded-full
                                    {{ $report->status->value === 'new' ? 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' : '' }}
                                    {{ $report->status->value === 'in_progress' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300' : '' }}
                                    {{ $report->status->value === 'resolved' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300' : '' }}
                                    {{ $report->status->value === 'rejected' ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/50 dark:text-rose-300' : '' }}
                                ">
                                    {{ $report->status->label() }}
                                </span>
                                <span class="text-xs font-mono bg-gray-100 dark:bg-slate-800 text-gray-500 px-2 py-0.5 rounded-full">#{{ $report->id }}</span>
                            </div>
                            <h4 class="font-semibold text-gray-900 dark:text-white capitalize text-lg">{{ __('messages.' . $report->report_type) }}</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 max-w-3xl">{{ $report->description }}</p>
                            
                            <div class="flex flex-wrap gap-4 mt-3">
                                @if($report->location_text)
                                <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                                    <svg class="w-4 h-4 text-purple-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                    </svg>
                                    {{ $report->location_text }}
                                </div>
                                @endif
                                
                                @if($report->vehicle)
                                <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                                    <svg class="w-4 h-4 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                                    </svg>
                                    {{ $report->vehicle->plate_number }}
                                </div>
                                @elseif($report->reported_vehicle_plate)
                                <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                                    <svg class="w-4 h-4 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                                    </svg>
                                    {{ $report->reported_vehicle_plate }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="sm:text-end text-sm text-gray-500 dark:text-gray-400 shrink-0 flex flex-col sm:items-end justify-between">
                        <div>
                            {{ $report->created_at->format('M d, Y') }}
                            <div class="text-xs mt-1">{{ $report->created_at->format('h:i A') }}</div>
                        </div>
                        <div class="mt-4 text-purple-600 dark:text-purple-400 text-xs font-semibold flex items-center gap-1 group-hover:text-purple-700 dark:group-hover:text-purple-300">
                            {{ __('التفاصيل') }}
                            <svg class="w-4 h-4 rtl:-scale-x-100" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </div>
                    </div>
                </div>
            </a>
            @empty
            <div class="p-12 text-center">
                <div class="flex flex-col items-center">
                    <div class="w-20 h-20 rounded-full bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-purple-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">{{ __('messages.no_reports') }}</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">{{ __('قم بتقديم بلاغ جديد في حال رصدت أي حادث أو مخالفة') }}</p>
                    <a href="{{ route('citizen.reports.create') }}" class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-purple-500 hover:bg-purple-600 shadow-md shadow-purple-500/20 transition-all duration-200">
                        {{ __('messages.new_report') }}
                    </a>
                </div>
            </div>
            @endforelse
        </div>

        <div class="p-4 border-t border-gray-200/50 dark:border-gray-700/50">
            {{ $reports->links() }}
        </div>
    </div>
</div>
@endsection
