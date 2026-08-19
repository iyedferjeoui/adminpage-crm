<?php

namespace App\Mcp\Tools;

use App\Models\Contact;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Name('findContact')]
#[Description('Find contacts in the CRM. Use contact_id for an exact lookup, or search by name/email.')]
#[IsReadOnly]
class FindContactTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'contact_id' => $schema->integer()
                ->description('The exact contact ID.'),
            'search' => $schema->string()
                ->description('Search first name, last name, or email.'),
        ];
    }

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'contact_id' => ['nullable', 'integer'],
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $query = Contact::query();

        if (!empty($validated['contact_id'])) {
            $query->where('id', $validated['contact_id']);
        }

        if (!empty($validated['search'])) {
            $search = $validated['search'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $contacts = $query->latest()->limit(20)->get();

        return Response::text($contacts->toJson(JSON_PRETTY_PRINT));
    }
}
