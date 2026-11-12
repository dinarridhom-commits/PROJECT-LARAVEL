<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Mengambil daftar notifikasi untuk user saat ini (JSON atau View)
     */
    public function getNotifications()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['unread' => 0, 'notifications' => []]);
        }

        $query = UserNotification::query();

        if ($user->role === 'superadmin') {
            $query->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('target_role', 'superadmin');
            });
        } else {
            $query->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('target_role', 'admin');
            });
        }

        $unreadCount = (clone $query)->where('is_read', false)->count();
        $notifications = $query->latest()->take(10)->get();

        return response()->json([
            'unread'        => $unreadCount,
            'notifications' => $notifications,
        ]);
    }

    /**
     * Tandai satu notifikasi sudah dibaca
     */
    public function markAsRead($id)
    {
        $notif = UserNotification::find($id);
        if ($notif) {
            $notif->update(['is_read' => true]);
        }
        return response()->json(['success' => true]);
    }

    /**
     * Tandai semua notifikasi sudah dibaca
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        if ($user) {
            $query = UserNotification::where('is_read', false);
            if ($user->role === 'superadmin') {
                $query->where(function($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhere('target_role', 'superadmin');
                });
            } else {
                $query->where('user_id', $user->id);
            }
            $query->update(['is_read' => true]);
        }
        return response()->json(['success' => true]);
    }
}
