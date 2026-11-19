<?php

namespace App\Http\Controllers;

use App\Models\GisLayer;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    /**
     * Halaman Rekapitulasi Data Infrastruktur Irigasi
     * Hanya menampilkan berkas yang sudah berstatus 'approved'
     * Hak akses: admin & superadmin
     */
    public function infrastruktur(Request $request)
    {
        $categoryFilter = $request->input('category', 'all');
        $search = $request->input('search');

        // Statistik per kategori (realtime dari DB)
        $stats = GisLayer::where('status', 'approved')
            ->selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        $totalApproved = GisLayer::where('status', 'approved')->count();

        // Query tabel utama
        $query = GisLayer::where('status', 'approved')
            ->with('user', 'reviewer')
            ->latest('reviewed_at');

        if ($categoryFilter && $categoryFilter !== 'all') {
            $query->where('category', $categoryFilter);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_objek', 'like', "%{$search}%")
                  ->orWhere('filename', 'like', "%{$search}%");
            });
        }

        $layers = $query->paginate(10)->withQueryString();

        // Palet warna & label per kategori
        $categoryLabels = [
            'baku'       => 'Daerah Irigasi Baku',
            'fungsional' => 'Daerah Irigasi Fungsional',
            'potensial'  => 'Daerah Irigasi Potensial',
            'saluran'    => 'Jaringan Saluran Irigasi',
            'bangunan'   => 'Bangunan Air / Pelengkap',
        ];

        $categoryColors = [
            'baku'       => '#27ae60',
            'fungsional' => '#2980b9',
            'potensial'  => '#e67e22',
            'saluran'    => '#0984e3',
            'bangunan'   => '#d63031',
        ];

        return view('superadmin.master_data_infrastruktur', compact(
            'layers', 'stats', 'totalApproved',
            'categoryFilter', 'search', 'categoryColors', 'categoryLabels'
        ));
    }
}
