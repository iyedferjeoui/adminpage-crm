<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LeadResource;
use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
public function index(Request $request)
    {
        $query = Lead::query();

        if ($contactId = $request->query('contact_id')) {
            $query->where('contact_id', $contactId);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        return LeadResource::collection($query->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'title' => 'required|string',
            'status' => 'required|in:new,contacted,qualified,converted,lost',
            'assigned_to' => 'nullable|exists:users,id',
            'value' => 'nullable|numeric',
        ]);

        $lead = Lead::create($validated);

        return new LeadResource($lead);
    }

    public function show(Lead $lead)
    {
        return new LeadResource($lead);
    }

    public function update(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'contact_id' => 'sometimes|exists:contacts,id',
            'title' => 'sometimes|string',
            'status' => 'sometimes|in:new,contacted,qualified,converted,lost',
            'assigned_to' => 'nullable|exists:users,id',
            'value' => 'nullable|numeric',
        ]);

        $lead->update($validated);

        return new LeadResource($lead);
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();

        return response()->json(['message' => 'Lead deleted']);
    }
}
