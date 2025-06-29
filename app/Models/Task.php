<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Task extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('task')
            ->logAll()
            ->setDescriptionForEvent(fn(string $eventName) => "Task '{$this->title}' has been {$eventName}")
            ->dontSubmitEmptyLogs();
    }
    protected $fillable = [
        'project_id',
        'title',
        'description',
        'assigned_to',
        'start_date',
        'end_date',
        'status',
        'created_by',
    ];
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }
    public function reminders()
    {
        return $this->morphMany(Reminder::class, 'remindable');
    }
    public function internalNotes()
    {
        return $this->morphMany(InternalNote::class, 'noteable');
    }
}
