@extends('layouts.app')

@section('title', __('messages.my_vehicles'))

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8" x-data="vehicleManager()">
    {{-- Header --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-fade-in-up">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                <svg class="w-8 h-8 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                </svg>
                {{ __('messages.my_vehicles') }}
            </h1>
        </div>
        <button @click="openAddModal()"
            class="flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md shadow-indigo-500/20 transition-all duration-200">
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            {{ __('messages.add_vehicle') }}
        </button>
    </div>

    {{-- Search and Filter --}}
    <div class="glass-card rounded-2xl p-4 mb-6 animate-fade-in-up stagger-1">
        <form method="GET" action="{{ route('citizen.vehicles.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" class="bg-gray-50 dark:bg-slate-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block w-full pr-10 pl-4 py-2.5" placeholder="{{ __('بحث برقم اللوحة أو الشركة') }}">
                </div>
            </div>
            <div class="md:w-48">
                <select name="type" class="bg-gray-50 dark:bg-slate-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
                    <option value="">{{ __('الكل') }}</option>
                    <option value="sedan" {{ request('type') == 'sedan' ? 'selected' : '' }}>Sedan</option>
                    <option value="suv" {{ request('type') == 'suv' ? 'selected' : '' }}>SUV</option>
                    <option value="truck" {{ request('type') == 'truck' ? 'selected' : '' }}>Truck</option>
                    <option value="motorcycle" {{ request('type') == 'motorcycle' ? 'selected' : '' }}>Motorcycle</option>
                    <option value="van" {{ request('type') == 'van' ? 'selected' : '' }}>Van</option>
                </select>
            </div>
            <div class="md:w-48">
                <select name="sort" class="bg-gray-50 dark:bg-slate-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
                    <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>{{ __('الأحدث') }}</option>
                    <option value="model_year" {{ request('sort') == 'model_year' ? 'selected' : '' }}>{{ __('سنة الصنع') }}</option>
                    <option value="make" {{ request('sort') == 'make' ? 'selected' : '' }}>{{ __('الشركة المصنعة') }}</option>
                </select>
            </div>
            <div>
                <button type="submit" class="w-full md:w-auto px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-sm transition-all shadow-md shadow-indigo-500/20">
                    {{ __('بحث وفلترة') }}
                </button>
            </div>
        </form>
    </div>

    {{-- Vehicles List --}}
    <div class="glass-card rounded-2xl overflow-hidden animate-fade-in-up stagger-2">
        <div class="overflow-x-auto">
            <table class="w-full text-start">
                <thead>
                    <tr class="border-b border-gray-200/50 dark:border-gray-700/50 bg-gray-50/50 dark:bg-slate-800/50">
                        <th class="px-5 py-4 text-start text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.plate_number') }}</th>
                        <th class="px-5 py-4 text-start text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.type') }}</th>
                        <th class="px-5 py-4 text-start text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.make') }}</th>
                        <th class="px-5 py-4 text-start text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.year') }}</th>
                        <th class="px-5 py-4 text-start text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.color') }}</th>
                        <th class="px-5 py-4 text-start text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($vehicles as $vehicle)
                    <tr @click="window.location.href = '{{ route('citizen.vehicles.show', $vehicle) }}'" class="hover:bg-indigo-50/30 dark:hover:bg-indigo-900/10 transition-colors duration-150 cursor-pointer">
                        <td class="px-5 py-4 font-semibold text-gray-900 dark:text-white">{{ $vehicle->plate_number }}</td>
                        <td class="px-5 py-4 text-gray-600 dark:text-gray-300 capitalize">{{ $vehicle->vehicle_type }}</td>
                        <td class="px-5 py-4 text-gray-600 dark:text-gray-300">{{ $vehicle->make }}</td>
                        <td class="px-5 py-4 text-gray-600 dark:text-gray-300">{{ $vehicle->model_year }}</td>
                        <td class="px-5 py-4 text-gray-600 dark:text-gray-300">
                            <span class="inline-flex items-center gap-1.5" dir="ltr">
                                <span class="w-4 h-4 rounded-full shadow-sm border border-gray-200 dark:border-gray-700" style="background-color: {{ $vehicle->color }};"></span>
                                <span class="text-xs font-mono uppercase">{{ $vehicle->color }}</span>
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('citizen.vehicles.show', $vehicle) }}" @click.stop class="text-emerald-500 hover:text-emerald-700 transition-colors tooltip" data-tip="{{ __('عرض التفاصيل') }}">
                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </a>
                                <button @click.stop="openEditModal({{ $vehicle->toJson() }})" class="text-indigo-500 hover:text-indigo-700 transition-colors tooltip" data-tip="{{ __('تعديل') }}">
                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </button>
                                <button @click.stop="openDeleteModal({{ $vehicle->id }})" class="text-rose-500 hover:text-rose-700 transition-colors tooltip" data-tip="{{ __('messages.delete') }}">
                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-3">
                                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                                    </svg>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 font-medium">{{ __('messages.no_vehicles') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200/50 dark:border-gray-700/50">
            {{ $vehicles->links() }}
        </div>
    </div>

    {{-- Add/Edit Modal --}}
    <div x-show="isModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4">
        <div x-show="isModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeModal()"></div>
        
        <div x-show="isModalOpen" 
            x-transition:enter="transition ease-[cubic-bezier(0.2,0.8,0.2,1)] duration-500"
            x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full max-w-lg bg-white dark:bg-slate-900 rounded-3xl shadow-2xl overflow-hidden border border-white/20 dark:border-white/10 m-auto">
            
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white" x-text="isEditing ? '{{ __('تعديل المركبة') }}' : '{{ __('messages.add_vehicle') }}'"></h3>
                <button @click="closeModal()" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form :action="formAction" method="POST" class="p-6">
                @csrf
                <template x-if="isEditing">
                    @method('PUT')
                </template>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.plate_number') }}</label>
                        <input type="text" name="plate_number" x-model="formData.plate_number" required
                            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 dark:text-white">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.type') }}</label>
                            <select name="vehicle_type" x-model="formData.vehicle_type" required
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 dark:text-white">
                                <option value="sedan">Sedan</option>
                                <option value="suv">SUV</option>
                                <option value="truck">Truck</option>
                                <option value="motorcycle">Motorcycle</option>
                                <option value="van">Van</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.make') }}</label>
                            <input type="text" name="make" x-model="formData.make" required
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 dark:text-white">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.model_year') }}</label>
                            <input type="number" name="model_year" x-model="formData.model_year" required min="1900" max="{{ date('Y') + 1 }}"
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.color') }}</label>
                            <div class="flex items-center gap-3">
                                <input type="color" name="color" x-model="formData.color" required
                                    class="h-11 w-14 rounded-xl cursor-pointer bg-gray-50 dark:bg-slate-800 border border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 p-1">
                                <span class="text-sm font-mono text-gray-500 uppercase" x-text="formData.color"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex gap-3">
                    <button type="button" @click="closeModal()" class="flex-1 px-5 py-2.5 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors font-medium">
                        {{ __('إلغاء') }}
                    </button>
                    <button type="submit" class="flex-1 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-md shadow-indigo-500/20 transition-all font-medium">
                        {{ __('messages.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div x-show="isDeleteModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4">
        <div x-show="isDeleteModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="isDeleteModalOpen = false"></div>
        
        <div x-show="isDeleteModalOpen" 
            x-transition:enter="transition ease-[cubic-bezier(0.2,0.8,0.2,1)] duration-500"
            x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full max-w-sm bg-white dark:bg-slate-900 rounded-3xl shadow-2xl p-6 text-center border border-white/20 dark:border-white/10 m-auto">
            
            <div class="w-16 h-16 rounded-full bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-rose-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ __('تأكيد الحذف') }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">{{ __('هل أنت متأكد من أنك تريد حذف هذه المركبة؟ لا يمكن التراجع عن هذا الإجراء.') }}</p>
            
            <div class="flex gap-3">
                <button @click="isDeleteModalOpen = false" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors font-medium">
                    {{ __('إلغاء') }}
                </button>
                <form :action="deleteAction" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2 bg-rose-500 hover:bg-rose-600 text-white rounded-xl shadow-md shadow-rose-500/20 transition-all font-medium">
                        {{ __('messages.delete') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('vehicleManager', () => ({
            isModalOpen: false,
            isDeleteModalOpen: false,
            isEditing: false,
            formAction: '{{ route("citizen.vehicles.store") }}',
            deleteAction: '',
            formData: {
                plate_number: '',
                vehicle_type: 'sedan',
                make: '',
                model_year: '',
                color: '#ffffff'
            },
            
            openAddModal() {
                this.isEditing = false;
                this.formAction = '{{ route("citizen.vehicles.store") }}';
                this.formData = {
                    plate_number: '',
                    vehicle_type: 'sedan',
                    make: '',
                    model_year: '',
                    color: '#ffffff'
                };
                this.isModalOpen = true;
            },
            
            openEditModal(vehicle) {
                this.isEditing = true;
                this.formAction = '{{ url("/citizen/vehicles") }}/' + vehicle.id;
                this.formData = {
                    plate_number: vehicle.plate_number,
                    vehicle_type: vehicle.vehicle_type,
                    make: vehicle.make,
                    model_year: vehicle.model_year,
                    color: vehicle.color
                };
                this.isModalOpen = true;
            },
            
            closeModal() {
                this.isModalOpen = false;
            },
            
            openDeleteModal(id) {
                this.deleteAction = '{{ url("/citizen/vehicles") }}/' + id;
                this.isDeleteModalOpen = true;
            }
        }));
    });
</script>
@endsection
