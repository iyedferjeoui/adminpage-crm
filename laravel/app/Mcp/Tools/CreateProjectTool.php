<?php

namespace App\Mcp\Tools;

use App\Models\Project;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;

#[Name('createProject')]
#[Description(
    'Create a new project in the CRM. Use this only when enough information is available to create the project.'
)]
class CreateProjectTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()
                ->description('Project name.')
                ->required(),

            'description' => $schema->string()
                ->description('Project description.')
                ->nullable(),

            'status' => $schema->string()
                ->description('Project status.')
                ->default('new'),

            'lead_id' => $schema->integer()
                ->description('ID of the lead associated with this project.')
                ->nullable(),

            'contact_id' => $schema->integer()
                ->description('ID of the contact associated with this project.')
                ->nullable(),
        ];
    }

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:100'],
            'lead_id' => ['nullable', 'integer', 'exists:leads,id'],
            'contact_id' => ['nullable', 'integer', 'exists:contacts,id'],
        ]);

        $project = Project::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'] ?? 'new',
            'lead_id' => $validated['lead_id'] ?? null,
            'contact_id' => $validated['contact_id'] ?? null,
        ]);

        return Response::text(
            json_encode([
                'success' => true,
                'message' => 'Project created successfully.',
                'project' => $project,
            ], JSON_PRETTY_PRINT)
        );
    }
}