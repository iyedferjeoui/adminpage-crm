<?php
namespace App\Filament\Resources\Leads\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('contact_id')
                    ->relationship('contact', 'first_name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Contact'),
                TextInput::make('title')
                    ->required(),
                Select::make('status')
                    ->options([
                        'new' => 'New',
                        'contacted' => 'Contacted',
                        'qualified' => 'Qualified',
                        'converted' => 'Converted',
                        'lost' => 'Lost',
                    ])
                    ->required()
                    ->default('new'),
                Select::make('assigned_to')
                    ->relationship('assignedUser', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Assigned to'),
                TextInput::make('value')
                    ->numeric()
                    ->prefix('$'),
            ]);
    }
}