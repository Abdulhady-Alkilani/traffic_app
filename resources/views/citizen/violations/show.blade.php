@extends('layouts.app')

@section('title', __('تفاصيل المخالفة') . ' #' . $violation->id)

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-8 animate-fade-in-up">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                <svg class="w-8 h-8 text-rose-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3Z" />
                </svg>
                {{ __('تفاصيل المخالفة') }}
                <span class="text-rose-600 font-mono">#{{ $violation->id }}</span>
            </h1>
        </div>
        <a href="{{ route('citizen.violations.index') }}" class="flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition-all shadow-sm">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
            </svg>
            {{ __('رجوع للمخالفات') }}
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Violation Info --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Status Card --}}
            <div class="glass-card rounded-2xl p-6 border-l-4 {{ $violation->status->value === 'paid' ? 'border-emerald-500 bg-emerald-50/10' : 'border-rose-500 bg-rose-50/10' }} animate-fade-in-up stagger-1">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">{{ __('حالة الدفع') }}</h2>
                        <div class="flex items-center gap-2">
                            @if($violation->status->value === 'unpaid')
                                <span class="flex h-3 w-3 relative">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500"></span>
                                </span>
                            @else
                                <svg class="w-5 h-5 text-emerald-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                </svg>
                            @endif
                            <p class="text-xl font-bold {{ $violation->status->value === 'paid' ? 'text-emerald-700 dark:text-emerald-400' : 'text-rose-700 dark:text-rose-400' }}">
                                {{ __('messages.' . $violation->status->value) }}
                            </p>
                        </div>
                        @if($violation->paid_at)
                            <p class="text-xs text-emerald-600 mt-1">{{ __('تم الدفع في') }}: {{ $violation->paid_at->format('Y/m/d h:i A') }}</p>
                        @endif
                    </div>
                    
                    <div class="text-start sm:text-end border-t sm:border-t-0 sm:border-r border-gray-200 dark:border-gray-700 pt-4 sm:pt-0 sm:pr-6">
                        <h2 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">{{ __('قيمة المخالفة') }}</h2>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($violation->fine_amount, 0) }} <span class="text-sm font-normal text-gray-500">ل.س</span></p>
                    </div>
                </div>

                @if($violation->status->value === 'unpaid')
                <div class="mt-6 pt-4 border-t border-rose-100 dark:border-rose-900/30 flex justify-end">
                    <form onsubmit="return false;" id="payment-form" class="inline-block">
                        <button type="button" onclick="processPayment({{ $violation->id }})" class="flex items-center gap-2 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/30 transition-all hover:scale-105 active:scale-95">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                            </svg>
                            {{ __('تسديد الفاتورة') }}
                        </button>
                    </form>
                </div>
                @endif
            </div>

            {{-- Violation Details --}}
            <div class="glass-card rounded-2xl p-6 animate-fade-in-up stagger-2">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-100 dark:border-gray-800">{{ __('تفاصيل المخالفة') }}</h2>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-800">
                        <span class="text-gray-500 font-semibold">{{ __('نوع المخالفة') }}</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ __('messages.' . $violation->violation_type) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-800">
                        <span class="text-gray-500 font-semibold text-sm">{{ __('تاريخ وتسجيل المخالفة') }}</span>
                        <span class="font-bold text-gray-900 dark:text-white text-left" dir="ltr">{{ $violation->issued_at->format('Y/m/d h:i A') }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row justify-between sm:items-center py-2 gap-2">
                        <span class="text-gray-500 font-semibold text-sm">{{ __('وصف موقع المخالفة') }}</span>
                        <span class="font-bold text-gray-900 dark:text-white max-w-sm sm:text-end">{{ $violation->location_text }}</span>
                    </div>
                </div>

                @if($violation->latitude && $violation->longitude)
                <div class="mt-6">
                    <p class="text-xs font-bold text-gray-500 uppercase mb-2">{{ __('الموقع الجغرافي') }}</p>
                    <div id="map" class="h-64 w-full rounded-xl border border-gray-200 dark:border-gray-700 shadow-inner z-10"></div>
                </div>
                @endif
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            
            {{-- Vehicle Details --}}
            <div class="glass-card rounded-2xl p-6 animate-fade-in-up stagger-3">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-100 dark:border-gray-800">{{ __('المركبة المخالفة') }}</h2>
                
                @if($violation->vehicle)
                <div class="text-center mb-4">
                    <div class="w-16 h-16 mx-auto bg-indigo-50 dark:bg-indigo-900/20 rounded-full flex items-center justify-center border-2 border-indigo-100 dark:border-indigo-800 mb-2">
                        <svg class="w-8 h-8 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                        </svg>
                    </div>
                    <p class="font-mono text-lg font-bold text-gray-900 dark:text-white">{{ $violation->vehicle->plate_number }}</p>
                    <p class="text-sm text-gray-500">{{ $violation->vehicle->make }} {{ $violation->vehicle->model_year }} • {{ $violation->vehicle->color }}</p>
                </div>
                
                <a href="{{ route('citizen.vehicles.show', $violation->vehicle) }}" class="block w-full text-center px-4 py-2 bg-indigo-50 dark:bg-indigo-900/30 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 font-semibold rounded-lg transition-colors text-sm">
                    {{ __('عرض تفاصيل المركبة') }}
                </a>
                @else
                <p class="text-gray-500 text-sm text-center py-4">{{ __('تفاصيل المركبة غير متوفرة') }}</p>
                @endif
            </div>

            {{-- Source / Report --}}
            @if($violation->report_id)
            <div class="glass-card rounded-2xl p-6 animate-fade-in-up stagger-4">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-100 dark:border-gray-800">{{ __('مصدر المخالفة') }}</h2>
                <div class="bg-purple-50 dark:bg-purple-900/10 border border-purple-100 dark:border-purple-900/30 p-4 rounded-xl">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                        {{ __('تم تسجيل هذه المخالفة بناءً على بلاغ.') }}
                    </p>
                    <a href="{{ route('citizen.reports.show', $violation->report_id) }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-purple-600 hover:text-purple-700">
                        {{ __('عرض تفاصيل البلاغ') }}
                        <svg class="w-4 h-4 rtl:-scale-x-100" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>
                </div>
            </div>
            @endif

        </div>

    </div>
</div>

@push('scripts')
@if($violation->status->value === 'unpaid')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function processPayment(violationId) {
        Swal.fire({
            title: '{{ __("هل أنت متأكد من تسديد المخالفة؟") }}',
            text: "{{ __('سيتم اقتطاع المبلغ من رصيدك.') }}",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#059669',
            cancelButtonColor: '#d33',
            confirmButtonText: '{{ __("نعم، سدد الآن") }}',
            cancelButtonText: '{{ __("إلغاء") }}',
            showClass: {
                popup: 'animate__animated animate__fadeInDown animate__faster'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp animate__faster'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: '{{ __("جاري المعالجة...") }}',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });

                fetch(`/citizen/violations/${violationId}/pay`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        Swal.fire({
                            title: '{{ __("نجاح!") }}',
                            text: data.message,
                            icon: 'success',
                            confirmButtonColor: '#059669'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Error', data.message || 'Something went wrong', 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error', 'Network error occurred', 'error');
                });
            }
        })
    }
</script>
@endif

@if($violation->latitude && $violation->longitude)
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var lat = {{ $violation->latitude }};
        var lng = {{ $violation->longitude }};
        
        var map = L.map('map').setView([lat, lng], 15);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        var circle = L.circle([lat, lng], {
            color: 'red',
            fillColor: '#f03',
            fillOpacity: 0.3,
            radius: 100
        }).addTo(map);
        
        L.marker([lat, lng]).addTo(map)
            .bindPopup('{{ __("موقع المخالفة التقريبي") }}')
            .openPopup();
    });
</script>
@endif
@endpush
@endsection
