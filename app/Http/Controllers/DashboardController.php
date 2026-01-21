<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Appointment;
use App\Models\DocumentRequest;
use App\Models\Service;
use App\Models\Task;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard');
    }

    public function admin()
    {
        return Inertia::render('Admin/Dashboard', [
            // Authenticated user
            'authUser' => Auth::user(),

            // Task and Appointment Metrics
            'totalTasks' => Task::count(),
            'totalAgents' => User::where('role', 'agent')->count(),
            'totalAppointments' => Appointment::count(),
            'pendingAppointments' => Appointment::where('status', 'pending')->count(),
            'completedAppointments' => Appointment::where('status', 'completed')->count(),

            // Document Request Metrics
            'totalDocumentRequests' => DocumentRequest::count(),
            'pendingDocumentRequests' => DocumentRequest::where('status', 'pending')->count(),
            'completedDocumentRequests' => DocumentRequest::where('status', 'completed')->count(),

            // Recent Appointments (last 10, sorted by requested_at ascending)
            'recentAppointments' => Appointment::with(['personnel', 'task', 'service'])
                ->orderBy('requested_at', 'asc')
                ->latest()
                ->limit(10)
                ->get(),

            // Recent Document Requests (last 10, sorted by created_at ascending)
            'recentDocumentRequests' => DocumentRequest::with(['personnel', 'service', 'template', 'agent'])
                ->orderBy('created_at', 'asc')
                ->latest()
                ->limit(10)
                ->get(),

            // Services with task + appointment stats
            'services' => Service::withCount([
                'tasks',
                'appointments as tasks_completed' => fn($q) => $q->where('status', 'completed'),
                'appointments as tasks_pending' => fn($q) => $q->where('status', 'pending'),
            ])->get(),

            // Agents by Department (for pie chart)
            'departments' => Service::withCount(['users as agent_count'])->get(),
        ]);
    }




    public function agent()
    {

        $agent = Auth::user();

        if (!$agent->service_id) {
            return redirect()->route('agent.dashboard')->with('error', 'You are not assigned to any service.');
        }

        $service = Service::with('tasks')->findOrFail($agent->service_id);

        // Current appointments
        $appointments = Appointment::where('service_id', $service->id)
            ->where('status', '!=', 'completed')
            ->where('status', '!=', 'canceled')
            ->orderBy('requested_at', 'asc')
            ->with('personnel', 'task')
            ->get();

        // Stats calculations
        $totalTasks = $service->tasks->count();
        $allAppointments = Appointment::where('service_id', $service->id)->get();

        // Status counts
        $pendingAppointments = $allAppointments->where('status', 'pending')->count();
        $inProgressAppointments = $allAppointments->where('status', 'in_progress')->count();
        $completedAppointments = $allAppointments->where('status', 'completed')->count();

        // Weekly appointment data for charts
        $weeklyAppointments = Appointment::where('service_id', $service->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, status, COUNT(*) as count')
            ->groupBy('date', 'status')
            ->orderBy('date')
            ->get();

        // Process data for charts
        $statusDistribution = [
            'pending' => $pendingAppointments,
            'in_progress' => $inProgressAppointments,
            'completed' => $completedAppointments,
        ];

        // Daily appointment trends
        $dailyTrends = [];
        for ($i = 30; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dailyTrends[$date] = [
                'date' => $date,
                'pending' => 0,
                'in_progress' => 0,
                'completed' => 0,
                'total' => 0,
            ];
        }

        foreach ($weeklyAppointments as $appointment) {
            $date = $appointment->date;
            if (isset($dailyTrends[$date])) {
                $dailyTrends[$date][$appointment->status] = $appointment->count;
                $dailyTrends[$date]['total'] += $appointment->count;
            }
        }

        // Task distribution
        $taskDistribution = $service->tasks->mapWithKeys(function ($task) {
            $count = Appointment::where('task_id', $task->id)->count();
            return [$task->name => $count];
        });

        return Inertia::render('Agent/Dashboard', [
            'authUser' => Auth::user(),
            'service' => $service,
            'tasks' => $service->tasks,
            'appointments' => $appointments,
            'stats' => [
                'totalTasks' => $totalTasks,
                'pendingAppointments' => $pendingAppointments,
                'inProgressAppointments' => $inProgressAppointments,
                'completedAppointments' => $completedAppointments,
                'totalAppointments' => $allAppointments->count(),
            ],
            'chartData' => [
                'statusDistribution' => $statusDistribution,
                'dailyTrends' => array_values($dailyTrends),
                'taskDistribution' => $taskDistribution,
            ],
        ]);
    }


    /**
     * Update the status of an appointment.
     */
    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,canceled',
        ]);

        // Ensure the agent is updating an appointment for their assigned service
        if ($appointment->service_id !== Auth::user()->service_id) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        // Update appointment status
        $appointment->status = $request->status;
        if ($request->status === 'in_progress') {
            $appointment->processed_at = now();
        }
        $appointment->save();

        return redirect()->route('agent.dashboard')->with('success', 'Appointment status updated.');

    }
}
