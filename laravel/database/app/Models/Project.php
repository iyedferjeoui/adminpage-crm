<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'lead_id', 'contact_id', 'name', 'status', 'owner_id',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}