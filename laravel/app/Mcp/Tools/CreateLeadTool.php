<?php

namespace App\Mcp\Tools;

use App\Models\Lead;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;

#[Name('createLead')]
#[Description(
    'Create a new lead in the CRM. The lead can be associated with a contact.'
)]
class CreateLeadTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()
                ->description('Lead name.')
                ->required(),

            'status' => $schema->string()
                ->description('Lead status.')
                ->default('new'),

            'contact_id' => $schema->integer()
                ->description('Contact associated with the lead.'),
        ];
    }

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:100'],
            'contact_id' => ['nullable', 'integer', 'exists:contacts,id'],
        ]);

        $lead = Lead::create([
            'name' => $validated['name'],
            'status' => $validated['status'] ?? 'new',
            'contact_id' => $validated['contact_id'] ?? null,
        ]);

        return Response::text(
            json_encode([
                'success' => true,
                'message' => 'Lead created successfully.',
                'lead' => $lead,
            ], JSON_PRETTY_PRINT)
        );
    }
}
