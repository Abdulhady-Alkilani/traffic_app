<div class="flex items-center gap-1">
    @php
        $currentLocale = app()->getLocale();
        $locales = ['en' => 'EN', 'ar' => 'AR'];
    @endphp

    @foreach($locales as $locale => $label)
        @if($locale !== $currentLocale)
            <a href="{{ LaravelLocalization::getLocalizedURL($locale) }}"
               class="px-2 py-1 text-xs rounded border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                {{ $label }}
            </a>
        @else
            <span class="px-2 py-1 text-xs rounded bg-blue-600 text-white font-bold">
                {{ $label }}
            </span>
        @endif
    @endforeach
</div>
