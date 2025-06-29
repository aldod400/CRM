<div class="flex items-center space-x-4 rtl:space-x-reverse">
    @foreach($availableLocales as $locale => $name)
        <button
            wire:click="switchLocale('{{ $locale }}')"
            @class([
                'px-3 py-2 text-sm font-medium rounded-md',
                'bg-primary-500 text-white' => $currentLocale === $locale,
                'text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-gray-100' => $currentLocale !== $locale,
            ])
        >
            {{ $name }}
        </button>
    @endforeach
</div>
