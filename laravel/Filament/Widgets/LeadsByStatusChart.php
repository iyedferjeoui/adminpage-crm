<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Widgets\ChartWidget;

class LeadsByStatusChart extends ChartWidget
{
    protected ?string $heading = 'Leads By Status';

    protected function getData(): array
    {
        $statuses = ['new', 'contacted', 'qualified', 'converted', 'lost'];

        $counts = collect($statuses)->map(
            fn ($status) => Lead::where('status', $status)->count()
        );

        return [
            'datasets' => [
                [
                    'label' => 'Leads',
                    'data' => $counts->toArray(),
                    'backgroundColor' => ['#94a3b8', '#60a5fa', '#fbbf24', '#34d399', '#f87171'],
                ],
            ],
            'labels' => array_map('ucfirst', $statuses),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}