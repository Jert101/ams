<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (!$user->user_id) {
                // Generate a new user_id (starting from 110600 or get the max and increment)
                $maxUserId = static::max('user_id') ?? 110600;
                $user->user_id = $maxUserId + 1;
            }
        });
    }

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'address',
        'mobile_number',
        'password',
        'role_id',
        'profile_photo_path',
        'approval_status',
        'rejection_reason',
        'date_of_birth',
        'gender',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
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
            'date_of_birth' => 'date',
        ];
    }

    /**
     * Get the user's role.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role && strtolower($this->role->name) === 'admin';
    }

    /**
     * Check if the user is an officer.
     */
    public function isOfficer(): bool
    {
        return $this->role && strtolower($this->role->name) === 'officer';
    }

    /**
     * Check if the user is a secretary.
     */
    public function isSecretary(): bool
    {
        return $this->role && strtolower($this->role->name) === 'secretary';
    }

    /**
     * Check if the user is a member.
     */
    public function isMember(): bool
    {
        return $this->role && strtolower($this->role->name) === 'member';
    }
    
    /**
     * Check if the user has a specific role.
     *
     * @param string $roleName
     * @return bool
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role && strtolower($this->role->name) === strtolower($roleName);
    }

    /**
     * Get the attendance records for the user.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'user_id', 'user_id');
    }
    
    /**
     * Get the QR code for the user.
     */
    public function qrCode()
    {
        return $this->hasOne(QrCode::class, 'user_id', 'user_id');
    }
    
    /**
     * Check if the user account is pending approval.
     */
    public function isPending(): bool
    {
        return $this->approval_status === 'pending';
    }
    
    /**
     * Check if the user account is approved.
     */
    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }
    
    /**
     * Check if the user account is rejected.
     */
    public function isRejected(): bool
    {
        return $this->approval_status === 'rejected';
    }

    /**
     * Get the URL for the user's profile photo.
     *
     * @return string
     */
    public function getProfilePhotoUrlAttribute()
    {
        $filename = $this->profile_photo_path;
        if ($filename && $filename !== 'kofa.png') {
            return asset($filename) . '?v=' . time();
        }
        return asset('img/kofa.png');
    }
    
    /**
     * Get the default profile photo URL if no profile photo has been uploaded.
     *
     * @return string
     */
    protected function defaultProfilePhotoUrl()
    {
        // Just return kofa.png directly
        return asset('img/kofa.png') . '?v=' . time();
    }
}