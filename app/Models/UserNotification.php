<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'from_user_id',
        'target_role',
        'title',
        'message',
        'type',
        'link',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    /**
     * Helper cepat untuk mencatat notifikasi
     */
    public static function notify($title, $message, $targetRole = null, $userId = null, $type = 'info', $link = null, $fromUserId = null)
    {
        return self::create([
            'user_id'      => $userId,
            'from_user_id' => $fromUserId ?? auth()->id(),
            'target_role'  => $targetRole,
            'title'        => $title,
            'message'      => $message,
            'type'         => $type,
            'link'         => $link,
            'is_read'      => false,
        ]);
    }
}
