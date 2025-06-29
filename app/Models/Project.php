<?php

namespace App\Models;

use App\Enum\ProjectStatus;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Project extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('project')
            ->logAll()
            ->setDescriptionForEvent(fn(string $eventName) => "Project {$this->name} has been {$eventName}")
            ->dontSubmitEmptyLogs();
    }
    protected $fillable = [
        'client_id',
        'name',
        'description',
        'start_date',
        'end_date',
        'status',
        'assigned_to',
        'created_by',
    ];
    protected $casts = [
        'status' => ProjectStatus::class,
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function assignedTo()
    {
        return $this->belongsToMany(User::class, 'project_users')
            ->using(ProjectUser::class)
            ->withPivot(['user_id', 'role', 'joined_at', 'is_active'])
            ->withTimestamps();
    }

    public function projectUsers()
    {
        return $this->hasMany(ProjectUser::class, 'project_id');
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
