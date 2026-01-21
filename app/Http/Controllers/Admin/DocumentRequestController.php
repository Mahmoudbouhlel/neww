<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DocumentRequestController extends Controller
{
   // DocumentRequestsController.php
// DocumentRequestsController.php
public function index()
{
    $requests = DocumentRequest::with(['personnel', 'template', 'service', 'agent'])->get();
    $services = Service::all();

    return inertia('Admin/DocumentRequests/Index', [
        'requests' => $requests,
        'services' => $services,
    ]);
}


    /**
     * ✍️ SUPER ADMIN - Mettre à jour le statut d'une demande
     */
    public function updateStatus(Request $request, $id)
    {
        // ✅ Validation des entrées
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,rejected',
            'agent_comment' => 'nullable|string',
        ]);

        // 🔍 Trouver la demande (ou erreur 404 si non trouvée)
        $docRequest = DocumentRequest::findOrFail($id);

        // 🛠️ Mise à jour des données de la demande
        $docRequest->status = $validated['status'];
        $docRequest->agent_comment = $validated['agent_comment'];
        $docRequest->agent_id = auth()->user()->id; // ✅ Qui a modifié ? Agent/Admin connecté
        $docRequest->save();

        // ✅ Redirection avec message de succès
        return redirect()->route('document_requests.index')->with('success', '✅ Statut mis à jour avec succès');
    }
}
