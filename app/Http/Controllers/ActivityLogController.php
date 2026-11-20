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
        $search       = $request->input('search');
        $actionFilter = $request->input('action');
        $quickFilter  = $request->input('filter');

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

        if ($quickFilter === 'today') {
            $query->whereDate('created_at', today());
        } elseif ($quickFilter === 'uploads') {
            $query->where('action', 'UPLOAD_GEOJSON');
        } elseif ($quickFilter === 'deletes') {
            $query->whereIn('action', ['DELETE_GEOJSON', 'DELETE_GEOJSON_BULK', 'DELETE_USER']);
        }

        $logs = $query->latest()->paginate(10)->withQueryString();

        $totalLogs    = ActivityLog::count();
        $todayLogs    = ActivityLog::whereDate('created_at', today())->count();
        $totalUploads = ActivityLog::where('action', 'UPLOAD_GEOJSON')->count();
        $totalDeletes = ActivityLog::whereIn('action', ['DELETE_GEOJSON', 'DELETE_GEOJSON_BULK', 'DELETE_USER'])->count();

        return view('superadmin.activity_logs', compact(
            'logs', 'search', 'actionFilter', 'quickFilter',
            'totalLogs', 'todayLogs', 'totalUploads', 'totalDeletes'
        ));
    }

    /**
     * Ekspor riwayat log aktivitas ke format CSV (Audit Trail BWS)
     */
    public function exportCsv(Request $request)
    {
        $logs = ActivityLog::latest()->get();
        $filename = 'Audit_Log_BWS_Kalimantan_I_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            // Tambahkan BOM UTF-8 agar Excel mengenali karakter dengan benar
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header kolom
            fputcsv($file, ['No.', 'Waktu', 'Pengguna', 'Role', 'Aksi', 'Keterangan', 'IP Address']);

            foreach ($logs as $i => $log) {
                fputcsv($file, [
                    $i + 1,
                    $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : '-',
                    $log->user_name ?? 'Sistem',
                    strtoupper($log->role ?? 'USER'),
                    $log->action,
                    $log->description ?? '-',
                    $log->ip_address ?? '127.0.0.1',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
