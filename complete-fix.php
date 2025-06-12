<?php
// A direct fix for the User model that simply replaces it with a working version

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Complete User Model Fix</h1>";

// File path to the User model
$userModelPath = __DIR__ . '/app/Models/User.php';

if (!file_exists($userModelPath)) {
    die("<p style='color:red;'>Error: User model file not found at $userModelPath</p>");
}

// Create backup of the User model before doing anything
$backupPath = $userModelPath . '.backup.' . time();
if (copy($userModelPath, $backupPath)) {
    echo "<p style='color:green;'>✅ Created backup of User model at $backupPath</p>";
} else {
    echo "<p style='color:orange;'>Warning: Could not create backup of User model</p>";
}

// The complete fixed User model
$fixedContent = '<?php

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
                $maxUserId = static::max(\'user_id\') ?? 110600;
                $user->user_id = $maxUserId + 1;
            }
        });
    }

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = \'user_id\';

    /**
     * Indicates if the model\'s ID is auto-incrementing.
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
        \'user_id\',
        \'name\',
        \'email\',
        \'address\',
        \'mobile_number\',
        \'password\',
        \'role_id\',
        \'profile_photo_path\',
        \'approval_status\',
        \'rejection_reason\',
        \'date_of_birth\',
        \'gender\',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        \'password\',
        \'remember_token\',
    ];

    /**
     * The accessors to append to the model\'s array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        \'profile_photo_url\',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            \'email_verified_at\' => \'datetime\',
            \'password\' => \'hashed\',
            \'date_of_birth\' => \'date\',
        ];
    }

    /**
     * Get the user\'s role.
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
        return $this->role && strtolower($this->role->name) === \'admin\';
    }

    /**
     * Check if the user is an officer.
     */
    public function isOfficer(): bool
    {
        return $this->role && strtolower($this->role->name) === \'officer\';
    }

    /**
     * Check if the user is a secretary.
     */
    public function isSecretary(): bool
    {
        return $this->role && strtolower($this->role->name) === \'secretary\';
    }

    /**
     * Check if the user is a member.
     */
    public function isMember(): bool
    {
        return $this->role && strtolower($this->role->name) === \'member\';
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
        return $this->hasMany(Attendance::class, \'user_id\', \'user_id\');
    }
    
    /**
     * Get the QR code for the user.
     */
    public function qrCode()
    {
        return $this->hasOne(QrCode::class, \'user_id\', \'user_id\');
    }
    
    /**
     * Check if the user account is pending approval.
     */
    public function isPending(): bool
    {
        return $this->approval_status === \'pending\';
    }
    
    /**
     * Check if the user account is approved.
     */
    public function isApproved(): bool
    {
        return $this->approval_status === \'approved\';
    }
    
    /**
     * Check if the user account is rejected.
     */
    public function isRejected(): bool
    {
        return $this->approval_status === \'rejected\';
    }

    /**
     * Get the default profile photo URL if no profile photo has been uploaded.
     *
     * @return string
     */
    protected function defaultProfilePhotoUrl()
    {
        return $this->generateSvgAvatar();
    }

    /**
     * Get the URL for the user\'s profile photo.
     *
     * @return string
     */
    public function getProfilePhotoUrlAttribute()
    {
        // Log debug info
        \Log::debug(\'Profile photo path\', [
            \'user_id\' => $this->user_id,
            \'path\' => $this->profile_photo_path,
            \'type\' => gettype($this->profile_photo_path)
        ]);
        
        // For any problematic values, generate an SVG avatar
        if (empty($this->profile_photo_path) || $this->profile_photo_path === "0" || $this->profile_photo_path === 0) {
            return $this->generateSvgAvatar();
        }
        
        // Check if it\'s the default logo
        if ($this->profile_photo_path === \'kofa.png\') {
            return asset(\'img/kofa.png\') . \'?v=\' . time();
        }
        
        // Check if file exists in public directory
        if (file_exists(public_path($this->profile_photo_path))) {
            return asset($this->profile_photo_path) . \'?v=\' . time();
        }
        
        // Check storage paths
        if (file_exists(storage_path(\'app/public/\' . $this->profile_photo_path))) {
            return asset(\'storage/\' . $this->profile_photo_path) . \'?v=\' . time();
        }
        
        // Fallback to SVG avatar
        return $this->generateSvgAvatar();
    }
    
    /**
     * Generate an SVG avatar with user\'s initials.
     *
     * @return string
     */
    protected function generateSvgAvatar()
    {
        // Generate initials from name
        $name = $this->name ?? \'User\';
        $initials = \'\';
        $words = explode(\' \', $name);
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
        }
        $initials = substr($initials, 0, 2); // Limit to 2 characters
        
        // Generate a consistent color based on the user ID
        $bgColor = \'#\' . substr(md5($this->user_id ?? 1), 0, 6);
        
        // Create SVG
        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200">
  <rect width="200" height="200" fill="$bgColor"/>
  <text x="100" y="115" font-family="Arial, sans-serif" font-size="80" font-weight="bold" text-anchor="middle" fill="#ffffff">$initials</text>
</svg>
SVG;
        
        // Convert to data URI
        $encoded = base64_encode($svg);
        return \'data:image/svg+xml;base64,\' . $encoded;
    }
}';

// Check the fixed content for syntax errors
$tempFile = __DIR__ . '/temp_user_model.php';
file_put_contents($tempFile, $fixedContent);

$output = [];
$returnVar = 0;
exec('php -l ' . escapeshellarg($tempFile) . ' 2>&1', $output, $returnVar);
unlink($tempFile); // Clean up temporary file

if ($returnVar !== 0) {
    echo "<p style='color:red;'>Syntax error detected in fixed model:</p>";
    echo "<pre style='background-color: #f8f8f8; padding: 10px; border: 1px solid #ddd;'>";
    echo implode("\n", $output);
    echo "</pre>";
} else {
    echo "<p style='color:green;'>✅ Fixed model passes syntax check</p>";
    
    // Write the fixed content to the User model file
    if (file_put_contents($userModelPath, $fixedContent)) {
        echo "<p style='color:green; font-weight: bold;'>✅ Successfully replaced User model with fixed version!</p>";
        
        // Clear Laravel caches
        echo "<p>Attempting to clear Laravel caches...</p>";
        
        $artisanCommands = [
            'php artisan cache:clear',
            'php artisan view:clear',
            'php artisan config:clear'
        ];
        
        foreach ($artisanCommands as $command) {
            $output = [];
            $returnVar = 0;
            exec($command . ' 2>&1', $output, $returnVar);
            
            if ($returnVar === 0) {
                echo "<p style='color:green;'>✅ Successfully executed: $command</p>";
            } else {
                echo "<p style='color:orange;'>⚠️ Command execution warning: $command</p>";
                echo "<pre style='background-color: #f8f8f8; padding: 10px; border: 1px solid #ddd;'>";
                echo implode("\n", $output);
                echo "</pre>";
            }
        }
        
        echo "<div style='margin-top: 20px; padding: 15px; background-color: #dff0d8; border: 1px solid #d6e9c6; color: #3c763d; border-radius: 4px;'>";
        echo "<h2 style='margin-top: 0;'>✅ Fix Complete!</h2>";
        echo "<p>The User model has been fixed and Laravel caches have been cleared.</p>";
        echo "<p>Try visiting the user profile page now:</p>";
        echo "<a href='/admin/users/1/edit?nocache=" . time() . "' target='_blank' style='display: inline-block; margin: 10px 0; padding: 10px 15px; background-color: #5cb85c; color: white; text-decoration: none; border-radius: 4px;'>View User Profile</a>";
        echo "<p>Important: Clear your browser cache or try in a private/incognito window.</p>";
        echo "</div>";
    } else {
        echo "<p style='color:red;'>Failed to write fixed model to file. Check file permissions.</p>";
    }
}
?> 