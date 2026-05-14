@extends('layouts.app')

@section('title', __('messages.home'))

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-16 px-4">
    <div class="text-center max-w-2xl">
        <div class="text-6xl mb-6">🚦</div>
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">
            {{ config('app.name') }}
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 mb-8">
            {{ __('messages.dashboard_subtitle') }}
        </p>

        @auth
            @if(auth()->user()->isCitizen())
                <a href="{{ route('citizen.dashboard') }}" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition font-medium text-lg me-4">
                    {{ __('messages.dashboard') }}
                </a>
                <a href="{{ route('citizen.reports.create') }}" class="bg-red-600 text-white px-8 py-3 rounded-lg hover:bg-red-700 transition font-medium text-lg">
                    {{ __('messages.new_report') }}
                </a>
            @endif

            @if(auth()->user()->isAdmin())
                <a href="/admin" class="bg-amber-600 text-white px-8 py-3 rounded-lg hover:bg-amber-700 transition font-medium text-lg">
                    {{ __('messages.admin_panel') }}
                </a>
            @endif

            @if(auth()->user()->isPolice())
                <a href="/police" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition font-medium text-lg">
                    {{ __('messages.police_panel') }}
                </a>
            @endif
        @else
            <a href="{{ route('login') }}" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition font-medium text-lg me-4">
                {{ __('messages.login') }}
            </a>
            <a href="{{ route('register') }}" class="bg-gray-800 text-white px-8 py-3 rounded-lg hover:bg-gray-900 transition font-medium text-lg">
                {{ __('messages.register') }}
            </a>
        @endauth
    </div>
</div>
@endsection
