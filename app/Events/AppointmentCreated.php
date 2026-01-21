<?php

namespace App\Events;

use App\Models\Appointment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppointmentCreated
{
    use InteractsWithSockets, SerializesModels;

    public $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment->load(['personnel', 'task']);
    }

    public function broadcastOn()
    {
        return new PrivateChannel('appointments');
    }

    public function broadcastAs()
    {
        return 'AppointmentCreated';
    }
}
