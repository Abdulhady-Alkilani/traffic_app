@extends('layouts.app')

@section('title', __('messages.new_report'))

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<div class="max-w-2xl mx-auto px-4 py-8" x-data="quickReport()">
    <div x-show="!submitted">
        {{-- Header --}}
        <div class="text-center mb-6 animate-fade-in-up">
            <div class="w-14 h-14 mx-auto bg-rose-600 rounded-2xl flex items-center justify-center shadow-lg shadow-rose-500/25 mb-3">
                <svg class="w-7 h-7 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('messages.new_report') }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('messages.report_wizard_subtitle') }}</p>
        </div>

        <form method="POST" action="{{ route('citizen.reports.store') }}" enctype="multipart/form-data" id="reportForm">
            @csrf

            {{-- 1. Report Type (Quick Select) --}}
            <div class="glass-card rounded-2xl p-5 mb-4 animate-fade-in-up stagger-1">
                <h2 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                    <span class="w-6 h-6 bg-indigo-600 text-white rounded-lg flex items-center justify-center text-xs font-bold">1</span>
                    {{ __('messages.step1_title') }}
                </h2>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <template x-for="type in reportTypes" :key="type.value">
                        <button type="button" @click="form.report_type = type.value"
                            :class="form.report_type === type.value
                                ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 ring-2 ring-indigo-500/30 shadow-md'
                                : 'border-gray-200 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-700 hover:shadow-sm'"
                            class="relative p-3 border-2 rounded-xl text-center transition-all duration-200">
                            <span class="text-2xl block mb-1" x-text="type.icon"></span>
                            <p class="font-semibold text-gray-900 dark:text-white text-xs leading-tight" x-text="type.label"></p>
                            <div x-show="form.report_type === type.value" x-transition class="absolute -top-1.5 -end-1.5">
                                <svg class="w-5 h-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                    <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </template>
                </div>
                <input type="hidden" name="report_type" x-model="form.report_type">
            </div>

            {{-- 2. Vehicle Search or Manual Entry --}}
            <div class="glass-card rounded-2xl p-5 mb-4 animate-fade-in-up stagger-2">
                <h2 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 bg-indigo-600 text-white rounded-lg flex items-center justify-center text-xs font-bold">2</span>
                        {{ __('المركبة المبلغ عنها') }}
                        <span class="text-xs font-normal text-gray-400">({{ __('اختياري') }})</span>
                    </div>
                    
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <div class="relative flex items-center justify-center">
                            <input type="checkbox" name="unknown_plate" value="1" x-model="form.unknown_plate" class="peer sr-only">
                            <div class="w-5 h-5 border-2 border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-slate-800 peer-checked:bg-indigo-500 peer-checked:border-indigo-500 transition-all"></div>
                            <svg class="absolute w-3 h-3 text-white opacity-0 peer-checked:opacity-100 pointer-events-none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        </div>
                        <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">{{ __('مجهولة / بدون لوحة') }}</span>
                    </label>
                </h2>

                <div class="relative" @click.away="vehicleDropdownOpen = false" x-show="!form.unknown_plate" x-transition>
                    <div class="relative">
                        <input type="text" x-model="vehicleSearch" @input.debounce.300ms="searchVehicles()" @focus="if(vehicleResults.length) vehicleDropdownOpen = true"
                            name="reported_vehicle_plate"
                            :placeholder="selectedVehicle ? '' : '{{ __('ابحث في مركباتك، أو اكتب رقم اللوحة مباشرة...') }}'"
                            class="w-full px-4 py-2.5 pr-10 border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-slate-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all"
                            :class="selectedVehicle ? 'hidden' : ''">
                        {{-- Selected Vehicle Display --}}
                        <div x-show="selectedVehicle" class="flex items-center gap-3 px-4 py-2.5 border border-indigo-200 dark:border-indigo-800 rounded-xl bg-indigo-50 dark:bg-indigo-900/20">
                            <div class="w-9 h-9 bg-indigo-600 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-900 dark:text-white" x-text="selectedVehicle?.plate_number"></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="selectedVehicle?.make + ' ' + selectedVehicle?.model_year"></p>
                            </div>
                            <button type="button" @click="clearVehicle()" class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors">
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div x-show="!selectedVehicle" class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                            </svg>
                        </div>
                    </div>
                    {{-- Dropdown Results --}}
                    <div x-show="vehicleDropdownOpen && vehicleResults.length > 0" x-transition
                        class="absolute z-50 w-full mt-2 bg-white dark:bg-slate-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl overflow-hidden">
                        <template x-for="v in vehicleResults" :key="v.id">
                            <button type="button" @click="selectVehicle(v)"
                                class="w-full flex items-center gap-3 px-4 py-3 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors text-start border-b border-gray-100 dark:border-gray-700 last:border-0">
                                <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white" x-text="v.plate_number"></p>
                                    <p class="text-xs text-gray-500" x-text="v.make + ' ' + v.model_year + (v.color ? ' • ' + v.color : '')"></p>
                                </div>
                            </button>
                        </template>
                    </div>
                    {{-- Loading --}}
                    <div x-show="vehicleSearching" class="absolute z-50 w-full mt-2 bg-white dark:bg-slate-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl p-4 text-center">
                        <svg class="animate-spin h-5 w-5 text-indigo-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
                <input type="hidden" name="vehicle_id" x-model="form.vehicle_id">
            </div>

            {{-- 3. Description --}}
            <div class="glass-card rounded-2xl p-5 mb-4 animate-fade-in-up stagger-3">
                <h2 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                    <span class="w-6 h-6 bg-indigo-600 text-white rounded-lg flex items-center justify-center text-xs font-bold">3</span>
                    {{ __('messages.description') }}
                </h2>
                <textarea name="description" x-model="form.description" rows="3"
                    class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-slate-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none resize-none transition-all"
                    placeholder="{{ __('messages.describe_incident') }}" required></textarea>
                <div class="flex items-center gap-2 mt-2">
                    <div class="flex-1 h-1 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-300"
                             :class="form.description.length >= 10 ? 'bg-emerald-500' : 'bg-rose-400'"
                             :style="'width: ' + Math.min(form.description.length / 10 * 100, 100) + '%'"></div>
                    </div>
                    <span class="text-xs font-medium" :class="form.description.length >= 10 ? 'text-emerald-600' : 'text-gray-400'">
                        <span x-text="form.description.length"></span>/10
                    </span>
                </div>
            </div>

            {{-- 4. Location --}}
            <div class="glass-card rounded-2xl p-5 mb-4 animate-fade-in-up stagger-4">
                <h2 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                    <span class="w-6 h-6 bg-indigo-600 text-white rounded-lg flex items-center justify-center text-xs font-bold">4</span>
                    {{ __('messages.location') }}
                </h2>
                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="button" @click="getLocation()" :disabled="gettingLocation"
                        class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 shrink-0"
                        :class="form.latitude ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-md shadow-indigo-500/20'">
                        <template x-if="gettingLocation">
                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                        <template x-if="!gettingLocation && !form.latitude">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                            </svg>
                        </template>
                        <template x-if="!gettingLocation && form.latitude">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                        </template>
                        <span x-text="form.latitude ? '{{ __('تم التحديد') }}' : (gettingLocation ? '{{ __('جاري التحديد...') }}' : '{{ __('messages.detect_location') }}')"></span>
                    </button>
                    <input type="text" name="location_text" x-model="form.location_text"
                        placeholder="{{ __('messages.location_description') }}"
                        class="flex-1 px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-slate-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all">
                </div>
                <div x-show="locationMessage" x-transition class="flex items-center gap-2 text-xs text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/20 px-3 py-2 rounded-lg mt-2">
                    <svg class="w-4 h-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                    </svg>
                    <span x-text="locationMessage"></span>
                </div>
                
                {{-- Show precise coordinates --}}
                <div x-show="form.latitude && form.longitude" x-transition class="mt-3 flex items-center gap-4 text-xs font-mono text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-slate-900/50 p-2.5 rounded-lg border border-gray-100 dark:border-gray-800">
                    <div class="flex items-center gap-1">
                        <span class="font-bold text-gray-400">X (الطول):</span>
                        <span x-text="parseFloat(form.longitude).toFixed(6)" class="text-indigo-600 dark:text-indigo-400"></span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="font-bold text-gray-400">Y (العرض):</span>
                        <span x-text="parseFloat(form.latitude).toFixed(6)" class="text-rose-600 dark:text-rose-400"></span>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="button" @click="toggleMap()" class="text-sm text-indigo-600 dark:text-indigo-400 font-medium hover:underline flex items-center gap-1">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z" /></svg>
                        {{ __('تحديد الموقع على الخريطة يدوياً') }}
                    </button>
                    <div x-show="showMap" class="mt-2 h-64 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-inner" id="locationMap" style="z-index: 10;"></div>
                </div>
                <input type="hidden" name="latitude" x-model="form.latitude">
                <input type="hidden" name="longitude" x-model="form.longitude">
            </div>

            {{-- 5. Media Upload (Image + Video) --}}
            <div class="glass-card rounded-2xl p-5 mb-4 animate-fade-in-up stagger-5">
                <h2 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                    <span class="w-6 h-6 bg-indigo-600 text-white rounded-lg flex items-center justify-center text-xs font-bold">5</span>
                    {{ __('الوسائط') }}
                    <span class="text-xs font-normal text-gray-400">({{ __('اختياري') }})</span>
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    {{-- Image Upload --}}
                    <div class="border-2 border-dashed rounded-xl p-4 text-center transition-all duration-200 cursor-pointer group"
                         :class="imagePreview ? 'border-emerald-300 dark:border-emerald-700 bg-emerald-50/50 dark:bg-emerald-900/10' : 'border-gray-200 dark:border-gray-700 hover:border-indigo-400 dark:hover:border-indigo-600'"
                         @click="$refs.imageInput.click()">
                        <input type="file" name="image" accept="image/*" x-ref="imageInput" class="hidden" @change="previewImage($event)">
                        <template x-if="!imagePreview">
                            <div class="py-2">
                                <div class="w-10 h-10 mx-auto rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                                    <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                                    </svg>
                                </div>
                                <p class="text-xs text-gray-500 font-medium">{{ __('رفع صورة') }}</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">{{ __('حد أقصى 5 ميجابايت') }}</p>
                            </div>
                        </template>
                        <template x-if="imagePreview">
                            <div class="relative inline-block">
                                <img :src="imagePreview" class="max-h-28 rounded-lg shadow" alt="Preview">
                                <button type="button" @click.stop="removeImage()" class="absolute -top-1.5 -end-1.5 w-6 h-6 bg-rose-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-rose-600 shadow transition">
                                    <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>

                    {{-- Video Upload --}}
                    <div class="border-2 border-dashed rounded-xl p-4 text-center transition-all duration-200 cursor-pointer group"
                         :class="videoName ? 'border-emerald-300 dark:border-emerald-700 bg-emerald-50/50 dark:bg-emerald-900/10' : 'border-gray-200 dark:border-gray-700 hover:border-purple-400 dark:hover:border-purple-600'"
                         @click="$refs.videoInput.click()">
                        <input type="file" name="video" accept="video/mp4,video/quicktime,video/webm,video/x-msvideo" x-ref="videoInput" class="hidden" @change="handleVideo($event)">
                        <template x-if="!videoName">
                            <div class="py-2">
                                <div class="w-10 h-10 mx-auto rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                                    <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                                    </svg>
                                </div>
                                <p class="text-xs text-gray-500 font-medium">{{ __('رفع فيديو') }}</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">{{ __('حد أقصى 50 ميجابايت') }}</p>
                            </div>
                        </template>
                        <template x-if="videoName">
                            <div class="relative py-2">
                                <div class="w-10 h-10 mx-auto rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center mb-2">
                                    <svg class="w-5 h-5 text-purple-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                                    </svg>
                                </div>
                                <p class="text-xs text-gray-700 dark:text-gray-300 font-semibold truncate px-2" x-text="videoName"></p>
                                <button type="button" @click.stop="removeVideo()" class="absolute -top-1 -end-1 w-6 h-6 bg-rose-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-rose-600 shadow transition">
                                    <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="animate-fade-in-up stagger-6">
                <button type="submit" :disabled="!canSubmit()" @click="submitting = true"
                    :class="!canSubmit() ? 'opacity-40 cursor-not-allowed' : 'hover:bg-emerald-700 hover:-translate-y-0.5 shadow-lg shadow-emerald-500/25'"
                    class="w-full flex items-center justify-center gap-3 px-8 py-3.5 rounded-xl text-base font-bold text-white bg-emerald-600 transition-all duration-300">
                    <template x-if="!submitting">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                        </svg>
                    </template>
                    <template x-if="submitting">
                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </template>
                    <span x-text="submitting ? '{{ __('جاري الإرسال...') }}' : '{{ __('messages.submit_report') }}'"></span>
                </button>
            </div>
        </form>
    </div>

    {{-- Success Screen --}}
    <div x-show="submitted" x-transition.duration.500ms class="text-center py-20 animate-fade-in-up">
        <div class="mb-6">
            <div class="w-24 h-24 mx-auto bg-emerald-600 rounded-full flex items-center justify-center shadow-xl shadow-emerald-500/30 animate-bounce-gentle">
                <svg class="w-12 h-12 text-white check-animate" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
            </div>
        </div>
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">{{ __('messages.report_success_title') }}</h2>
        <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-md mx-auto">{{ __('messages.report_success_message') }}</p>

        <div class="glass-card rounded-2xl p-6 inline-block">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">{{ __('messages.tracking_number') }}</p>
            <p class="text-3xl font-mono font-extrabold gradient-text">{{ session('tracking_number', 'RPT-000001') }}</p>
        </div>

        <div class="mt-8">
            <a href="{{ route('citizen.dashboard') }}" class="inline-flex items-center gap-2 px-8 py-3 rounded-xl font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-500/25 transition-all duration-300 hover:-translate-y-0.5">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                </svg>
                {{ __('messages.back_to_dashboard') }}
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
function quickReport() {
    return {
        submitted: {{ session('success') ? 'true' : 'false' }},
        submitting: false,
        gettingLocation: false,
        locationMessage: null,
        imagePreview: null,
        videoName: null,
        vehicleSearch: '',
        vehicleResults: [],
        vehicleDropdownOpen: false,
        vehicleSearching: false,
        selectedVehicle: null,
        showMap: false,
        map: null,
        marker: null,
        form: {
            report_type: '',
            latitude: null,
            longitude: null,
            location_text: '',
            vehicle_id: '',
            unknown_plate: false,
            description: '',
        },
        reportTypes: [
            { value: 'accident', label: '{{ __("messages.accident") }}', icon: '🚗' },
            { value: 'hazard', label: '{{ __("messages.hazard") }}', icon: '⚠️' },
            { value: 'traffic_jam', label: '{{ __("messages.traffic_jam") }}', icon: '🚦' },
            { value: 'security_threat', label: '{{ __("messages.security_threat") }}', icon: '🛡️' },
        ],

        init() {
            this.getLocation();
        },

        canSubmit() {
            return this.form.report_type && this.form.description.length >= 10;
        },

        getLocation() {
            this.gettingLocation = true;
            this.locationMessage = null;
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    this.form.latitude = pos.coords.latitude;
                    this.form.longitude = pos.coords.longitude;
                    this.gettingLocation = false;
                    this.locationMessage = '{{ __("تم تحديد موقعك بدقة عبر الـ GPS.") }}';
                    if (this.map) {
                        const latlng = [pos.coords.latitude, pos.coords.longitude];
                        this.map.setView(latlng, 15);
                        if (this.marker) {
                            this.marker.setLatLng(latlng);
                        } else {
                            this.marker = L.marker(latlng).addTo(this.map);
                        }
                    }
                },
                (err) => {
                    this.gettingLocation = false;
                    let msg = '{{ __("يرجى إعطاء صلاحية الموقع للمتصفح، أو حدد موقعك على الخريطة.") }}';
                    if (err.code === 1) {
                        msg = '{{ __("لقد قمت برفض صلاحية تحديد الموقع. يرجى تفعيلها من إعدادات المتصفح أو حدد يدوياً.") }}';
                    } else if (err.code === 2) {
                        msg = '{{ __("تعذر تحديد الموقع (GPS غير متاح). يرجى التحديد على الخريطة.") }}';
                    } else if (err.code === 3) {
                        msg = '{{ __("انتهى وقت محاولة تحديد الموقع. يرجى المحاولة مرة أخرى أو التحديد على الخريطة.") }}';
                    }
                    this.locationMessage = msg;
                    
                    if (!this.showMap) {
                        this.toggleMap(); // Auto open map on error
                    }
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        },

        toggleMap() {
            this.showMap = !this.showMap;
            if (this.showMap) {
                this.$nextTick(() => {
                    this.initMap();
                    // Fix leaflet rendering bug inside Alpine x-show
                    setTimeout(() => {
                        if (this.map) {
                            this.map.invalidateSize();
                        }
                    }, 300);
                });
            }
        },

        initMap() {
            if (this.map) {
                this.map.invalidateSize();
                return;
            }
            const defaultLat = this.form.latitude || 33.5138;
            const defaultLng = this.form.longitude || 36.2765;
            
            this.map = L.map('locationMap').setView([defaultLat, defaultLng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(this.map);

            if (this.form.latitude && this.form.longitude) {
                this.marker = L.marker([this.form.latitude, this.form.longitude]).addTo(this.map);
            }

            this.map.on('click', (e) => {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;
                this.form.latitude = lat;
                this.form.longitude = lng;
                
                if (this.marker) {
                    this.marker.setLatLng(e.latlng);
                } else {
                    this.marker = L.marker(e.latlng).addTo(this.map);
                }
                this.locationError = null;
            });
        },

        async searchVehicles() {
            if (this.vehicleSearch.length < 1) {
                this.vehicleResults = [];
                this.vehicleDropdownOpen = false;
                return;
            }
            this.vehicleSearching = true;
            try {
                const locale = '{{ app()->getLocale() === "en" ? "" : app()->getLocale() }}';
                const prefix = locale ? '/' + locale : '';
                const response = await fetch(prefix + '/citizen/reports/search-vehicles?q=' + encodeURIComponent(this.vehicleSearch), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                this.vehicleResults = await response.json();
                this.vehicleDropdownOpen = this.vehicleResults.length > 0;
            } catch (e) {
                console.error(e);
            } finally {
                this.vehicleSearching = false;
            }
        },

        selectVehicle(vehicle) {
            this.selectedVehicle = vehicle;
            this.form.vehicle_id = vehicle.id;
            this.vehicleSearch = '';
            this.vehicleDropdownOpen = false;
            this.vehicleResults = [];
        },

        clearVehicle() {
            this.selectedVehicle = null;
            this.form.vehicle_id = '';
            this.vehicleSearch = '';
            // Allow manual input again
            setTimeout(() => {
                const input = document.querySelector('input[name="reported_vehicle_plate"]');
                if (input) input.focus();
            }, 50);
        },

        previewImage(event) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (e) => { this.imagePreview = e.target.result; };
            reader.readAsDataURL(file);
        },

        removeImage() {
            this.imagePreview = null;
            this.$refs.imageInput.value = '';
        },

        handleVideo(event) {
            const file = event.target.files[0];
            if (!file) return;
            if (file.size > 50 * 1024 * 1024) {
                alert('{{ __("حجم الفيديو يتجاوز 50 ميجابايت") }}');
                this.$refs.videoInput.value = '';
                return;
            }
            this.videoName = file.name;
        },

        removeVideo() {
            this.videoName = null;
            this.$refs.videoInput.value = '';
        }
    }
}
</script>
@endpush
@endsection
