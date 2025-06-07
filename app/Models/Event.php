<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'mass_order',
        'date',
        'time',
        'end_time',
        'attendance_start_time',
        'attendance_end_time',
        'description',
        'selfie_instruction',
        'location',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime',
        'end_time' => 'datetime',
        'attendance_start_time' => 'datetime',
        'attendance_end_time' => 'datetime',
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
        return $this->belongsToMany(User::class, 'attendances', 'event_id', 'user_id')
            ->withPivot('status', 'approved_by', 'approved_at', 'remarks')
            ->withTimestamps();
    }

    /**
     * Get the mass schedule associated with this event.
     */
    public function massSchedule()
    {
        return $this->hasOne(MassSchedule::class);
    }

    /**
     * Check if this event is a Sunday mass.
     */
    public function isSundayMass()
    {
        return $this->massSchedule && $this->massSchedule->type === 'sunday_mass';
    }

    /**
     * Check if attendance is allowed for this event at the current time.
     */
    public function isAttendanceAllowed()
    {
        return $this->massSchedule ? $this->massSchedule->isAttendanceAllowed() : true;
    }
}
