<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use App\Models\Contact;
use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LeadsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Contacts', Contact::count()),
            Stat::make('Open Leads', Lead::whereNotIn('status', ['converted', 'lost'])->count()),
            Stat::make('Active Projects', Project::whereNotIn('status', ['completed', 'cancelled'])->count()),
        ];
    }
}