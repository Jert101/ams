<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class QrCode extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'code',
        'last_used_at',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'last_used_at' => 'datetime',
    ];

    /**
     * Get the user that owns the QR code.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Generate a unique QR code.
     *
     * @return string
     */
    public static function generateUniqueCode()
    {
        do {
            // Create a random 8-character code
            $code = strtoupper(Str::random(8));
        } while (static::where('code', $code)->exists());

        return $code;
    }
    
    /**
     * Generate a unique QR code that includes the user ID.
     * This method can be used if you want to embed the user ID in the QR code.
     *
     * @param int $userId
     * @return string
     */
    public static function generateCodeWithUserId($userId)
    {
        do {
            // Format: UID{user_id}-{random_string}
            // This helps to identify the user even if the QR code record is lost
            $randomPart = strtoupper(Str::random(5));
            $code = "UID{$userId}-{$randomPart}";
        } while (static::where('code', $code)->exists());

        return $code;
    }
} 