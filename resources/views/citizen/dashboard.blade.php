@extends('layouts.app')

@section('title', __('messages.dashboard'))

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('messages.welcome') }}, {{ $citizenData->full_name }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('messages.dashboard_subtitle') }}</p>
    </div>

    <div class="mb-6">
        <a href="{{ route('citizen.reports.create') }}"
           class="inline-flex items-center gap-2 bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition font-medium text-lg">
            <span>🚨</span> {{ __('messages.new_report') }}
        </a>
    </div>

    <div x-data="{ tab: 'vehicles' }">
        <div class="flex border-b border-gray-200 dark:border-gray-700 mb-6">
            <button @click="tab = 'vehicles'"
                :class="tab === 'vehicles' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700'"
                class="px-6 py-3 font-medium border-b-2 transition">
                {{ __('messages.my_vehicles') }} ({{ $vehicles->total() }})
            </button>
            <button @click="tab = 'reports'"
                :class="tab === 'reports' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700'"
                class="px-6 py-3 font-medium border-b-2 transition">
                {{ __('messages.my_reports') }} ({{ $reports->total() }})
            </button>
            <button @click="tab = 'violations'"
                :class="tab === 'violations' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700'"
                class="px-6 py-3 font-medium border-b-2 transition">
                {{ __('messages.my_violations') }} ({{ $violations->total() }})
            </button>
        </div>

        <div x-show="tab === 'vehicles'" x-cloak>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('messages.my_vehicles') }}</h3>
                    <button onclick="document.getElementById('addVehicleForm').classList.toggle('hidden')"
                        class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                        + {{ __('messages.add_vehicle') }}
                    </button>
                </div>

                <div id="addVehicleForm" class="hidden p-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <form method="POST" action="{{ route('citizen.vehicles.store') }}">
                        @csrf
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <input type="text" name="plate_number" placeholder="{{ __('messages.plate_number') }}"
                                class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm" required>
                            <select name="vehicle_type" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm" required>
                                <option value="sedan">Sedan</option>
                                <option value="suv">SUV</option>
                                <option value="truck">Truck</option>
                                <option value="motorcycle">Motorcycle</option>
                                <option value="van">Van</option>
                            </select>
                            <input type="text" name="make" placeholder="{{ __('messages.make') }}"
                                class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm" required>
                            <input type="text" name="model_year" placeholder="{{ __('messages.model_year') }}"
                                class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm" required>
                            <input type="text" name="color" placeholder="{{ __('messages.color') }}"
                                class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm" required>
                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700 col-span-2 md:col-span-1">
                                {{ __('messages.save') }}
                            </button>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('messages.plate_number') }}</th>
                                <th class="px-4 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('messages.type') }}</th>
                                <th class="px-4 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('messages.make') }}</th>
                                <th class="px-4 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('messages.year') }}</th>
                                <th class="px-4 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('messages.color') }}</th>
                                <th class="px-4 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                            @forelse($vehicles as $vehicle)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-3 font-medium">{{ $vehicle->plate_number }}</td>
                                <td class="px-4 py-3">{{ $vehicle->vehicle_type }}</td>
                                <td class="px-4 py-3">{{ $vehicle->make }}</td>
                                <td class="px-4 py-3">{{ $vehicle->model_year }}</td>
                                <td class="px-4 py-3">{{ $vehicle->color }}</td>
                                <td class="px-4 py-3">
                                    <form method="POST" action="{{ route('citizen.vehicles.destroy', $vehicle) }}" onsubmit="return confirm('Are you sure?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm">{{ __('messages.delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">{{ __('messages.no_vehicles') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $vehicles->withQueryString()->links() }}
                </div>
            </div>
        </div>

        <div x-show="tab === 'reports'" x-cloak>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('messages.my_reports') }}</h3>
                </div>

                <div class="divide-y divide-gray-200 dark:divide-gray-600">
                    @forelse($reports as $report)
                    <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="inline-block px-2 py-1 text-xs font-medium rounded-full
                                    {{ $report->status->value === 'new' ? 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200' : '' }}
                                    {{ $report->status->value === 'in_progress' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-200' : '' }}
                                    {{ $report->status->value === 'resolved' ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200' : '' }}
                                    {{ $report->status->value === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200' : '' }}
                                ">
                                    {{ $report->status->label() }}
                                </span>
                                <h4 class="mt-1 font-medium text-gray-900 dark:text-white capitalize">{{ str_replace('_', ' ', $report->report_type) }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ Str::limit($report->description, 100) }}</p>
                                @if($report->vehicle)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('messages.vehicle') }}: {{ $report->vehicle->plate_number }}</p>
                                @endif
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $report->created_at->format('M d, Y H:i') }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-gray-500">{{ __('messages.no_reports') }}</div>
                    @endforelse
                </div>

                <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $reports->withQueryString()->links() }}
                </div>
            </div>
        </div>

        <div x-show="tab === 'violations'" x-cloak>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('messages.my_violations') }}</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">#</th>
                                <th class="px-4 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('messages.issued_at') }}</th>
                                <th class="px-4 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('messages.violation_type') }}</th>
                                <th class="px-4 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('messages.fine_amount') }}</th>
                                <th class="px-4 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('messages.plate_number') }}</th>
                                <th class="px-4 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('messages.status') }}</th>
                                <th class="px-4 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                            @forelse($violations as $violation)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-3 font-medium">{{ $violation->id }}</td>
                                <td class="px-4 py-3">{{ $violation->issued_at->format('M d, Y') }}</td>
                                <td class="px-4 py-3 capitalize">{{ str_replace('_', ' ', $violation->violation_type) }}</td>
                                <td class="px-4 py-3">{{ number_format($violation->fine_amount, 2) }} SAR</td>
                                <td class="px-4 py-3">{{ $violation->vehicle?->plate_number ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-block px-2 py-1 text-xs font-medium rounded-full
                                        {{ $violation->status->value === 'unpaid' ? 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200' : '' }}
                                        {{ $violation->status->value === 'paid' ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200' : '' }}
                                        {{ $violation->status->value === 'canceled' ? 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200' : '' }}
                                    ">
                                        {{ __( 'messages.' . $violation->status->value) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($violation->status->value === 'unpaid')
                                    <button onclick="payViolation({{ $violation->id }})"
                                        class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">
                                        {{ __('messages.pay_now') }}
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">{{ __('messages.no_violations') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $violations->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function payViolation(violationId) {
    if (!confirm('{{ __("messages.pay_now") }}?')) return;

    fetch('/citizen/violations/' + violationId + '/pay', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message || 'Error');
        }
    })
    .catch(() => alert('Error processing payment'));
}
</script>
@endsection
