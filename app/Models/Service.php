<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'description'
    ];
    public function appointments()
{
    return $this->hasMany(Appointment::class, 'service_id');
}
public function tasks()
{
    return $this->hasMany(Task::class);
}
public function agents()
{
    return $this->belongsToMany(Agent::class, 'agent_service', 'service_id', 'agent_id');
}
// app/Models/Service.php
public function users()
{
    return $this->hasMany(\App\Models\User::class);
}

}
