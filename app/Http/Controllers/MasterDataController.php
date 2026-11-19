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

        $layers = $query->paginate(10)->withQueryString();

        // Palet warna per kategori (konsisten dengan nav)
        $categoryColors = [
            'DIR Baku'        => '#27ae60',
            'DIR Fungsional'  => '#2980b9',
            'DIR Potensial'   => '#e67e22',
            'Jaringan Saluran Irigasi' => '#0984e3',
            'Bangunan Air'    => '#d63031',
        ];

        return view('superadmin.master_data_infrastruktur', compact(
            'layers', 'stats', 'totalApproved',
            'categoryFilter', 'categoryColors'
        ));
    }
}
