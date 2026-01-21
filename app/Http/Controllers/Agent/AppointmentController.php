<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AppointmentController extends Controller
{


    public function index()
    {
        $agent = Auth::user();

        if (!$agent->service_id) {
            return redirect()->route('agent.dashboard')->with('error', 'No service assigned.');
        }

        $appointments = Appointment::where('service_id', $agent->service_id)
            ->with(['personnel', 'task', 'service'])
            ->orderBy('requested_at', 'asc')
            ->get();

        return Inertia::render('Agent/AgentAppointments/Index', [
            'appointments' => $appointments
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,canceled'
        ]);

        $appointment = Appointment::findOrFail($id);

        if ($validated['status'] === 'in_progress') {
            $appointment->processed_at = now();
        } elseif ($validated['status'] === 'completed') {
            $appointment->completed_at = now();
        }

        $appointment->status = $validated['status'];
        $appointment->save();

        return redirect()->route('Agent.appointments.index')->with('success', 'Appointment updated successfully.');
    }

    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();

        return redirect()->route('Agent.appointments.index')->with('success', 'Appointment deleted successfully.');
    }

    public function getAvailableTimes(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'date' => 'required|date',
        ]);

        $serviceId = $request->service_id;
        $date = Carbon::parse($request->date);

        $tasks = Task::where('service_id', $serviceId)->get();

        if ($tasks->isEmpty()) {
            return response()->json([]);
        }

        $processingTime = $tasks->first()->processing_time;
        $startTime = Carbon::createFromTime(8, 0);
        $endTime = Carbon::createFromTime(17, 0);

        $existingAppointments = Appointment::whereDate('requested_at', $date)
            ->where('service_id', $serviceId)
            ->with('task')
            ->get();

        $availableSlots = [];

        while ($startTime < $endTime) {
            $slotEnd = $startTime->copy()->addMinutes($processingTime);

            $conflict = $existingAppointments->contains(function ($appointment) use ($startTime, $slotEnd) {
                $start = Carbon::parse($appointment->requested_at);
                $end = $start->copy()->addMinutes($appointment->task->processing_time);

                return ($startTime < $end && $slotEnd > $start);
            });

            if (!$conflict) {
                $availableSlots[] = $startTime->format('H:i');
            }

            $startTime->addMinutes($processingTime);
        }

        return response()->json($availableSlots);
    }

}