<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Kategori;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

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
            ActivityLog::record('UPLOAD_GEOJSON', 'Mengunggah ' . count($uploaded) . ' file GIS: ' . implode(', ', $uploaded));
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

        // Cegah path traversal dan atasi URL-encoding
        $fileName = basename(urldecode($request->input('file_name')));
        $folderPath = public_path('geojson');
        $filePath = $folderPath . DIRECTORY_SEPARATOR . $fileName;

        if (File::exists($filePath)) {
            @chmod($folderPath, 0777);
            @chmod($filePath, 0777);
            $deleted = @unlink($filePath);
            if (!$deleted && File::exists($filePath)) {
                $deleted = File::delete($filePath);
            }

            if ($deleted || !File::exists($filePath)) {
                ActivityLog::record('DELETE_GEOJSON', "Menghapus file GIS '{$fileName}' dari server");
                return redirect()->route('peta')->with('success', "File GIS '{$fileName}' berhasil dihapus dari sistem dan peta!");
            }

            return redirect()->route('peta')->with('error', "Gagal menghapus file '{$fileName}'. Server tidak memiliki izin hapus (permission denied).");
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

        $folderPath = public_path('geojson');
        @chmod($folderPath, 0777);

        $deleted = [];
        $failed = [];
        $notFound = [];

        foreach ($request->input('file_names') as $rawName) {
            $fileName = basename(urldecode($rawName));
            $filePath = $folderPath . DIRECTORY_SEPARATOR . $fileName;

            if (File::exists($filePath)) {
                @chmod($filePath, 0777);
                $isDel = @unlink($filePath);
                if (!$isDel && File::exists($filePath)) {
                    $isDel = File::delete($filePath);
                }

                if ($isDel || !File::exists($filePath)) {
                    $deleted[] = $fileName;
                } else {
                    $failed[] = $fileName;
                }
            } else {
                $notFound[] = $fileName;
            }
        }

        if (count($deleted) > 0) {
            ActivityLog::record('DELETE_GEOJSON_BULK', 'Menghapus massal ' . count($deleted) . ' file GIS: ' . implode(', ', $deleted));
            $msg = count($deleted) . ' file GIS berhasil dihapus (' . implode(', ', $deleted) . ').';
            if (count($failed) > 0) {
                $msg .= ' Beberapa file gagal dihapus (masalah izin akses server): ' . implode(', ', $failed);
            }
            if (count($notFound) > 0) {
                $msg .= ' ' . count($notFound) . ' file tidak ditemukan: ' . implode(', ', $notFound);
            }
            return redirect()->route('peta')->with('success', $msg);
        }

        if (count($failed) > 0) {
            return redirect()->route('peta')->with('error', 'Gagal menghapus file dari server karena izin akses (permission denied): ' . implode(', ', $failed));
        }

        return redirect()->route('peta')->with('error', 'Tidak ada file yang berhasil dihapus. Pastikan file masih tersedia di server.');
    }
}

