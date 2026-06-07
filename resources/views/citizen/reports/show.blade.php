@extends('layouts.app')

@section('title', __('تفاصيل البلاغ') . ' RPT-' . str_pad((string) $report->id, 6, '0', STR_PAD_LEFT))

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-8 animate-fade-in-up">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                {{ __('تفاصيل البلاغ') }}
                <span class="text-indigo-600 font-mono text-xl">RPT-{{ str_pad((string) $report->id, 6, '0', STR_PAD_LEFT) }}</span>
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                تم التقديم في: <span dir="ltr">{{ $report->created_at->format('Y/m/d h:i A') }}</span>
            </p>
        </div>
        <a href="{{ route('citizen.reports.index') }}" class="flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition-all shadow-sm">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
            </svg>
            {{ __('رجوع للقائمة') }}
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Main Details Column --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Status Card --}}
            <div class="glass-card rounded-2xl p-6 border-l-4 {{ 
                $report->status->value === 'pending' ? 'border-amber-500' : 
                ($report->status->value === 'under_review' ? 'border-indigo-500' : 
                ($report->status->value === 'resolved' ? 'border-emerald-500' : 'border-rose-500')) 
            }} animate-fade-in-up stagger-1">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">{{ __('حالة البلاغ') }}</h2>
                        <div class="flex items-center gap-2">
                            @if($report->status->value === 'pending')
                                <span class="flex h-3 w-3 relative">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                                </span>
                            @endif
                            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ __('messages.' . $report->status->value) }}</p>
                        </div>
                    </div>
                    <div class="text-start">
                        <h2 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">{{ __('القسم المختص') }}</h2>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('messages.' . $report->assigned_department->value) }}</p>
                    </div>
                </div>
            </div>

            {{-- Info Card --}}
            <div class="glass-card rounded-2xl p-6 animate-fade-in-up stagger-2">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-100 dark:border-gray-800">{{ __('معلومات الحادثة') }}</h2>
                
                <div class="space-y-5">
                    <div>
                        <p class="text-sm font-bold text-gray-500 mb-1">{{ __('نوع البلاغ') }}</p>
                        <div class="flex items-center gap-2 text-gray-900 dark:text-white font-semibold bg-gray-50 dark:bg-slate-900/50 inline-flex px-3 py-1.5 rounded-lg border border-gray-100 dark:border-gray-800">
                            @switch($report->report_type)
                                @case('accident') 🚗 @break
                                @case('hazard') ⚠️ @break
                                @case('traffic_jam') 🚦 @break
                                @case('security_threat') 🛡️ @break
                            @endswitch
                            {{ __('messages.' . $report->report_type) }}
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-bold text-gray-500 mb-2">{{ __('المركبة المبلغ عنها') }}</p>
                        @if($report->vehicle)
                            <div class="flex items-center gap-3 p-3 border border-indigo-100 dark:border-indigo-900/50 bg-indigo-50/50 dark:bg-indigo-900/20 rounded-xl">
                                <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center shrink-0">
                                    <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 dark:text-white">{{ $report->vehicle->plate_number }}</p>
                                    <p class="text-xs text-gray-500">{{ $report->vehicle->make }} {{ $report->vehicle->model_year }} • {{ $report->vehicle->color }}</p>
                                </div>
                            </div>
                        @elseif($report->reported_vehicle_plate)
                            @if($report->reported_vehicle_plate === 'بدون لوحة')
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm font-semibold text-gray-600 dark:text-gray-300">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    {{ __('مركبة مجهولة / بدون لوحة') }}
                                </div>
                            @else
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-800 rounded-lg text-sm font-bold text-indigo-700 dark:text-indigo-300 font-mono tracking-wider">
                                    {{ $report->reported_vehicle_plate }}
                                </div>
                            @endif
                        @else
                            <p class="text-sm text-gray-500 italic">{{ __('لم يتم تحديد مركبة') }}</p>
                        @endif
                    </div>

                    <div>
                        <p class="text-sm font-bold text-gray-500 mb-2">{{ __('الوصف') }}</p>
                        <div class="bg-gray-50 dark:bg-slate-900/50 p-4 rounded-xl border border-gray-100 dark:border-gray-800">
                            <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed whitespace-pre-wrap">{{ $report->description }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Location Card --}}
            <div class="glass-card rounded-2xl p-6 animate-fade-in-up stagger-3">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-100 dark:border-gray-800">{{ __('الموقع') }}</h2>
                @if($report->location_text)
                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-4 bg-gray-50 dark:bg-slate-900/50 p-3 rounded-xl border border-gray-100 dark:border-gray-800">
                        <span class="font-bold text-gray-500 block mb-1 text-xs">{{ __('الوصف النصي للموقع') }}</span>
                        {{ $report->location_text }}
                    </p>
                @endif

                @if($report->latitude && $report->longitude)
                    <div class="flex items-center gap-4 mb-3 text-xs font-mono text-gray-500">
                        <div class="bg-gray-100 dark:bg-slate-800 px-2 py-1 rounded">X: {{ $report->longitude }}</div>
                        <div class="bg-gray-100 dark:bg-slate-800 px-2 py-1 rounded">Y: {{ $report->latitude }}</div>
                    </div>
                    <div id="map" class="h-64 w-full rounded-xl border border-gray-200 dark:border-gray-700 shadow-inner z-10"></div>
                @else
                    <p class="text-sm text-amber-600 bg-amber-50 p-3 rounded-lg">{{ __('لم يتم إرفاق إحداثيات جغرافية مع هذا البلاغ.') }}</p>
                @endif
            </div>

        </div>

        {{-- Sidebar: Media Column --}}
        <div class="space-y-6">
            <div class="glass-card rounded-2xl p-6 animate-fade-in-up stagger-4">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-100 dark:border-gray-800">{{ __('المرفقات') }}</h2>
                
                @if(!$report->image_url && !$report->video_url)
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                        <p class="text-sm text-gray-500">{{ __('لا يوجد مرفقات') }}</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @if($report->image_url)
                            <div>
                                <p class="text-xs font-bold text-gray-500 mb-2 uppercase">{{ __('صورة مرفقة') }}</p>
                                <a href="{{ Storage::url($report->image_url) }}" target="_blank" class="block group relative rounded-xl overflow-hidden shadow-sm border border-gray-200 dark:border-gray-700">
                                    <img src="{{ Storage::url($report->image_url) }}" class="w-full object-cover transition-transform duration-300 group-hover:scale-105" alt="Report Image">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <svg class="w-8 h-8 text-white drop-shadow" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607ZM10.5 7.5v6m3-3h-6" />
                                        </svg>
                                    </div>
                                </a>
                            </div>
                        @endif

                        @if($report->video_url)
                            <div>
                                <p class="text-xs font-bold text-gray-500 mb-2 uppercase">{{ __('فيديو مرفق') }}</p>
                                <div class="rounded-xl overflow-hidden shadow-sm border border-gray-200 dark:border-gray-700 bg-black">
                                    <video src="{{ Storage::url($report->video_url) }}" controls class="w-full"></video>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

@push('scripts')
@if($report->latitude && $report->longitude)
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var lat = {{ $report->latitude }};
        var lng = {{ $report->longitude }};
        
        var map = L.map('map').setView([lat, lng], 15);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        L.marker([lat, lng]).addTo(map)
            .bindPopup('{{ __("موقع البلاغ") }}')
            .openPopup();
    });
</script>
@endif
@endpush
@endsection
