<?php
namespace App\Filament\Resources\Leads\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Models\Project;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('contact.first_name')
                    ->label('Contact')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->searchable(),
                TextColumn::make('assignedUser.name')
                    ->label('Assigned to')
                    ->sortable(),
                TextColumn::make('value')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'new' => 'New',
                        'contacted' => 'Contacted',
                        'qualified' => 'Qualified',
                        'converted' => 'Converted',
                        'lost' => 'Lost',
                    ]),
            ])
->recordActions([
                Action::make('convertToProject')
                    ->label('Convert to Project')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->visible(fn ($record) => $record->status !== 'converted')
                    ->schema([
                        TextInput::make('name')
                            ->label('Project name')
                            ->required(),
                    ])
                    ->action(function (array $data, $record) {
                        Project::create([
                            'lead_id' => $record->id,
                            'contact_id' => $record->contact_id,
                            'name' => $data['name'],
                            'status' => 'planning',
                            'owner_id' => $record->assigned_to,
                        ]);

                        $record->update(['status' => 'converted']);

                        Notification::make()
                            ->title('Lead converted to project')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}