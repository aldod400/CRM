<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class InternalNote extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('internal_note')
            ->logAll()
            ->setDescriptionForEvent(fn(string $eventName) => "Internal note has been {$eventName}")
            ->dontSubmitEmptyLogs();
    }
    protected $fillable = [
        'noteable',
        'content',
        'created_by',
    ];

    public function noteable()
    {
        return $this->morphTo();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }
}
