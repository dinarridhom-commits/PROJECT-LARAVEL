<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Statistics
        $totalProduk = Produk::count();
        $totalKategori = Kategori::count();
        $totalStok = Produk::sum('stok') ?? 0;
        $totalNilaiInventaris = Produk::selectRaw('SUM(harga * stok) as total')->value('total') ?? 0;

        // Query produk
        $produkQuery = Produk::with('kategori');
        if ($search) {
            $produkQuery->where(function($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                  ->orWhere('kode_produk', 'like', "%{$search}%")
                  ->orWhereHas('kategori', function($k) use ($search) {
                      $k->where('nama_kategori', 'like', "%{$search}%");
                  });
            });
        }
        $produks = $produkQuery->latest()->get();

        // Query kategori dengan jumlah produk (withCount untuk produks_count)
        $kategoriQuery = Kategori::withCount('produks');
        if ($search) {
            $kategoriQuery->where('nama_kategori', 'like', "%{$search}%")
                          ->orWhere('deskripsi', 'like', "%{$search}%");
        }
        $kategori = $kategoriQuery->latest()->get();

        return view('dashboard.index', [
            'title' => 'Dashboard Utama',
            'totalProduk' => $totalProduk,
            'totalKategori' => $totalKategori,
            'totalStok' => $totalStok,
            'totalNilaiInventaris' => $totalNilaiInventaris,
            'produks' => $produks,
            'kategori' => $kategori,
            'search' => $search
        ]);
    }

   public function peta()
    {
        $geojsonFiles = [];
        $fileList = [];
        $path = public_path('geojson');
        if (file_exists($path)) {
            $files = File::files($path);
            foreach ($files as $file) {
                $ext = strtolower($file->getExtension());
                if (in_array($ext, ['json', 'geojson'])) {
                    $filename = $file->getFilename();
                    $geojsonFiles[] = $filename;
                    $prefix = strtoupper(explode('_', $filename)[0] ?? '');
                    $isDefault = in_array($prefix, ['BAKU', 'FUNGSIONAL', 'POTENSIAL', 'SALURAN', 'BANGUNAN']);
                    $fileList[] = [
                        'name'        => $filename,
                        'size'        => round($file->getSize() / 1024, 1) . ' KB',
                        'date'        => date('d M Y, H:i', $file->getMTime()),
                        'is_default'  => $isDefault,
                    ];
                }
            }
            // Urutkan: file upload kustom (bukan default) tampil di atas
            usort($fileList, function($a, $b) {
                if ($a['is_default'] === $b['is_default']) {
                    return strcmp($a['name'], $b['name']);
                }
                return $a['is_default'] ? 1 : -1;
            });
        }

        // Deteksi tipe layer yang tersedia dari file yang ada di folder
        $availableTypes = [
            'baku'       => false,
            'fungsional' => false,
            'potensial'  => false,
            'saluran'    => false,
            'bangunan'   => false,
            'custom'     => false,
        ];
        foreach ($fileList as $f) {
            $nameUpper = strtoupper($f['name']);
            if      (str_starts_with($nameUpper, 'BAKU'))       $availableTypes['baku']       = true;
            elseif  (str_starts_with($nameUpper, 'FUNGSIONAL')) $availableTypes['fungsional'] = true;
            elseif  (str_starts_with($nameUpper, 'POTENSIAL'))  $availableTypes['potensial']  = true;
            elseif  (str_starts_with($nameUpper, 'SALURAN'))    $availableTypes['saluran']    = true;
            elseif  (str_starts_with($nameUpper, 'BANGUNAN'))   $availableTypes['bangunan']   = true;
            else                                                 $availableTypes['custom']     = true;
        }

        $hasFiles = count($fileList) > 0;
        $lokasi   = []; // Tidak ada data hardcoded — semua data bersumber dari file GeoJSON yang diunggah

        return view('dashboard.peta', compact('lokasi', 'geojsonFiles', 'fileList', 'availableTypes', 'hasFiles'));
    }


    /**
     * Upload file GeoJSON dan simpan langsung ke folder public/geojson
     */
    public function uploadGeojson(Request $request)
    {
        $request->validate([
            'file_geojson'   => 'required',
            'file_geojson.*' => 'file|max:102400', // Maks 100MB per file
        ], [
            'file_geojson.required' => 'Silakan pilih file GeoJSON terlebih dahulu.',
        ]);

        $destinationPath = public_path('geojson');
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        $files = $request->file('file_geojson');
        if (!is_array($files)) {
            $files = [$files];
        }

        $uploaded = [];
        $failed = [];

        foreach ($files as $file) {
            $ext = strtolower($file->getClientOriginalExtension());
            if (!in_array($ext, ['geojson', 'json'])) {
                $failed[] = $file->getClientOriginalName() . ' (Bukan format .geojson/.json)';
                continue;
            }

            $originalName = $file->getClientOriginalName();
            $file->move($destinationPath, $originalName);
            $uploaded[] = $originalName;
        }

        if (count($uploaded) > 0) {
            $msg = count($uploaded) . ' file GeoJSON berhasil diunggah ke public/geojson (' . implode(', ', $uploaded) . ').';
            if (count($failed) > 0) {
                $msg .= ' Beberapa file dilewati: ' . implode(', ', $failed);
            }
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success'  => true,
                    'uploaded' => $uploaded,
                    'failed'   => $failed,
                    'message'  => $msg
                ]);
            }
            return redirect()->route('peta')->with('success', $msg);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah file. Pastikan file berformat .geojson atau .json.'
            ], 422);
        }

        return redirect()->route('peta')->with('error', 'Gagal mengunggah file. Pastikan file berformat .geojson atau .json.');
    }

    /**
     * Menghapus file GeoJSON dari folder public/geojson
     */
    public function deleteGeojson(Request $request)
    {
        $request->validate([
            'file_name' => 'required|string',
        ], [
            'file_name.required' => 'Nama file wajib ditentukan.',
        ]);

        // Cegah path traversal dengan basename
        $fileName = basename($request->input('file_name'));
        $filePath = public_path('geojson/' . $fileName);

        if (File::exists($filePath)) {
            File::delete($filePath);
            return redirect()->route('peta')->with('success', "File GIS '{$fileName}' berhasil dihapus dari sistem!");
        }

        return redirect()->route('peta')->with('error', "File '{$fileName}' tidak ditemukan di folder public/geojson.");
    }

    /**
     * Menghapus banyak file GeoJSON sekaligus dari folder public/geojson
     */
    public function deleteGeojsonBulk(Request $request)
    {
        $request->validate([
            'file_names'   => 'required|array|min:1',
            'file_names.*' => 'required|string',
        ], [
            'file_names.required' => 'Pilih minimal satu file untuk dihapus.',
            'file_names.min'      => 'Pilih minimal satu file untuk dihapus.',
        ]);

        $deleted = [];
        $notFound = [];

        foreach ($request->input('file_names') as $rawName) {
            // Cegah path traversal
            $fileName = basename($rawName);
            $filePath = public_path('geojson/' . $fileName);

            if (File::exists($filePath)) {
                File::delete($filePath);
                $deleted[] = $fileName;
            } else {
                $notFound[] = $fileName;
            }
        }

        if (count($deleted) > 0) {
            $msg = count($deleted) . ' file GIS berhasil dihapus (' . implode(', ', $deleted) . ').';
            if (count($notFound) > 0) {
                $msg .= ' ' . count($notFound) . ' file tidak ditemukan: ' . implode(', ', $notFound);
            }
            return redirect()->route('peta')->with('success', $msg);
        }

        return redirect()->route('peta')->with('error', 'Tidak ada file yang berhasil dihapus. Pastikan file masih tersedia di server.');
    }
}

