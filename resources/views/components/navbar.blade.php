<nav class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center gap-2">
                <span class="text-2xl">🚦</span>
                <a href="{{ route('home') ?? '/' }}" class="text-xl font-bold text-gray-900 dark:text-white">
                    {{ config('app.name') }}
                </a>
            </div>

            <div class="flex items-center gap-4">
                @include('components.language-switcher')
                @include('components.dark-mode-toggle')

                @auth
                    <span class="text-sm text-gray-600 dark:text-gray-300">
                        {{ auth()->user()->username }}
                    </span>

                    @if(auth()->user()->isAdmin())
                        <a href="/admin" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                            {{ __('messages.admin_panel') }}
                        </a>
                    @endif

                    @if(auth()->user()->isPolice())
                        <a href="/police" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                            {{ __('messages.police_panel') }}
                        </a>
                    @endif

                    @if(auth()->user()->isCitizen())
                        <a href="{{ route('citizen.dashboard') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                            {{ __('messages.dashboard') }}
                        </a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:underline">
                            {{ __('messages.logout') }}
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                        {{ __('messages.login') }}
                    </a>
                    <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                        {{ __('messages.register') }}
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
