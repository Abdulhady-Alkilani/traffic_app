<footer class="relative mt-auto">
    {{-- Gradient top border --}}
    <div class="h-px bg-gradient-to-r from-transparent via-indigo-500/30 to-transparent"></div>

    <div class="bg-white/50 dark:bg-slate-900/50 backdrop-blur-sm">
        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                {{-- Logo & Copyright --}}
                <div class="flex items-center gap-3">
                    <img src="{{ asset('logo.png') }}" alt="{{ config('app.name') }}" class="w-8 h-8 rounded-lg object-contain">
                    <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ config('app.name') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">&copy; {{ date('Y') }} {{ __('messages.all_rights_reserved') }}</p>
                    </div>
                </div>

                {{-- Quick Links --}}
                <div class="flex items-center gap-6 text-sm">
                    <a href="{{ route('home') }}" class="text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-200">
                        {{ __('messages.home') }}
                    </a>
                    @auth
                        @if(auth()->user()->isCitizen())
                            <a href="{{ route('citizen.dashboard') }}" class="text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-200">
                                {{ __('messages.dashboard') }}
                            </a>
                        @endif
                    @endauth
                    <a href="{{ route('login') }}" class="text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-200">
                        {{ __('messages.login') }}
                    </a>
                </div>

                {{-- Tech badge --}}
                <div class="flex items-center gap-1.5 text-xs text-gray-400 dark:text-gray-500">
                    <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5" />
                    </svg>
                    <span>Laravel + FilamentPHP</span>
                </div>
            </div>
        </div>
    </div>
</footer>
