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

    // Cek apakah halaman menggunakan layout v2 (tab-based)
    var isV2 = document.body.classList.contains('page-peta-v2');

    if (isV2) {
        // Di v2, gunakan switchPetaTab jika tersedia
        if (window.switchPetaTab) {
            if (mode === 'table') window.switchPetaTab('tabel');
            else window.switchPetaTab('peta');
        }
    } else {
        // Layout lama (v1)
        if (mode === 'maps') {
            if (mapSec) mapSec.style.display = 'block';
            if (tblSec) tblSec.style.display = 'none';
            if (mapEl)  mapEl.style.height = 'calc(100vh - 150px)';
        } else if (mode === 'table') {
            if (mapSec) mapSec.style.display = 'none';
            if (tblSec) tblSec.style.display = 'block';
        } else {
            if (mapSec) mapSec.style.display = 'block';
            if (tblSec) tblSec.style.display = 'block';
            if (mapEl)  mapEl.style.height = '420px';
        }
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

// ============================================================
// MAP ACTION CONTROLS (Sesuai Gambar 3 & 4: GPS, Home, Fullscreen)
// ============================================================
var userLocationMarker = null;
var userLocationCircle = null;

function handleUserGeolocation(btnEl) {
    if (!navigator.geolocation) {
        alert('Fitur Geolocation tidak didukung oleh browser Anda.');
        return;
    }

    var icon = btnEl ? btnEl.querySelector('i') : null;
    if (icon) icon.className = 'fas fa-spinner fa-spin';

    navigator.geolocation.getCurrentPosition(
        function(position) {
            if (icon) icon.className = 'fas fa-crosshairs';
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;
            var accuracy = position.coords.accuracy;

            // Hapus marker sebelumnya jika ada
            if (userLocationMarker) map.removeLayer(userLocationMarker);
            if (userLocationCircle) map.removeLayer(userLocationCircle);

            // Buat marker lokasi dengan radar glow
            userLocationCircle = L.circle([lat, lng], {
                radius: Math.max(accuracy, 100),
                color: '#10b981',
                fillColor: '#10b981',
                fillOpacity: 0.15,
                weight: 1.5
            }).addTo(map);

            var pulsingIcon = L.divIcon({
                className: 'user-loc-pulsing-marker',
                html: '<div class="user-loc-dot"></div><div class="user-loc-pulse"></div>',
                iconSize: [22, 22],
                iconAnchor: [11, 11]
            });

            userLocationMarker = L.marker([lat, lng], { icon: pulsingIcon }).addTo(map);
            userLocationMarker.bindPopup(
                '<div style="font-family: inherit; font-size: 12.5px; padding: 4px;">' +
                    '<div style="font-weight: 700; color: #166534; margin-bottom: 2px;">' +
                        '<i class="fas fa-map-pin mr-1 text-danger"></i> Lokasi Anda Saat Ini' +
                    '</div>' +
                    '<div style="color: #64748b; font-size: 11px;">Akurasi GPS: &plusmn;' + Math.round(accuracy) + ' meter</div>' +
                '</div>'
            ).openPopup();

            // Zoom dengan pembesaran optimal (sesuai Gambar 4: zoom 12.5 tidak terlalu besar)
            map.flyTo([lat, lng], 12.5, { animate: true, duration: 1.0 });
        },
        function(error) {
            if (icon) icon.className = 'fas fa-crosshairs';
            var msg = 'Gagal mendeteksi lokasi GPS.';
            if (error.code === error.PERMISSION_DENIED) {
                msg = 'Izin akses lokasi ditolak oleh browser.';
            } else if (error.code === error.POSITION_UNAVAILABLE) {
                msg = 'Informasi lokasi tidak tersedia saat ini.';
            } else if (error.code === error.TIMEOUT) {
                msg = 'Waktu permintaan lokasi habis.';
            }
            alert(msg);
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
}

function handleHomeReset() {
    if (mapBounds && mapBounds.isValid && mapBounds.isValid()) {
        map.fitBounds(mapBounds, { padding: [50, 50], maxZoom: 12.5 });
    } else {
        map.setView(defaultCenter, 8.5);
    }
}

var MapActionsControl = L.Control.extend({
    options: { position: 'topleft' },
    onAdd: function() {
        var container = L.DomUtil.create('div', 'leaflet-bar map-action-control-group');
        
        // 1. Tombol GPS / Lokasi Saya (Target Crosshairs)
        var btnLocate = L.DomUtil.create('a', 'map-action-btn btn-locate', container);
        btnLocate.href = '#';
        btnLocate.title = 'Deteksi Lokasi Saya (GPS)';
        btnLocate.setAttribute('role', 'button');
        btnLocate.setAttribute('aria-label', 'Lokasi Saya');
        btnLocate.innerHTML = '<i class="fas fa-crosshairs"></i>';

        // 2. Tombol Home / Pusat Peta (Rumah)
        var btnHome = L.DomUtil.create('a', 'map-action-btn btn-home', container);
        btnHome.href = '#';
        btnHome.title = 'Kembali ke Pusat Peta (Reset View)';
        btnHome.setAttribute('role', 'button');
        btnHome.setAttribute('aria-label', 'Pusat Peta');
        btnHome.innerHTML = '<i class="fas fa-home"></i>';

        // 3. Tombol Zoom/Perbesar Layar Peta (Mode Fokus Bersih sesuai Gambar 2)
        var btnFs = L.DomUtil.create('a', 'map-action-btn btn-fullscreen', container);
        btnFs.href = '#';
        btnFs.title = 'Perbesar Tampilan Peta (Mode Layar Penuh Antarmuka)';
        btnFs.setAttribute('role', 'button');
        btnFs.setAttribute('aria-label', 'Perbesar Tampilan Peta');
        btnFs.innerHTML = '<i class="fas fa-expand"></i>';

        L.DomEvent.disableClickPropagation(container);
        L.DomEvent.disableScrollPropagation(container);

        L.DomEvent.on(btnLocate, 'click', function(e) {
            L.DomEvent.stop(e);
            handleUserGeolocation(btnLocate);
        });

        L.DomEvent.on(btnHome, 'click', function(e) {
            L.DomEvent.stop(e);
            handleHomeReset();
        });

        L.DomEvent.on(btnFs, 'click', function(e) {
            L.DomEvent.stop(e);
            toggleFocusGisMode();
        });

        return container;
    }
});
map.addControl(new MapActionsControl());

function toggleMapFullscreen() {
    toggleFocusGisMode();
}

// Listener tombol ESC untuk keluar dari mode fokus peta
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' || e.key === 'Esc') {
        if (document.body.classList.contains('gis-focus-mode')) {
            toggleFocusGisMode();
        }
    }
});

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

// Jaringan Saluran dan Bangunan Air default nonaktif saat awal buka website (tidak langsung addTo(map))
var layerSaluran    = L.layerGroup();
var layerBangunan   = L.layerGroup();

// Ekspos ke global window untuk kontrol filter sidebar & reset
window.layerBaku       = layerBaku;
window.layerFungsional = layerFungsional;
window.layerPotensial  = layerPotensial;
window.layerSaluran    = layerSaluran;
window.layerBangunan   = layerBangunan;

// Koleksi titik untuk penyesuaian ukuran adaptif saat zoom in / zoom out
var allCircleMarkers = [];
function getAdaptiveMarkerSize(zoom) {
    if (zoom <= 9)  return { radius: 2.2, weight: 0.6 };
    if (zoom <= 11) return { radius: 3.2, weight: 0.9 };
    if (zoom <= 13) return { radius: 4.2, weight: 1.2 };
    return { radius: 5.5, weight: 1.5 };
}

/**
 * Penyesuaian Ukuran Popup Adaptif saat Pengecilan/Pembesaran Peta (Zoom In / Zoom Out)
 * Menjaga card popup proporsional dan posisinya TETAP 100% AKURAT di atas marker
 * (Murni tata letak CSS tanpa CSS transform/scale/zoom yang merusak koordinat Leaflet).
 */
function updateAdaptivePopupSize() {
    if (!window.map) return;
    var currentZoom = map.getZoom ? map.getZoom() : 8;
    var mapEl = document.getElementById('map');
    if (!mapEl) return;

    var sizeClass = 'bws-popup-zoom-lg';
    if (currentZoom >= 15) {
        sizeClass = 'bws-popup-zoom-xl';
    } else if (currentZoom >= 12) {
        sizeClass = 'bws-popup-zoom-lg';
    } else if (currentZoom >= 9) {
        sizeClass = 'bws-popup-zoom-md';
    } else {
        sizeClass = 'bws-popup-zoom-sm';
    }

    mapEl.classList.remove('bws-popup-zoom-xl', 'bws-popup-zoom-lg', 'bws-popup-zoom-md', 'bws-popup-zoom-sm');
    mapEl.classList.add(sizeClass);

    // Minta Leaflet untuk mereposisi popup agar tip SELALU 100% tepat menancap di marker
    map.eachLayer(function(layer) {
        if (layer.getPopup && layer.getPopup() && layer.isPopupOpen && layer.isPopupOpen()) {
            layer.getPopup().update();
        }
    });
    if (map._popup && map._popup.isOpen && map._popup.isOpen()) {
        map._popup.update();
    }
}

// Inisialisasi awal ukuran adaptif
updateAdaptivePopupSize();

map.on('zoomend', function() {
    var currentZoom = map.getZoom();
    var size = getAdaptiveMarkerSize(currentZoom);
    for (var i = 0; i < allCircleMarkers.length; i++) {
        allCircleMarkers[i].setRadius(size.radius);
        allCircleMarkers[i].setStyle({ weight: size.weight });
    }
    updateAdaptivePopupSize();
});

map.on('popupopen', function(e) {
    if (e && e.popup && e.popup.update) {
        setTimeout(function() {
            e.popup.update();
        }, 15);
    }
});

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
        '<span class="layer-ctrl-title font-weight-bold">Pilihan layer</span>' +
        '<button type="button" class="layer-ctrl-toggle-btn" aria-label="Minimize Layer">Sembunyikan</button>';

    var formList = layerContainer.querySelector('.leaflet-control-layers-list');
    if (formList) {
        formList.classList.add('map-ctrl-body');
        layerContainer.insertBefore(layerHeader, formList);
    }

    layerHeader.addEventListener('click', function(e) {
        e.stopPropagation();
        layerContainer.classList.toggle('is-minimized');
        var btn = layerContainer.querySelector('.layer-ctrl-toggle-btn');
        if (btn) {
            btn.textContent = layerContainer.classList.contains('is-minimized') ? 'Tampilkan' : 'Sembunyikan';
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
// Legenda dinonaktifkan sesuai permintaan pengguna
if (window.showMapLegend === true) {
    legend.addTo(map);
}

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
        map.fitBounds(mapBounds, { padding: [50, 50], maxZoom: 12.5 });
    } else {
        map.setView(defaultCenter, 8.5);
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

// Point-in-polygon test (ray casting) untuk menjamin popup berada di dalam area poligon
function isPointInPolygon(point, vs) {
    var x = point[0], y = point[1];
    var inside = false;
    for (var i = 0, j = vs.length - 1; i < vs.length; j = i++) {
        var xi = vs[i][0], yi = vs[i][1];
        var xj = vs[j][0], yj = vs[j][1];
        var intersect = ((yi > y) !== (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
        if (intersect) inside = !inside;
    }
    return inside;
}

// Menghitung centroid area poligon
function getPolygonCentroid(pts) {
    if (!pts || pts.length === 0) return null;
    var area = 0, cx = 0, cy = 0;
    for (var i = 0, j = pts.length - 1; i < pts.length; j = i++) {
        var p1 = pts[j], p2 = pts[i];
        var f = (p1[0] * p2[1]) - (p2[0] * p1[1]);
        area += f;
        cx += (p1[0] + p2[0]) * f;
        cy += (p1[1] + p2[1]) * f;
    }
    area = area * 0.5;
    if (Math.abs(area) < 1e-9) {
        var sx = 0, sy = 0;
        for (var k = 0; k < pts.length; k++) { sx += pts[k][0]; sy += pts[k][1]; }
        return [sx / pts.length, sy / pts.length];
    }
    var factor = 1 / (6 * area);
    return [cx * factor, cy * factor];
}

// Menghitung koordinat penempatan akurat di dalam geometri objek (anti-meleset ke ruang kosong / laut)
function getFeatureAccurateLatLng(feat, layer) {
    if (!feat || !feat.geometry) {
        if (layer && layer.getLatLng) return layer.getLatLng();
        if (layer && layer.getBounds && layer.getBounds().isValid && layer.getBounds().isValid()) {
            return layer.getBounds().getCenter();
        }
        return null;
    }

    var geom = feat.geometry;
    var type = geom.type;
    var coords = geom.coordinates;

    if (type === 'Point' && coords && coords.length >= 2) {
        return L.latLng(coords[1], coords[0]);
    }

    if (type === 'LineString' && coords && coords.length > 0) {
        var midIdx = Math.floor(coords.length / 2);
        return L.latLng(coords[midIdx][1], coords[midIdx][0]);
    }

    if (type === 'MultiLineString' && coords && coords.length > 0) {
        var line = coords[Math.floor(coords.length / 2)] || coords[0];
        if (line && line.length > 0) {
            var m = Math.floor(line.length / 2);
            return L.latLng(line[m][1], line[m][0]);
        }
    }

    if (type === 'Polygon' || type === 'MultiPolygon') {
        var ring = null;
        if (type === 'Polygon' && coords && coords.length > 0) {
            ring = coords[0];
        } else if (type === 'MultiPolygon' && coords && coords.length > 0) {
            var maxLen = 0;
            coords.forEach(function(poly) {
                if (poly && poly[0] && poly[0].length > maxLen) {
                    maxLen = poly[0].length;
                    ring = poly[0];
                }
            });
            if (!ring && coords[0] && coords[0][0]) ring = coords[0][0];
        }

        if (ring && ring.length >= 3) {
            var centroid = getPolygonCentroid(ring);
            if (centroid && isPointInPolygon(centroid, ring)) {
                return L.latLng(centroid[1], centroid[0]);
            }

            // Jika centroid berada di luar poligon (bentuk cekung/C/tapal kuda),
            // cari titik interior yang dijamin berada di dalam poligon
            for (var step = 1; step < Math.floor(ring.length / 2); step++) {
                for (var i = 0; i < ring.length - step; i += 2) {
                    var pA = ring[i];
                    var pB = ring[i + step];
                    var mid = [(pA[0] + pB[0]) / 2, (pA[1] + pB[1]) / 2];
                    if (isPointInPolygon(mid, ring)) {
                        return L.latLng(mid[1], mid[0]);
                    }
                }
            }

            var v = ring[Math.floor(ring.length / 2)];
            if (v && v.length >= 2) {
                return L.latLng(v[1], v[0]);
            }
        }
    }

    if (layer && layer.getBounds && layer.getBounds().isValid && layer.getBounds().isValid()) {
        return layer.getBounds().getCenter();
    }
    return null;
}

/**
 * Format nama field GIS menjadi label yang rapi dan mudah dibaca
 * (Menghindari kata terpotong seperti 'Nomenklatu' -> 'Nomenklatur')
 */
function formatAttributeLabel(key) {
    if (!key) return '';
    var map = {
        'Nomenklatu': 'Nomenklatur',
        'nomenklatu': 'Nomenklatur',
        'NOMENKLATU': 'Nomenklatur',
        'Ket': 'Keterangan',
        'ket': 'Keterangan',
        'KET': 'Keterangan',
        'OID_': 'ID Objek',
        'oid_': 'ID Objek',
        'FID': 'FID',
        'fid': 'FID',
        'Luas_Ha': 'Luas (Ha)',
        'LUAS_HA': 'Luas (Ha)',
        'D_I_R': 'Daerah Irigasi',
        'D_I': 'Daerah Irigasi',
        'Kewenangan': 'Kewenangan',
        'KEWENANGAN': 'Kewenangan',
        'Kabupaten': 'Kabupaten',
        'KABUPATEN': 'Kabupaten',
        'Name': 'Nama Objek',
        'NAME': 'Nama Objek',
        'Sumber': 'Sumber Data'
    };
    if (map[key]) return map[key];
    return key.replace(/_/g, ' ');
}

/**
 * Salin titik koordinat ke clipboard pengguna dengan umpan balik visual
 */
window.copyPopupCoord = function(lat, lng, btnEl) {
    var coordStr = Number(lat).toFixed(6) + ', ' + Number(lng).toFixed(6);
    function showCopied() {
        if (!btnEl) return;
        var prevHtml = btnEl.innerHTML;
        btnEl.innerHTML = '<i class="fas fa-check text-success mr-1"></i><span>Tersalin!</span>';
        btnEl.classList.add('is-copied');
        setTimeout(function() {
            btnEl.innerHTML = prevHtml;
            btnEl.classList.remove('is-copied');
        }, 1800);
    }
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(coordStr).then(showCopied).catch(function() {
            prompt('Salin koordinat berikut:', coordStr);
        });
    } else {
        prompt('Salin koordinat berikut:', coordStr);
    }
};

/**
 * Membangun konten HTML Card Popup interaktif (sesuai standar Gambar No. 2 & PUPR)
 * Digunakan saat marker di klik di peta maupun saat tombol "Fokus" di tabel ditekan.
 */
function buildFeatureCardPopup(cleanName, categoryBadge, properties, fileName, lat, lng) {
    var p = properties || {};
    var html = '<div class="bws-popup-card shadow-sm">' +
        '<div class="bws-popup-header-row d-flex align-items-center justify-content-between mb-1.5">' +
            '<div class="bws-popup-badge-wrap">' + categoryBadge + '</div>' +
        '</div>' +
        '<div class="bws-popup-title font-weight-bold mb-2">' + cleanName + '</div>' +
        '<table class="table table-sm table-bordered mb-2"><tbody>';

    var countProp = 0;
    for (var k in p) {
        if (Object.prototype.hasOwnProperty.call(p, k)) {
            var val = p[k];
            if (val !== null && val !== undefined && val !== '' && typeof val !== 'object') {
                if (countProp < 5) {
                    var displayLabel = formatAttributeLabel(k);
                    var kUpper = k.toUpperCase();
                    var valFormatted = val;

                    // Highlight atribut penting (Luas, Panjang, Debit)
                    if (kUpper.indexOf('LUAS') !== -1 || kUpper.indexOf('AREA') !== -1 || kUpper.indexOf('HA') !== -1) {
                        valFormatted = '<span class="badge badge-success px-1.5 py-0.5 font-weight-bold">' + val + '</span>';
                    } else if (kUpper.indexOf('PANJANG') !== -1 || kUpper.indexOf('LENGTH') !== -1) {
                        valFormatted = '<span class="badge badge-info px-1.5 py-0.5 font-weight-bold">' + val + '</span>';
                    }

                    html += '<tr><th class="bws-attr-th text-muted">' + displayLabel + '</th><td class="bws-attr-td font-weight-medium">' + valFormatted + '</td></tr>';
                    countProp++;
                }
            }
        }
    }

    var validLat = (lat !== null && lat !== undefined && !isNaN(lat)) ? Number(lat) : null;
    var validLng = (lng !== null && lng !== undefined && !isNaN(lng)) ? Number(lng) : null;

    // Jika GeoJSON tidak memiliki properties bawaan, tampilkan koordinat pusat & nama file
    if (countProp === 0) {
        var latStr = validLat ? validLat.toFixed(5) : '-';
        var lngStr = validLng ? validLng.toFixed(5) : '-';
        html += '<tr><th class="bws-attr-th">Latitude (Y)</th><td class="bws-attr-td"><code>' + latStr + '</code></td></tr>' +
                '<tr><th class="bws-attr-th">Longitude (X)</th><td class="bws-attr-td"><code>' + lngStr + '</code></td></tr>';
        if (fileName) {
            html += '<tr><th class="bws-attr-th">Sumber File</th><td class="bws-attr-td text-monospace small">' + fileName + '</td></tr>';
        }
    }

    html += '</tbody></table>';

    // Tombol Aksi Lapangan: "Salin Koordinat" dan "Buka di Google Maps"
    if (validLat !== null && validLng !== null) {
        var gmapsUrl = 'https://www.google.com/maps/search/?api=1&query=' + validLat.toFixed(6) + ',' + validLng.toFixed(6);
        html += '<div class="bws-popup-actions d-flex gap-2 pt-1 border-top">' +
            '<button type="button" class="bws-popup-act-btn bws-btn-copy btn btn-sm btn-outline-secondary flex-grow-1" onclick="copyPopupCoord(' + validLat + ', ' + validLng + ', this)" title="Salin titik koordinat (Latitude, Longitude)">' +
                '<i class="far fa-copy mr-1"></i><span>Salin Koordinat</span>' +
            '</button>' +
            '<a href="' + gmapsUrl + '" target="_blank" rel="noopener noreferrer" class="bws-popup-act-btn bws-btn-gmaps btn btn-sm btn-outline-primary flex-grow-1" title="Buka rute navigasi dan lokasi fisik di Google Maps">' +
                '<i class="fas fa-location-arrow mr-1"></i><span>Google Maps</span>' +
            '</a>' +
        '</div>';
    }

    html += '</div>';
    return html;
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
var currentCategoryFilter = 'all';
var currentTablePage = 1;
var tablePageSize = 10;
if (window.geojsonFiles && window.geojsonFiles.length > 0) {
    var loadedCount = 0;

    // Safety timeout: jika ada kendala jaringan/file corrupt, pastikan loading indikator ditutup maksimal 8 detik
    setTimeout(function() {
        if (mapLoadingEl && mapLoadingEl.style.display !== 'none') {
            mapLoadingEl.style.display = 'none';
            if (mapBounds.isValid()) map.fitBounds(mapBounds, { padding: [50, 50], maxZoom: 12.5 });
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
                        strokeColor = '#15803d'; fillColor = '#22c55e';   // hijau terang, border hijau tua
                    } else if (nameUpper.indexOf('FUNGSIONAL') === 0) {
                        targetLayer = layerFungsional; categoryType = 'fungsional';
                        categoryBadge = '<span class="badge badge-fungsional px-2 py-1">DIR Fungsional</span>';
                        strokeColor = '#1e40af'; fillColor = '#3b82f6';   // biru terang, border biru tua
                    } else if (nameUpper.indexOf('POTENSIAL') === 0) {
                        targetLayer = layerPotensial; categoryType = 'potensial';
                        categoryBadge = '<span class="badge badge-potensial px-2 py-1">DIR Potensial</span>';
                        strokeColor = '#b45309'; fillColor = '#f59e0b';   // oranye terang, border oranye tua
                    } else if (nameUpper.indexOf('SALURAN') === 0) {
                        targetLayer = layerSaluran; categoryType = 'saluran';
                        categoryBadge = '<span class="badge badge-saluran px-2 py-1">Saluran Irigasi</span>';
                        strokeColor = '#0369a1'; fillColor = '#38bdf8';   // biru muda terang, border biru tua
                        isSaluran = true;
                    } else if (nameUpper.indexOf('BANGUNAN') === 0) {
                        targetLayer = layerBangunan; categoryType = 'bangunan';
                        categoryBadge = '<span class="badge badge-bangunan px-2 py-1">Bangunan Air</span>';
                        strokeColor = '#991b1b'; fillColor = '#ef4444';   // merah terang, border merah tua
                    } else {
                        targetLayer = layerBaku; categoryType = 'baku';
                        categoryBadge = '<span class="badge badge-baku px-2 py-1">DIR Baku</span>';
                    }
                }

                var gjLayer = L.geoJSON(gj, {
                    style: function(feature) {
                        var geomType = feature && feature.geometry ? feature.geometry.type : '';
                        var isLine = geomType.indexOf('Line') !== -1;
                        if (isLine || isSaluran) {
                            // Garis saluran irigasi: tebal & jelas
                            return {
                                color: strokeColor,
                                weight: 3,
                                opacity: 0.85,
                                fillOpacity: 0
                            };
                        }
                        // Polygon area: fill sangat transparan, border tegas
                        return {
                            color: strokeColor,
                            weight: 2,
                            opacity: 0.9,
                            fillColor: fillColor,
                            fillOpacity: 0.18
                        };
                    },
                    pointToLayer: function(feat, latlng) {
                        // Titik bersih tanpa nomor: ukuran adaptif sesuai zoom agar tidak bertumpuk saat layar diperkecil
                        var currentZoom = map.getZoom ? map.getZoom() : 8;
                        var sz = (typeof getAdaptiveMarkerSize === 'function')
                            ? getAdaptiveMarkerSize(currentZoom)
                            : { radius: 3, weight: 1 };

                        var cm = L.circleMarker(latlng, {
                            radius: sz.radius,
                            fillColor: fillColor,
                            color: '#ffffff',
                            weight: sz.weight,
                            fillOpacity: 0.9,
                            opacity: 1
                        });
                        allCircleMarkers.push(cm);
                        return cm;
                    },
                    onEachFeature: function(feat, layer) {
                        var p = feat.properties || {};
                        var pLat = null, pLng = null;
                        if (feat.geometry && feat.geometry.type === 'Point' && feat.geometry.coordinates) {
                            pLng = feat.geometry.coordinates[0];
                            pLat = feat.geometry.coordinates[1];
                        } else if (layer.getBounds && layer.getBounds().isValid && layer.getBounds().isValid()) {
                            var centerPt = layer.getBounds().getCenter();
                            pLat = centerPt.lat;
                            pLng = centerPt.lng;
                        } else if (layer.getLatLng) {
                            var ll = layer.getLatLng();
                            pLat = ll.lat;
                            pLng = ll.lng;
                        }
                        var popup = buildFeatureCardPopup(cleanName, categoryBadge, p, fileName, pLat, pLng);
                        layer.bindPopup(popup, { autoPan: true, autoPanPadding: [50, 50] });
                    }
                });

                if (targetLayer) targetLayer.addLayer(gjLayer);

                var b = gjLayer.getBounds();
                if (b.isValid()) {
                    mapBounds.extend(b);
                    var center = b.getCenter();

                    // Hitung koordinat penempatan akurat di dalam geometri objek (anti-meleset ke ruang kosong)
                    var accuratePt = null;
                    if (gj.features && gj.features.length > 0) {
                        accuratePt = getFeatureAccurateLatLng(gj.features[0], gjLayer);
                    }
                    var finalLat = accuratePt ? accuratePt.lat : center.lat;
                    var finalLng = accuratePt ? accuratePt.lng : center.lng;

                    allTableRows.push({
                        name: cleanName,
                        categoryType: categoryType,
                        categoryBadge: categoryBadge,
                        lat: finalLat,
                        lng: finalLng,
                        accurateLatLng: accuratePt || center,
                        fileName: fileName,
                        bounds: b,
                        layerObj: gjLayer,
                        targetLayer: targetLayer,
                        features: gj.features,
                        properties: (gj.features && gj.features[0] && gj.features[0].properties) ? gj.features[0].properties : {}
                    });
                }
            })
            .catch(function(err) {})
            .finally(function() {
                loadedCount++;
                if (loadedCount >= window.geojsonFiles.length) {
                    if (mapLoadingEl) mapLoadingEl.style.display = 'none';
                    if (mapBounds.isValid()) map.fitBounds(mapBounds, { padding: [50, 50], maxZoom: 12.5 });
                    renderTable();
                    applyUrlLayerFilter();
                    if (typeof initSidebarSearch === 'function') initSidebarSearch();
                }
            });
    });
} else {
    if (mapLoadingEl) mapLoadingEl.style.display = 'none';
    renderTable();
    applyUrlLayerFilter();
    if (typeof initSidebarSearch === 'function') initSidebarSearch();
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
        map.fitBounds(layerBounds, { padding: [50, 50], maxZoom: 12.5 });
    }
}

/* ============================================================
   6. RENDER DATA TABLE (DESAIN MODERN SESUAI GAMBAR NO. 2)
   Fungsi: Mengisi baris tabel data spasial dengan 5 kolom bersih,
   filter pill kategori, deteksi wilayah/kabupaten, dan paginasi 10 baris.
   ============================================================ */
function detectWilayah(name, fileName, props) {
    var str = ((name || '') + ' ' + (fileName || '') + ' ' + (props ? JSON.stringify(props) : '')).toUpperCase();
    if (str.indexOf('SAMBAS') !== -1 || str.indexOf('PEMANGKAT') !== -1 || str.indexOf('SEBANGKAU') !== -1 ||
        str.indexOf('SEBUBUS') !== -1 || str.indexOf('SEMELAGI') !== -1 || str.indexOf('SARANG BURUNG') !== -1 ||
        str.indexOf('SARANG_BURUNG') !== -1 || str.indexOf('PMK') !== -1 || str.indexOf('SBK') !== -1 ||
        str.indexOf('SBB') !== -1 || str.indexOf('SML') !== -1) {
        return 'Kab. Sambas';
    }
    if (str.indexOf('KETAPANG') !== -1 || str.indexOf('PEMATANG GADUNG') !== -1 || str.indexOf('PEMATANG_GADUNG') !== -1 ||
        str.indexOf('SUNGAI KINJIL') !== -1 || str.indexOf('SUNGAI_KINJIL') !== -1 || str.indexOf('TELUK BAYUR') !== -1 ||
        str.indexOf('TB.JSON') !== -1 || str.indexOf('SKJ') !== -1 || str.indexOf('PGI') !== -1) {
        return 'Kab. Ketapang';
    }
    if (str.indexOf('KUBU') !== -1 || str.indexOf('KERAWANG') !== -1 || str.indexOf('KRW') !== -1) {
        return 'Kab. Kubu Raya';
    }
    if (str.indexOf('MEMPAWAH') !== -1) {
        return 'Kab. Mempawah';
    }
    if (str.indexOf('LANDAK') !== -1) {
        return 'Kab. Landak';
    }
    if (str.indexOf('BENGKAYANG') !== -1) {
        return 'Kab. Bengkayang';
    }
    if (str.indexOf('SINGKAWANG') !== -1) {
        return 'Kota Singkawang';
    }
    if (str.indexOf('PONTIANAK') !== -1) {
        return 'Kota Pontianak';
    }
    if (props) {
        if (props.KABUPATEN) return 'Kab. ' + props.KABUPATEN;
        if (props.Kabupaten) return 'Kab. ' + props.Kabupaten;
        if (props.KECAMATAN) return 'Kec. ' + props.KECAMATAN;
    }
    return 'Kalimantan Barat';
}

function getCategoryPlainLabel(catType) {
    if (catType === 'baku') return 'DIR Baku';
    if (catType === 'fungsional') return 'DIR Fungsional';
    if (catType === 'potensial') return 'DIR Potensial';
    if (catType === 'saluran') return 'Saluran';
    if (catType === 'bangunan') return 'Bangunan Air';
    return 'Data Spasial';
}

window.filterTableByPill = function(category, btnEl) {
    currentCategoryFilter = category;
    currentTablePage = 1;
    var pills = document.querySelectorAll('.bws-pill-btn');
    pills.forEach(function(p) { p.classList.remove('active'); });
    if (btnEl) btnEl.classList.add('active');
    renderTable();
};

window.changeTablePage = function(page) {
    currentTablePage = page;
    renderTable();
    var tbl = document.getElementById('section-table');
    if (tbl) tbl.scrollIntoView({ behavior: 'smooth', block: 'start' });
};

function renderPagination(totalPages, currentPage) {
    var pagEl = document.getElementById('tabel-pagination');
    if (!pagEl) return;
    if (totalPages <= 1) {
        pagEl.innerHTML = '';
        return;
    }

    var html = '';
    // Tombol Previous «
    if (currentPage > 1) {
        html += '<button type="button" class="bws-pg-btn" onclick="changeTablePage(' + (currentPage - 1) + ')" title="Halaman Sebelumnya">&laquo;</button>';
    } else {
        html += '<button type="button" class="bws-pg-btn disabled" disabled>&laquo;</button>';
    }

    // Nomor Halaman
    var pages = [];
    if (totalPages <= 7) {
        for (var p = 1; p <= totalPages; p++) pages.push(p);
    } else {
        pages.push(1);
        if (currentPage > 3) pages.push('...');
        var start = Math.max(2, currentPage - 1);
        var end = Math.min(totalPages - 1, currentPage + 1);
        for (var i = start; i <= end; i++) pages.push(i);
        if (currentPage < totalPages - 2) pages.push('...');
        pages.push(totalPages);
    }

    pages.forEach(function(p) {
        if (p === '...') {
            html += '<span class="bws-pg-dots">&hellip;</span>';
        } else if (p === currentPage) {
            html += '<button type="button" class="bws-pg-btn active">' + p + '</button>';
        } else {
            html += '<button type="button" class="bws-pg-btn" onclick="changeTablePage(' + p + ')">' + p + '</button>';
        }
    });

    // Tombol Next »
    if (currentPage < totalPages) {
        html += '<button type="button" class="bws-pg-btn" onclick="changeTablePage(' + (currentPage + 1) + ')" title="Halaman Selanjutnya">&raquo;</button>';
    } else {
        html += '<button type="button" class="bws-pg-btn disabled" disabled>&raquo;</button>';
    }

    pagEl.innerHTML = html;
}

function renderTable() {
    var tbody = document.getElementById('tbody-lokasi');
    var footerInfo = document.getElementById('tabel-footer-info');
    if (!tbody) return;

    var filtered = allTableRows.filter(function(row) {
        // Filter kategori via pill
        if (currentCategoryFilter !== 'all') {
            if (row.categoryType !== currentCategoryFilter) return false;
        }
        // Filter teks pencarian
        if (currentSearchText === '') return true;
        var term = currentSearchText.toLowerCase();
        var wil = detectWilayah(row.name, row.fileName, row.properties).toLowerCase();
        return (row.name && row.name.toLowerCase().indexOf(term) !== -1) ||
               (row.fileName && row.fileName.toLowerCase().indexOf(term) !== -1) ||
               (row.categoryType && row.categoryType.toLowerCase().indexOf(term) !== -1) ||
               (wil.indexOf(term) !== -1);
    });

    var totalItems = filtered.length;
    var totalPages = Math.ceil(totalItems / tablePageSize) || 1;
    if (currentTablePage > totalPages) currentTablePage = totalPages;
    if (currentTablePage < 1) currentTablePage = 1;

    var startIdx = (currentTablePage - 1) * tablePageSize;
    var endIdx = Math.min(startIdx + tablePageSize, totalItems);
    var pageItems = filtered.slice(startIdx, endIdx);

    // Update info footer
    if (footerInfo) {
        if (totalItems === 0) {
            footerInfo.innerHTML = 'Tidak ada data pos &amp; file GIS yang sesuai.';
        } else {
            footerInfo.innerHTML = 'Menampilkan <b>' + (startIdx + 1) + ' - ' + endIdx + '</b> dari <b>' + totalItems + '</b> data pos &amp; file GIS';
        }
    }

    // Update badge jumlah di header toolbar
    var totalBadge = document.getElementById('badge-total-file-tabel');
    if (totalBadge) totalBadge.innerText = allTableRows.length + ' File';
    var pillCountAll = document.getElementById('pill-count-all');
    if (pillCountAll) pillCountAll.innerText = allTableRows.length;

    // Render pagination
    renderPagination(totalPages, currentTablePage);

    if (filtered.length === 0) {
        if (allTableRows.length === 0) {
            var actionBtnHtml = (window.canManageGis)
                ? '<p class="small text-muted mb-3">Silakan klik tombol "Upload GeoJSON" untuk menambahkan file koordinat dan layer ke peta.</p>' +
                  '<button type="button" class="btn btn-sm btn-primary font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalUploadGeojson">' +
                  '<i class="fas fa-cloud-upload-alt mr-1"></i> Upload GeoJSON Sekarang</button>'
                : '<p class="small text-muted mb-0">Data layer dan koordinat spasial belum diunggah oleh Administrator.</p>';

            tbody.innerHTML =
                '<tr><td colspan="5" class="text-center py-5 text-muted">' +
                '<div class="mb-2"><i class="fas fa-folder-open fa-3x text-gray-300"></i></div>' +
                '<div class="font-weight-bold text-gray-700 mb-1" style="font-size:15px;">Belum Ada Data GIS Tersimpan</div>' +
                actionBtnHtml +
                '</td></tr>';
        } else {
            tbody.innerHTML =
                '<tr><td colspan="5" class="text-center py-4 text-muted">' +
                '<i class="fas fa-info-circle mr-2 text-info"></i>Tidak ditemukan data GIS yang cocok dengan kriteria pencarian.' +
                '</td></tr>';
        }
        return;
    }

    var frag = document.createDocumentFragment();
    pageItems.forEach(function(row, idx) {
        var globalIdx = startIdx + idx + 1;
        var tr = document.createElement('tr');
        var latStr = (row.lat !== null && !isNaN(row.lat)) ? Number(row.lat).toFixed(5) : '-';
        var lngStr = (row.lng !== null && !isNaN(row.lng)) ? Number(row.lng).toFixed(5) : '-';
        var wilayah = detectWilayah(row.name, row.fileName, row.properties);
        var catLabel = getCategoryPlainLabel(row.categoryType);

        var coordHtml = (latStr !== '-' && lngStr !== '-')
            ? '<span class="bws-coord-badge"><i class="fas fa-map-pin text-danger mr-1" style="font-size:11px;"></i> ' + latStr + ', ' + lngStr + '</span>'
            : '<span class="text-muted">-</span>';

        var downloadBtnHtml = (window.canManageGis)
            ? '<a href="/peta/download-file/' + encodeURIComponent(row.fileName) + '" class="btn-download-row ml-1" title="Unduh GeoJSON (' + row.fileName + ')"><i class="fas fa-download"></i></a>'
            : '';

        tr.innerHTML =
            '<td class="text-center font-weight-bold text-muted" style="font-size:12.5px;">' + globalIdx + '</td>' +
            '<td>' +
                '<div class="bws-row-name">' + row.name + '</div>' +
                '<div class="bws-row-sub"><i class="fas fa-map-marker-alt text-muted mr-1" style="font-size:11px;"></i> ' + wilayah + '</div>' +
            '</td>' +
            '<td><span class="bws-category-plain">' + catLabel + '</span></td>' +
            '<td>' + coordHtml + '</td>' +
            '<td class="text-center" style="white-space:nowrap;">' +
                '<button type="button" class="bws-btn-fokus btn-focus-row" title="Fokuskan ke peta">' +
                '<i class="fas fa-crosshairs mr-1"></i> Fokus</button>' +
                downloadBtnHtml +
            '</td>';

        // Tombol Fokus: klik → langsung menuju ke peta dan tampilkan card popup (seperti Gambar No. 2)
        var btn = tr.querySelector('.btn-focus-row');
        btn.addEventListener('click', function() {
            focusOnMapObject(row);
        });

        frag.appendChild(tr);
    });
    tbody.innerHTML = '';
    tbody.appendChild(frag);
}

/**
 * Fokus ke objek pada peta (dipanggil saat klik tombol Fokus tabel atau klik item di sidebar search)
 */
function focusOnMapObject(row) {
    if (!row || !map) return;

    // 1. Pastikan tampilan peta aktif jika sebelumnya ada tab switcher
    var isV2 = document.body.classList.contains('page-peta-v2');
    if (isV2) {
        if (window.switchPetaTab) window.switchPetaTab('peta');
    } else {
        var filterViewMode = document.getElementById('filterViewMode');
        if (filterViewMode && filterViewMode.value === 'table') {
            filterViewMode.value = 'both';
            if (window.switchViewMode) window.switchViewMode('both');
        } else {
            var mapSec = document.getElementById('section-map');
            var tblSec = document.getElementById('section-table');
            if (mapSec && mapSec.style.display === 'none') {
                mapSec.style.display = 'block';
                if (tblSec) tblSec.style.display = 'block';
                var mapEl = document.getElementById('map');
                if (mapEl) mapEl.style.height = '420px';
            }
        }
    }
    if (map && map.invalidateSize) {
        map.invalidateSize();
    }

    // 2. Scroll mulus tepat ke posisi peta jika tertutup
    var mapSection = document.getElementById('section-map');
    if (mapSection) {
        var navHeight = isV2 ? 64 : 76;
        var rect = mapSection.getBoundingClientRect();
        var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        var targetTop = rect.top + scrollTop - navHeight - 10;
        window.scrollTo({
            top: Math.max(0, targetTop),
            behavior: 'smooth'
        });
    }

    // 3. Pastikan Layer Group untuk file ini aktif di peta
    if (row.targetLayer && !map.hasLayer(row.targetLayer)) {
        map.addLayer(row.targetLayer);
    }
    if (row.fileName && customFileMap && customFileMap[row.fileName]) {
        if (!map.hasLayer(customFileMap[row.fileName].layerGroup)) {
            map.addLayer(customFileMap[row.fileName].layerGroup);
        }
    }

    // 4. Cari titik koordinat penempatan akurat di dalam geometri objek (bukan bounding box luar)
    var targetLatLng = row.accurateLatLng || ((row.lat !== null && row.lng !== null && !isNaN(row.lat) && !isNaN(row.lng))
        ? L.latLng(row.lat, row.lng)
        : null);
    var targetSubLayer = null;

    if (row.layerObj && row.layerObj.getLayers) {
        var subLayers = row.layerObj.getLayers();
        if (subLayers.length > 0) {
            targetSubLayer = subLayers[0];
            if (targetSubLayer.feature) {
                var accurate = getFeatureAccurateLatLng(targetSubLayer.feature, targetSubLayer);
                if (accurate) targetLatLng = accurate;
            }
        }
    }

    if (!targetLatLng && row.bounds && row.bounds.isValid && row.bounds.isValid()) {
        targetLatLng = row.bounds.getCenter();
    }

    // 5. Tampilkan Card Popup tepat saat pergerakan peta selesai (akurat anti-meleset)
    var popupTriggered = false;
    function displayFocusPopup() {
        if (popupTriggered) return;
        popupTriggered = true;
        if (!targetLatLng) return;

        var cardContent = '';
        if (targetSubLayer && targetSubLayer.getPopup && targetSubLayer.getPopup()) {
            cardContent = targetSubLayer.getPopup().getContent();
        }
        if (!cardContent) {
            var props = (targetSubLayer && targetSubLayer.feature && targetSubLayer.feature.properties)
                ? targetSubLayer.feature.properties
                : (row.properties || {});
            cardContent = buildFeatureCardPopup(
                row.name,
                row.categoryBadge,
                props,
                row.fileName,
                targetLatLng.lat,
                targetLatLng.lng
            );
        }

        var popupOffset = [0, -10];
        if (targetSubLayer && targetSubLayer.options && targetSubLayer.options.radius) {
            popupOffset = [0, -targetSubLayer.options.radius - 4];
        }

        L.popup({
            offset: popupOffset,
            closeButton: true,
            autoClose: true,
            className: 'bws-focus-popup'
        })
        .setLatLng(targetLatLng)
        .setContent(cardContent)
        .openOn(map);
    }

    // Jalankan animasi zoom peta (dibatasi maxZoom 12.5 agar tidak terlalu besar sesuai Gambar 4)
    if (row.bounds && row.bounds.isValid && row.bounds.isValid()) {
        map.fitBounds(row.bounds, { padding: [60, 60], maxZoom: 12.5 });
    } else if (targetLatLng) {
        map.flyTo(targetLatLng, 12.5, { animate: true, duration: 0.8 });
    }

    map.once('moveend', function() {
        setTimeout(displayFocusPopup, 60);
    });
    setTimeout(displayFocusPopup, 650);
}
window.focusOnMapObject = focusOnMapObject;

/* ============================================================
   7. PENCARIAN SIDEBAR DENGAN AUTOCOMPLETE & LIVE DROPDOWN
   Fungsi: Menampilkan dropdown saran objek saat pengguna mengetik
   di sidebar, dan langsung zoom + buka card popup saat dipilih.
   ============================================================ */
function initSidebarSearch() {
    var input    = document.getElementById('cari-sidebar');
    var clearBtn = document.getElementById('cari-sidebar-clear');
    var dropdown = document.getElementById('cari-sidebar-dropdown');
    var listEl   = document.getElementById('cari-sidebar-list');
    var countEl  = document.getElementById('cari-sidebar-count');

    if (!input || !dropdown || !listEl) return;

    var currentMatches = [];

    function performSearch(query) {
        query = (query || '').trim().toLowerCase();
        if (!query) {
            if (clearBtn) clearBtn.style.display = 'none';
            dropdown.style.display = 'none';
            currentMatches = [];
            return;
        }

        if (clearBtn) clearBtn.style.display = 'inline-block';

        if (!allTableRows || allTableRows.length === 0) {
            listEl.innerHTML = '<div class="psb-search-no-result"><i class="fas fa-spinner fa-spin mr-1"></i> Memuat data GIS...</div>';
            dropdown.style.display = 'block';
            return;
        }

        currentMatches = allTableRows.filter(function(row) {
            var wil = (typeof detectWilayah === 'function')
                ? detectWilayah(row.name, row.fileName, row.properties).toLowerCase()
                : '';
            var nameMatch = row.name && row.name.toLowerCase().indexOf(query) !== -1;
            var fileMatch = row.fileName && row.fileName.toLowerCase().indexOf(query) !== -1;
            var catMatch  = row.categoryType && row.categoryType.toLowerCase().indexOf(query) !== -1;
            var wilMatch  = wil && wil.indexOf(query) !== -1;
            return nameMatch || fileMatch || catMatch || wilMatch;
        });

        if (countEl) {
            countEl.textContent = currentMatches.length + ' objek ditemukan';
        }

        if (currentMatches.length === 0) {
            listEl.innerHTML = '<div class="psb-search-no-result"><i class="fas fa-search-minus mr-1 text-muted"></i> Tidak ada objek irigasi yang cocok</div>';
            dropdown.style.display = 'block';
            return;
        }

        var maxShow = Math.min(currentMatches.length, 15);
        var html = '';
        for (var i = 0; i < maxShow; i++) {
            var row = currentMatches[i];
            var wil = (typeof detectWilayah === 'function')
                ? detectWilayah(row.name, row.fileName, row.properties)
                : 'Kalimantan Barat';
            var catLabel = (typeof getCategoryPlainLabel === 'function')
                ? getCategoryPlainLabel(row.categoryType)
                : (row.categoryType || 'Data Spasial');

            var badgeColor = '#2563eb';
            var badgeBg = '#dbeafe';
            var iconClass = 'fa-map-marker-alt';

            if (row.categoryType === 'baku') {
                badgeColor = '#16a34a'; badgeBg = '#dcfce7'; iconClass = 'fa-leaf';
            } else if (row.categoryType === 'fungsional') {
                badgeColor = '#2563eb'; badgeBg = '#dbeafe'; iconClass = 'fa-layer-group';
            } else if (row.categoryType === 'potensial') {
                badgeColor = '#d97706'; badgeBg = '#fef3c7'; iconClass = 'fa-seedling';
            } else if (row.categoryType === 'saluran') {
                badgeColor = '#0284c7'; badgeBg = '#e0f2fe'; iconClass = 'fa-water';
            } else if (row.categoryType === 'bangunan') {
                badgeColor = '#dc2626'; badgeBg = '#fee2e2'; iconClass = 'fa-cube';
            }

            html += '<div class="psb-search-item" data-idx="' + i + '">' +
                '<div class="psb-search-item-icon" style="background:' + badgeBg + '; color:' + badgeColor + ';">' +
                    '<i class="fas ' + iconClass + '"></i>' +
                '</div>' +
                '<div class="psb-search-item-info">' +
                    '<div class="psb-search-item-name" title="' + (row.name || '') + '">' + (row.name || '') + '</div>' +
                    '<div class="psb-search-item-sub">' +
                        '<span class="psb-search-badge" style="background:' + badgeBg + '; color:' + badgeColor + ';">' + catLabel + '</span>' +
                        '<span class="text-muted"><i class="fas fa-map-marker-alt mr-1" style="font-size:9px;"></i>' + wil + '</span>' +
                    '</div>' +
                '</div>' +
                '<i class="fas fa-chevron-right text-muted" style="font-size:10px;"></i>' +
            '</div>';
        }

        if (currentMatches.length > maxShow) {
            html += '<div class="psb-search-hint text-center py-1" style="background:#f8fafc; font-size:10px; color:#64748b; border-top:1px solid #f1f5f9;">' +
                '+ ' + (currentMatches.length - maxShow) + ' objek lainnya... Ketik lebih spesifik' +
            '</div>';
        }

        listEl.innerHTML = html;
        dropdown.style.display = 'block';

        // Event listener klik setiap item hasil pencarian
        var items = listEl.querySelectorAll('.psb-search-item');
        items.forEach(function(itemEl) {
            itemEl.addEventListener('click', function(e) {
                e.stopPropagation();
                var idx = parseInt(this.getAttribute('data-idx'), 10);
                var selected = currentMatches[idx];
                if (selected) {
                    input.value = selected.name;
                    dropdown.style.display = 'none';
                    if (window.focusOnMapObject) {
                        window.focusOnMapObject(selected);
                    }
                }
            });
        });
    }

    // Input realtime
    input.addEventListener('input', function() {
        performSearch(this.value);
        if (typeof window.filterTabelKoordinat === 'function') {
            window.filterTabelKoordinat(this.value);
        }
        if (typeof window.syncSearchInputs === 'function') {
            window.syncSearchInputs(this.value);
        }
    });

    // Saat fokus kembali ke input jika ada teks
    input.addEventListener('focus', function() {
        if (this.value && this.value.trim().length > 0) {
            performSearch(this.value);
        }
    });

    // Navigasi keyboard (Enter)
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (currentMatches && currentMatches.length > 0) {
                var topMatch = currentMatches[0];
                input.value = topMatch.name;
                dropdown.style.display = 'none';
                if (window.focusOnMapObject) {
                    window.focusOnMapObject(topMatch);
                }
            }
        } else if (e.key === 'Escape') {
            dropdown.style.display = 'none';
        }
    });

    // Tombol Clear (X)
    if (clearBtn) {
        clearBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            input.value = '';
            clearBtn.style.display = 'none';
            dropdown.style.display = 'none';
            currentMatches = [];
            input.focus();
            if (typeof window.filterTabelKoordinat === 'function') {
                window.filterTabelKoordinat('');
            }
            if (typeof window.syncSearchInputs === 'function') {
                window.syncSearchInputs('');
            }
        });
    }

    // Klik di luar dropdown → tutup
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.psb-search-wrap')) {
            dropdown.style.display = 'none';
        }
    });
}
window.initSidebarSearch = initSidebarSearch;

// Inisialisasi search sidebar saat DOM siap
document.addEventListener('DOMContentLoaded', function() {
    initSidebarSearch();
});

/* ============================================================
   8. PENCARIAN DATA REALTIME (TABEL)
   ============================================================ */
window.filterTabelKoordinat = function(val) {
    currentSearchText = (val || '').trim();
    currentTablePage = 1;
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

    /* ============================================================
       SISTEM PEMILAHAN BERKAS GIS (Interactive File & Category Selector)
       Sesuai Konsep:
       1. Pilih Berkas Spasial (.ZIP / .GeoJSON / .KML)
       2. Layer Terdeteksi di dalam Berkas (Pilih yang diunggah & Kategori)
       ============================================================ */
    window.triageLayers = [];
    var currentTriageArchiveName = '';

    function guessCategoryFromName(name) {
        var lower = (name || '').toLowerCase();
        if (lower.indexOf('saluran') !== -1 || lower.indexOf('sal_') !== -1 || lower.indexOf('sekunder') !== -1 || lower.indexOf('primer') !== -1 || lower.indexOf('tersier') !== -1) {
            return 'saluran';
        }
        if (lower.indexOf('bangunan') !== -1 || lower.indexOf('bgn_') !== -1 || lower.indexOf('pintu') !== -1 || lower.indexOf('bendung') !== -1 || lower.indexOf('intake') !== -1 || lower.indexOf('talang') !== -1 || lower.indexOf('gorong') !== -1) {
            return 'bangunan';
        }
        if (lower.indexOf('fungsional') !== -1 || lower.indexOf('fung_') !== -1) {
            return 'fungsional';
        }
        if (lower.indexOf('potensial') !== -1 || lower.indexOf('pot_') !== -1) {
            return 'potensial';
        }
        return 'baku';
    }

    function cleanDisplayName(name) {
        var clean = (name || '')
            .replace(/\.[^/.]+$/, "")
            .replace(/^(baku|fungsional|potensial|saluran|bangunan)[_\s-]*/i, '')
            .replace(/[_-]+/g, ' ')
            .trim();
        return clean.replace(/\b\w/g, function(l) { return l.toUpperCase(); });
    }

    window.resetTriageModal = function() {
        window.triageLayers = [];
        currentTriageArchiveName = '';

        var fileInput = document.getElementById('inputGeojsonFile');
        if (fileInput) fileInput.value = '';

        var label = document.getElementById('labelGeojsonFile');
        if (label) label.textContent = 'Pilih file .ZIP / .GeoJSON / .KML...';

        var loading = document.getElementById('step1InspectingLoading');
        if (loading) loading.style.display = 'none';

        var err1 = document.getElementById('step1ErrorAlert');
        if (err1) err1.style.display = 'none';

        var placeholder = document.getElementById('triagePlaceholder');
        if (placeholder) placeholder.style.display = 'block';

        var tableBox = document.getElementById('triageTableBox');
        if (tableBox) tableBox.style.display = 'none';

        var actionBtns = document.getElementById('triageActionButtons');
        if (actionBtns) actionBtns.style.display = 'none';

        var footerInfo = document.getElementById('triageFooterInfo');
        if (footerInfo) footerInfo.style.display = 'none';

        var progress = document.getElementById('uploadProgressSection');
        if (progress) progress.style.display = 'none';

        var success = document.getElementById('uploadSuccessSection');
        if (success) success.style.display = 'none';

        var err2 = document.getElementById('uploadErrorSection');
        if (err2) err2.style.display = 'none';

        var submitBtn = document.getElementById('btnSubmitTriage');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-arrow-up mr-1.5"></i>Upload Terpilih';
        }
    };

    // Reset saat modal dibuka atau ditutup
    $('#modalUploadGeojson').on('show.bs.modal', function() {
        resetTriageModal();
    });

    window.handleTriageFileSelection = async function(input) {
        if (!input || !input.files || input.files.length === 0) return;

        var rawFiles = Array.from(input.files);
        var label = document.getElementById('labelGeojsonFile');
        var loading = document.getElementById('step1InspectingLoading');
        var errorAlert = document.getElementById('step1ErrorAlert');
        var placeholder = document.getElementById('triagePlaceholder');
        var tableBox = document.getElementById('triageTableBox');
        var actionBtns = document.getElementById('triageActionButtons');
        var footerInfo = document.getElementById('triageFooterInfo');

        // Update teks label file input
        if (label) {
            label.textContent = rawFiles.length === 1 ? rawFiles[0].name : (rawFiles.length + ' berkas GIS dipilih');
        }

        // Cek file .rar
        var rarFiles = rawFiles.filter(function(f) { return f.name.toLowerCase().endsWith('.rar'); });
        if (rarFiles.length > 0) {
            if (errorAlert) {
                errorAlert.style.display = 'block';
                errorAlert.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i><strong>Format .RAR Tidak Didukung:</strong> ' + rarFiles.map(function(f){return f.name;}).join(', ') + '<br>' +
                    'Harap gunakan format <strong>.ZIP</strong> untuk Shapefile.';
            }
            input.value = '';
            return;
        }

        if (errorAlert) errorAlert.style.display = 'none';
        if (placeholder) placeholder.style.display = 'none';
        if (loading) loading.style.display = 'block';

        window.triageLayers = [];
        currentTriageArchiveName = rawFiles.length === 1 ? rawFiles[0].name : (rawFiles.length + ' Berkas GIS Dipilih');

        try {
            for (var i = 0; i < rawFiles.length; i++) {
                var file = rawFiles[i];
                var nameLower = file.name.toLowerCase();
                var baseName = file.name.replace(/\.[^/.]+$/, "");

                if (nameLower.endsWith('.zip')) {
                    if (typeof shp !== 'undefined') {
                        var buffer = await file.arrayBuffer();
                        var converted = await shp(buffer);

                        if (Array.isArray(converted)) {
                            converted.forEach(function(item, cIdx) {
                                var subName = item.fileName || (baseName + '_' + (cIdx + 1));
                                var geomType = item.features && item.features[0] && item.features[0].geometry ? item.features[0].geometry.type : 'Polygon';
                                var blob = new Blob([JSON.stringify(item)], { type: 'application/geo+json' });

                                window.triageLayers.push({
                                    id: window.triageLayers.length,
                                    originalName: subName,
                                    filenameWithExt: subName + '.shp',
                                    format: 'Shapefile',
                                    geomType: geomType,
                                    featureCount: item.features ? item.features.length : 0,
                                    displayName: cleanDisplayName(subName),
                                    category: guessCategoryFromName(subName),
                                    selected: true,
                                    fileBlob: blob
                                });
                            });
                        } else if (converted && converted.type === 'FeatureCollection') {
                            var subName = converted.fileName || baseName;
                            var geomType = converted.features && converted.features[0] && converted.features[0].geometry ? converted.features[0].geometry.type : 'Polygon';
                            var blob = new Blob([JSON.stringify(converted)], { type: 'application/geo+json' });

                            window.triageLayers.push({
                                id: window.triageLayers.length,
                                originalName: subName,
                                filenameWithExt: subName + '.shp',
                                format: 'Shapefile',
                                geomType: geomType,
                                featureCount: converted.features ? converted.features.length : 0,
                                displayName: cleanDisplayName(subName),
                                category: guessCategoryFromName(subName),
                                selected: true,
                                fileBlob: blob
                            });
                        }
                    } else {
                        window.triageLayers.push({
                            id: window.triageLayers.length,
                            originalName: file.name,
                            filenameWithExt: file.name,
                            format: 'ZIP Archive',
                            geomType: 'Unknown',
                            featureCount: 1,
                            displayName: cleanDisplayName(baseName),
                            category: guessCategoryFromName(baseName),
                            selected: true,
                            fileBlob: file
                        });
                    }
                } else if (nameLower.endsWith('.geojson') || nameLower.endsWith('.json')) {
                    var text = await file.text();
                    var parsed = JSON.parse(text);
                    var geomType = parsed.features && parsed.features[0] && parsed.features[0].geometry ? parsed.features[0].geometry.type : 'Feature';
                    var blob = new Blob([text], { type: 'application/geo+json' });

                    window.triageLayers.push({
                        id: window.triageLayers.length,
                        originalName: file.name,
                        filenameWithExt: file.name,
                        format: 'GeoJSON',
                        geomType: geomType,
                        featureCount: parsed.features ? parsed.features.length : 1,
                        displayName: cleanDisplayName(baseName),
                        category: guessCategoryFromName(baseName),
                        selected: true,
                        fileBlob: blob
                    });
                } else if ((nameLower.endsWith('.kml') || nameLower.endsWith('.kmz')) && typeof toGeoJSON !== 'undefined') {
                    var kmlText = await file.text();
                    var kmlDom = new DOMParser().parseFromString(kmlText, 'text/xml');
                    var kmlGeojson = toGeoJSON.kml(kmlDom);
                    var blob = new Blob([JSON.stringify(kmlGeojson)], { type: 'application/geo+json' });

                    window.triageLayers.push({
                        id: window.triageLayers.length,
                        originalName: file.name,
                        filenameWithExt: file.name,
                        format: nameLower.endsWith('.kmz') ? 'KMZ' : 'KML',
                        geomType: 'KML Vector',
                        featureCount: kmlGeojson.features ? kmlGeojson.features.length : 1,
                        displayName: cleanDisplayName(baseName),
                        category: guessCategoryFromName(baseName),
                        selected: true,
                        fileBlob: blob
                    });
                }
            }

            if (window.triageLayers.length === 0) {
                throw new Error('Tidak ditemukan layer spasial valid di dalam berkas yang dipilih.');
            }

            if (loading) loading.style.display = 'none';
            if (placeholder) placeholder.style.display = 'none';
            if (tableBox) tableBox.style.display = 'block';
            if (actionBtns) actionBtns.style.display = 'block';
            if (footerInfo) footerInfo.style.display = 'block';

            renderTriageTable();

        } catch (err) {
            console.error('Triage extraction error:', err);
            if (loading) loading.style.display = 'none';
            if (placeholder) placeholder.style.display = 'block';
            if (errorAlert) {
                errorAlert.style.display = 'block';
                errorAlert.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i><b>Gagal Membaca Berkas:</b> ' + err.message;
            }
            input.value = '';
        }
    };

    window.renderTriageTable = function() {
        var tbody = document.getElementById('triageTableBody');
        if (!tbody) return;

        var html = '';
        window.triageLayers.forEach(function(layer, idx) {
            var iconClass = 'fa-draw-polygon text-success';
            if (layer.format === 'Shapefile') iconClass = 'fa-draw-polygon text-info';
            else if (layer.format === 'KML' || layer.format === 'KMZ') iconClass = 'fa-globe-asia text-warning';
            else if (layer.format === 'GeoJSON') iconClass = 'fa-file-code text-primary';

            var isChecked = layer.selected;

            html += '<tr data-triage-idx="' + idx + '" class="' + (isChecked ? '' : 'table-light text-muted') + '">' +
                '<td style="width: 42px;" class="text-center align-middle">' +
                    '<input type="checkbox" style="width: 17px; height: 17px; cursor: pointer;" ' +
                        (isChecked ? 'checked' : '') +
                        ' onchange="onTriageCheckboxChange(' + idx + ', this.checked)">' +
                '</td>' +
                '<td class="align-middle">' +
                    '<div class="d-flex align-items-center flex-wrap">' +
                        '<i class="fas ' + iconClass + ' mr-2" style="font-size: 14px;"></i>' +
                        '<span class="font-weight-bold ' + (isChecked ? 'text-dark' : 'text-muted') + '">' + escapeHtml(layer.filenameWithExt) + '</span>' +
                        (!isChecked ? '<span class="badge badge-light text-muted border ml-2" style="font-size: 10px;">(Tidak Dicentang)</span>' : '') +
                    '</div>' +
                    '<small class="text-muted d-block mt-0.5" style="font-size: 11px;">' + layer.format + ' &bull; ' + layer.featureCount + ' fitur spasial</small>' +
                '</td>' +
                '<td style="width: 230px;" class="align-middle">' +
                    '<div class="d-flex align-items-center justify-content-end">' +
                        '<span class="text-muted small mr-2 font-weight-bold" style="font-size: 11px; white-space: nowrap;">Kategori:</span>' +
                        '<select class="form-control form-control-sm font-weight-bold" ' +
                            'onchange="onTriageCatChange(' + idx + ', this.value)" ' +
                            'style="border-radius: 6px; font-size: 12px; height: 32px; max-width: 165px;">' +
                            '<option value="baku" ' + (layer.category === 'baku' ? 'selected' : '') + '>🟢 DIR Baku</option>' +
                            '<option value="fungsional" ' + (layer.category === 'fungsional' ? 'selected' : '') + '>🔵 DIR Fungsional</option>' +
                            '<option value="potensial" ' + (layer.category === 'potensial' ? 'selected' : '') + '>🟠 DIR Potensial</option>' +
                            '<option value="saluran" ' + (layer.category === 'saluran' ? 'selected' : '') + '>🟣 Saluran Irigasi</option>' +
                            '<option value="bangunan" ' + (layer.category === 'bangunan' ? 'selected' : '') + '>🔴 Bangunan Air</option>' +
                        '</select>' +
                    '</div>' +
                '</td>' +
            '</tr>';
        });

        tbody.innerHTML = html;
        updateTriageCounter();
    };

    window.selectAllTriage = function(checked) {
        window.triageLayers.forEach(function(l) { l.selected = checked; });
        renderTriageTable();
    };

    window.onTriageCheckboxChange = function(idx, checked) {
        if (window.triageLayers[idx]) {
            window.triageLayers[idx].selected = checked;
            renderTriageTable();
        }
    };

    window.onTriageCatChange = function(idx, val) {
        if (window.triageLayers[idx]) {
            window.triageLayers[idx].category = val;
        }
    };

    window.updateTriageCounter = function() {
        var total = window.triageLayers.length;
        var selectedCount = window.triageLayers.filter(function(l) { return l.selected; }).length;

        var selectedBadge = document.getElementById('triageSelectedCountBadge');
        var totalBadge = document.getElementById('triageTotalCountBadge');
        var submitBtn = document.getElementById('btnSubmitTriage');

        if (selectedBadge) selectedBadge.textContent = selectedCount;
        if (totalBadge) totalBadge.textContent = total;

        if (submitBtn) {
            submitBtn.disabled = (selectedCount === 0);
            if (selectedCount === 0) {
                submitBtn.innerHTML = '<i class="fas fa-ban mr-1.5"></i>Upload Terpilih (0)';
            } else {
                submitBtn.innerHTML = '<i class="fas fa-arrow-up mr-1.5"></i>Upload Terpilih (' + selectedCount + ')';
            }
        }
    };

    window.submitTriageUpload = async function() {
        var selectedLayers = window.triageLayers.filter(function(l) { return l.selected; });
        if (selectedLayers.length === 0) {
            alert('Silakan centang setidaknya satu layer spasial untuk diunggah.');
            return;
        }

        var progressSection = document.getElementById('uploadProgressSection');
        var progressBar     = document.getElementById('uploadProgressBar');
        var percentText     = document.getElementById('uploadPercentText');
        var statusText      = document.getElementById('uploadStatusText');
        var successSection  = document.getElementById('uploadSuccessSection');
        var errorSection    = document.getElementById('uploadErrorSection');
        var submitBtn       = document.getElementById('btnSubmitTriage');
        var batalBtn        = document.getElementById('btnBatalUpload');
        var closeBtn        = document.getElementById('btnCloseUploadModal');

        if (errorSection) errorSection.style.display = 'none';
        if (successSection) successSection.style.display = 'none';
        if (progressSection) progressSection.style.display = 'block';

        if (submitBtn) { submitBtn.disabled = true; submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1.5"></i>Mengunggah...'; }
        if (batalBtn) batalBtn.disabled = true;
        if (closeBtn) closeBtn.style.display = 'none';

        var csrfMeta = document.querySelector('meta[name="csrf-token"]');
        var csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

        var totalFiles = selectedLayers.length;
        var BATCH_SIZE = 5;
        var totalBatches = Math.ceil(totalFiles / BATCH_SIZE);
        var totalUploaded = 0;

        try {
            for (var b = 0; b < totalBatches; b++) {
                var start = b * BATCH_SIZE;
                var end = Math.min(start + BATCH_SIZE, totalFiles);
                var batchLayers = selectedLayers.slice(start, end);
                var currentBatchNum = b + 1;

                if (statusText) statusText.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Mengunggah batch ' + currentBatchNum + ' dari ' + totalBatches + ' (' + batchLayers.length + ' layer)...';

                var formData = new FormData();
                formData.append('_token', csrfToken);

                for (var f = 0; f < batchLayers.length; f++) {
                    var l = batchLayers[f];
                    var catPrefix = l.category.charAt(0).toUpperCase() + l.category.slice(1) + '_';
                    var cleanBase = l.displayName.replace(/[^a-zA-Z0-9_\-\s]/g, '').trim().replace(/\s+/g, '_');
                    var targetFilename = catPrefix + cleanBase + '.geojson';

                    var fileObj = new File([l.fileBlob], targetFilename, { type: 'application/geo+json' });
                    formData.append('file_geojson[]', fileObj);
                    formData.append('categories[]', l.category);
                    formData.append('object_names[]', l.displayName);
                }

                var uploadUrl = '/peta/upload-geojson';

                var response = await fetch(uploadUrl, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    body: formData
                });

                if (!response.ok) {
                    var errJson = await response.json().catch(function() { return {}; });
                    throw new Error(errJson.message || ('Gagal pada batch ' + currentBatchNum + ' (HTTP ' + response.status + ')'));
                }

                var resData = await response.json();
                if (resData.uploaded) totalUploaded += resData.uploaded.length;

                var pct = Math.round((end / totalFiles) * 100);
                if (progressBar) { progressBar.style.width = pct + '%'; progressBar.textContent = pct + '%'; }
                if (percentText) percentText.textContent = pct + '%';
            }

            if (statusText) statusText.innerHTML = '<i class="fas fa-check-circle text-success mr-1"></i>Semua layer terpilih berhasil disimpan!';
            if (successSection) {
                var successMsg = totalUploaded + ' layer spasial berhasil diunggah dan disimpan sesuai kategori!';
                successSection.innerHTML = '<i class="fas fa-check-circle mr-1.5 fa-lg"></i>' + successMsg;
                successSection.style.display = 'block';
            }

            setTimeout(function() {
                window.location.reload();
            }, 1400);

        } catch (err) {
            console.error('Triage upload error:', err);
            if (errorSection) {
                errorSection.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i><b>Terjadi Kesalahan:</b> ' + err.message;
                errorSection.style.display = 'block';
            }
            if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = '<i class="fas fa-redo mr-1.5"></i>Coba Lagi'; }
            if (batalBtn) batalBtn.disabled = false;
            if (closeBtn) closeBtn.style.display = '';
        }
    };

    window.submitTriageUpload = async function() {
        var selectedLayers = window.triageLayers.filter(function(l) { return l.selected; });
        if (selectedLayers.length === 0) {
            alert('Silakan centang setidaknya satu layer spasial untuk diunggah.');
            return;
        }

        var progressSection = document.getElementById('uploadProgressSection');
        var progressBar     = document.getElementById('uploadProgressBar');
        var percentText     = document.getElementById('uploadPercentText');
        var statusText      = document.getElementById('uploadStatusText');
        var successSection  = document.getElementById('uploadSuccessSection');
        var errorSection    = document.getElementById('uploadErrorSection');
        var submitBtn       = document.getElementById('btnSubmitTriage');
        var batalBtn        = document.getElementById('btnBatalUpload');
        var closeBtn        = document.getElementById('btnCloseUploadModal');

        if (errorSection) errorSection.style.display = 'none';
        if (successSection) successSection.style.display = 'none';
        if (progressSection) progressSection.style.display = 'block';

        if (submitBtn) { submitBtn.disabled = true; submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Menyimpan Berkas...'; }
        if (batalBtn) batalBtn.disabled = true;
        if (closeBtn) closeBtn.style.display = 'none';

        var csrfMeta = document.querySelector('meta[name="csrf-token"]');
        var csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

        var totalFiles = selectedLayers.length;
        var BATCH_SIZE = 5;
        var totalBatches = Math.ceil(totalFiles / BATCH_SIZE);
        var totalUploaded = 0;

        try {
            for (var b = 0; b < totalBatches; b++) {
                var start = b * BATCH_SIZE;
                var end = Math.min(start + BATCH_SIZE, totalFiles);
                var batchLayers = selectedLayers.slice(start, end);
                var currentBatchNum = b + 1;

                if (statusText) statusText.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Mengunggah batch ' + currentBatchNum + ' dari ' + totalBatches + ' (' + batchLayers.length + ' layer)...';

                var formData = new FormData();
                formData.append('_token', csrfToken);

                for (var f = 0; f < batchLayers.length; f++) {
                    var l = batchLayers[f];
                    var catPrefix = l.category.charAt(0).toUpperCase() + l.category.slice(1) + '_';
                    var cleanBase = l.displayName.replace(/[^a-zA-Z0-9_\-\s]/g, '').trim().replace(/\s+/g, '_');
                    var targetFilename = catPrefix + cleanBase + '.geojson';

                    var fileObj = new File([l.fileBlob], targetFilename, { type: 'application/geo+json' });
                    formData.append('file_geojson[]', fileObj);
                    formData.append('categories[]', l.category);
                    formData.append('object_names[]', l.displayName);
                }

                var uploadUrl = '/peta/upload-geojson';

                var response = await fetch(uploadUrl, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    body: formData
                });

                if (!response.ok) {
                    var errJson = await response.json().catch(function() { return {}; });
                    throw new Error(errJson.message || ('Gagal pada batch ' + currentBatchNum + ' (HTTP ' + response.status + ')'));
                }

                var resData = await response.json();
                if (resData.uploaded) totalUploaded += resData.uploaded.length;

                var pct = Math.round((end / totalFiles) * 100);
                if (progressBar) { progressBar.style.width = pct + '%'; progressBar.textContent = pct + '%'; }
                if (percentText) percentText.textContent = pct + '%';
            }

            if (statusText) statusText.innerHTML = '<i class="fas fa-check-circle text-success mr-1"></i>Semua layer terpilih berhasil disimpan!';
            if (successSection) {
                var successMsg = totalUploaded + ' layer spasial berhasil dipilah dan disimpan sesuai kategori!';
                successSection.innerHTML = '<i class="fas fa-check-circle mr-1 fa-lg"></i>' + successMsg;
                successSection.style.display = 'block';
            }

            setTimeout(function() {
                window.location.reload();
            }, 1400);

        } catch (err) {
            console.error('Triage upload error:', err);
            if (errorSection) {
                errorSection.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i><b>Terjadi Kesalahan:</b> ' + err.message;
                errorSection.style.display = 'block';
            }
            if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = '<i class="fas fa-redo mr-1"></i>Coba Lagi'; }
            if (batalBtn) batalBtn.disabled = false;
            if (closeBtn) closeBtn.style.display = '';
        }
    };

    /* ============================================================
       6. HANDLER FITUR BARU: HERO COLLAPSIBLE, LEGENDA & FOCUS GIS
       ============================================================ */

    // A. Toggle Hero Banner (Ciutkan / Perluas)
    window.toggleHeroBanner = function() {
        var heroEl = document.querySelector('.peta-hero') || document.querySelector('.peta-hero-wrap');
        if (!heroEl) return;
        var isCollapsed = heroEl.classList.toggle('is-collapsed');
        try {
            localStorage.setItem('bws_hero_collapsed', isCollapsed ? '1' : '0');
        } catch(e) {}
        setTimeout(function() {
            if (map && map.invalidateSize) map.invalidateSize();
        }, 320);
    };

    // B. Toggle Floating Legend
    window.toggleFloatingLegend = function() {
        var legEl = document.getElementById('bwsFloatingLegend');
        if (!legEl) return;
        var isCollapsed = legEl.classList.toggle('is-collapsed');
        try {
            localStorage.setItem('bws_legend_collapsed', isCollapsed ? '1' : '0');
        } catch(e) {}
    };

    // C. Toggle Mode Layar Penuh GIS (Clean Focus GIS Mode sesuai Gambar 2)
    window.toggleFocusGisMode = function() {
        var isFocus = document.body.classList.toggle('gis-focus-mode');

        // Sinkronisasi ikon tombol di Leaflet map control
        var btnFs = document.querySelector('.map-action-btn.btn-fullscreen');
        if (btnFs) {
            var icon = btnFs.querySelector('i');
            if (icon) {
                icon.className = isFocus ? 'fas fa-compress' : 'fas fa-expand';
            }
            btnFs.title = isFocus ? 'Kembalikan Ukuran Peta (Keluar Mode Layar Penuh)' : 'Perbesar Tampilan Peta';
            btnFs.setAttribute('aria-label', isFocus ? 'Kembalikan Ukuran' : 'Perbesar Tampilan Peta');
        }

        setTimeout(function() {
            if (map && map.invalidateSize) map.invalidateSize();
        }, 320);
    };

    // Restore preferensi yang tersimpan
    try {
        if (localStorage.getItem('bws_hero_collapsed') === '1') {
            var heroEl = document.querySelector('.peta-hero') || document.querySelector('.peta-hero-wrap');
            if (heroEl) heroEl.classList.add('is-collapsed');
        }
        if (localStorage.getItem('bws_legend_collapsed') === '1') {
            var legEl = document.getElementById('bwsFloatingLegend');
            if (legEl) legEl.classList.add('is-collapsed');
        }
    } catch(e) {}
});
