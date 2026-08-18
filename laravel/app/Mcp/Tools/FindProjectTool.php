<?php

namespace App\Mcp\Tools;

use App\Models\Project;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Name('findProject')]
#[Description(
    'Find projects in the CRM. Use project_id for an exact project lookup, or use search/status/lead_id/contact_id to filter projects.'
)]
#[IsReadOnly]
class FindProjectTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'project_id' => $schema->integer()
                ->description('The exact project ID.')
                ->nullable(),

            'search' => $schema->string()
                ->description('Search project name or description.')
                ->nullable(),

            'status' => $schema->string()
                ->description('Filter by project status.')
                ->nullable(),

            'lead_id' => $schema->integer()
                ->description('Filter projects belonging to a lead.')
                ->nullable(),

            'contact_id' => $schema->integer()
                ->description('Filter projects belonging to a contact.')
                ->nullable(),
        ];
    }

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'project_id' => ['nullable', 'integer'],
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:100'],
            'lead_id' => ['nullable', 'integer'],
            'contact_id' => ['nullable', 'integer'],
        ]);

        $query = Project::query();

        if (!empty($validated['project_id'])) {
            $query->where('id', $validated['project_id']);
        }

        if (!empty($validated['search'])) {
            $search = $validated['search'];

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (!empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        if (!empty($validated['lead_id'])) {
            $query->where('lead_id', $validated['lead_id']);
        }

        if (!empty($validated['contact_id'])) {
            $query->where('contact_id', $validated['contact_id']);
        }

        $projects = $query
            ->latest()
            ->limit(20)
            ->get();

        return Response::text(
            $projects->toJson(JSON_PRETTY_PRINT)
        );
    }
}