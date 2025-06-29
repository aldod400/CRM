<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Reminder extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('reminder')
            ->logAll()
            ->setDescriptionForEvent(fn(string $eventName) => "Reminder for " . class_basename($this->remindable_type) . " has been {$eventName}")
            ->dontSubmitEmptyLogs();
    }
    protected $fillable = [
        'title',
        'remind_at',
        'notified',
        'created_by',
        'remindable_id',
        'remindable_type',
    ];
    protected $casts = [
        'remind_at' => 'datetime',
    ];

    /**
     * Get the parent remindable model (morph to).
     */
    public function remindable()
    {
        return $this->morphTo();
    }

    /**
     * Get the user that owns the reminder.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }
}
