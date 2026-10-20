/**
 * peta-map.js
 * JavaScript khusus untuk halaman Peta Daerah Irigasi (DIR)
 * BWS Kalimantan I - WebGIS
 *
 * Fungsi utama:
 * 1. Registrasi sistem koordinat ESRI/ArcMap (Proj4)
 * 2. Toggle Fullscreen
 * 3. Switcher View Mode (Peta / Tabel / Keduanya)
 * 4. Inisialisasi Leaflet Map + Layer Groups + Legenda
 * 5. Konversi & Reproyeksi Koordinat GeoJSON (WKID 54034, 3857, UTM 49, WGS84)
 * 6. Fetch & Load file GeoJSON dari server ke peta
 * 7. Render Data Table koordinat GIS
 * 8. Pencarian Realtime di tabel
 * 9. Export data tabel ke CSV
 * 10. Helper Modal Kelola File GIS (filter, checkbox, bulk delete)
 * 11. Chunked Batch Upload GeoJSON (anti PostTooLargeException)
 */

/* ============================================================
   0. REGISTRASI PROJ4 - SISTEM KOORDINAT ARCMAP / ESRI
   Fungsi: Mendaftarkan definisi proyeksi peta yang dipakai
   ArcMap/ESRI agar koordinat bisa dikonversi ke WGS84 (Leaflet).
   ============================================================ */
if (typeof proj4 !== 'undefined') {
    proj4.defs("EPSG:54034", "+proj=eqc +lat_ts=0 +lat_0=0 +lon_0=0 +x_0=0 +y_0=0 +datum=WGS84 +units=m +no_defs");
    proj4.defs("ESRI:54034", "+proj=eqc +lat_ts=0 +lat_0=0 +lon_0=0 +x_0=0 +y_0=0 +datum=WGS84 +units=m +no_defs");
    proj4.defs("EPSG:3857",  "+proj=merc +a=6378137 +b=6378137 +lat_ts=0 +lon_0=0 +x_0=0 +y_0=0 +k=1 +units=m +nadgrids=@null +wktext +no_defs");
    proj4.defs("EPSG:32649", "+proj=utm +zone=49 +datum=WGS84 +units=m +no_defs");
    proj4.defs("EPSG:32749", "+proj=utm +zone=49 +south +datum=WGS84 +units=m +no_defs");
}

/* ============================================================
   1. FULLSCREEN TOGGLE
   Fungsi: Membuat halaman masuk/keluar mode layar penuh.
   Bisa dipanggil dari tombol manapun dengan id="btnFullscreenToggle".
   ============================================================ */
var btnFs = document.getElementById('btnFullscreenToggle');
if (btnFs) {
    btnFs.addEventListener('click', function() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(function(err){});
            btnFs.innerHTML = '<i class="fas fa-compress"></i>';
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
                btnFs.innerHTML = '<i class="fas fa-expand"></i>';
            }
        }
    });
}

// Fullscreen keyboard shortcut (F11 override)
document.addEventListener('keydown', function(e) {
    if (e.key === 'F11' && document.getElementById('map')) {
        e.preventDefault();
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(function(err){});
        } else {
            if (document.exitFullscreen) document.exitFullscreen();
        }
    }
});

/* ============================================================
   2. VIEW SWITCHER
   Fungsi: Dropdown "View" di sidebar untuk memilih tampilan:
   - "both"  → Tampilkan peta DAN tabel sekaligus
   - "maps"  → Tampilkan peta saja (peta diperbesar penuh)
   - "table" → Tampilkan tabel saja (peta disembunyikan)
   ============================================================ */
window.switchViewMode = function(mode) {
    var mapSec = document.getElementById('section-map');
    var tblSec = document.getElementById('section-table');
    var mapEl  = document.getElementById('map');

    if (mode === 'maps') {
        mapSec.style.display = 'block';
        tblSec.style.display = 'none';
        mapEl.style.height = 'calc(100vh - 190px)';
    } else if (mode === 'table') {
        mapSec.style.display = 'none';
        tblSec.style.display = 'block';
    } else {
        mapSec.style.display = 'block';
        tblSec.style.display = 'block';
        mapEl.style.height = '480px';
    }

    // Paksa Leaflet untuk menghitung ulang ukuran peta
    setTimeout(function() {
        if (window.map) window.map.invalidateSize();
    }, 200);
};

/* ============================================================
   3. INISIALISASI LEAFLET MAP
   Fungsi: Membuat peta interaktif Leaflet dengan 2 pilihan
   basemap (OpenStreetMap & Google Satellite), layer groups
   per kategori GIS, dan kontrol layer toggle di kanan atas.
   ============================================================ */
var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors', maxZoom: 19
});
var googleSat = L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
    attribution: '&copy; Google Satellite', maxZoom: 20
});

var defaultCenter = [-0.0263, 109.3425]; // Kalimantan Barat / Pontianak
var map = L.map('map', { center: defaultCenter, zoom: 8, layers: [osm], zoomControl: true });
window.map = map;
var mapBounds = L.latLngBounds();

// 54 File Baseline Default Sistem BWS
var DEFAULT_BASELINE_FILES = [
    'Baku_DIR_PEMANGKAT_KOMPLEK____OVERLAP.json',
    'Baku_DIR_SEBANGKAU.json',
    'Baku_Kubu_Komplek___Overlap_Hutan_Lindung.json',
    'Baku_Pematang_Gadung_1.json',
    'Baku_Pematang_Gadung_II.json',
    'Baku_SEBUBUS.json',
    'Baku_Sarang_Burung.json',
    'Baku_Semelagi.json',
    'Baku_Sungai_Kinjil____OVERLAP.json',
    'Bangunan_KK.json',
    'Bangunan_KRW.json',
    'Bangunan_PGI.json',
    'Bangunan_PGII.json',
    'Bangunan_PMK.json',
    'Bangunan_PPK.json',
    'Bangunan_SBB.json',
    'Bangunan_SBK.json',
    'Bangunan_SBU.json',
    'Bangunan_SBW.json',
    'Bangunan_SKJ.json',
    'Bangunan_SLK.json',
    'Bangunan_SML.json',
    'Bangunan_TB.json',
    'Fungsional_DIR_PEMANGKAT_KOMPLEK.json',
    'Fungsional_DIR_PIMPINAN_KOMPLEK.json',
    'Fungsional_DIR_SEBANGKAU.json',
    'Fungsional_Kubu_Komplek.json',
    'Fungsional_SEBUBUS.json',
    'Fungsional_Sarang_Burung.json',
    'Potensial_DIR_PEMANGKAT_KOMPLEK____OVERLAP.json',
    'Potensial_DIR_PIMPINAN_KOMPLEK.json',
    'Potensial_DIR_SEBANGKAU.json',
    'Potensial_Kerawang_Komplek.json',
    'Potensial_Kubu_Komplek____Overlap_Hutan_Lindung.json',
    'Potensial_Pematang_Gadung_II.json',
    'Potensial_SEBUBUS.json',
    'Potensial_Sarang_Burung.json',
    'Potensial_Sungai_Kinjil____OVERLAP.json',
    'Potensial_Teluk_Bayur.json',
    'Saluran_KK.json',
    'Saluran_KRW.json',
    'Saluran_PGI.json',
    'Saluran_PGII.json',
    'Saluran_PMK.json',
    'Saluran_PPK.json',
    'Saluran_SBB.json',
    'Saluran_SBK.json',
    'Saluran_SBU.json',
    'Saluran_SBW.json',
    'Saluran_SKJ.json',
    'Saluran_SLK.json',
    'Saluran_SML.json',
    'Saluran_Samalagi_full.json',
    'Saluran_TB.json'
];

// Palet warna estetik untuk file GIS yang diunggah
var CUSTOM_LAYER_COLORS = [
    { stroke: '#7b1fa2', fill: '#9c27b0' }, // Ungu
    { stroke: '#00796b', fill: '#009688' }, // Toska
    { stroke: '#c2185b', fill: '#e91e63' }, // Pink
    { stroke: '#e65100', fill: '#f57c00' }, // Oranye
    { stroke: '#303f9f', fill: '#3f51b5' }, // Indigo
    { stroke: '#0097a7', fill: '#00bcd4' }, // Cyan
    { stroke: '#512da8', fill: '#673ab7' }, // Deep Purple
    { stroke: '#d32f2f', fill: '#f44336' }, // Merah
    { stroke: '#388e3c', fill: '#4caf50' }, // Hijau Daun
    { stroke: '#afb42b', fill: '#cddc39' }, // Lime
    { stroke: '#455a64', fill: '#607d8b' }  // Slate
];

// Layer Groups per Kategori GIS Sistem
var layerBaku       = L.layerGroup(); if (window.availableTypes && window.availableTypes.baku)       layerBaku.addTo(map);
var layerFungsional = L.layerGroup(); if (window.availableTypes && window.availableTypes.fungsional) layerFungsional.addTo(map);
var layerPotensial  = L.layerGroup(); if (window.availableTypes && window.availableTypes.potensial)  layerPotensial.addTo(map);
var layerSaluran    = L.layerGroup(); if (window.availableTypes && window.availableTypes.saluran)    layerSaluran.addTo(map);
var layerBangunan   = L.layerGroup(); if (window.availableTypes && window.availableTypes.bangunan)   layerBangunan.addTo(map);

// Inisialisasi Layer Group khusus per file upload (otomatis muncul & hilang sesuai file di server)
var customFileMap = {};
var cIdx = 0;
var allGeojsonList = window.geojsonFiles || [];
allGeojsonList.forEach(function(fn) {
    if (DEFAULT_BASELINE_FILES.indexOf(fn) === -1) {
        var clr = CUSTOM_LAYER_COLORS[cIdx % CUSTOM_LAYER_COLORS.length];
        var grp = L.layerGroup().addTo(map);
        var clean = fn.replace(/\.(json|geojson)$/i, '').replace(/_/g, ' ');
        customFileMap[fn] = {
            layerGroup: grp,
            color: clr,
            cleanName: clean,
            geomType: 'polygon'
        };
        cIdx++;
    }
});

var baseMaps = { "Peta (OpenStreetMap)": osm, "Satelit (Google Hybrid)": googleSat };
var overlayMaps = {};
if (window.availableTypes) {
    if (window.availableTypes.baku)       overlayMaps['<span class="legend-color-box-sm" style="background:#27ae60;border-color:#1b7a43;"></span> Daerah Irigasi Baku (Hijau)']       = layerBaku;
    if (window.availableTypes.fungsional) overlayMaps['<span class="legend-color-box-sm" style="background:#2980b9;border-color:#1a5276;"></span> Daerah Irigasi Fungsional (Biru)']  = layerFungsional;
    if (window.availableTypes.potensial)  overlayMaps['<span class="legend-color-box-sm" style="background:#e67e22;border-color:#b95c00;"></span> Daerah Irigasi Potensial (Oranye)'] = layerPotensial;
    if (window.availableTypes.saluran)    overlayMaps['<span class="legend-line-sm" style="background:#0984e3;"></span> Jaringan Saluran Irigasi']           = layerSaluran;
    if (window.availableTypes.bangunan)   overlayMaps['<span class="legend-circle-sm" style="background:#d63031;"></span> Bangunan Air / Pelengkap']           = layerBangunan;
}

// Tambahkan setiap file upload kustom secara dinamis ke Layer Peta
for (var cFn in customFileMap) {
    var cInfo = customFileMap[cFn];
    var cLabel = '<span class="legend-color-box-sm" id="layer-swatch-' + cFn.replace(/[^a-zA-Z0-9]/g, '_') + '" style="background:' + cInfo.color.fill + ';border-color:' + cInfo.color.stroke + ';"></span> ' + cInfo.cleanName;
    overlayMaps[cLabel] = cInfo.layerGroup;
}

var layerControl = L.control.layers(baseMaps, overlayMaps, { collapsed: false, position: 'topright' }).addTo(map);

// Inisialisasi Header & Fitur Minimize pada Layer Control (Kanan Atas)
var layerContainer = layerControl.getContainer();
if (layerContainer) {
    L.DomEvent.disableClickPropagation(layerContainer);
    L.DomEvent.disableScrollPropagation(layerContainer);

    var layerHeader = document.createElement('div');
    layerHeader.className = 'map-ctrl-header';
    layerHeader.id = 'layer-header';
    layerHeader.title = 'Klik untuk minimize / expand';
    layerHeader.innerHTML = 
        '<span><i class="fas fa-layer-group text-primary mr-1"></i> Layer Peta</span>' +
        '<button type="button" class="map-ctrl-min-btn" aria-label="Minimize Layer" title="Minimize / Expand"><i class="fas fa-minus" id="layer-min-icon"></i></button>';

    var formList = layerContainer.querySelector('.leaflet-control-layers-list');
    if (formList) {
        formList.classList.add('map-ctrl-body');
        layerContainer.insertBefore(layerHeader, formList);
    }

    layerHeader.addEventListener('click', function(e) {
        e.stopPropagation();
        layerContainer.classList.toggle('is-minimized');
        var icon = layerContainer.querySelector('#layer-min-icon');
        if (icon) {
            icon.className = layerContainer.classList.contains('is-minimized') ? 'fas fa-plus' : 'fas fa-minus';
        }
    });
}

// Fungsi render HTML untuk isi Legenda Peta (Kategori default + File Upload)
function getLegendBodyHtml() {
    var legendItems = '';
    if (window.availableTypes) {
        if (window.availableTypes.baku)       legendItems += '<div class="legend-item"><span class="legend-color-box" style="background:#27ae60;border-color:#1b7a43;"></span> <b>DIR Baku</b> (Hijau)</div>';
        if (window.availableTypes.fungsional) legendItems += '<div class="legend-item"><span class="legend-color-box" style="background:#2980b9;border-color:#1a5276;"></span> <b>DIR Fungsional</b> (Biru)</div>';
        if (window.availableTypes.potensial)  legendItems += '<div class="legend-item"><span class="legend-color-box" style="background:#e67e22;border-color:#b95c00;"></span> <b>DIR Potensial</b> (Oranye)</div>';
        if (window.availableTypes.saluran)    legendItems += '<div class="legend-line" style="background:#0984e3;"></div> <b>Jaringan Saluran</b>';
        if (window.availableTypes.bangunan)   legendItems += '<div class="legend-circle" style="background:#d63031;"></div> <b>Bangunan Air</b>';
    }

    var customKeys = Object.keys(customFileMap);
    if (customKeys.length > 0) {
        legendItems += '<div class="legend-divider my-1 border-top pt-1 text-muted font-weight-bold" style="font-size: 10px; letter-spacing: 0.5px;">DATA TERUPLOAD:</div>';
        customKeys.forEach(function(cFn) {
            var cInfo = customFileMap[cFn];
            var swatch = '';
            if (cInfo.geomType === 'point') {
                swatch = '<span class="legend-circle" style="background:' + cInfo.color.fill + ';border:1px solid ' + cInfo.color.stroke + ';"></span>';
            } else if (cInfo.geomType === 'line') {
                swatch = '<span class="legend-line" style="background:' + cInfo.color.fill + ';"></span>';
            } else {
                swatch = '<span class="legend-color-box" style="background:' + cInfo.color.fill + ';border-color:' + cInfo.color.stroke + ';"></span>';
            }
            var isHidden = !map.hasLayer(cInfo.layerGroup);
            var opacityStyle = isHidden ? 'opacity: 0.35;' : '';
            legendItems += '<div class="legend-item" id="legend-item-' + cFn.replace(/[^a-zA-Z0-9]/g, '_') + '" style="' + opacityStyle + '">' +
                swatch + ' <b>' + cInfo.cleanName + '</b></div>';
        });
    }

    return legendItems;
}

// Legenda Dinamis dengan Fitur Minimize (Pojok Kiri Bawah)
var legend = L.control({ position: 'bottomleft' });
legend.onAdd = function() {
    var div = L.DomUtil.create('div', 'map-legend');
    div.id = 'map-legend-box';
    var legendItems = getLegendBodyHtml();
    if (!legendItems) { div.style.display = 'none'; return div; }

    div.innerHTML = 
        '<div class="map-ctrl-header" id="legend-header" title="Klik untuk minimize / expand">' +
            '<span><i class="fas fa-palette text-primary mr-1"></i> Legenda Peta</span>' +
            '<button type="button" class="map-ctrl-min-btn" aria-label="Minimize Legenda" title="Minimize / Expand"><i class="fas fa-minus" id="legend-min-icon"></i></button>' +
        '</div>' +
        '<div class="map-ctrl-body" id="legend-body">' + legendItems + '</div>';

    L.DomEvent.disableClickPropagation(div);
    L.DomEvent.disableScrollPropagation(div);

    var header = div.querySelector('#legend-header');
    if (header) {
        header.addEventListener('click', function(e) {
            e.stopPropagation();
            div.classList.toggle('is-minimized');
            var icon = div.querySelector('#legend-min-icon');
            if (icon) {
                icon.className = div.classList.contains('is-minimized') ? 'fas fa-plus' : 'fas fa-minus';
            }
        });
    }

    return div;
};
legend.addTo(map);

function refreshLegendBody() {
    var body = document.getElementById('legend-body');
    if (body) {
        body.innerHTML = getLegendBodyHtml();
    }
}

// Sinkronisasi status layer tersembunyi dengan Legenda Peta
map.on('overlayremove', function(e) {
    for (var cFn in customFileMap) {
        if (customFileMap[cFn].layerGroup === e.layer) {
            var item = document.getElementById('legend-item-' + cFn.replace(/[^a-zA-Z0-9]/g, '_'));
            if (item) item.style.opacity = '0.35';
        }
    }
});
map.on('overlayadd', function(e) {
    for (var cFn in customFileMap) {
        if (customFileMap[cFn].layerGroup === e.layer) {
            var item = document.getElementById('legend-item-' + cFn.replace(/[^a-zA-Z0-9]/g, '_'));
            if (item) item.style.opacity = '1';
        }
    }
});

// Tombol Reset Peta (kembali ke bounds awal semua layer)
window.resetMapView = function() {
    if (mapBounds.isValid && mapBounds.isValid()) {
        map.fitBounds(mapBounds, { padding: [40, 40] });
    } else {
        map.setView(defaultCenter, 8);
    }
};

/* ============================================================
   4. REPROYEKSI KOORDINAT (WKID 54034, 3857, UTM 49, WGS84)
   Fungsi: Mengkonversi koordinat dari sistem proyeksi ArcMap
   ke WGS84 (derajat lat/lng) agar bisa ditampilkan di Leaflet.
   Mendukung: Equidistant Cylindrical, Web Mercator, UTM Zone 49.
   ============================================================ */
function projectCoordinate(x, y, wkid) {
    if (typeof x !== 'number' || typeof y !== 'number' || isNaN(x) || isNaN(y)) return [x, y];
    // Sudah dalam derajat WGS84
    if (Math.abs(x) <= 180 && Math.abs(y) <= 90) return [x, y];
    // Koordinat dalam meter (Mercator)
    if (Math.abs(x) >= 5000000 && Math.abs(x) <= 20000000 && Math.abs(y) <= 2000000) {
        var lon = (x / 6378137.0) * (180.0 / Math.PI);
        var lat = (2.0 * Math.atan(Math.exp(y / 6378137.0)) - (Math.PI / 2.0)) * (180.0 / Math.PI);
        return [lon, lat];
    }
    try {
        var wkidStr = String(wkid || '');
        if (wkid === 54034 || wkidStr.indexOf('54034') !== -1) {
            var lonEqc = (x / 6378137.0) * (180.0 / Math.PI);
            var latEqc = (2.0 * Math.atan(Math.exp(y / 6378137.0)) - (Math.PI / 2.0)) * (180.0 / Math.PI);
            return [lonEqc, latEqc];
        }
        if (wkid === 3857 || wkidStr.indexOf('3857') !== -1)  return proj4('EPSG:3857',  'EPSG:4326', [x, y]);
        if (wkid === 32649 || wkidStr.indexOf('32649') !== -1) return proj4('EPSG:32649', 'EPSG:4326', [x, y]);
        if (wkid === 32749 || wkidStr.indexOf('32749') !== -1) return proj4('EPSG:32749', 'EPSG:4326', [x, y]);
        if (wkid && proj4.defs['EPSG:' + wkid]) return proj4('EPSG:' + wkid, 'EPSG:4326', [x, y]);
    } catch(e) {}
    // Fallback UTM zona 49
    if (x > 100000 && x < 1000000) { try { return proj4('EPSG:32649', 'EPSG:4326', [x, y]); } catch(e){} }
    return [x, y];
}

function transformGeoJsonCoords(coords, geomType, wkid) {
    if (!coords) return coords;
    if (geomType === 'Point') return projectCoordinate(coords[0], coords[1], wkid);
    if (geomType === 'LineString' || geomType === 'MultiPoint')
        return coords.map(function(c) { return projectCoordinate(c[0], c[1], wkid); });
    if (geomType === 'Polygon' || geomType === 'MultiLineString')
        return coords.map(function(ring) { return ring.map(function(c) { return projectCoordinate(c[0], c[1], wkid); }); });
    if (geomType === 'MultiPolygon')
        return coords.map(function(poly) { return poly.map(function(ring) { return ring.map(function(c) { return projectCoordinate(c[0], c[1], wkid); }); }); });
    return coords;
}

// Konversi format ESRI FeatureService ke GeoJSON standar
function convertEsriToGeoJson(data) {
    if (!data) return null;
    var wkid = null;
    if (data.crs && data.crs.properties && data.crs.properties.name) wkid = String(data.crs.properties.name);
    else if (data.spatialReference) wkid = data.spatialReference.wkid || data.spatialReference.latestWkid;

    if (data.type === 'FeatureCollection' && Array.isArray(data.features)) {
        data.features.forEach(function(f) {
            if (f.geometry && f.geometry.coordinates)
                f.geometry.coordinates = transformGeoJsonCoords(f.geometry.coordinates, f.geometry.type, wkid);
        });
        return data;
    }
    if (!data.features || !Array.isArray(data.features)) return null;

    var geomType = data.geometryType;
    var gj = { type: 'FeatureCollection', features: [] };
    data.features.forEach(function(f, idx) {
        if (!f.geometry) return;
        var geom = null;
        if (geomType === 'esriGeometryPoint' || (f.geometry.x !== undefined && f.geometry.y !== undefined)) {
            var pt = projectCoordinate(f.geometry.x, f.geometry.y, wkid);
            geom = { type: 'Point', coordinates: [pt[0], pt[1]] };
        } else if (geomType === 'esriGeometryPolyline' || f.geometry.paths) {
            var paths = f.geometry.paths || [];
            if (paths.length === 1) geom = { type: 'LineString', coordinates: paths[0].map(function(c){ return projectCoordinate(c[0],c[1],wkid); }) };
            else if (paths.length > 1) geom = { type: 'MultiLineString', coordinates: paths.map(function(p){ return p.map(function(c){ return projectCoordinate(c[0],c[1],wkid); }); }) };
        } else if (geomType === 'esriGeometryPolygon' || f.geometry.rings) {
            var rings = f.geometry.rings || [];
            if (rings.length > 0) geom = { type: 'Polygon', coordinates: rings.map(function(r){ return r.map(function(c){ return projectCoordinate(c[0],c[1],wkid); }); }) };
        }
        if (geom) {
            gj.features.push({
                type: 'Feature',
                id: (f.attributes && (f.attributes.FID || f.attributes.OBJECTID)) ? (f.attributes.FID || f.attributes.OBJECTID) : idx,
                geometry: geom,
                properties: f.attributes || {}
            });
        }
    });
    return gj;
}

/* ============================================================
   5. FETCH & LOAD GEOJSON FILES KE PETA
   Fungsi: Mengambil setiap file GeoJSON dari folder public/geojson/,
   mengkonversi koordinatnya, lalu menampilkan layer di peta Leaflet
   dengan warna sesuai kategori (Baku/Fungsional/Potensial/dll).
   Data koordinat pusat juga disimpan untuk ditampilkan di tabel.
   ============================================================ */
var mapLoadingEl = document.getElementById('map-loading');
var allTableRows = [];
var currentSearchText = '';
if (window.geojsonFiles && window.geojsonFiles.length > 0) {
    var loadedCount = 0;

    // Safety timeout: jika ada kendala jaringan/file corrupt, pastikan loading indikator ditutup maksimal 8 detik
    setTimeout(function() {
        if (mapLoadingEl && mapLoadingEl.style.display !== 'none') {
            mapLoadingEl.style.display = 'none';
            if (mapBounds.isValid()) map.fitBounds(mapBounds, { padding: [40, 40] });
            renderTable();
        }
    }, 8000);

    window.geojsonFiles.forEach(function(fileName) {
        fetch('/geojson/' + encodeURIComponent(fileName))
            .then(function(res) { if (!res.ok) throw new Error('HTTP ' + res.status); return res.json(); })
            .then(function(rawData) {
                var gj = convertEsriToGeoJson(rawData);
                if (!gj || !gj.features || gj.features.length === 0) return;

                var cleanName = fileName.replace(/\.(json|geojson)$/i, '').replace(/_/g, ' ');
                var isDefault = DEFAULT_BASELINE_FILES.indexOf(fileName) !== -1;
                var targetLayer = null;
                var categoryType = '';
                var categoryBadge = '';
                var strokeColor = '#27ae60', fillColor = '#2ecc71';
                var isSaluran = false;

                if (!isDefault && customFileMap[fileName]) {
                    var cInfo = customFileMap[fileName];
                    targetLayer = cInfo.layerGroup;
                    categoryType = 'custom';
                    categoryBadge = '<span class="badge px-2 py-1 text-white shadow-sm" style="background:' + cInfo.color.fill + ';border:1px solid ' + cInfo.color.stroke + ';font-size:11px;">' + cInfo.cleanName + '</span>';
                    strokeColor = cInfo.color.stroke;
                    fillColor = cInfo.color.fill;

                    // Deteksi tipe geometri aktual dari feature
                    var f0 = gj.features[0];
                    if (f0 && f0.geometry) {
                        var gType = f0.geometry.type || '';
                        if (gType.indexOf('Point') !== -1) {
                            cInfo.geomType = 'point';
                        } else if (gType.indexOf('Line') !== -1) {
                            cInfo.geomType = 'line';
                            isSaluran = true;
                        } else {
                            cInfo.geomType = 'polygon';
                        }
                        refreshLegendBody();
                        var sw = document.getElementById('layer-swatch-' + fileName.replace(/[^a-zA-Z0-9]/g, '_'));
                        if (sw) {
                            if (cInfo.geomType === 'line') {
                                sw.className = 'legend-line-sm';
                                sw.style.border = 'none';
                            } else if (cInfo.geomType === 'point') {
                                sw.className = 'legend-circle-sm';
                            }
                        }
                    }
                } else {
                    var nameUpper = fileName.toUpperCase();
                    if (nameUpper.indexOf('BAKU') === 0) {
                        targetLayer = layerBaku; categoryType = 'baku';
                        categoryBadge = '<span class="badge badge-baku px-2 py-1">DIR Baku</span>';
                        strokeColor = '#1e824c'; fillColor = '#27ae60';
                    } else if (nameUpper.indexOf('FUNGSIONAL') === 0) {
                        targetLayer = layerFungsional; categoryType = 'fungsional';
                        categoryBadge = '<span class="badge badge-fungsional px-2 py-1">DIR Fungsional</span>';
                        strokeColor = '#1f618d'; fillColor = '#2980b9';
                    } else if (nameUpper.indexOf('POTENSIAL') === 0) {
                        targetLayer = layerPotensial; categoryType = 'potensial';
                        categoryBadge = '<span class="badge badge-potensial px-2 py-1">DIR Potensial</span>';
                        strokeColor = '#b95c00'; fillColor = '#e67e22';
                    } else if (nameUpper.indexOf('SALURAN') === 0) {
                        targetLayer = layerSaluran; categoryType = 'saluran';
                        categoryBadge = '<span class="badge badge-saluran px-2 py-1">Saluran Irigasi</span>';
                        strokeColor = '#0984e3'; fillColor = '#0984e3';
                        isSaluran = true;
                    } else if (nameUpper.indexOf('BANGUNAN') === 0) {
                        targetLayer = layerBangunan; categoryType = 'bangunan';
                        categoryBadge = '<span class="badge badge-bangunan px-2 py-1">Bangunan Air</span>';
                        strokeColor = '#d63031'; fillColor = '#d63031';
                    } else {
                        targetLayer = layerBaku; categoryType = 'baku';
                        categoryBadge = '<span class="badge badge-baku px-2 py-1">DIR Baku</span>';
                    }
                }

                var gjLayer = L.geoJSON(gj, {
                    style: function() {
                        return { color: strokeColor, weight: isSaluran ? 2.5 : 1.8, fillOpacity: 0.45, fillColor: fillColor };
                    },
                    pointToLayer: function(feat, latlng) {
                        return L.circleMarker(latlng, { radius: 6, fillColor: fillColor, color: '#ffffff', weight: 1.5, fillOpacity: 0.9 });
                    },
                    onEachFeature: function(feat, layer) {
                        var p = feat.properties || {};
                        var popup = '<div style="min-width:200px;font-size:12px;">' +
                            '<div class="font-weight-bold text-primary mb-1">' + cleanName + '</div>' +
                            '<div class="mb-1">' + categoryBadge + '</div>' +
                            '<table class="table table-sm table-bordered mt-2 mb-0" style="font-size:11px;">';
                        var countProp = 0;
                        for (var k in p) {
                            if (countProp < 5 && p[k] !== null && p[k] !== '') {
                                popup += '<tr><th class="bg-light">' + k + '</th><td>' + p[k] + '</td></tr>';
                                countProp++;
                            }
                        }
                        popup += '</table></div>';
                        layer.bindPopup(popup);
                    }
                });

                if (targetLayer) targetLayer.addLayer(gjLayer);

                var b = gjLayer.getBounds();
                if (b.isValid()) {
                    mapBounds.extend(b);
                    var center = b.getCenter();
                    allTableRows.push({ name: cleanName, categoryType: categoryType, categoryBadge: categoryBadge, lat: center.lat, lng: center.lng, fileName: fileName, bounds: b, layerObj: gjLayer });
                }
            })
            .catch(function(err) {})
            .finally(function() {
                loadedCount++;
                if (loadedCount >= window.geojsonFiles.length) {
                    if (mapLoadingEl) mapLoadingEl.style.display = 'none';
                    if (mapBounds.isValid()) map.fitBounds(mapBounds, { padding: [40, 40] });
                    renderTable();
                    applyUrlLayerFilter();
                }
            });
    });
} else {
    if (mapLoadingEl) mapLoadingEl.style.display = 'none';
    renderTable();
    applyUrlLayerFilter();
}

/**
 * Otomatis memfilter tabel dan zoom ke layer irigasi
 * jika user membuka link dari menu Master Data > Infrastruktur (?layer=...)
 */
function applyUrlLayerFilter() {
    var urlParams = new URLSearchParams(window.location.search);
    var layerParam = (urlParams.get('layer') || '').toLowerCase().trim();
    if (!layerParam) return;

    var searchInput = document.getElementById('cari-tabel-koordinat');
    if (searchInput) {
        searchInput.value = layerParam;
    }
    filterTabelKoordinat(layerParam);

    var layerBounds = L.latLngBounds();
    allTableRows.forEach(function(r) {
        if (r.categoryType === layerParam && r.bounds && r.bounds.isValid && r.bounds.isValid()) {
            layerBounds.extend(r.bounds);
        }
    });
    if (layerBounds.isValid && layerBounds.isValid()) {
        map.fitBounds(layerBounds, { padding: [50, 50], maxZoom: 14 });
    }
}

/* ============================================================
   6. RENDER DATA TABLE
   Fungsi: Mengisi baris tabel koordinat GIS secara dinamis dari
   data yang sudah diload. Mendukung filter pencarian realtime dan
   menampilkan state kosong yang informatif jika belum ada data.
   ============================================================ */
function renderTable() {
    var tbody = document.getElementById('tbody-lokasi');
    var footerInfo = document.getElementById('tabel-footer-info');
    if (!tbody) return;

    var filtered = allTableRows.filter(function(row) {
        if (currentSearchText === '') return true;
        var term = currentSearchText.toLowerCase();
        return (row.name && row.name.toLowerCase().indexOf(term) !== -1) ||
               (row.fileName && row.fileName.toLowerCase().indexOf(term) !== -1) ||
               (row.categoryType && row.categoryType.toLowerCase().indexOf(term) !== -1);
    });

    if (footerInfo) {
        footerInfo.innerHTML = 'Menampilkan <b>' + filtered.length + '</b> dari <b>' + allTableRows.length + '</b> file GIS';
    }

    if (filtered.length === 0) {
        if (allTableRows.length === 0) {
            var actionBtnHtml = (window.canManageGis)
                ? '<p class="small text-muted mb-3">Silakan klik tombol "Upload GeoJSON" untuk menambahkan file koordinat dan layer ke peta.</p>' +
                  '<button type="button" class="btn btn-sm btn-primary font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalUploadGeojson">' +
                  '<i class="fas fa-cloud-upload-alt mr-1"></i> Upload GeoJSON Sekarang</button>'
                : '<p class="small text-muted mb-0">Data layer dan koordinat spasial belum diunggah oleh Administrator.</p>';

            tbody.innerHTML =
                '<tr><td colspan="7" class="text-center py-5 text-muted">' +
                '<div class="mb-2"><i class="fas fa-folder-open fa-3x text-gray-300"></i></div>' +
                '<div class="font-weight-bold text-gray-700 mb-1" style="font-size:15px;">Belum Ada Data GIS Tersimpan</div>' +
                actionBtnHtml +
                '</td></tr>';
        } else {
            tbody.innerHTML =
                '<tr><td colspan="7" class="text-center py-4 text-muted">' +
                '<i class="fas fa-info-circle mr-2 text-info"></i>Tidak ditemukan data GIS yang cocok dengan pencarian "<b>' + currentSearchText + '</b>".' +
                '</td></tr>';
        }
        return;
    }

    var frag = document.createDocumentFragment();
    filtered.forEach(function(row, idx) {
        var tr = document.createElement('tr');
        var latStr = (row.lat !== null && !isNaN(row.lat)) ? Number(row.lat).toFixed(5) : '-';
        var lngStr = (row.lng !== null && !isNaN(row.lng)) ? Number(row.lng).toFixed(5) : '-';

        tr.innerHTML =
            '<td class="text-center font-weight-bold text-muted">' + (idx + 1) + '</td>' +
            '<td class="font-weight-bold text-dark">' + row.name + '</td>' +
            '<td>' + row.categoryBadge + '</td>' +
            '<td class="text-center font-weight-bold text-dark"><code>' + latStr + '</code></td>' +
            '<td class="text-center font-weight-bold text-dark"><code>' + lngStr + '</code></td>' +
            '<td><span class="badge badge-light border text-monospace text-truncate d-inline-block" style="max-width:180px;" title="' + row.fileName + '">' + row.fileName + '</span></td>' +
            '<td class="text-center" style="white-space:nowrap;">' +
                '<button type="button" class="btn-focus-row mr-1" title="Fokuskan peta ke objek ini">' +
                '<i class="fas fa-crosshairs"></i> Fokus</button>' +
                '<a href="/peta/download-file/' + encodeURIComponent(row.fileName) + '" class="btn-download-row" title="Unduh GeoJSON (' + row.fileName + ')">' +
                '<i class="fas fa-download"></i></a>' +
            '</td>';

        // Tombol Fokus: klik → peta zoom ke bounds objek tersebut
        var btn = tr.querySelector('.btn-focus-row');
        btn.addEventListener('click', function() {
            if (row.fileName && customFileMap[row.fileName]) {
                if (!map.hasLayer(customFileMap[row.fileName].layerGroup)) {
                    map.addLayer(customFileMap[row.fileName].layerGroup);
                }
            }
            if (row.bounds && row.bounds.isValid && row.bounds.isValid()) {
                map.fitBounds(row.bounds, { padding: [50, 50], maxZoom: 16 });
            } else if (row.lat && row.lng && !isNaN(row.lat) && !isNaN(row.lng)) {
                map.flyTo([row.lat, row.lng], 15, { animate: true, duration: 1.0 });
            }
            if (row.layerObj) {
                if (row.layerObj.openPopup) row.layerObj.openPopup();
                else if (row.layerObj.eachLayer) row.layerObj.eachLayer(function(l) { if (l.openPopup) { l.openPopup(); return; } });
            }
            var mapSec = document.getElementById('section-map');
            if (mapSec) window.scrollTo({ top: mapSec.offsetTop - 85, behavior: 'smooth' });
        });

        frag.appendChild(tr);
    });
    tbody.innerHTML = '';
    tbody.appendChild(frag);
}

/* ============================================================
   7. PENCARIAN DATA REALTIME
   Fungsi: Input cari di toolbar tabel, filter baris berdasarkan
   nama objek atau nama file GeoJSON secara langsung (tanpa reload).
   ============================================================ */
window.filterTabelKoordinat = function(val) {
    currentSearchText = (val || '').trim();
    renderTable();
};

/* ============================================================
   8. EXPORT DATA KE CSV
   Fungsi: Mengunduh data koordinat GIS dari tabel sebagai file
   CSV yang bisa dibuka di Excel / Google Sheets.
   ============================================================ */
window.exportDataToCsv = function() {
    if (!allTableRows || allTableRows.length === 0) {
        alert('Belum ada data GIS yang dapat diexport.');
        return;
    }
    var csv = 'No,Nama Pos / Objek DIR,Kategori Layer,Latitude (Y),Longitude (X),Sumber File GIS\n';
    allTableRows.forEach(function(r, i) {
        var clean = (r.name || '').replace(/,/g, ' ');
        var lat = (r.lat !== null && !isNaN(r.lat)) ? Number(r.lat).toFixed(5) : '';
        var lng = (r.lng !== null && !isNaN(r.lng)) ? Number(r.lng).toFixed(5) : '';
        csv += (i + 1) + ',"' + clean + '","' + (r.categoryType || '') + '",' + lat + ',' + lng + ',"' + (r.fileName || '') + '"\n';
    });
    var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    var link = document.createElement('a');
    link.setAttribute('href', URL.createObjectURL(blob));
    link.setAttribute('download', 'Data_GIS_DIR_BWS_Kalimantan_I_' + new Date().toISOString().slice(0,10) + '.csv');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};

/* ============================================================
   9. HELPER MODAL KELOLA FILE GIS
   Fungsi:
   - updateFileInputLabel: Update label saat user pilih file upload
   - filterTabelFileGeojson: Filter nama file di modal Kelola File GIS
   - updateBulkDeleteUI: Update counter & state tombol hapus terpilih
   - konfirmasiHapusTerpilih: Konfirmasi & submit form bulk delete
   ============================================================ */
function updateFileInputLabel(input) {
    var label = document.getElementById('labelGeojsonFile');
    var rarAlert = document.getElementById('rarWarningAlert');
    var rarText = document.getElementById('rarWarningText');
    var hasRar = false;
    var rarNames = [];

    if (input.files && input.files.length > 0) {
        for (var i = 0; i < input.files.length; i++) {
            if (input.files[i].name.toLowerCase().endsWith('.rar')) {
                hasRar = true;
                rarNames.push(input.files[i].name);
            }
        }
    }

    if (rarAlert) {
        if (hasRar) {
            if (rarText) {
                rarText.innerHTML = 'File <strong>' + rarNames.join(', ') + '</strong> terdeteksi berformat <strong>.RAR</strong>.<br>' +
                    'Sistem GIS dan Server membutuhkan Shapefile dalam format <strong>.ZIP</strong> (berisi file .shp, .dbf, .shx, .prj).<br>' +
                    '<strong>Solusi:</strong> Ekstrak file RAR tersebut, lalu kompres ulang ke format <strong>ZIP</strong> (di WinRAR pilih opsi <em>ZIP</em>, atau di Windows klik kanan &rarr; <em>Send to / Kirim ke &rarr; Compressed (zipped) folder</em>).';
            }
            rarAlert.style.display = 'block';
        } else {
            rarAlert.style.display = 'none';
        }
    }

    if (input.files && input.files.length > 1) {
        label.textContent = input.files.length + ' file dipilih';
    } else if (input.files && input.files.length === 1) {
        label.textContent = input.files[0].name;
    } else {
        label.textContent = 'Pilih satu atau beberapa file...';
    }
}
window.updateFileInputLabel = updateFileInputLabel;

function filterTabelFileGeojson(query) {
    var term = (query || '').toLowerCase().trim();
    document.querySelectorAll('#tabel-kelola-geojson tbody tr.item-file-row').forEach(function(row) {
        var text = row.querySelector('.nama-file-text');
        if (!text) return;
        row.style.display = (text.textContent.toLowerCase().indexOf(term) !== -1) ? '' : 'none';
    });
}
window.filterTabelFileGeojson = filterTabelFileGeojson;

function updateBulkDeleteUI() {
    var checked = document.querySelectorAll('.chk-file-geojson:checked');
    var total = checked.length;
    var btn        = document.getElementById('btn-hapus-terpilih');
    var countEl    = document.getElementById('bulk-selected-count');
    var btnCountEl = document.getElementById('btn-bulk-count');
    var infoEl     = document.getElementById('bulk-selected-info');

    if (countEl) countEl.textContent = total;
    if (btnCountEl) btnCountEl.textContent = total;
    if (infoEl) infoEl.style.display = total > 0 ? '' : 'none';
    if (btn) btn.disabled = total === 0;

    var all = document.querySelectorAll('.chk-file-geojson');
    var chkAll = document.getElementById('chk-select-all-geojson');
    if (chkAll) {
        if (total === 0)           { chkAll.checked = false; chkAll.indeterminate = false; }
        else if (total === all.length) { chkAll.checked = true;  chkAll.indeterminate = false; }
        else                        { chkAll.checked = false; chkAll.indeterminate = true; }
    }
}

function konfirmasiHapusTerpilih() {
    var checked = document.querySelectorAll('.chk-file-geojson:checked');
    if (checked.length === 0) return;
    var names = [];
    checked.forEach(function(c) { names.push(c.value); });
    var msg = 'Anda akan menghapus ' + checked.length + ' file GIS secara permanen:\n\n';
    names.forEach(function(n, i) { msg += (i + 1) + '. ' + n + '\n'; });
    msg += '\nLanjutkan penghapusan?';
    if (!confirm(msg)) return;
    var container = document.getElementById('bulk-delete-inputs');
    container.innerHTML = '';
    names.forEach(function(name) {
        var inp = document.createElement('input');
        inp.type = 'hidden'; inp.name = 'file_names[]'; inp.value = name;
        container.appendChild(inp);
    });
    document.getElementById('form-bulk-delete').submit();
}
window.konfirmasiHapusTerpilih = konfirmasiHapusTerpilih;

/* ============================================================
   10. CHUNKED BATCH UPLOAD GEOJSON
   Fungsi: Upload banyak file GeoJSON sekaligus secara bertahap
   (5 file per batch) untuk menghindari error PostTooLargeException
   dari PHP. Menampilkan progress bar realtime per batch.
   ============================================================ */
document.addEventListener('DOMContentLoaded', function() {
    // Checkbox select-all di modal Kelola File
    var chkAll = document.getElementById('chk-select-all-geojson');
    if (chkAll) {
        chkAll.addEventListener('change', function() {
            document.querySelectorAll('.chk-file-geojson').forEach(function(c) { c.checked = chkAll.checked; });
            updateBulkDeleteUI();
        });
    }
    document.querySelectorAll('.chk-file-geojson').forEach(function(c) {
        c.addEventListener('change', updateBulkDeleteUI);
    });

    // Form upload dengan chunked batch
    var formUpload = document.getElementById('formUploadGeojson');
    if (formUpload) {
        formUpload.addEventListener('submit', async function(e) {
            e.preventDefault();
            var input = document.getElementById('inputGeojsonFile');
            if (!input || !input.files || input.files.length === 0) {
                alert('Silakan pilih setidaknya satu file spasial (GeoJSON, SHP .zip, atau KML) terlebih dahulu.');
                return;
            }

            var progressSection = document.getElementById('uploadProgressSection');
            var progressBar     = document.getElementById('uploadProgressBar');
            var percentText     = document.getElementById('uploadPercentText');
            var statusText      = document.getElementById('uploadStatusText');
            var detailText      = document.getElementById('uploadDetailText');
            var batchCountText  = document.getElementById('uploadBatchCountText');
            var successSection  = document.getElementById('uploadSuccessSection');
            var errorSection    = document.getElementById('uploadErrorSection');
            var btnSubmit       = document.getElementById('btnSubmitUpload');
            var btnBatal        = document.getElementById('btnBatalUpload');
            var btnClose        = document.getElementById('btnCloseUploadModal');

            if (errorSection)   { errorSection.style.display = 'none'; errorSection.innerText = ''; }
            if (successSection)   successSection.style.display = 'none';
            if (progressSection)  progressSection.style.display = 'block';
            if (btnSubmit) { btnSubmit.disabled = true; btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Memproses file...'; }
            if (btnBatal)  btnBatal.disabled = true;
            if (btnClose)  btnClose.style.display = 'none';

            var rawFiles = Array.from(input.files);

            // Cek jika ada file .rar
            var rarFiles = rawFiles.filter(function(f) { return f.name.toLowerCase().endsWith('.rar'); });
            if (rarFiles.length > 0) {
                var rarNames = rarFiles.map(function(f) { return f.name; }).join(', ');
                if (errorSection) {
                    errorSection.style.display = 'block';
                    errorSection.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i><strong>Format .RAR Tidak Didukung:</strong> ' + rarNames + '<br><br>' +
                        'File Shapefile GIS harus dikompresi dalam format <strong>.ZIP</strong> (bukan .RAR) yang berisi file <code>.shp</code>, <code>.dbf</code>, <code>.shx</code>, dan <code>.prj</code>.<br><br>' +
                        '<strong>Langkah Mengubah ke .ZIP:</strong><br>' +
                        '1. Buka file <code>' + rarNames + '</code> dan ekstrak isinya ke folder.<br>' +
                        '2. Pilih/blok file <code>.shp</code>, <code>.dbf</code>, <code>.shx</code>, <code>.prj</code>.<br>' +
                        '3. Klik kanan &rarr; pilih <strong>Send to (Kirim ke) &rarr; Compressed (zipped) folder</strong>, ATAU di WinRAR pilih radio button <strong>ZIP</strong>.<br>' +
                        '4. Unggah kembali file <code>.zip</code> yang baru dibuat tersebut.';
                }
                if (progressSection) progressSection.style.display = 'none';
                if (btnSubmit) { btnSubmit.disabled = false; btnSubmit.innerHTML = '<i class="fas fa-upload mr-1"></i>Upload Sekarang'; }
                if (btnBatal)  btnBatal.disabled = false;
                if (btnClose)  btnClose.style.display = '';
                return;
            }

            var files = [];

            // Pra-pemrosesan: Konversi otomatis Shapefile (.zip) atau KML/KMZ ke GeoJSON
            for (var i = 0; i < rawFiles.length; i++) {
                var file = rawFiles[i];
                var nameLower = file.name.toLowerCase();
                var baseName = file.name.replace(/\.[^/.]+$/, "");

                if (nameLower.endsWith('.zip') && typeof shp !== 'undefined') {
                    if (statusText) statusText.innerHTML = '<i class="fas fa-cog fa-spin mr-1"></i>Mengkonversi Shapefile (' + file.name + ') ke GeoJSON...';
                    try {
                        var buffer = await file.arrayBuffer();
                        var converted = await shp(buffer);
                        if (Array.isArray(converted)) {
                            converted.forEach(function(item, cIdx) {
                                var subName = (item.fileName || baseName + '_' + (cIdx + 1)) + '.geojson';
                                var blob = new Blob([JSON.stringify(item)], { type: 'application/geo+json' });
                                files.push(new File([blob], subName, { type: 'application/geo+json' }));
                            });
                        } else if (converted && converted.type === 'FeatureCollection') {
                            var blob = new Blob([JSON.stringify(converted)], { type: 'application/geo+json' });
                            files.push(new File([blob], baseName + '.geojson', { type: 'application/geo+json' }));
                        } else {
                            files.push(file);
                        }
                    } catch (shpErr) {
                        console.warn('shpjs conversion warning, fallback to server:', shpErr);
                        files.push(file);
                    }
                } else if (nameLower.endsWith('.kml') && typeof toGeoJSON !== 'undefined') {
                    if (statusText) statusText.innerHTML = '<i class="fas fa-cog fa-spin mr-1"></i>Mengkonversi KML (' + file.name + ') ke GeoJSON...';
                    try {
                        var kmlText = await file.text();
                        var kmlDom = new DOMParser().parseFromString(kmlText, 'text/xml');
                        var kmlGeojson = toGeoJSON.kml(kmlDom);
                        var blob = new Blob([JSON.stringify(kmlGeojson)], { type: 'application/geo+json' });
                        files.push(new File([blob], baseName + '.geojson', { type: 'application/geo+json' }));
                    } catch (kmlErr) {
                        console.warn('toGeoJSON conversion warning, fallback to server:', kmlErr);
                        files.push(file);
                    }
                } else {
                    files.push(file);
                }
            }

            var totalFiles   = files.length;
            var BATCH_SIZE   = 5;
            var totalBatches = Math.ceil(totalFiles / BATCH_SIZE);

            var totalUploaded = 0, allUploadedNames = [], allFailed = [];
            var csrfToken = formUpload.querySelector('input[name="_token"]')
                ? formUpload.querySelector('input[name="_token"]').value : '';

            try {
                for (var b = 0; b < totalBatches; b++) {
                    var start = b * BATCH_SIZE;
                    var end   = Math.min(start + BATCH_SIZE, totalFiles);
                    var batchFiles = files.slice(start, end);
                    var currentBatchNum = b + 1;

                    if (statusText) statusText.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Mengunggah batch ' + currentBatchNum + ' dari ' + totalBatches + '...';
                    if (batchCountText) batchCountText.textContent = 'Batch ' + currentBatchNum + '/' + totalBatches + ' (' + batchFiles.length + ' file)';

                    var formData = new FormData();
                    formData.append('_token', csrfToken);
                    for (var f = 0; f < batchFiles.length; f++) formData.append('file_geojson[]', batchFiles[f]);

                    var uploadUrl = formUpload.getAttribute('action');
                    var response = await fetch(uploadUrl, {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                        body: formData
                    });

                    if (!response.ok) {
                        var errJson = await response.json().catch(function() { return {}; });
                        throw new Error(errJson.message || ('Gagal pada pengunggahan batch ke-' + currentBatchNum + ' (HTTP ' + response.status + ')'));
                    }

                    var result = await response.json();
                    if (result.uploaded && result.uploaded.length > 0) { allUploadedNames = allUploadedNames.concat(result.uploaded); totalUploaded += result.uploaded.length; }
                    if (result.failed && result.failed.length > 0)     allFailed = allFailed.concat(result.failed);

                    var pct = Math.round((end / totalFiles) * 100);
                    if (progressBar)  { progressBar.style.width = pct + '%'; progressBar.textContent = pct + '%'; }
                    if (percentText)    percentText.textContent = pct + '%';
                    if (detailText) detailText.innerHTML = '<span>Tersimpan <b>' + totalUploaded + '</b> dari <b>' + totalFiles + '</b> file</span><span>Batch ' + currentBatchNum + '/' + totalBatches + '</span>';
                }

                if (statusText) statusText.innerHTML = '<i class="fas fa-check-circle text-success mr-1"></i>Semua file selesai diunggah!';
                if (successSection) {
                    var successMsg = totalUploaded + ' file GeoJSON berhasil diunggah! Memuat ulang halaman peta...';
                    if (allFailed.length > 0) successMsg += '<br><small class="text-warning">File dilewati: ' + allFailed.join(', ') + '</small>';
                    successSection.innerHTML = '<i class="fas fa-check-circle mr-1"></i>' + successMsg;
                    successSection.style.display = 'block';
                }
                setTimeout(function() { window.location.reload(); }, 1200);

            } catch (err) {
                if (errorSection) {
                    errorSection.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i><b>Terjadi Kesalahan:</b> ' + err.message +
                        (totalUploaded > 0 ? '<br><small>(' + totalUploaded + ' file sempat tersimpan sebelum error)</small>' : '');
                    errorSection.style.display = 'block';
                }
                if (btnSubmit) { btnSubmit.disabled = false; btnSubmit.innerHTML = '<i class="fas fa-redo mr-1"></i>Coba Lagi'; }
                if (btnBatal)  btnBatal.disabled = false;
                if (btnClose)  btnClose.style.display = '';
            }
        });
    }
});
