<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Tasks/Index', [
            'tasks' => Task::with('service')->get(),
            'services' => Service::all()
        ]);
    }

    public function create()
    {
        $services = Service::all();
        return view('admin.tasks.create', compact('services'));
    }

    public function store(Request $request)
    {
        Task::create($request->validate(['name' => 'required', 'service_id' => 'required', 'processing_time' => 'required|integer']));
        return redirect()->route('tasks.index');
    }

    public function edit($id)
    {
        $task = Task::findOrFail($id);
        $services = Service::all();
        return view('admin.tasks.edit', compact('task', 'services'));
    }

    public function update(Request $request, Task $task)
    {
        $task->update($request->all());
        return redirect()->route('tasks.index');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index');
    }
}
