<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone', 'company', 'type', 'owner_id',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}