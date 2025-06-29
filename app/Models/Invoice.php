<?php

namespace App\Models;

use App\Enum\InvoiceStatus;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Invoice extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('role')
            ->logAll()
            ->setDescriptionForEvent(fn(string $eventName) => "Invoice for client {$this->client?->name} has been {$eventName}")
            ->dontSubmitEmptyLogs();
    }
    protected $fillable = [
        'client_id',
        'project_id',
        'amount',
        'due_date',
        'status',
        'notes',
    ];
    protected $casts = [
        'due_date' => 'date',
        'status'   => InvoiceStatus::class
    ];
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
