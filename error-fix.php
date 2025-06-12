<?php
// Quick script to check and fix syntax errors in the User model

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>User Model Syntax Checker</h1>";

// File path to the User model
$userModelPath = __DIR__ . '/app/Models/User.php';

if (!file_exists($userModelPath)) {
    die("<p style='color:red;'>Error: User model file not found at $userModelPath</p>");
}

echo "<p>Found User model at: $userModelPath</p>";

// Create backup of the User model before doing anything
$backupPath = $userModelPath . '.backup.' . time();
if (copy($userModelPath, $backupPath)) {
    echo "<p>Created backup of User model at $backupPath</p>";
}

// Read the User model file
$modelContent = file_get_contents($userModelPath);

// Test if the model has syntax errors using PHP's lint capability
$tempFile = __DIR__ . '/temp_syntax_check.php';
file_put_contents($tempFile, $modelContent);

$output = [];
$returnVar = 0;
exec('php -l ' . escapeshellarg($tempFile) . ' 2>&1', $output, $returnVar);
unlink($tempFile); // Clean up temporary file

if ($returnVar !== 0) {
    echo "<p style='color:red;'>Syntax error detected in User model:</p>";
    echo "<pre style='background-color: #f8f8f8; padding: 10px; border: 1px solid #ddd;'>";
    echo implode("\n", $output);
    echo "</pre>";
    
    // Attempt to fix common syntax errors
    echo "<h2>Attempting automatic fixes...</h2>";
    
    // Check for duplicate method declarations
    $methodPattern = '/\s*(public|protected|private)\s+function\s+([a-zA-Z0-9_]+)\s*\(/';
    preg_match_all($methodPattern, $modelContent, $matches, PREG_OFFSET_CAPTURE);
    
    $methods = [];
    $duplicates = [];
    
    if (!empty($matches[2])) {
        foreach ($matches[2] as $index => $method) {
            $methodName = $method[0];
            $position = $method[1];
            
            if (isset($methods[$methodName])) {
                $duplicates[$methodName][] = $position;
                $methods[$methodName][] = $position;
            } else {
                $methods[$methodName] = [$position];
            }
        }
    }
    
    if (!empty($duplicates)) {
        echo "<p>Found duplicate method declarations:</p>";
        echo "<ul>";
        foreach ($duplicates as $methodName => $positions) {
            echo "<li>Method <strong>$methodName</strong> is declared multiple times</li>";
        }
        echo "</ul>";
        
        // Create a clean version of the User model
        echo "<h2>Creating fixed version of User model</h2>";
        
        // Simplified approach: Create a clean version with only one declaration of each method
        $cleanedContent = '<?php

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
}
';

        // Write the fixed content to a new file
        $fixedModelPath = $userModelPath . '.fixed.' . time() . '.php';
        if (file_put_contents($fixedModelPath, $cleanedContent)) {
            echo "<p style='color:green;'>Created fixed version of User model at $fixedModelPath</p>";
            
            // Test if the fixed model has syntax errors
            $output = [];
            $returnVar = 0;
            exec('php -l ' . escapeshellarg($fixedModelPath) . ' 2>&1', $output, $returnVar);
            
            if ($returnVar === 0) {
                echo "<p style='color:green;'>✅ Fixed version passes syntax check!</p>";
                
                // Offer to replace the original model
                echo "<h2>Replace the original User model</h2>";
                echo "<p>The fixed version passes syntax check. You can replace the original model with this fixed version.</p>";
                echo "<form method='post' action=''>";
                echo "<input type='hidden' name='fixed_path' value='$fixedModelPath'>";
                echo "<input type='hidden' name='original_path' value='$userModelPath'>";
                echo "<button type='submit' name='replace_model' style='background-color: #4CAF50; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer;'>Replace User Model with Fixed Version</button>";
                echo "</form>";
            } else {
                echo "<p style='color:red;'>❌ Fixed version still has syntax errors:</p>";
                echo "<pre style='background-color: #f8f8f8; padding: 10px; border: 1px solid #ddd;'>";
                echo implode("\n", $output);
                echo "</pre>";
            }
        } else {
            echo "<p style='color:red;'>Failed to write fixed version of User model</p>";
        }
    } else {
        echo "<p>No duplicate method declarations found.</p>";
        
        // Check for other common syntax errors
        echo "<h2>Checking for other common syntax errors...</h2>";
        
        // Check for unbalanced braces
        $openBraces = substr_count($modelContent, '{');
        $closeBraces = substr_count($modelContent, '}');
        
        if ($openBraces !== $closeBraces) {
            echo "<p style='color:red;'>Unbalanced braces detected: $openBraces opening braces, $closeBraces closing braces</p>";
        } else {
            echo "<p style='color:green;'>Brace count is balanced: $openBraces opening and closing braces</p>";
        }
        
        // Check for missing semicolons after class properties
        if (preg_match('/protected\s+\$[a-zA-Z0-9_]+\s*=\s*\[[^\]]*\][^;]/m', $modelContent)) {
            echo "<p style='color:red;'>Possible missing semicolon after array property declaration</p>";
        }
        
        // Check for unterminated strings
        if (preg_match('/[\'"][^\'"]*$/m', $modelContent)) {
            echo "<p style='color:red;'>Possible unterminated string literal</p>";
        }
    }
} else {
    echo "<p style='color:green;'>✅ No syntax errors detected in User model!</p>";
}

// Process form submission to replace the model
if (isset($_POST['replace_model'])) {
    $fixedPath = $_POST['fixed_path'];
    $originalPath = $_POST['original_path'];
    
    if (file_exists($fixedPath) && file_exists($originalPath)) {
        if (copy($fixedPath, $originalPath)) {
            echo "<p style='color:green; font-weight: bold;'>✅ Successfully replaced User model with fixed version!</p>";
            echo "<p>You should now clear all Laravel caches:</p>";
            echo "<code>php artisan cache:clear</code><br>";
            echo "<code>php artisan view:clear</code><br>";
            echo "<code>php artisan config:clear</code><br>";
        } else {
            echo "<p style='color:red;'>Failed to replace User model. Check file permissions.</p>";
        }
    } else {
        echo "<p style='color:red;'>One or both of the files doesn't exist.</p>";
    }
}

?> 