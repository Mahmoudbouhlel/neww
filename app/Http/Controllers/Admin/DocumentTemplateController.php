<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentTemplate;
use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DocumentTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $documents = DocumentTemplate::with('service')->get();
        $services = Service::all();

        return Inertia::render('Admin/Documents/Index', [
            'documents' => $documents,
            'services' => $services
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $services = Service::all();
        return Inertia::render('Admin/Documents/Create', [
            'services' => $services
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'service_id' => 'required|exists:services,id',
            'fields' => 'required|array'
        ]);

        DocumentTemplate::create([
            'name' => $request->name,
            'service_id' => $request->service_id,
            'fields' => json_encode($request->fields),
        ]);

        return redirect()->route('documents.index')->with('success', 'Document créé avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $document = DocumentTemplate::with('service')->findOrFail($id);
        return Inertia::render('Admin/Documents/Show', [
            'document' => $document
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $document = DocumentTemplate::findOrFail($id);
        $services = Service::all();

        return Inertia::render('Admin/Documents/Edit', [
            'document' => $document,
            'services' => $services
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string',
            'service_id' => 'required|exists:services,id',
            'fields' => 'required|array'
        ]);

        $document = DocumentTemplate::findOrFail($id);
        $document->update([
            'name' => $request->name,
            'service_id' => $request->service_id,
            'fields' => json_encode($request->fields),
        ]);

        return redirect()->route('documents.index')->with('success', 'Document mis à jour');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DocumentTemplate::destroy($id);
        return redirect()->route('documents.index')->with('success', 'Document supprimé');
    }
}
