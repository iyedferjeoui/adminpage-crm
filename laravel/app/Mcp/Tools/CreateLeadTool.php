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
#[Description('Create a new lead in the CRM. Requires contact_id and title.')]
class CreateLeadTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'contact_id' => $schema->integer()
                ->description('The ID of the contact this lead belongs to.'),
            'title' => $schema->string()
                ->description('The lead title (e.g. "Wash my car").'),
            'status' => $schema->string()
                ->description('Lead status. Must be one of: new, contacted, qualified, converted, lost. Defaults to new.'),
            'assigned_to' => $schema->integer()
                ->description('The user ID this lead is assigned to.'),
            'value' => $schema->number()
                ->description('The estimated deal value in dollars.'),
        ];
    }

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'contact_id' => ['required', 'integer', 'exists:contacts,id'],
            'title' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'in:new,contacted,qualified,converted,lost'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'value' => ['nullable', 'numeric'],
        ]);

        $lead = Lead::create($validated);

        return Response::text($lead->toJson(JSON_PRETTY_PRINT));
    }
}
