@extends('layouts.app')

@section('title', __('messages.my_violations'))

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    {{-- Header --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-fade-in-up">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                <svg class="w-8 h-8 text-rose-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
                {{ __('messages.my_violations') }}
            </h1>
        </div>
    </div>

    {{-- Search and Filter --}}
    <div class="glass-card rounded-2xl p-4 mb-6 animate-fade-in-up stagger-1">
        <form method="GET" action="{{ route('citizen.violations.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" class="bg-gray-50 dark:bg-slate-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-xl focus:ring-rose-500 focus:border-rose-500 block w-full pr-10 pl-4 py-2.5" placeholder="{{ __('بحث برقم اللوحة') }}">
                </div>
            </div>
            <div class="md:w-40">
                <select name="type" class="bg-gray-50 dark:bg-slate-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-xl focus:ring-rose-500 focus:border-rose-500 block w-full p-2.5">
                    <option value="">{{ __('الكل') }}</option>
                    <option value="speeding" {{ request('type') == 'speeding' ? 'selected' : '' }}>{{ __('messages.speeding') }}</option>
                    <option value="reckless_driving" {{ request('type') == 'reckless_driving' ? 'selected' : '' }}>{{ __('messages.reckless_driving') }}</option>
                    <option value="red_light" {{ request('type') == 'red_light' ? 'selected' : '' }}>{{ __('messages.red_light') }}</option>
                    <option value="illegal_parking" {{ request('type') == 'illegal_parking' ? 'selected' : '' }}>{{ __('messages.illegal_parking') }}</option>
                    <option value="no_seatbelt" {{ request('type') == 'no_seatbelt' ? 'selected' : '' }}>{{ __('messages.no_seatbelt') }}</option>
                    <option value="using_phone" {{ request('type') == 'using_phone' ? 'selected' : '' }}>{{ __('messages.using_phone') }}</option>
                </select>
            </div>
            <div class="md:w-40">
                <select name="status" class="bg-gray-50 dark:bg-slate-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-xl focus:ring-rose-500 focus:border-rose-500 block w-full p-2.5">
                    <option value="">{{ __('جميع الحالات') }}</option>
                    <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>{{ __('messages.unpaid') }}</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>{{ __('messages.paid') }}</option>
                    <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>{{ __('messages.canceled') }}</option>
                </select>
            </div>
            <div class="md:w-40">
                <select name="sort" class="bg-gray-50 dark:bg-slate-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-xl focus:ring-rose-500 focus:border-rose-500 block w-full p-2.5">
                    <option value="issued_at" {{ request('sort') == 'issued_at' ? 'selected' : '' }}>{{ __('الأحدث') }}</option>
                    <option value="fine_amount" {{ request('sort') == 'fine_amount' ? 'selected' : '' }}>{{ __('قيمة المخالفة') }}</option>
                    <option value="status" {{ request('sort') == 'status' ? 'selected' : '' }}>{{ __('الحالة') }}</option>
                </select>
            </div>
            <div>
                <button type="submit" class="w-full md:w-auto px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-semibold rounded-xl text-sm transition-all shadow-md shadow-rose-500/20">
                    {{ __('بحث وفلترة') }}
                </button>
            </div>
        </form>
    </div>

    {{-- Violations List --}}
    <div class="glass-card rounded-2xl overflow-hidden animate-fade-in-up stagger-2">
        <div class="overflow-x-auto">
            <table class="w-full text-start">
                <thead>
                    <tr class="border-b border-gray-200/50 dark:border-gray-700/50 bg-gray-50/50 dark:bg-slate-800/50">
                        <th class="px-5 py-4 text-start text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">#</th>
                        <th class="px-5 py-4 text-start text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.issued_at') }}</th>
                        <th class="px-5 py-4 text-start text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.violation_type') }}</th>
                        <th class="px-5 py-4 text-start text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.fine_amount') }}</th>
                        <th class="px-5 py-4 text-start text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.plate_number') }}</th>
                        <th class="px-5 py-4 text-start text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.status') }}</th>
                        <th class="px-5 py-4 text-start text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody x-data class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($violations as $violation)
                    <tr @click="window.location.href = '{{ route('citizen.violations.show', $violation) }}'" class="hover:bg-rose-50/30 dark:hover:bg-rose-900/10 transition-colors duration-150 cursor-pointer">
                        <td class="px-5 py-4 font-semibold text-gray-900 dark:text-white">{{ $violation->id }}</td>
                        <td class="px-5 py-4 text-gray-600 dark:text-gray-300">{{ $violation->issued_at->format('M d, Y') }}</td>
                        <td class="px-5 py-4 text-gray-600 dark:text-gray-300 capitalize">{{ __('messages.' . $violation->violation_type) }}</td>
                        <td class="px-5 py-4 font-semibold text-gray-900 dark:text-white">{{ number_format($violation->fine_amount, 0) }} ل.س</td>
                        <td class="px-5 py-4 text-gray-600 dark:text-gray-300">{{ $violation->vehicle?->plate_number ?? '-' }}</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full
                                {{ $violation->status->value === 'unpaid' ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/50 dark:text-rose-300' : '' }}
                                {{ $violation->status->value === 'paid' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300' : '' }}
                                {{ $violation->status->value === 'canceled' ? 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' : '' }}
                            ">
                                {{ __('messages.' . $violation->status->value) }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <a href="{{ route('citizen.violations.show', $violation) }}" @click.stop class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold transition-all {{ $violation->status->value === 'unpaid' ? 'bg-rose-600 hover:bg-rose-700 text-white shadow-md shadow-rose-500/20' : 'bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-gray-300' }}">
                                @if($violation->status->value === 'unpaid')
                                    <svg class="w-4 h-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                    </svg>
                                    {{ __('تسديد الفاتورة') }}
                                @else
                                    {{ __('التفاصيل') }}
                                    <svg class="w-4 h-4 rtl:-scale-x-100" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                    </svg>
                                @endif
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-3">
                                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 font-medium">{{ __('messages.no_violations') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200/50 dark:border-gray-700/50">
            {{ $violations->links() }}
        </div>
    </div>
</div>

<script>
function payViolation(violationId) {
    if (!confirm('{{ __("messages.pay_now") }}?')) return;

    const baseUrl = '{{ url("/") }}';
    const locale = '{{ app()->getLocale() === "en" ? "" : app()->getLocale() }}';
    const prefix = locale ? '/' + locale : '';

    fetch(prefix + '/citizen/violations/' + violationId + '/pay', {
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
