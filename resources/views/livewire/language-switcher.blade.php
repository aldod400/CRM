<div>
    @foreach(config('app.available_locales') as $locale => $name)
        <button
            wire:click="switchLocale('{{ $locale }}')"
            @class([
                'w-full px-4 py-2 text-start text-sm hover:bg-gray-100 dark:hover:bg-gray-700',
                'bg-primary-500/10 dark:bg-primary-400/10' => $currentLocale === $locale,
            ])
        >
            {{ $name }}
        </button>
    @endforeach
</div>
