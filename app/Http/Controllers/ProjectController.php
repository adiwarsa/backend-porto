<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ProjectCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProjectController extends Controller
{
    public function index()
    {
        if (request()->wantsJson()) {
            $query = Project::query();
            
            // Filter by type if provided
            if (request()->has('type') && request('type') !== '') {
                $query->where('type', request('type'));
            }
            
            // Filter by status if provided
            if (request()->has('status') && request('status') !== '') {
                $query->where('status', request('status'));
            }
            
            // Filter by technology if provided
            if (request()->has('technology') && request('technology') !== '') {
                $technology = request('technology');
                $query->whereJsonContains('technologies', $technology);
            }
            
            $projects = $query->orderBy('created_at', 'desc')->get();
            return new ProjectCollection($projects);
        }
        
        $items = Project::paginate(10);
        return view('pages.project.index', compact('items'));
    }

    public function show($id)
    {
        $item = Project::findOrFail($id);
        
        if (request()->wantsJson()) {
            return new ProjectResource($item);
        }
        
        return view('pages.project.show', compact('item'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'type' => 'required|string',
            'images' => 'nullable|array',
            'author' => 'required|string',
            'date' => 'required|string',
            'description' => 'nullable|string',
            'technologies' => 'nullable|array',
            'features' => 'nullable|array',
            'status' => 'required|string',
            'liveUrl' => 'nullable|string',
            'githubUrl' => 'nullable|string',
        ]);
        
        $validated['technologies'] = $validated['technologies'] ?? [];
        $validated['features'] = $validated['features'] ?? [];
        $validated['images'] = $validated['images'] ?? [];
        
        $project = Project::create($validated);
        
        if (request()->wantsJson()) {
            return (new ProjectResource($project))
                ->response()
                ->setStatusCode(201);
        }
        
        return redirect()->route('project.index')->with($this->successNotification('success_create', 'menu.project'));
    }

    public function update(Request $request, $id)
    {
        $item = Project::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string',
            'type' => 'required|string',
            'images' => 'nullable|array',
            'author' => 'required|string',
            'date' => 'required|string',
            'description' => 'nullable|string',
            'technologies' => 'nullable|array',
            'features' => 'nullable|array',
            'status' => 'required|string',
            'liveUrl' => 'nullable|string',
            'githubUrl' => 'nullable|string',
        ]);
        
        $validated['technologies'] = $validated['technologies'] ?? [];
        $validated['features'] = $validated['features'] ?? [];
        $validated['images'] = $validated['images'] ?? [];
        
        $item->update($validated);
        
        if (request()->wantsJson()) {
            return new ProjectResource($item);
        }
        
        return redirect()->route('project.index')->with($this->successNotification('success_update', 'menu.project'));
    }

    public function destroy($id)
    {
        $item = Project::findOrFail($id);
        $item->delete();
        
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Project deleted successfully'
            ]);
        }
        
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