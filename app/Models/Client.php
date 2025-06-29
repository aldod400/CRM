<?php

namespace App\Models;

use App\Enum\ClientStatus;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Client extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('client')
            ->logAll()
            ->setDescriptionForEvent(fn(string $eventName) => "Client {$this->name} has been {$eventName}")
            ->dontSubmitEmptyLogs();
    }
    protected $fillable = [
        'name',
        'company_name',
        'email',
        'phone',
        'status',
        'source',
        'notes',
        'created_by',
    ];

    /**
     * Get the user that created the client.
     */
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
    protected $casts = [
        'status' => ClientStatus::class,
    ];
}
