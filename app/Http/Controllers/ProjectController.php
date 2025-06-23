<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProjectController extends Controller
{
    public function index()
    {
        $items = Project::paginate(10);
        if (request()->wantsJson()) {
            return response()->json(Project::all());
        }
        return view('pages.project.index', compact('items'));
    }

    public function show($id)
    {
        $item = Project::findOrFail($id);
        if (request()->wantsJson()) {
            return response()->json($item);
        }
        return view('pages.project.show', compact('item'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'type' => 'required|string',
            'image' => 'nullable|string',
            'author' => 'required|string',
            'date' => 'required|string',
            'gradient' => 'nullable|string',
            'description' => 'nullable|string',
            'technologies' => 'nullable|array',
            'features' => 'nullable|array',
            'status' => 'required|string',
            'liveUrl' => 'nullable|string',
            'githubUrl' => 'nullable|string',
        ]);
        $validated['technologies'] = $validated['technologies'] ?? [];
        $validated['features'] = $validated['features'] ?? [];
        Project::create($validated);
        return redirect()->route('project.index')->with($this->successNotification('success_create', 'menu.project'));
    }

    public function update(Request $request, $id)
    {
        $item = Project::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string',
            'type' => 'required|string',
            'image' => 'nullable|string',
            'author' => 'required|string',
            'date' => 'required|string',
            'gradient' => 'nullable|string',
            'description' => 'nullable|string',
            'technologies' => 'nullable|array',
            'features' => 'nullable|array',
            'status' => 'required|string',
            'liveUrl' => 'nullable|string',
            'githubUrl' => 'nullable|string',
        ]);
        $validated['technologies'] = $validated['technologies'] ?? [];
        $validated['features'] = $validated['features'] ?? [];
        $item->update($validated);
        return redirect()->route('project.index')->with($this->successNotification('success_update', 'menu.project'));
    }

    public function destroy($id)
    {
        $item = Project::findOrFail($id);
        $item->delete();
        return redirect()->route('project.index')->with($this->successNotification('success_delete', 'menu.project'));
    }

    public function create()
    {
        return view('pages.project.create');
    }

    public function edit($id)
    {
        $item = Project::findOrFail($id);
        return view('pages.project.edit', compact('item'));
    }
} 