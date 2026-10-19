<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'role',
        'action',
        'description',
        'ip_address',
        'user_agent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper cepat untuk mencatat aktivitas sistem
     */
    public static function record(string $action, ?string $description = null, $user = null): self
    {
        $currentUser = $user ?: Auth::user();

        return self::create([
            'user_id'     => $currentUser ? $currentUser->id : null,
            'user_name'   => $currentUser ? $currentUser->name : 'Tamu / Publik',
            'role'        => $currentUser ? ($currentUser->role ?? 'user') : 'guest',
            'action'      => strtoupper($action),
            'description' => $description,
            'ip_address'  => request() ? request()->ip() : null,
            'user_agent'  => request() ? substr(request()->userAgent() ?? '', 0, 255) : null,
        ]);
    }

    /**
     * Badge CSS class untuk aksi
     */
    public function getBadgeClassAttribute(): string
    {
        return match (strtoupper($this->action)) {
            'LOGIN'                  => 'badge-primary',
            'LOGOUT'                 => 'badge-secondary',
            'UPLOAD_GEOJSON'         => 'badge-success',
            'DELETE_GEOJSON'         => 'badge-danger',
            'DELETE_GEOJSON_BULK'    => 'badge-danger',
            'DELETE_USER'            => 'badge-danger',
            'UPDATE_USER'            => 'badge-warning',
            default                  => 'badge-info',
        };
    }
}
