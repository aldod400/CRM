<style>
    [x-cloak] {
        display: none !important;
    }
</style>

<!-- Toast Styling -->
<style>
    .custom-toast {
        position: fixed;
        top: 24px;
        right: 24px;
        min-width: 320px;
        max-width: 400px;
        background: #fff;
        color: #1f2937;
        border-radius: 0.75rem;
        box-shadow: 0 10px 15px -3px rgba(31,41,55,0.1), 0 4px 6px -4px rgba(31,41,55,0.1);
        border: 1px solid #e5e7eb;
        padding: 1rem 1.25rem;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
        z-index: 9999;
        font-size: 15px;
        animation: filament-toast-in 0.25s cubic-bezier(.4,0,.2,1);
    }

    @keyframes filament-toast-in {
        from { opacity: 0; transform: translateY(-16px);}
        to { opacity: 1; transform: translateY(0);}
    }

    .custom-toast-title {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .custom-toast-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        width: 100%;
    }

    .custom-toast a {
        color: #2563eb;
        font-weight: 500;
        border: none;
        border-radius: 0.375rem;
        padding: 0.25rem 0.75rem;
        font-size: 13px;
        background: transparent;
        transition: background 0.2s, color 0.2s;
    }

    .custom-toast a:hover {
        background: #f1f5f9;
        color: #1d4ed8;
    }

    .custom-toast .close {
        cursor: pointer;
        font-size: 18px;
        color: #9ca3af;
        margin-left: auto;
        margin-top: 2px;
        transition: color 0.2s;
    }

    .custom-toast .close:hover {
        color: #ef4444;
    }
</style>

<div x-data="{
    open: false,
    count: @json(auth()->user()->unreadNotifications->count()),
    notifications: [],
    previousIds: [],
    
    showToast(notification) {
        const toast = document.createElement('div');
        toast.className = 'custom-toast';

        toast.innerHTML = `
            <div>${notification.title} 🔔</div>
            <div>
                <a href='/notifications/read-and-redirect/${notification.id}'>{{ __('message.show') }}</a>
                <span class='close'>&times;</span>
            </div>
        `;

        toast.querySelector('.close').addEventListener('click', () => toast.remove());
        document.body.appendChild(toast);

        // Play notification sound
        const audio = new Audio('/sounds/notification.mp3');
        audio.play().catch(() => {
            // ignore autoplay errors
        });

        setTimeout(() => {
            toast.remove();
        }, 10000);
    },

    markOneAsRead(id) {
        fetch('{{ route('notifications.read') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id })
        }).then(() => {
            this.notifications = this.notifications.filter(n => n.id !== id);
            this.count = Math.max(this.count - 1, 0);
        });
    },

    fetchNotifications() {
        fetch('/notifications/unread-list')
            .then(res => res.json())
            .then(data => {
                const currentIds = data.notifications.map(n => n.id);

                data.notifications.forEach(n => {
                    if (!this.previousIds.includes(n.id)) {
                        this.showToast(n);
                    }
                });

                this.previousIds = [...new Set([...this.previousIds, ...currentIds])];
                this.notifications = data.notifications;
                this.count = data.notifications.length;
            });
    },

    init() {
        this.fetchNotifications();
        setInterval(() => this.fetchNotifications(), 10000);
    }
}" x-init="init()" class="relative">

    <button @click="open = !open" class="relative focus:outline-none">
        <x-heroicon-o-bell class="w-6 h-6 text-gray-600 dark:text-gray-300" />

        <template x-if="count > 0">
            <span x-text="count"
                class="p-2 absolute -top-2 w-5 h-5 flex items-center justify-center text-white rounded-full shadow-lg border-2 border-white dark:border-gray-800 animate-pulse"
                style="z-index: 999; background: linear-gradient(90deg, #3B82F6 60%, #2563EB 100%); right: 14px;font-size: 0.75rem;">
            </span>
        </template>
    </button>

    <div x-show="open" @click.outside="open = false"
        class="absolute right-0 mt-2 w-[300px] bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded shadow-lg z-50"
        style="{{ app()->getLocale() === 'ar' ? 'top: 31px;left:-20px;width: 300px;' : 'top: 31px;right:-20px;width: 300px;' }}">

        <div class="p-2 font-semibold text-gray-700 dark:text-gray-200 border-b dark:border-gray-700">
            {{ __('message.notifications') }}
        </div>

        <ul class="max-h-80 overflow-y-auto">
            <template x-if="notifications.length === 0">
                <li class="p-4 text-center text-sm text-gray-500 dark:text-gray-400">
                    {{ __('message.no_notifications') }}
                </li>
            </template>

            <template x-for="notification in notifications" :key="notification.id">
                <li :id="'notification-' + notification.id"
                    class="px-4 py-2 border-b border-gray-100 dark:border-gray-800 flex justify-between items-start gap-2 hover:bg-gray-100 transition-colors">
                    <a :href="`{{ route('notifications.read.redirect', ['id' => '__ID__']) }}`.replace('__ID__', notification.id)"
                        class="block flex-1">
                        <div class="text-sm font-medium text-gray-800 dark:text-gray-100" x-text="notification.title"></div>
                        <div class="text-xs text-gray-500 dark:text-gray-400" x-text="notification.time"></div>
                    </a>
                    <button @click.prevent="markOneAsRead(notification.id)"
                        class="text-xs text-blue-500 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 p-2 rounded-full">
                        <svg style="--c-400:var(--success-400);--c-500:var(--success-500);"
                            class="fi-ta-icon-item fi-ta-icon-item-size-lg h-6 w-6 fi-color-custom text-custom-500 dark:text-custom-400 fi-color-success"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" aria-hidden="true" data-slot="icon">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </button>
                </li>
            </template>
        </ul>
    </div>
</div>