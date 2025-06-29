<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class File extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('file')
            ->logAll()
            ->setDescriptionForEvent(fn(string $eventName) => "File has been {$eventName}")
            ->dontSubmitEmptyLogs();
    }
    protected $fillable = [
        'fileable_type',
        'fileable_id',
        'file_path',
        'uploaded_by',
        'description',
    ];

    public function fileable()
    {
        return $this->morphTo();
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
