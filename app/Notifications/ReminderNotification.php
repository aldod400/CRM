<?php

namespace App\Notifications;

use App\Models\Reminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class ReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;
    /**
     * The reminder instance.
     *
     * @var \App\Models\Reminder
     */
    protected $reminder;
    /**
     * Create a new notification instance.
     */
    public function __construct(Reminder $reminder)
    {
        $this->reminder = $reminder;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable)
    {
        Log::info('via() method called for user: ' . $notifiable->id);

        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toDatabase($notifiable)
    {
        Log::info('Notification toDatabase called for user: ' . $notifiable->id);

        $reminder = $this->reminder;
        $url = null;

        switch ($reminder->remindable_type) {
            case \App\Models\Client::class:
                $url = route('filament.admin.resources.clients.edit', $reminder->remindable_id);
                break;

            case \App\Models\Project::class:
                $url = route('filament.admin.resources.projects.edit', $reminder->remindable_id);
                break;

            case \App\Models\Task::class:
                $url = route('filament.admin.resources.tasks.index');
                break;
        }

        return [
            'title' => 'reminder: ' . $reminder->title,
            'remind_at' => $reminder->remind_at->format('Y-m-d H:i A'),
            'url' => $url,
        ];
    }
}
