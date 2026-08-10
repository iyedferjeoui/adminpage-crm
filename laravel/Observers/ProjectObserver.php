<?php

namespace App\Observers;

use App\Models\Project;
use Illuminate\Support\Facades\Http;

class ProjectObserver
{
    public function updated(Project $project): void
    {
        if ($project->isDirty('status') && $project->status === 'completed') {
            Http::post('https://automation-interns.tuintek.com/webhook/bde404bf-2159-4945-8a45-0517da86d417', [
                'id' => $project->id,
                'name' => $project->name,
                'status' => $project->status,
            ]);
        }
    }
}