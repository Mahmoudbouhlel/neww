<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Service;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function index()
    {
        $agents = Agent::all();
        return view('admin.agents.index', compact('agents'));
    }

    public function create()
    {
        $services = Service::all();
        return view('admin.agents.create', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'service_id' => 'required|exists:services,id',
            'email' => 'required|email|unique:agents,email',
        ]);

        Agent::create([
            'name' => $request->name,
            'service_id' => $request->service_id,
            'email' => $request->email,
        ]);

        return redirect()->route('admin.agents.index');
    }

    public function edit($id)
    {
        $agent = Agent::findOrFail($id);
        $services = Service::all();
        return view('admin.agents.edit', compact('agent', 'services'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'service_id' => 'required|exists:services,id',
            'email' => 'required|email|unique:agents,email,' . $id,
        ]);

        $agent = Agent::findOrFail($id);
        $agent->update([
            'name' => $request->name,
            'service_id' => $request->service_id,
            'email' => $request->email,
        ]);

        return redirect()->route('admin.agents.index');
    }

    public function destroy($id)
    {
        Agent::destroy($id);
        return redirect()->route('admin.agents.index');
    }
}
