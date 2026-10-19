<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Menampilkan daftar log aktivitas sistem (Hanya Super Admin)
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $actionFilter = $request->input('action');

        $query = ActivityLog::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        if ($actionFilter) {
            $query->where('action', $actionFilter);
        }

        $logs = $query->latest()->paginate(20)->withQueryString();

        $totalLogs    = ActivityLog::count();
        $todayLogs    = ActivityLog::whereDate('created_at', today())->count();
        $totalUploads = ActivityLog::where('action', 'UPLOAD_GEOJSON')->count();
        $totalDeletes = ActivityLog::whereIn('action', ['DELETE_GEOJSON', 'DELETE_GEOJSON_BULK', 'DELETE_USER'])->count();

        return view('superadmin.activity_logs', compact(
            'logs', 'search', 'actionFilter',
            'totalLogs', 'todayLogs', 'totalUploads', 'totalDeletes'
        ));
    }

    /**
     * Menghapus semua riwayat log (Hanya Super Admin)
     */
    public function clear()
    {
        ActivityLog::truncate();
        ActivityLog::record('CLEAR_LOGS', 'Membersihkan seluruh data riwayat log aktivitas sistem.');

        return redirect()->route('activity-logs.index')->with('success', 'Semua riwayat log aktivitas berhasil dibersihkan.');
    }
}
