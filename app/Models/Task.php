<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $fillable = [
        'service_id', 'name', 'processing_time'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    public function appointments()
{
    return $this->hasMany(Appointment::class, 'task_id');
}

}
