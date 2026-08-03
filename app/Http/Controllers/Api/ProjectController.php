<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        return ProjectResource::collection(Project::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'contact_id' => 'required|exists:contacts,id',
            'name' => 'required|string',
            'status' => 'required|in:planning,active,completed,cancelled',
            'owner_id' => 'nullable|exists:users,id',
        ]);

        $project = Project::create($validated);

        return new ProjectResource($project);
    }

    public function show(Project $project)
    {
        return new ProjectResource($project);
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'lead_id' => 'sometimes|exists:leads,id',
            'contact_id' => 'sometimes|exists:contacts,id',
            'name' => 'sometimes|string',
            'status' => 'sometimes|in:planning,active,completed,cancelled',
            'owner_id' => 'nullable|exists:users,id',
        ]);

        $project->update($validated);

        return new ProjectResource($project);
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return response()->json(['message' => 'Project deleted']);
    }
}