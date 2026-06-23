<div>
    <x-filament::section>
        <x-slot name="heading">{{ __('analytics.sections.custom') }}</x-slot>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('analytics.custom.type') }}</label>
                <select wire:model.live="reportType" class="block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm text-sm">
                    <option value="reports">{{ __('analytics.custom.type_reports') }}</option>
                    <option value="violations">{{ __('analytics.custom.type_violations') }}</option>
                    <option value="incidents">{{ __('analytics.custom.type_incidents') }}</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('analytics.custom.status') }}</label>
                <select wire:model="statusFilter" class="block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm text-sm">
                    @foreach ($this->statusOptions() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('analytics.custom.from') }}</label>
                <input type="date" wire:model="from" class="block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm text-sm">
                @error('from')
                    <span class="mt-1 block text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('analytics.custom.to') }}</label>
                <input type="date" wire:model="to" class="block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm text-sm">
                @error('to')
                    <span class="mt-1 block text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mt-4 flex items-center gap-3">
            <x-filament::button wire:click="build" wire:loading.attr="disabled" color="primary">
                <span wire:loading.remove.delay wire:target="build">{{ __('analytics.custom.build') }}</span>
                <span wire:loading wire:target="build" class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    {{ __('analytics.custom.build') }}
                </span>
            </x-filament::button>

            <x-filament::button wire:click="resetFilters" color="gray" variant="outlined">
                {{ __('analytics.custom.reset') }}
            </x-filament::button>
        </div>
    </x-filament::section>

    @if ($built)
        <x-filament::section>
            <x-slot name="heading">{{ __('analytics.custom.results') }}</x-slot>
            <x-slot name="description">{{ __('analytics.custom.row_count', ['count' => count($results)]) }}</x-slot>

            @if (empty($results))
                <div class="py-8 text-center text-gray-400">{{ __('analytics.custom.no_data') }}</div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-start font-semibold text-gray-600 dark:text-gray-300">{{ __('analytics.export.id') }}</th>
                                <th class="px-4 py-3 text-start font-semibold text-gray-600 dark:text-gray-300">{{ __('analytics.export.citizen') }}</th>
                                <th class="px-4 py-3 text-start font-semibold text-gray-600 dark:text-gray-300">{{ __('analytics.export.subtype') }}</th>
                                <th class="px-4 py-3 text-start font-semibold text-gray-600 dark:text-gray-300">{{ __('analytics.export.fine') }}</th>
                                <th class="px-4 py-3 text-start font-semibold text-gray-600 dark:text-gray-300">{{ __('analytics.export.status') }}</th>
                                <th class="px-4 py-3 text-start font-semibold text-gray-600 dark:text-gray-300">{{ __('analytics.export.location') }}</th>
                                <th class="px-4 py-3 text-start font-semibold text-gray-600 dark:text-gray-300">{{ __('analytics.export.date') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($results as $row)
                                <tr wire:key="report-row-{{ $row['type'] ?? 'item' }}-{{ $row['id'] ?? $loop->index }}">
                                    <td class="px-4 py-2 text-gray-500 dark:text-gray-400">{{ $row['id'] ?? '' }}</td>
                                    <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $row['citizen'] ?? '—' }}</td>
                                    <td class="px-4 py-2 text-gray-700 dark:text-gray-200">{{ __('messages.' . ($row['subtype'] ?? ''), ['' => $row['subtype'] ?? '']) }}</td>
                                    <td class="px-4 py-2 text-gray-700 dark:text-gray-200">{{ isset($row['fine']) ? number_format((float) $row['fine'], 0) : '—' }}</td>
                                    <td class="px-4 py-2">{{ $row['status'] ?? '' }}</td>
                                    <td class="px-4 py-2 text-gray-700 dark:text-gray-200">{{ $row['location'] ?? '—' }}</td>
                                    <td class="px-4 py-2 text-gray-500 dark:text-gray-400">{{ $row['date'] ?? '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-filament::section>
    @endif
</div>
