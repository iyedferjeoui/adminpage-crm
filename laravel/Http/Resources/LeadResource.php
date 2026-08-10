<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'status' => $this->status,
            'value' => $this->value,
            'contact' => $this->contact?->first_name . ' ' . $this->contact?->last_name,
            'assigned_to' => $this->assignedUser?->name,
            'created_at' => $this->created_at,
        ];
    }
}