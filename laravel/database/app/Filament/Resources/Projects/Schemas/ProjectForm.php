<?php
namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('lead_id')
                    ->relationship('lead', 'title')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Lead'),
                Select::make('contact_id')
                    ->relationship('contact', 'first_name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Contact'),
                TextInput::make('name')
                    ->required(),
                Select::make('status')
                    ->options([
                        'planning' => 'Planning',
                        'active' => 'Active',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required()
                    ->default('planning'),
                Select::make('owner_id')
                    ->relationship('owner', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Owner'),
            ]);
    }
}