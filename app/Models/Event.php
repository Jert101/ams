<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'name',
        'date',
        'time',
        'description',
        'is_active',
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the attendances for this event.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the users who attended this event.
     */
    public function attendees()
    {
        return $this->belongsToMany(User::class, 'attendances')
            ->withPivot('status', 'approved_by', 'approved_at', 'remarks')
            ->withTimestamps();
    }
}
