<?php

namespace App\Mcp\Tools;

use App\Models\Project;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;

#[Name('updateProject')]
#[Description(
    'Update an existing project. Only provided fields will be changed.'
)]
class UpdateProjectTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'project_id' => $schema->integer()
                ->description('ID of the project to update.')
                ->required(),

            'name' => $schema->string()
                ->description('New project name.')
                ->nullable(),

            'description' => $schema->string()
                ->description('New project description.')
                ->nullable(),

            'status' => $schema->string()
                ->description('New project status.')
                ->nullable(),

            'lead_id' => $schema->integer()
                ->description('New lead ID.')
                ->nullable(),

            'contact_id' => $schema->integer()
                ->description('New contact ID.')
                ->nullable(),
        ];
    }

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'status' => ['sometimes', 'nullable', 'string', 'max:100'],
            'lead_id' => ['sometimes', 'nullable', 'integer', 'exists:leads,id'],
            'contact_id' => ['sometimes', 'nullable', 'integer', 'exists:contacts,id'],
        ]);

        $project = Project::findOrFail($validated['project_id']);

        unset($validated['project_id']);

        $project->update($validated);

        return Response::text(
            json_encode([
                'success' => true,
                'message' => 'Project updated successfully.',
                'project' => $project->fresh(),
            ], JSON_PRETTY_PRINT)
        );
    }
}