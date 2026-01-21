<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Users/Index', [
            'users' => User::with('service')->get(), // Eager load service
            'services' => Service::all(), // Fetch all services
        ]);
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|min:6',
            'role'       => 'required|in:super_admin,agent,user',
            'service_id' => 'nullable|exists:services,id',
        ]);

        // Assign service_id only if the role is 'agent'
        $validated['password'] = Hash::make($validated['password']);
        if ($validated['role'] !== 'agent') {
            $validated['service_id'] = null;
        }

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $id,
            'password'   => 'nullable|min:6',
            'role'       => 'required|in:super_admin,agent,user',
            'service_id' => 'nullable|exists:services,id',
        ]);

        // Update password only if provided
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Ensure service_id is only assigned to agents
        if ($validated['role'] !== 'agent') {
            $validated['service_id'] = null;
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        User::destroy($id);
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
