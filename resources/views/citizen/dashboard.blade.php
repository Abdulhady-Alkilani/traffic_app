@extends('layouts.app')

@section('title', __('messages.dashboard'))

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    {{-- Header --}}
    <div class="mb-8 animate-fade-in-up">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                    {{ __('messages.welcome') }}, {{ $citizenData->full_name }}
                    <span class="animate-bounce-gentle inline-block">👋</span>
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">{{ __('messages.dashboard_subtitle') }}</p>
            </div>
            <a href="{{ route('citizen.reports.create') }}"
               class="group inline-flex items-center gap-2 px-6 py-3 rounded-xl font-semibold text-white bg-rose-600 hover:bg-rose-700 shadow-lg shadow-rose-500/25 hover:shadow-xl hover:shadow-rose-500/30 transition-all duration-300 hover:-translate-y-0.5">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
                {{ __('messages.new_report') }}
                <svg class="w-4 h-4 rtl:rotate-180 group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </a>
        </div>
    </div>

    {{-- Stats Cards Row 1 --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
        {{-- Vehicles --}}
        <a href="{{ route('citizen.vehicles.index') }}" class="glass-card rounded-2xl p-5 animate-fade-in-up stagger-1 group hover:shadow-lg hover:shadow-indigo-500/10 transition-all duration-300 block">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.my_vehicles') }}</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $vehiclesCount }}</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-500/20 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                    </svg>
                </div>
            </div>
        </a>

        {{-- Reports --}}
        <a href="{{ route('citizen.reports.index') }}" class="glass-card rounded-2xl p-5 animate-fade-in-up stagger-2 group hover:shadow-lg hover:shadow-purple-500/10 transition-all duration-300 block">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.my_reports') }}</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $reportsCount }}</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-purple-600 flex items-center justify-center shadow-lg shadow-purple-500/20 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                </div>
            </div>
        </a>

        {{-- Violations --}}
        <a href="{{ route('citizen.violations.index') }}" class="glass-card rounded-2xl p-5 animate-fade-in-up stagger-3 group hover:shadow-lg hover:shadow-rose-500/10 transition-all duration-300 block">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.my_violations') }}</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $violationsCount }}</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-rose-600 flex items-center justify-center shadow-lg shadow-rose-500/20 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                </div>
            </div>
        </a>

        {{-- Unpaid Fines --}}
        <div class="glass-card rounded-2xl p-5 animate-fade-in-up stagger-4 group hover:shadow-lg hover:shadow-amber-500/10 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('غرامات غير مدفوعة') }}</p>
                    <p class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ number_format($unpaidFines, 0) }} <span class="text-sm font-medium">ل.س</span></p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-500 flex items-center justify-center shadow-lg shadow-amber-500/20 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-800">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ __('إجمالي الغرامات') }}: <span class="font-bold text-gray-700 dark:text-gray-300">{{ number_format($totalFines, 0) }} ل.س</span>
                </p>
            </div>
        </div>
    </div>

    {{-- Charts Row 1: Doughnut + Bar --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Violations by Status (Doughnut) --}}
        <div class="glass-card rounded-2xl p-6 animate-fade-in-up stagger-5">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                    {{ __('حالة المخالفات') }}
                </h3>
            </div>
            <div class="relative" style="height: 280px;">
                <canvas id="violationsStatusChart"></canvas>
            </div>
        </div>

        {{-- Reports by Status (Bar) --}}
        <div class="glass-card rounded-2xl p-6 animate-fade-in-up stagger-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-purple-500"></span>
                    {{ __('حالة البلاغات') }}
                </h3>
            </div>
            <div class="relative" style="height: 280px;">
                <canvas id="reportsStatusChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Charts Row 2: Line (full width) --}}
    <div class="glass-card rounded-2xl p-6 mb-6 animate-fade-in-up stagger-7">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                {{ __('المخالفات خلال الأشهر الستة الأخيرة') }}
            </h3>
        </div>
        <div class="relative" style="height: 280px;">
            <canvas id="monthlyViolationsChart"></canvas>
        </div>
    </div>

    {{-- Charts Row 3: Polar Area + Horizontal Bar --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Violations by Type (Polar Area) --}}
        <div class="glass-card rounded-2xl p-6 animate-fade-in-up stagger-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-cyan-500"></span>
                    {{ __('أنواع المخالفات') }}
                </h3>
            </div>
            <div class="relative" style="height: 280px;">
                <canvas id="violationsTypeChart"></canvas>
            </div>
        </div>

        {{-- Vehicles by Type (Horizontal Bar) --}}
        <div class="glass-card rounded-2xl p-6 animate-fade-in-up stagger-9">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-indigo-500"></span>
                    {{ __('أنواع المركبات') }}
                </h3>
            </div>
            <div class="relative" style="height: 280px;">
                <canvas id="vehiclesTypeChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isDark = document.documentElement.classList.contains('dark');
    const textColor = isDark ? '#9ca3af' : '#6b7280';
    const gridColor = isDark ? 'rgba(75,85,99,0.3)' : 'rgba(229,231,235,0.8)';
    const fontFamily = "'Tajawal', sans-serif";

    const commonLegend = {
        position: 'bottom',
        labels: {
            color: textColor,
            font: { family: fontFamily, size: 12, weight: '600' },
            padding: 16,
            usePointStyle: true,
            pointStyle: 'circle'
        }
    };

    // 1) Violations by Status — Doughnut
    new Chart(document.getElementById('violationsStatusChart'), {
        type: 'doughnut',
        data: {
            labels: [
                '{{ __("messages.unpaid") }}',
                '{{ __("messages.paid") }}',
                '{{ __("messages.canceled") }}'
            ],
            datasets: [{
                data: [
                    {{ $violationsByStatus['unpaid'] ?? 0 }},
                    {{ $violationsByStatus['paid'] ?? 0 }},
                    {{ $violationsByStatus['canceled'] ?? 0 }}
                ],
                backgroundColor: ['#e11d48', '#059669', '#6b7280'],
                hoverBackgroundColor: ['#be123c', '#047857', '#4b5563'],
                borderWidth: 0,
                cutout: '72%',
                borderRadius: 6,
                spacing: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: commonLegend,
                tooltip: {
                    backgroundColor: isDark ? '#1e293b' : '#fff',
                    titleColor: isDark ? '#f1f5f9' : '#111827',
                    bodyColor: isDark ? '#cbd5e1' : '#4b5563',
                    borderColor: isDark ? '#334155' : '#e5e7eb',
                    borderWidth: 1,
                    cornerRadius: 12,
                    padding: 12,
                    titleFont: { family: fontFamily, weight: 'bold' },
                    bodyFont: { family: fontFamily }
                }
            }
        }
    });

    // 2) Reports by Status — Bar Chart
    new Chart(document.getElementById('reportsStatusChart'), {
        type: 'bar',
        data: {
            labels: [
                '{{ __("messages.pending") }}',
                '{{ __("messages.in_progress") }}',
                '{{ __("messages.resolved") }}',
                '{{ __("messages.rejected") }}'
            ],
            datasets: [{
                label: '{{ __("عدد البلاغات") }}',
                data: [
                    {{ $reportsByStatus['pending'] ?? 0 }},
                    {{ $reportsByStatus['in_progress'] ?? 0 }},
                    {{ $reportsByStatus['resolved'] ?? 0 }},
                    {{ $reportsByStatus['rejected'] ?? 0 }}
                ],
                backgroundColor: ['#f59e0b', '#3b82f6', '#10b981', '#ef4444'],
                borderRadius: 8,
                borderSkipped: false,
                barThickness: 36,
                maxBarThickness: 44
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: isDark ? '#1e293b' : '#fff',
                    titleColor: isDark ? '#f1f5f9' : '#111827',
                    bodyColor: isDark ? '#cbd5e1' : '#4b5563',
                    borderColor: isDark ? '#334155' : '#e5e7eb',
                    borderWidth: 1,
                    cornerRadius: 12,
                    padding: 12,
                    titleFont: { family: fontFamily, weight: 'bold' },
                    bodyFont: { family: fontFamily }
                }
            },
            scales: {
                x: {
                    ticks: { color: textColor, font: { family: fontFamily, size: 12, weight: '600' } },
                    grid: { display: false },
                    border: { display: false }
                },
                y: {
                    beginAtZero: true,
                    ticks: { color: textColor, font: { family: fontFamily }, stepSize: 1, precision: 0 },
                    grid: { color: gridColor },
                    border: { display: false }
                }
            }
        }
    });

    // 3) Monthly Violations — Line Chart
    new Chart(document.getElementById('monthlyViolationsChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($monthLabels) !!},
            datasets: [{
                label: '{{ __("المخالفات") }}',
                data: {!! json_encode($monthData) !!},
                borderColor: '#6366f1',
                backgroundColor: (ctx) => {
                    const chart = ctx.chart;
                    const { ctx: c, chartArea } = chart;
                    if (!chartArea) return 'rgba(99,102,241,0.1)';
                    const gradient = c.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                    gradient.addColorStop(0, 'rgba(99,102,241,0.3)');
                    gradient.addColorStop(1, 'rgba(99,102,241,0.02)');
                    return gradient;
                },
                fill: true,
                tension: 0.4,
                pointRadius: 6,
                pointHoverRadius: 8,
                pointBackgroundColor: '#6366f1',
                pointBorderColor: isDark ? '#1e293b' : '#ffffff',
                pointBorderWidth: 3,
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: isDark ? '#1e293b' : '#fff',
                    titleColor: isDark ? '#f1f5f9' : '#111827',
                    bodyColor: isDark ? '#cbd5e1' : '#4b5563',
                    borderColor: isDark ? '#334155' : '#e5e7eb',
                    borderWidth: 1,
                    cornerRadius: 12,
                    padding: 12,
                    titleFont: { family: fontFamily, weight: 'bold' },
                    bodyFont: { family: fontFamily }
                }
            },
            scales: {
                x: {
                    ticks: { color: textColor, font: { family: fontFamily, size: 11, weight: '600' } },
                    grid: { display: false },
                    border: { display: false }
                },
                y: {
                    beginAtZero: true,
                    ticks: { color: textColor, font: { family: fontFamily }, stepSize: 1, precision: 0 },
                    grid: { color: gridColor },
                    border: { display: false }
                }
            }
        }
    });

    // 4) Violations by Type — Polar Area
    const violationTypeLabels = {!! json_encode(array_keys($violationsByType)) !!};
    const violationTypeData = {!! json_encode(array_values($violationsByType)) !!};
    const typeColors = ['#06b6d4', '#8b5cf6', '#f43f5e', '#f97316', '#22d3ee', '#84cc16'];
    const translatedTypeLabels = violationTypeLabels.map(label => {
        const map = {
            'speeding': '{{ __("messages.speeding") }}',
            'reckless_driving': '{{ __("messages.reckless_driving") }}',
            'red_light': '{{ __("messages.red_light") }}',
            'illegal_parking': '{{ __("messages.illegal_parking") }}',
            'no_seatbelt': '{{ __("messages.no_seatbelt") }}',
            'using_phone': '{{ __("messages.using_phone") }}'
        };
        return map[label] || label;
    });

    new Chart(document.getElementById('violationsTypeChart'), {
        type: 'polarArea',
        data: {
            labels: translatedTypeLabels,
            datasets: [{
                data: violationTypeData,
                backgroundColor: typeColors.map(c => c + '99'),
                borderColor: typeColors,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    ...commonLegend,
                    position: 'bottom'
                },
                tooltip: {
                    backgroundColor: isDark ? '#1e293b' : '#fff',
                    titleColor: isDark ? '#f1f5f9' : '#111827',
                    bodyColor: isDark ? '#cbd5e1' : '#4b5563',
                    borderColor: isDark ? '#334155' : '#e5e7eb',
                    borderWidth: 1,
                    cornerRadius: 12,
                    padding: 12,
                    titleFont: { family: fontFamily, weight: 'bold' },
                    bodyFont: { family: fontFamily }
                }
            },
            scales: {
                r: {
                    ticks: { display: false },
                    grid: { color: gridColor }
                }
            }
        }
    });

    // 5) Vehicles by Type — Horizontal Bar
    const vehicleTypeLabels = {!! json_encode(array_keys($vehiclesByType)) !!};
    const vehicleTypeData = {!! json_encode(array_values($vehiclesByType)) !!};
    const vehicleTypeMap = {
        'sedan': '{{ __("سيدان") }}',
        'suv': '{{ __("دفع رباعي") }}',
        'truck': '{{ __("شاحنة") }}',
        'motorcycle': '{{ __("دراجة نارية") }}',
        'bus': '{{ __("حافلة") }}',
        'van': '{{ __("فان") }}'
    };
    const translatedVehicleLabels = vehicleTypeLabels.map(l => vehicleTypeMap[l] || l);
    const vehicleColors = ['#6366f1', '#a855f7', '#ec4899', '#14b8a6', '#f59e0b', '#3b82f6'];

    new Chart(document.getElementById('vehiclesTypeChart'), {
        type: 'bar',
        data: {
            labels: translatedVehicleLabels,
            datasets: [{
                label: '{{ __("عدد المركبات") }}',
                data: vehicleTypeData,
                backgroundColor: vehicleColors.slice(0, vehicleTypeData.length),
                borderRadius: 8,
                borderSkipped: false,
                barThickness: 24,
                maxBarThickness: 32
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: isDark ? '#1e293b' : '#fff',
                    titleColor: isDark ? '#f1f5f9' : '#111827',
                    bodyColor: isDark ? '#cbd5e1' : '#4b5563',
                    borderColor: isDark ? '#334155' : '#e5e7eb',
                    borderWidth: 1,
                    cornerRadius: 12,
                    padding: 12,
                    titleFont: { family: fontFamily, weight: 'bold' },
                    bodyFont: { family: fontFamily }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: { color: textColor, font: { family: fontFamily }, stepSize: 1, precision: 0 },
                    grid: { color: gridColor },
                    border: { display: false }
                },
                y: {
                    ticks: { color: textColor, font: { family: fontFamily, size: 12, weight: '600' } },
                    grid: { display: false },
                    border: { display: false }
                }
            }
        }
    });
});
</script>
@endsection
