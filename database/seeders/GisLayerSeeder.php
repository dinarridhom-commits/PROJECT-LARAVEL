<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\GisLayer;

class GisLayerSeeder extends Seeder
{
    public function run(): void
    {
        $path = public_path('geojson');
        if (!File::exists($path)) return;

        $files = File::files($path);
        foreach ($files as $f) {
            $ext = strtolower($f->getExtension());
            if (in_array($ext, ['json', 'geojson'])) {
                $fn = $f->getFilename();
                $upper = strtoupper($fn);
                $cat = 'custom';
                if (str_starts_with($upper, 'BAKU')) $cat = 'baku';
                elseif (str_starts_with($upper, 'FUNGSIONAL')) $cat = 'fungsional';
                elseif (str_starts_with($upper, 'POTENSIAL')) $cat = 'potensial';
                elseif (str_starts_with($upper, 'SALURAN')) $cat = 'saluran';
                elseif (str_starts_with($upper, 'BANGUNAN')) $cat = 'bangunan';

                $cleanName = str_replace(['.json', '.geojson', '_'], ['', '', ' '], $fn);
                $sizeKb = round($f->getSize() / 1024, 1) . ' KB';

                GisLayer::firstOrCreate(
                    ['filename' => $fn],
                    [
                        'nama_objek' => trim($cleanName),
                        'category'   => $cat,
                        'file_size'  => $sizeKb,
                        'status'     => 'approved',
                        'reviewed_at'=> now(),
                    ]
                );
            }
        }
    }
}

