<?php

namespace App\Livewire;

use Livewire\Component;

class NotificationBell extends Component
{
    public $showDropdown = false;

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
    }

    public function markAsRead($notificationId)
    {
        auth('web')->user()->notifications()->where('id', $notificationId)->first()?->markAsRead();
    }

    public function render()
    {
        return view('livewire.notification-bell', [
            'notifications' => auth('web')->user()?->unreadNotifications()->get() ?? collect(),
        ]);
    }
}
