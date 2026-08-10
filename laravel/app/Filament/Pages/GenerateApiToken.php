<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;

class GenerateApiToken extends Page
{
    protected string $view = 'filament.pages.generate-api-token';

    public ?string $generatedToken = null;

    public function generateToken(): void
    {
        $user = Auth::user();

        $user->tokens()->delete();

        $token = $user->createToken('n8n-token')->plainTextToken;

        $this->generatedToken = $token;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate')
                ->label('Generate API Token')
                ->action('generateToken'),
        ];
    }
}
