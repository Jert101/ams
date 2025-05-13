<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'image_path',
        'is_active',
        'last_used_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    /**
     * Get the user that owns this QR code.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a unique QR code for a user.
     */
    public static function generateUniqueCode()
    {
        $code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 10));
        
        // Check if the code already exists
        if (self::where('code', $code)->exists()) {
            return self::generateUniqueCode();
        }
        
        return $code;
    }
}
