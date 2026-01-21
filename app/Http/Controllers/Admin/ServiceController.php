<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function index()
    {
        // Fetch services from the database
        $services = Service::all();

        // Return the Inertia response with the services passed as props
        return Inertia::render('Admin/services/Index', [
            'services' => $services
        ]);
    }

    public function create()
    {
        return view('admin.services.create');
    }



    public function store(Request $request)
    {
        // Validate the request data (you can customize this as per your need)
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        // Create the new service
        $service = Service::create($validatedData);

        // Return an Inertia response after creating the service
        return redirect()->route('services.index');

    }



    public function update(Request $request, $id)
    {
       // Validate the request data
       $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
    ]);

    // Find the service by ID and update its information
    $service = Service::findOrFail($id);
    $service->update([
        'name' => $request->name,
        'description' => $request->description,
    ]);

    // Redirect back to the service index
    return redirect()->route('services.index');
    }

    public function destroy($id)
    {
        // Delete the service by its ID
        Service::destroy($id);

        // Redirect back to the service index with a success message
        return redirect()->route('services.index')
            ->with('success', 'Service Deleted successfully!');
    }
}
