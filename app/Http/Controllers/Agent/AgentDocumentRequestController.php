<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AgentDocumentRequestController extends Controller
{
    /**
     * Display a listing of the agent's assigned document requests.
     */
    public function index()
    {
        $documentRequests = DocumentRequest::with(['personnel', 'template', 'service'])
            ->where('service_id', auth()->user()->service_id)
            ->latest()
            ->get();

        return Inertia::render('Agent/DocumentRequests/Index', [
            'documentRequests' => $documentRequests,
        ]);
    }


    /**
     * Update the status or comment of a document request.
     */
    // In AgentDocumentRequestController.php
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,rejected',
            'agent_comment' => 'nullable|string|max:1000',
        ]);

        $documentRequest = DocumentRequest::where('service_id', auth()->user()->service_id)
            ->findOrFail($id);

        $documentRequest->update([
            'status' => $request->status,
            'agent_comment' => $request->input('agent_comment', $documentRequest->agent_comment),
            'agent_id' => auth()->id()
        ]);

        // === Send SMS to the personnel who created the request ===
        // Assuming you have a personnel relationship on DocumentRequest
        $personnel = $documentRequest->personnel;
        if ($personnel && $personnel->phone) {
            $phone = '216' . ltrim($personnel->phone, '0');
            $statusMsg = match ($request->status) {
                'pending' => "en attente",
                'processing' => "en cours de traitement",
                'completed' => "terminée",
                'rejected' => "refusée",
                default => $request->status,
            };
            $sms = "Bonjour {$personnel->name}, votre demande de document (ID: {$documentRequest->id}) est maintenant: $statusMsg. - LEONI";
            $apiKey = 'YtZJpkX6Sxnjk406/-/Ti2ihQVhIQFIJ4yIt6xJVz22x01A6JsQJhvvqnza9NfuyierAt8bc2UL8XFykhJ/I7lMcl/-/7YM9IRM4';

            // Send the SMS, ignoring SSL verification if needed
            $url = 'https://api.l2t.io/tn/v0/api/api.aspx';
            $response = Http::withoutVerifying()->get($url, [
                'fct'    => 'sms',
                'key'    => $apiKey,
                'mobile' => $phone,
                'sms'    => $sms,
                'sender' => 'LEONI',
            ]);

            Log::info('SMS sent to personnel', [
                'personnel_id' => $personnel->id,
                'phone' => $phone,
                'sms' => $sms,
                'response' => $response->body(),
            ]);
        }

        return back()->with('success', 'Document request updated successfully.');
    }

}