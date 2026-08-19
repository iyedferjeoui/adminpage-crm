<?php

namespace App\Mcp\Tools;

use App\Models\Contact;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;

#[Name('createContact')]
#[Description('Create a new contact in the CRM.')]
class CreateContactTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'first_name' => $schema->string()
                ->description('Contact first name.'),
            'last_name' => $schema->string()
                ->description('Contact last name.'),
            'email' => $schema->string()
                ->description('Contact email address.'),
            'phone' => $schema->string()
                ->description('Contact phone number.'),
            'company' => $schema->string()
                ->description('Contact company name.'),
            'type' => $schema->string()
                ->description('Contact type (e.g. prospect, customer).'),
        ];
    }

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:100'],
        ]);

        $contact = Contact::create($validated);

        return Response::text($contact->toJson(JSON_PRETTY_PRINT));
    }
}
