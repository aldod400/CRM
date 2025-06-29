<?php

namespace App\Jobs;

use App\Models\Reminder;
use App\Models\User;
use App\Notifications\ReminderNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendReminderNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $reminderId;
    public int $userId;

    public function __construct(int $reminderId, int $userId)
    {
        $this->reminderId = $reminderId;
        $this->userId = $userId;
    }

    public function handle(): void
    {
        $reminder = Reminder::find($this->reminderId);
        $user = User::find($this->userId);

        if (! $reminder || ! $user)
            return;


        $user->notify(new ReminderNotification($reminder));
    }
}
