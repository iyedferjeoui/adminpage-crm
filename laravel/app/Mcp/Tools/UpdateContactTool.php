<?php

namespace App\Mcp\Tools;

use App\Models\Contact;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;

#[Name('updateContact')]
#[Description('Update an existing contact. Only provided fields will be changed.')]
class UpdateContactTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'contact_id' => $schema->integer()
                ->description('The ID of the contact to update.'),
            'first_name' => $schema->string()
                ->description('Updated first name.'),
            'last_name' => $schema->string()
                ->description('Updated last name.'),
            'email' => $schema->string()
                ->description('Updated email address.'),
            'phone' => $schema->string()
                ->description('Updated phone number.'),
            'company' => $schema->string()
                ->description('Updated company name.'),
            'type' => $schema->string()
                ->description('Updated contact type.'),
        ];
    }

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'contact_id' => ['required', 'integer'],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:100'],
        ]);

        $contact = Contact::findOrFail($validated['contact_id']);
        $contact->update(collect($validated)->except('contact_id')->toArray());

        return Response::text($contact->fresh()->toJson(JSON_PRETTY_PRINT));
    }
}
