@extends('layouts.app')

@section('title', __('messages.new_report'))

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8" x-data="reportWizard()">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ __('messages.new_report') }}</h1>
    <p class="text-gray-600 dark:text-gray-400 mb-8">{{ __('messages.report_wizard_subtitle') }}</p>

    <div class="mb-8">
        <div class="flex items-center justify-between mb-2">
            <template for="1, 2, 3">
                <div></div>
            </template>
        </div>
        <div class="flex items-center gap-2">
            <template x-for="i in 3" :key="i">
                <div class="flex-1 flex items-center gap-2">
                    <div :class="step >= i ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-500'"
                         class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">
                        <span x-text="i"></span>
                    </div>
                    <div x-show="i < 3" :class="step > i ? 'bg-blue-600' : 'bg-gray-200 dark:bg-gray-700'" class="flex-1 h-1 rounded"></div>
                </div>
            </template>
        </div>
    </div>

    <form method="POST" action="{{ route('citizen.reports.store') }}" enctype="multipart/form-data" id="reportForm">
        @csrf

        <div x-show="step === 1">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">{{ __('messages.step1_title') }}</h2>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <template x-for="type in reportTypes" :key="type.value">
                        <button type="button" @click="form.report_type = type.value"
                            :class="form.report_type === type.value ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 ring-2 ring-blue-500' : 'border-gray-200 dark:border-gray-600'"
                            class="p-4 border-2 rounded-xl text-center hover:border-blue-300 transition">
                            <span class="text-3xl" x-text="type.icon"></span>
                            <p class="mt-2 font-medium text-gray-900 dark:text-white" x-text="type.label"></p>
                        </button>
                    </template>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h3 class="font-medium text-gray-900 dark:text-white mb-2">{{ __('messages.location') }}</h3>
                    <div x-show="gettingLocation" class="text-sm text-blue-600">
                        {{ __('messages.getting_location') }}...
                    </div>
                    <div x-show="!gettingLocation && form.latitude">
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            {{ __('messages.coordinates') }}: <span x-text="form.latitude?.toFixed(6)"></span>, <span x-text="form.longitude?.toFixed(6)"></span>
                        </p>
                    </div>
                    <div x-show="!gettingLocation && !form.latitude">
                        <button type="button" @click="getLocation()" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                            {{ __('messages.detect_location') }}
                        </button>
                    </div>
                    <input type="text" name="location_text" x-model="form.location_text"
                        placeholder="{{ __('messages.location_description') }}"
                        class="mt-3 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                </div>

                <input type="hidden" name="latitude" x-model="form.latitude">
                <input type="hidden" name="longitude" x-model="form.longitude">
            </div>
        </div>

        <div x-show="step === 2" x-cloak>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">{{ __('messages.step2_title') }}</h2>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.select_vehicle') }}</label>
                    <select name="vehicle_id" x-model="form.vehicle_id"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">{{ __('messages.no_vehicle') }}</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}">{{ $vehicle->plate_number }} - {{ $vehicle->make }} {{ $vehicle->model_year }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.image') }}</label>
                    <input type="file" name="image" accept="image/*"
                        class="w-full text-sm text-gray-600 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.description') }}</label>
                    <textarea name="description" x-model="form.description" rows="4"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm"
                        placeholder="{{ __('messages.describe_incident') }}" required></textarea>
                </div>
            </div>
        </div>

        <div x-show="step === 3" x-cloak>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">{{ __('messages.step3_title') }}</h2>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">{{ __('messages.report_type') }}</span>
                        <span class="font-medium text-gray-900 dark:text-white capitalize" x-text="reportTypes.find(t => t.value === form.report_type)?.label || '-'"></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700" x-show="form.latitude">
                        <span class="text-gray-600 dark:text-gray-400">{{ __('messages.location') }}</span>
                        <span class="font-medium text-gray-900 dark:text-white" x-text="form.latitude?.toFixed(6) + ', ' + form.longitude?.toFixed(6)"></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700" x-show="form.location_text">
                        <span class="text-gray-600 dark:text-gray-400">{{ __('messages.location_description') }}</span>
                        <span class="font-medium text-gray-900 dark:text-white" x-text="form.location_text"></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700" x-show="form.vehicle_id">
                        <span class="text-gray-600 dark:text-gray-400">{{ __('messages.vehicle') }}</span>
                        <span class="font-medium text-gray-900 dark:text-white" x-text="form.vehicle_id"></span>
                    </div>
                    <div class="py-2">
                        <span class="text-gray-600 dark:text-gray-400">{{ __('messages.description') }}</span>
                        <p class="font-medium text-gray-900 dark:text-white mt-1" x-text="form.description"></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-between">
            <button type="button" x-show="step > 1" @click="step--"
                class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                {{ __('messages.back') }}
            </button>
            <div x-show="step === 1"></div>

            <button type="button" x-show="step < 3" @click="nextStep()"
                :disabled="step === 1 && !form.report_type"
                :class="!form.report_type && step === 1 ? 'opacity-50 cursor-not-allowed' : ''"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                {{ __('messages.next') }}
            </button>

            <button type="submit" x-show="step === 3"
                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                {{ __('messages.submit_report') }}
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function reportWizard() {
    return {
        step: 1,
        gettingLocation: false,
        form: {
            report_type: '',
            latitude: null,
            longitude: null,
            location_text: '',
            vehicle_id: '',
            description: '',
        },
        reportTypes: [
            { value: 'accident', label: '{{ __("messages.accident") }}', icon: '🚗' },
            { value: 'hazard', label: '{{ __("messages.hazard") }}', icon: '⚠️' },
            { value: 'traffic_jam', label: '{{ __("messages.traffic_jam") }}', icon: '🚦' },
            { value: 'security_threat', label: '{{ __("messages.security_threat") }}', icon: '🛡️' },
        ],
        nextStep() {
            if (this.step === 1 && !this.form.report_type) return;
            this.step++;
        },
        getLocation() {
            this.gettingLocation = true;
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    this.form.latitude = pos.coords.latitude;
                    this.form.longitude = pos.coords.longitude;
                    this.gettingLocation = false;
                },
                () => { this.gettingLocation = false; },
                { enableHighAccuracy: true }
            );
        }
    }
}
</script>
@endpush
@endsection
