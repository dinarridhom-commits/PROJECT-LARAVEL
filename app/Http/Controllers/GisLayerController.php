<?php

namespace App\Http\Controllers;

use App\Models\GisLayer;
use App\Models\ActivityLog;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class GisLayerController extends Controller
{
    /**
     * Halaman Verifikasi Berkas GIS untuk Super Admin
     */
    public function verificationIndex()
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403, 'Akses terbatas untuk Super Admin.');
        }

        $pendingLayers = GisLayer::where('status', 'pending')->with('user')->latest()->get();
        $historyLayers = GisLayer::whereIn('status', ['approved', 'rejected'])->with(['user', 'reviewer'])->latest()->take(15)->get();

        return view('admin.gis_verification', compact('pendingLayers', 'historyLayers'));
    }

    /**
     * Super Admin menyetujui file GIS
     */
    public function approve($id)
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403, 'Akses terbatas untuk Super Admin.');
        }

        $layer = GisLayer::findOrFail($id);

        $pendingPath = storage_path('app/gis_pending/' . $layer->filename);
        $publicPath  = public_path('geojson/' . $layer->filename);

        // Jika file ada di pending, pindahkan ke public/geojson
        if (File::exists($pendingPath)) {
            File::move($pendingPath, $publicPath);
        }

        $layer->update([
            'status'       => 'approved',
            'reviewed_by'  => Auth::id(),
            'reviewed_at'  => now(),
            'review_notes' => 'Disetujui oleh Super Admin.',
        ]);

        ActivityLog::record(
            'APPROVE_GIS',
            "Super Admin menyetujui berkas spasial '{$layer->nama_objek}' ({$layer->filename})"
        );

        // Kirim notifikasi ke Admin pengunggah
        if ($layer->user_id) {
            UserNotification::notify(
                'Berkas GIS Disetujui',
                "Berkas GIS '{$layer->nama_objek}' telah diverifikasi & disetujui Super Admin dan kini aktif di peta.",
                null,
                $layer->user_id,
                'success',
                route('peta')
            );
        }

        return back()->with('success', "Berkas GIS '{$layer->nama_objek}' berhasil disetujui dan kini aktif di peta.");
    }

    /**
     * Super Admin menolak file GIS
     */
    public function reject(Request $request, $id)
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403, 'Akses terbatas untuk Super Admin.');
        }

        $request->validate([
            'review_notes' => 'required|string|max:1000',
        ], [
            'review_notes.required' => 'Catatan alasan penolakan wajib diisi agar pengunggah dapat memperbaikinya.',
        ]);

        $layer = GisLayer::findOrFail($id);

        $layer->update([
            'status'       => 'rejected',
            'reviewed_by'  => Auth::id(),
            'reviewed_at'  => now(),
            'review_notes' => $request->review_notes,
        ]);

        ActivityLog::record(
            'REJECT_GIS',
            "Super Admin menolak berkas spasial '{$layer->nama_objek}' ({$layer->filename}). Alasan: {$request->review_notes}"
        );

        // Kirim notifikasi ke Admin pengunggah
        if ($layer->user_id) {
            UserNotification::notify(
                'Berkas GIS Ditolak',
                "Berkas GIS '{$layer->nama_objek}' ditolak oleh Super Admin. Catatan: {$request->review_notes}",
                null,
                $layer->user_id,
                'danger'
            );
        }

        return back()->with('success', "Berkas GIS '{$layer->nama_objek}' telah ditolak dan catatan telah dikirim ke pengunggah.");
    }

    /**
     * SOFT DELETE FILE GIS: Pindahkan file ke tong sampah
     */
    public function destroy($id)
    {
        $layer = GisLayer::findOrFail($id);

        $publicPath = public_path('geojson/' . $layer->filename);
        $trashPath  = storage_path('app/gis_trash/' . $layer->filename);

        // Pindahkan berkas fisik ke folder cadangan sampah (trash)
        if (File::exists($publicPath)) {
            if (File::exists($trashPath)) {
                File::delete($trashPath);
            }
            File::move($publicPath, $trashPath);
        }

        $layer->delete(); // Soft delete di database

        ActivityLog::record(
            'SOFT_DELETE_GIS',
            "Memindahkan berkas spasial '{$layer->nama_objek}' ({$layer->filename}) ke Tong Sampah (Trash)"
        );

        return back()->with('success', "Berkas GIS '{$layer->nama_objek}' berhasil dipindahkan ke Tong Sampah.");
    }

    /**
     * Halaman Tong Sampah (Trash) Berkas GIS untuk Super Admin
     */
    public function trash()
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403, 'Akses terbatas untuk Super Admin.');
        }

        $trashedLayers = GisLayer::onlyTrashed()->with('user')->latest('deleted_at')->get();

        return view('admin.gis_trash', compact('trashedLayers'));
    }

    /**
     * RESTORE FILE GIS: Pulihkan dari tong sampah ke peta
     */
    public function restore($id)
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403, 'Akses terbatas untuk Super Admin.');
        }

        $layer = GisLayer::onlyTrashed()->findOrFail($id);

        $trashPath  = storage_path('app/gis_trash/' . $layer->filename);
        $publicPath = public_path('geojson/' . $layer->filename);

        // Kembalikan berkas fisik ke public/geojson
        if (File::exists($trashPath)) {
            File::move($trashPath, $publicPath);
        }

        $layer->restore();

        ActivityLog::record(
            'RESTORE_GIS',
            "Memulihkan berkas spasial '{$layer->nama_objek}' ({$layer->filename}) dari Tong Sampah ke Peta Aktif"
        );

        return back()->with('success', "Berkas GIS '{$layer->nama_objek}' berhasil dipulihkan dan kembali aktif di peta.");
    }

    /**
     * FORCE DELETE FILE GIS: Hapus permanen total dari disk dan basis data
     */
    public function forceDelete($id)
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403, 'Akses terbatas untuk Super Admin.');
        }

        $layer = GisLayer::onlyTrashed()->findOrFail($id);

        $trashPath = storage_path('app/gis_trash/' . $layer->filename);
        if (File::exists($trashPath)) {
            File::delete($trashPath);
        }

        $nama = $layer->nama_objek;
        $file = $layer->filename;
        $layer->forceDelete();

        ActivityLog::record(
            'FORCE_DELETE_GIS',
            "Menghapus permanen berkas spasial '{$nama}' ({$file}) dari sistem secara total"
        );

        return back()->with('success', "Berkas GIS '{$nama}' telah dihapus secara permanen dari server.");
    }
}

