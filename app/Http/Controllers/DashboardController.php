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
            $originalName = $file->getClientOriginalName();
            $baseName = pathinfo($originalName, PATHINFO_FILENAME);

            if (in_array($ext, ['geojson', 'json'])) {
                $file->move($destinationPath, $originalName);
                $uploaded[] = $originalName;
            } elseif ($ext === 'kml') {
                $kmlContent = file_get_contents($file->getRealPath());
                $geojson = $this->parseKmlToGeoJson($kmlContent, $baseName);
                if ($geojson) {
                    $targetName = $baseName . '.geojson';
                    File::put($destinationPath . DIRECTORY_SEPARATOR . $targetName, json_encode($geojson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                    $uploaded[] = $targetName . ' (dari .kml)';
                } else {
                    $failed[] = $originalName . ' (Gagal mengkonversi struktur KML ke GeoJSON)';
                }
            } elseif ($ext === 'kmz') {
                $kmlContent = $this->extractKmlFromKmz($file->getRealPath());
                if ($kmlContent) {
                    $geojson = $this->parseKmlToGeoJson($kmlContent, $baseName);
                    if ($geojson) {
                        $targetName = $baseName . '.geojson';
                        File::put($destinationPath . DIRECTORY_SEPARATOR . $targetName, json_encode($geojson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                        $uploaded[] = $targetName . ' (dari .kmz)';
                    } else {
                        $failed[] = $originalName . ' (Gagal mem-parsing KML di dalam KMZ)';
                    }
                } else {
                    $failed[] = $originalName . ' (Tidak ditemukan file .kml di dalam arsip KMZ)';
                }
            } elseif ($ext === 'zip') {
                // Periksa apakah ini arsip KMZ atau Shapefile yang sudah dikonversi
                $kmlContent = $this->extractKmlFromKmz($file->getRealPath());
                if ($kmlContent) {
                    $geojson = $this->parseKmlToGeoJson($kmlContent, $baseName);
                    if ($geojson) {
                        $targetName = $baseName . '.geojson';
                        File::put($destinationPath . DIRECTORY_SEPARATOR . $targetName, json_encode($geojson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                        $uploaded[] = $targetName . ' (dari .zip KML)';
                    } else {
                        $failed[] = $originalName . ' (Gagal mem-parsing KML di dalam ZIP)';
                    }
                } else {
                    $failed[] = $originalName . ' (Arsip Shapefile .zip diproses otomatis via konverter browser)';
                }
            } else {
                $failed[] = $originalName . ' (Format tidak didukung. Gunakan .geojson, .json, .kml, .kmz, atau Shapefile .zip)';
            }
        }

        if (count($uploaded) > 0) {
            $msg = count($uploaded) . ' file spasial berhasil diunggah dan disimpan ke GeoJSON (' . implode(', ', $uploaded) . ').';
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
                'message' => 'Gagal mengunggah file. ' . (count($failed) > 0 ? implode(', ', $failed) : 'Pastikan file berformat .geojson, .json, .kml, .kmz, atau Shapefile .zip.')
            ], 422);
        }

        return redirect()->route('peta')->with('error', 'Gagal mengunggah file. Pastikan file berformat .geojson, .json, .kml, .kmz, atau Shapefile .zip.');
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

    /**
     * Unduh satu file GeoJSON spesifik
     */
    public function downloadFile($fileName)
    {
        $cleanName = basename(urldecode($fileName));
        $filePath = public_path('geojson' . DIRECTORY_SEPARATOR . $cleanName);

        if (!File::exists($filePath)) {
            return redirect()->route('peta')->with('error', "File '{$cleanName}' tidak ditemukan di server.");
        }

        ActivityLog::record('DOWNLOAD_GEOJSON', "Mengunduh file GeoJSON: '{$cleanName}'");

        return response()->download($filePath, $cleanName, [
            'Content-Type' => 'application/geo+json',
        ]);
    }

    /**
     * Unduh koleksi file GeoJSON per kategori dalam format arsip .ZIP
     */
    public function downloadCategory($category = 'all')
    {
        $category = strtolower(trim($category ?? 'all'));
        $folderPath = public_path('geojson');

        if (!File::exists($folderPath)) {
            return redirect()->route('peta')->with('error', 'Folder data GeoJSON tidak ditemukan.');
        }

        $allFiles = File::files($folderPath);
        $filesToZip = [];

        foreach ($allFiles as $file) {
            $ext = strtolower($file->getExtension());
            if (!in_array($ext, ['json', 'geojson'])) continue;

            $filename = $file->getFilename();
            $prefix = strtoupper(explode('_', $filename)[0] ?? '');

            if ($category === 'all') {
                $filesToZip[] = $file;
            } elseif ($category === 'baku' && $prefix === 'BAKU') {
                $filesToZip[] = $file;
            } elseif ($category === 'fungsional' && $prefix === 'FUNGSIONAL') {
                $filesToZip[] = $file;
            } elseif ($category === 'potensial' && $prefix === 'POTENSIAL') {
                $filesToZip[] = $file;
            } elseif ($category === 'saluran' && $prefix === 'SALURAN') {
                $filesToZip[] = $file;
            } elseif ($category === 'bangunan' && $prefix === 'BANGUNAN') {
                $filesToZip[] = $file;
            } elseif ($category === 'custom' && !in_array($prefix, ['BAKU', 'FUNGSIONAL', 'POTENSIAL', 'SALURAN', 'BANGUNAN'])) {
                $filesToZip[] = $file;
            }
        }

        if (empty($filesToZip)) {
            return redirect()->route('peta')->with('error', "Tidak ada file GeoJSON yang ditemukan untuk kategori '{$category}'.");
        }

        $zipFileName = 'Data_GIS_' . strtoupper($category) . '_BWS_KALIMANTAN_I_' . date('Ymd_His') . '.zip';
        $tempDir = storage_path('app/temp');
        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }
        $zipPath = $tempDir . DIRECTORY_SEPARATOR . $zipFileName;

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return redirect()->route('peta')->with('error', 'Gagal membuat file arsip ZIP di server.');
        }

        foreach ($filesToZip as $file) {
            $zip->addFile($file->getRealPath(), $file->getFilename());
        }
        $zip->close();

        ActivityLog::record('EXPORT_GEOJSON', "Mengekspor " . count($filesToZip) . " file GIS kategori '{$category}' ke dalam arsip ZIP");

        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    /**
     * Konversi string XML KML menjadi GeoJSON FeatureCollection
     */
    protected function parseKmlToGeoJson(string $kmlContent, string $fallbackName = 'kml_layer'): ?array
    {
        $cleanXml = preg_replace('/xmlns[^=]*="[^"]*"/i', '', $kmlContent);
        $cleanXml = preg_replace('/[a-zA-Z0-9]+:([a-zA-Z0-9]+[ =>])/', '$1', $cleanXml);

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($cleanXml);
        if (!$xml) return null;

        $features = [];
        $placemarks = $xml->xpath('//Placemark');
        if (!$placemarks) return null;

        foreach ($placemarks as $idx => $pm) {
            $props = [];
            if (isset($pm->name)) $props['name'] = (string)$pm->name;
            if (isset($pm->description)) $props['description'] = strip_tags((string)$pm->description);

            if (isset($pm->ExtendedData)) {
                if (isset($pm->ExtendedData->Data)) {
                    foreach ($pm->ExtendedData->Data as $d) {
                        $attrName = (string)$d['name'];
                        $props[$attrName] = (string)$d->value;
                    }
                }
                if (isset($pm->ExtendedData->SchemaData->SimpleData)) {
                    foreach ($pm->ExtendedData->SchemaData->SimpleData as $sd) {
                        $attrName = (string)$sd['name'];
                        $props[$attrName] = (string)$sd;
                    }
                }
            }

            $geometry = $this->parseKmlGeometry($pm);
            if ($geometry) {
                $features[] = [
                    'type'       => 'Feature',
                    'id'         => $idx + 1,
                    'properties' => $props,
                    'geometry'   => $geometry,
                ];
            }
        }

        if (empty($features)) return null;

        return [
            'type'     => 'FeatureCollection',
            'name'     => $fallbackName,
            'features' => $features,
        ];
    }

    protected function parseKmlGeometry($element): ?array
    {
        if (isset($element->Point) && isset($element->Point->coordinates)) {
            $coords = $this->parseCoordsString((string)$element->Point->coordinates);
            if (!empty($coords)) {
                return ['type' => 'Point', 'coordinates' => $coords[0]];
            }
        }
        if (isset($element->LineString) && isset($element->LineString->coordinates)) {
            $coords = $this->parseCoordsString((string)$element->LineString->coordinates);
            if (!empty($coords)) {
                return ['type' => 'LineString', 'coordinates' => $coords];
            }
        }
        if (isset($element->Polygon)) {
            $rings = [];
            if (isset($element->Polygon->outerBoundaryIs->LinearRing->coordinates)) {
                $outer = $this->parseCoordsString((string)$element->Polygon->outerBoundaryIs->LinearRing->coordinates);
                if (!empty($outer)) $rings[] = $outer;
            }
            if (isset($element->Polygon->innerBoundaryIs)) {
                foreach ($element->Polygon->innerBoundaryIs as $inner) {
                    $inRing = $this->parseCoordsString((string)$inner->LinearRing->coordinates);
                    if (!empty($inRing)) $rings[] = $inRing;
                }
            }
            if (!empty($rings)) {
                return ['type' => 'Polygon', 'coordinates' => $rings];
            }
        }
        return null;
    }

    protected function parseCoordsString(string $raw): array
    {
        $raw = trim($raw);
        $pairs = preg_split('/[\s\r\n]+/', $raw);
        $coords = [];
        foreach ($pairs as $p) {
            $parts = explode(',', trim($p));
            if (count($parts) >= 2 && is_numeric($parts[0]) && is_numeric($parts[1])) {
                $lng = (float)$parts[0];
                $lat = (float)$parts[1];
                $coords[] = [$lng, $lat];
            }
        }
        return $coords;
    }

    protected function extractKmlFromKmz(string $kmzPath): ?string
    {
        $zip = new \ZipArchive();
        if ($zip->open($kmzPath) === true) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex($i);
                if (str_ends_with(strtolower($stat['name']), '.kml')) {
                    $kmlContent = $zip->getFromIndex($i);
                    $zip->close();
                    return $kmlContent;
                }
            }
            $zip->close();
        }
        return null;
    }
}

