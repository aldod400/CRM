<x-filament-widgets::widget>
    <x-filament::card>
        <h2 class="text-lg font-bold mb-4">{{ __('Reminder Notifications') }}</h2>

        @forelse ($notifications as $notification)
            <div class="mb-3">
                <a href="{{ $notification->data['url'] ?? '#' }}" class="text-primary-600 hover:underline">
                    {{ $notification->data['title'] }}
                </a>
                <div class="text-sm text-gray-500">{{ $notification->data['remind_at'] }}</div>
            </div>
        @empty
            <div class="text-gray-500">{{ __('No notifications.') }}</div>
        @endforelse
    </x-filament::card>
</x-filament-widgets::widget>
