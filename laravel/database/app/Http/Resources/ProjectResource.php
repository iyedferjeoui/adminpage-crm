<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'lead' => $this->lead?->title,
            'contact' => $this->contact?->first_name . ' ' . $this->contact?->last_name,
            'owner' => $this->owner?->name,
            'created_at' => $this->created_at,
        ];
    }
}