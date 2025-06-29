<?php

namespace App\Console\Commands;

use App\Jobs\SendReminderNotification;
use App\Models\Reminder;
use App\Models\User;
use App\Notifications\ReminderNotification;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders to users based on their permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $reminders = Reminder::where('remind_at', '<=', now())
            ->where('notified', false)
            ->get();

        foreach ($reminders as $reminder) {
            $remindable = $reminder->remindable;

            if (! $remindable)
                continue;

            $users = collect();
            $resourceName = class_basename($remindable);

            switch ($resourceName) {
                case 'Client':
                    $permission = 'edit ' . Str::plural(Str::kebab($resourceName));
                    $users = User::permission($permission)->get();
                    break;

                case 'Project':
                    if ($remindable instanceof \App\Models\Project) {
                        $users = $remindable->assignedTo;
                    }
                    break;

                case 'Task':
                    if ($remindable->assigned_to) {
                        $user = User::find($remindable->assigned_to);
                        if ($user) {
                            $users = collect([$user]);
                        }
                    }
                    break;
            }
            foreach ($users as $user) {
                SendReminderNotification::dispatch($reminder->id, $user->id);
            }


            $reminder->update(['notified' => true]);
        }
        return Command::SUCCESS;
    }
}
