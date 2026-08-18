<?php

namespace App\Mcp\Tools;

use App\Models\Lead;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;

#[Name('updateLead')]
#[Description(
    'Update an existing lead. Only the provided fields will be changed.'
)]
class UpdateLeadTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'lead_id' => $schema->integer()
                ->description('ID of the lead to update.')
                ->required(),

            'name' => $schema->string()
                ->description('New lead name.')
                ->nullable(),

            'status' => $schema->string()
                ->description('New lead status.')
                ->nullable(),

            'contact_id' => $schema->integer()
                ->description('New contact ID.')
                ->nullable(),
        ];
    }

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'lead_id' => ['required', 'integer', 'exists:leads,id'],
            'name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'nullable', 'string', 'max:100'],
            'contact_id' => ['sometimes', 'nullable', 'integer', 'exists:contacts,id'],
        ]);

        $lead = Lead::findOrFail($validated['lead_id']);

        unset($validated['lead_id']);

        $lead->update($validated);

        return Response::text(
            json_encode([
                'success' => true,
                'message' => 'Lead updated successfully.',
                'lead' => $lead->fresh(),
            ], JSON_PRETTY_PRINT)
        );
    }
}