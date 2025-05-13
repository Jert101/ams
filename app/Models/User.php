<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the role that owns this user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the QR code associated with this user.
     */
    public function qrCode()
    {
        return $this->hasOne(QrCode::class);
    }

    /**
     * Get the attendances for this user.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the events that this user has attended.
     */
    public function events()
    {
        return $this->belongsToMany(Event::class, 'attendances')
            ->withPivot('status', 'approved_by', 'approved_at', 'remarks')
            ->withTimestamps();
    }

    /**
     * Get the notifications for this user.
     */
    public function userNotifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole($roleName)
    {
        // Make sure role relationship is loaded
        if (!$this->relationLoaded('role')) {
            $this->load('role');
        }
        
        // If no role relationship exists, the user has no role
        if (!$this->role) {
            return false;
        }
        
        // If we're checking for multiple roles (passed as comma-separated string from middleware)
        if (is_string($roleName) && strpos($roleName, ',') !== false) {
            $roles = explode(',', $roleName);
            foreach ($roles as $role) {
                if ($this->role->name === trim($role)) {
                    return true;
                }
            }
            return false;
        }
        
        // If we're passed a single role as string
        return $this->role->name === $roleName;
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin()
    {
        return $this->hasRole('Admin');
    }

    /**
     * Check if the user is an officer.
     */
    public function isOfficer()
    {
        return $this->hasRole('Officer');
    }

    /**
     * Check if the user is a secretary.
     */
    public function isSecretary()
    {
        return $this->hasRole('Secretary');
    }

    /**
     * Check if the user is a member.
     */
    public function isMember()
    {
        return $this->hasRole('Member');
    }
}
