<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminappointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with(['personnel', 'service', 'task'])
            ->orderBy('requested_at', 'asc') // FIFO order
            ->paginate(10);

        return Inertia::render('Admin/Appointments/Index', [
            'appointments' => $appointments,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'personnel_id' => 'required|exists:personnels,id',
            'service_id' => 'required|exists:services,id',
            'task_id' => 'required|exists:tasks,id',
        ]);

        Appointment::create([
            'personnel_id' => $request->personnel_id,
            'service_id' => $request->service_id,
            'task_id' => $request->task_id,
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        return redirect()->route('appointments.index')->with('success', 'Appointment created successfully.');
    }

    public function update(Request $request, Appointment $appointment)
{
    $request->validate([
        'status' => 'required|in:pending,in_progress,completed,canceled',
    ]);

    if ($request->status === 'in_progress') {
        $appointment->processed_at = now(); // Track when task starts
    } elseif ($request->status === 'completed') {
        $appointment->completed_at = now(); // Track when task ends
    }

    $appointment->status = $request->status;
    $appointment->save();

    return redirect()->route('appointments.index')->with('success', 'Appointment updated successfully.');
}


    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return redirect()->route('appointments.index')->with('success', 'Appointment deleted successfully.');
    }
}
