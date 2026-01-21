<?php

namespace App\Http\Controllers;

use App\Events\AppointmentCreated;
use App\Models\Appointment;
use App\Models\DocumentRequest;
use App\Models\DocumentTemplate;
use App\Models\Service;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

class UserDashboardController extends Controller
{
    /**
     * Show the user dashboard with available services and tasks.
     */
    public function index()
    {
        $user = Auth::guard('personnel')->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Fetch available services with related tasks
        $services = Service::with('tasks:id,service_id,name')->select('id', 'name')->get();

        // Fetch document templates
        $document_templates = DocumentTemplate::all()->map(function($template) {
            $template->fields = $template->fields ?? '[]';
            return $template;
        });

        // Fetch user appointments
        $appointments = Appointment::where('personnel_id', $user->pers_no)
            ->with(['service:id,name', 'task:id,name'])
            ->orderBy('requested_at', 'desc')
            ->get()
            ->map(fn($appointment) => [
                'id' => $appointment->id,
                'service_name' => $appointment->service->name ?? 'Unknown',
                'task_name' => $appointment->task->name ?? 'Unknown',
                'date' => $appointment->requested_at
                    ? Carbon::parse($appointment->requested_at)->format('Y-m-d H:i')
                    : 'N/A',
                'status' => ucfirst($appointment->status),
            ]);


        // FIXED: Use pers_no instead of user_id
        $document_requests = DocumentRequest::where('user_id', $user->pers_no)
    ->with(['service:id,name', 'documentTemplate:id,name'])
    ->orderBy('created_at', 'desc')
    ->get()
    ->map(fn($request) => [
        'id' => $request->id,
        'service_name' => $request->service->name ?? 'Unknown',
        'document_template_name' => $request->documentTemplate->name ?? 'Unknown',
        'status' => ucfirst($request->status),
        'created_at' => Carbon::parse($request->created_at)->format('Y-m-d H:i'),
    ]);




        return Inertia::render('User/UserDashboard', [
            'user' => $user,
            'services' => $services,
            'appointments' => $appointments,
            'document_templates' => $document_templates,
            'document_requests' => $document_requests,
        ]);
    }

    /**
     * Store a new appointment.
     */
    public function storeAppointment(Request $request)
    {
        $user = Auth::guard('personnel')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Validate input
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'task_id' => 'required|exists:tasks,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        // Fix personnel_id reference
        $appointment = Appointment::create([
            'personnel_id' => $user->pers_no, // Use pers_no instead of id
            'service_id' => $request->service_id,
            'task_id' => $request->task_id,
            'requested_at' => Carbon::parse($request->date . ' ' . $request->time),
            'status' => 'pending',
        ]);

        event(new AppointmentCreated($appointment)); // ✅ Now this will work

        return redirect()->back()->with('success', 'Appointment booked successfully!');
    }


    /**
     * Cancel an appointment.
     */
    public function cancelAppointment($id)
    {
        $user = Auth::guard('personnel')->user();

        $appointment = Appointment::where('id', $id)
            ->where('personnel_id', $user->pers_no)
            ->firstOrFail();

        if ($appointment->status !== 'pending') {
            return response()->json(['message' => 'Only pending appointments can be canceled'], 400);
        }

        $appointment->delete();

        return redirect()->back()->with('success', 'Appointment canceled successfully!');
    }
     /**
     * ✅ Store a new document request.
     */
    public function storeDocumentRequest(Request $request)
    {
        $user = Auth::guard('personnel')->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'document_template_id' => 'required|exists:document_templates,id',
            'data' => 'nullable|array',
            'comment' => 'nullable|string',
        ]);

        $template = DocumentTemplate::findOrFail($request->document_template_id);

        // Get dynamic form data as an array
        $data = $request->input('data', []);

        // Add the comment if provided
        if ($request->filled('comment')) {
            $data['comment'] = $request->comment;
        }

        // Store the data array directly — no json_encode
        DocumentRequest::create([
            'user_id' => $user->pers_no,
            'document_template_id' => $template->id,
            'service_id' => $template->service_id,
            'data' => $data, // Store as an array directly
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Document request submitted successfully!');
    }



}
