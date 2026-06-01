<?php
/**
 * One-off maintenance script: replace external CDN references in all Blade
 * templates with local /public/vendor asset() paths so the app works behind
 * a restrictive firewall (no outbound internet on the server).
 *
 * Run:  php scripts/localize_cdn_assets.php
 */

$root = dirname(__DIR__);
$viewsDir = $root . '/resources/views';

// regex => replacement ({{ asset(...) }} blade expression)
$rules = [
    // Tailwind play CDN (any version path)
    '#https?://cdn\.tailwindcss\.com[^"\'\s]*#' => "{{ asset('vendor/tailwind/tailwind.min.js') }}",

    // Font Awesome CSS (any version)
    '#https?://cdnjs\.cloudflare\.com/ajax/libs/font-awesome/[^"\']*/css/all\.min\.css#' => "{{ asset('vendor/fontawesome/css/all.min.css') }}",

    // Bootstrap CSS / JS (any version)
    '#https?://cdn\.jsdelivr\.net/npm/bootstrap@[^"\']*/dist/css/bootstrap\.min\.css#' => "{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}",
    '#https?://cdn\.jsdelivr\.net/npm/bootstrap@[^"\']*/dist/js/bootstrap\.bundle\.min\.js#' => "{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}",

    // Alpine.js (unpkg or jsdelivr, any version)
    '#https?://unpkg\.com/alpinejs@[^"\']*#' => "{{ asset('vendor/alpine/alpine.min.js') }}",
    '#https?://cdn\.jsdelivr\.net/npm/alpinejs@[^"\']*#' => "{{ asset('vendor/alpine/alpine.min.js') }}",

    // Chart.js (versioned or not)
    '#https?://cdn\.jsdelivr\.net/npm/chart\.js@[^"\']*#' => "{{ asset('vendor/chartjs/chart.umd.min.js') }}",
    '#https?://cdn\.jsdelivr\.net/npm/chart\.js(?![@a-zA-Z0-9.\-/])#' => "{{ asset('vendor/chartjs/chart.umd.min.js') }}",

    // jQuery
    '#https?://code\.jquery\.com/jquery-[^"\']*\.js#' => "{{ asset('vendor/jquery/jquery-3.7.0.min.js') }}",

    // Google Fonts (Inter) stylesheet
    '#https?://fonts\.googleapis\.com/css2[^"\']*#' => "{{ asset('vendor/fonts/inter.css') }}",

    // SweetAlert2
    '#https?://cdn\.jsdelivr\.net/npm/sweetalert2@[^"\']*#' => "{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}",

    // SortableJS
    '#https?://cdn\.jsdelivr\.net/npm/sortablejs@[^"\']*#' => "{{ asset('vendor/sortablejs/Sortable.min.js') }}",

    // AOS
    '#https?://unpkg\.com/aos@[^"\']*/dist/aos\.css#' => "{{ asset('vendor/aos/aos.css') }}",
    '#https?://unpkg\.com/aos@[^"\']*/dist/aos\.js#' => "{{ asset('vendor/aos/aos.js') }}",

    // Leaflet
    '#https?://unpkg\.com/leaflet@[^"\']*/dist/leaflet\.css#' => "{{ asset('vendor/leaflet/leaflet.css') }}",
    '#https?://unpkg\.com/leaflet@[^"\']*/dist/leaflet\.js#' => "{{ asset('vendor/leaflet/leaflet.js') }}",

    // Pannellum
    '#https?://cdn\.jsdelivr\.net/npm/pannellum@[^"\']*/build/pannellum\.css#' => "{{ asset('vendor/pannellum/pannellum.css') }}",
    '#https?://cdn\.jsdelivr\.net/npm/pannellum@[^"\']*/build/pannellum\.js#' => "{{ asset('vendor/pannellum/pannellum.js') }}",

    // DataTables
    '#https?://cdn\.datatables\.net/[^"\']*/css/dataTables\.bootstrap5\.min\.css#' => "{{ asset('vendor/datatables/css/dataTables.bootstrap5.min.css') }}",
    '#https?://cdn\.datatables\.net/[^"\']*/js/jquery\.dataTables\.min\.js#' => "{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}",
    '#https?://cdn\.datatables\.net/[^"\']*/js/dataTables\.bootstrap5\.min\.js#' => "{{ asset('vendor/datatables/js/dataTables.bootstrap5.min.js') }}",

    // TinyMCE (must come before generic — points to self-hosted package root)
    '#https?://cdn\.jsdelivr\.net/npm/tinymce@[^"\']*/tinymce\.min\.js#' => "{{ asset('vendor/tinymce/tinymce.min.js') }}",

    // TensorFlow (face recognition) — JS libs only; ML models still load at runtime
    '#https?://cdn\.jsdelivr\.net/npm/@tensorflow/tfjs@[^"\']*/dist/tf\.min\.js#' => "{{ asset('vendor/tensorflow/tf.min.js') }}",
    '#https?://cdn\.jsdelivr\.net/npm/@tensorflow/tfjs(?![@a-zA-Z0-9.\-/])#' => "{{ asset('vendor/tensorflow/tf.min.js') }}",
    '#https?://cdn\.jsdelivr\.net/npm/@tensorflow-models/blazeface@[^"\']*/dist/blazeface\.js#' => "{{ asset('vendor/tensorflow/blazeface.js') }}",
    '#https?://cdn\.jsdelivr\.net/npm/@tensorflow-models/face-landmarks-detection(@[^"\']*)?#' => "{{ asset('vendor/tensorflow/face-landmarks-detection.js') }}",

    // QR code
    '#https?://cdnjs\.cloudflare\.com/ajax/libs/qrcodejs/[^"\']*/qrcode\.min\.js#' => "{{ asset('vendor/qrcode/qrcode.min.js') }}",

    // pdf.js (worker rule before main so the more specific matches first)
    '#https?://cdnjs\.cloudflare\.com/ajax/libs/pdf\.js/[^"\']*/pdf\.worker\.min\.js#' => "{{ asset('vendor/pdfjs/pdf.worker.min.js') }}",
    '#https?://cdnjs\.cloudflare\.com/ajax/libs/pdf\.js/[^"\']*/pdf\.min\.js#' => "{{ asset('vendor/pdfjs/pdf.min.js') }}",

    // animate.css
    '#https?://cdnjs\.cloudflare\.com/ajax/libs/animate\.css/[^"\']*/animate\.min\.css#' => "{{ asset('vendor/animatecss/animate.min.css') }}",

    // Swiper
    '#https?://cdn\.jsdelivr\.net/npm/swiper@[^"\']*/swiper-bundle\.min\.css#' => "{{ asset('vendor/swiper/swiper-bundle.min.css') }}",
    '#https?://cdn\.jsdelivr\.net/npm/swiper@[^"\']*/swiper-bundle\.min\.js#' => "{{ asset('vendor/swiper/swiper-bundle.min.js') }}",

    // Select2
    '#https?://cdn\.jsdelivr\.net/npm/select2@[^"\']*/dist/css/select2\.min\.css#' => "{{ asset('vendor/select2/css/select2.min.css') }}",
    '#https?://cdn\.jsdelivr\.net/npm/select2@[^"\']*/dist/js/select2\.min\.js#' => "{{ asset('vendor/select2/js/select2.min.js') }}",

    // FullCalendar
    '#https?://cdn\.jsdelivr\.net/npm/fullcalendar@[^"\']*/main\.min\.css#' => "{{ asset('vendor/fullcalendar/main.min.css') }}",
    '#https?://cdn\.jsdelivr\.net/npm/fullcalendar@[^"\']*/main\.min\.js#' => "{{ asset('vendor/fullcalendar/main.min.js') }}",

    // Pusher + Laravel Echo (realtime — needs websocket server, JS localized so pages don't break)
    '#https?://cdn\.jsdelivr\.net/npm/pusher-js@[^"\']*/dist/web/pusher\.min\.js#' => "{{ asset('vendor/pusher/pusher.min.js') }}",
    '#https?://cdn\.jsdelivr\.net/npm/laravel-echo@[^"\']*/dist/echo\.iife\.js#' => "{{ asset('vendor/pusher/echo.iife.js') }}",

    // Language flags (flagcdn)
    '#https?://flagcdn\.com/w40/uz\.png#' => "{{ asset('vendor/flags/uz.png') }}",
    '#https?://flagcdn\.com/w40/ru\.png#' => "{{ asset('vendor/flags/ru.png') }}",
    '#https?://flagcdn\.com/w40/(gb|en)\.png#' => "{{ asset('vendor/flags/gb.png') }}",

    // flag-icons CSS (minimal local replacement: uz/ru/gb only)
    '#https?://cdnjs\.cloudflare\.com/ajax/libs/flag-icon-css/[^"\']*/css/flag-icons\.min\.css#' => "{{ asset('vendor/flagicons/flag-icons.min.css') }}",
];

$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsDir, FilesystemIterator::SKIP_DOTS));
$changedFiles = 0;
$totalReplacements = 0;
$report = [];

foreach ($rii as $file) {
    if ($file->getExtension() !== 'php') continue;
    $path = $file->getPathname();
    $orig = file_get_contents($path);
    $content = $orig;
    $fileCount = 0;

    foreach ($rules as $pattern => $replacement) {
        $content = preg_replace($pattern, $replacement, $content, -1, $n);
        $fileCount += $n;
    }

    if ($content !== $orig) {
        file_put_contents($path, $content);
        $changedFiles++;
        $totalReplacements += $fileCount;
        $report[] = str_replace($root . DIRECTORY_SEPARATOR, '', $path) . " ({$fileCount})";
    }
}

echo "Changed files: {$changedFiles}\n";
echo "Total replacements: {$totalReplacements}\n\n";
foreach ($report as $r) echo "  {$r}\n";
