<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MassSchedule extends Model
{
    protected $fillable = [
        'event_id',
        'type',
        'mass_order',
        'start_time',
        'end_time',
        'attendance_start_time',
        'attendance_end_time',
        'is_active',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'attendance_start_time' => 'datetime',
        'attendance_end_time' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the event associated with the mass schedule.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Check if attendance is allowed at the current time.
     */
    public function isAttendanceAllowed()
    {
        $now = Carbon::now();
        $attendanceStart = Carbon::parse($this->attendance_start_time);
        $attendanceEnd = Carbon::parse($this->attendance_end_time);
        
        return $now->between($attendanceStart, $attendanceEnd);
    }

    /**
     * Get the formatted start time.
     */
    public function getFormattedStartTimeAttribute()
    {
        return Carbon::parse($this->start_time)->format('g:i A');
    }

    /**
     * Get the formatted end time.
     */
    public function getFormattedEndTimeAttribute()
    {
        return Carbon::parse($this->end_time)->format('g:i A');
    }

    /**
     * Get the formatted attendance start time.
     */
    public function getFormattedAttendanceStartTimeAttribute()
    {
        return Carbon::parse($this->attendance_start_time)->format('g:i A');
    }

    /**
     * Get the formatted attendance end time.
     */
    public function getFormattedAttendanceEndTimeAttribute()
    {
        return Carbon::parse($this->attendance_end_time)->format('g:i A');
    }
}
