<?php

namespace App\Mcp\Tools;

use App\Models\Lead;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Name('findLead')]
#[Description(
    'Find leads in the CRM by ID, name, status, or contact.'
)]
#[IsReadOnly]
class FindLeadTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'lead_id' => $schema->integer()
                ->description('Exact lead ID.')
                ->nullable(),

            'search' => $schema->string()
                ->description('Search by lead name or relevant text.')
                ->nullable(),

            'status' => $schema->string()
                ->description('Filter by lead status.')
                ->nullable(),

            'contact_id' => $schema->integer()
                ->description('Filter leads belonging to a contact.')
                ->nullable(),
        ];
    }

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'lead_id' => ['nullable', 'integer'],
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:100'],
            'contact_id' => ['nullable', 'integer'],
        ]);

        $query = Lead::query();

        if (!empty($validated['lead_id'])) {
            $query->where('id', $validated['lead_id']);
        }

        if (!empty($validated['search'])) {
            $search = $validated['search'];

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if (!empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        if (!empty($validated['contact_id'])) {
            $query->where('contact_id', $validated['contact_id']);
        }

        $leads = $query
            ->latest()
            ->limit(20)
            ->get();

        return Response::text(
            $leads->toJson(JSON_PRETTY_PRINT)
        );
    }
}