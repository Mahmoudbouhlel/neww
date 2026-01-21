<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;
    protected $fillable = [
        'personnel_id', 'service_id', 'task_id', 'status', 'requested_at', 'processed_at,completed_at'
    ];

    // Relationship with Personnel
    public function personnel()
    {
        return $this->belongsTo(Personnel::class, 'personnel_id');
    }

    // Relationship with Service
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    // Relationship with Task
    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
