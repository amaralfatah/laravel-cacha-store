@if(app()->environment('local'))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@else
    @php
        // Coba dengan beberapa cara berbeda untuk menemukan path yang benar
        try {
            // Metode 1: Menggunakan __DIR__ untuk relatif terhadap file template saat ini
            $basePath = dirname(__DIR__, 3); // Sesuaikan level direktori sesuai kebutuhan
            $manifestPath1 = $basePath . '/public/build/manifest.json';

            // Metode 2: Menggunakan base_path()
            $manifestPath2 = base_path('public/build/manifest.json');

            // Metode 3: Menggunakan public_path()
            $manifestPath3 = public_path('build/manifest.json');

            // Metode 4: Menggunakan path absolut (jika Anda tahu pasti)
            $manifestPath4 = '/home/u618744358/domains/tokocacha.com/public_html/public/build/manifest.json';

            // Cek masing-masing path
            $manifestPath = null;
            foreach ([$manifestPath1, $manifestPath2, $manifestPath3, $manifestPath4] as $path) {
                if (file_exists($path)) {
                    $manifestPath = $path;
                    break;
                }
            }

            // Jika manifest ditemukan, gunakan
            if ($manifestPath) {
                $manifest = json_decode(file_get_contents($manifestPath), true);

                if (isset($manifest['resources/css/app.css']) && isset($manifest['resources/js/app.js'])) {
                    // Menggunakan manifest yang ditemukan
                    echo '<link rel="stylesheet" href="' . asset('build/' . $manifest['resources/css/app.css']['file']) . '">';
                    echo '<script src="' . asset('build/' . $manifest['resources/js/app.js']['file']) . '" defer></script>';
                } else {
                    // Manifest ditemukan tapi struktur berbeda
                    throw new Exception("Manifest ditemukan tapi tidak valid");
                }
            } else {
                // Manifest tidak ditemukan, gunakan nama file hardcoded
                throw new Exception("Manifest tidak ditemukan");
            }
        } catch (Exception $e) {
            // Fallback ke nama file yang diketahui
            echo '<link rel="stylesheet" href="' . asset('build/assets/app-wIOcQJrJ.css') . '">';
            echo '<link rel="stylesheet" href="' . asset('build/assets/app-DcDb03eT.css') . '">';
            echo '<script src="' . asset('build/assets/app-BKFiQ_FA.js') . '" defer></script>';
        }
    @endphp
@endif
