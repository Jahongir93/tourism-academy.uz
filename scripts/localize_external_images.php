<?php
/**
 * Replace external image URLs (unsplash / picsum / placeholder) in Blade
 * templates with local /public/images/ext assets, so a firewalled server
 * never makes outbound image requests (no 404 / blocked images).
 *
 * Unsplash images were pre-downloaded to images/ext/<md5(url)>.jpg.
 * Missing/failed downloads + picsum + via.placeholder fall back to
 * images/ext/placeholder.jpg.
 *
 * Run: php scripts/localize_external_images.php
 */

$root = dirname(__DIR__);
$viewsDir = $root . '/resources/views';
$extDir = $root . '/public/images/ext';
$placeholder = "images/ext/placeholder.jpg";

$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsDir, FilesystemIterator::SKIP_DOTS));
$changed = 0; $count = 0; $report = [];

foreach ($rii as $file) {
    if ($file->getExtension() !== 'php') continue;
    $path = $file->getPathname();
    $orig = file_get_contents($path);

    // Match full external image URLs (stop at quote, space, ), >, or blade {{)
    $content = preg_replace_callback(
        '#https://(?:images\.unsplash\.com|picsum\.photos|via\.placeholder\.com)/[^"\'\s)>]*#',
        function ($m) use ($extDir, $placeholder, &$count) {
            $url = $m[0];
            // strip a trailing blade artifact if any
            $clean = preg_replace('#\{\{.*$#', '', $url);
            $local = $placeholder;
            if (str_contains($clean, 'images.unsplash.com')) {
                $h = substr(md5($clean), 0, 16);
                $f = $extDir . '/' . $h . '.jpg';
                if (is_file($f) && filesize($f) > 1000) {
                    $local = "images/ext/{$h}.jpg";
                }
            }
            $count++;
            return "{{ asset('{$local}') }}";
        },
        $orig
    );

    if ($content !== $orig) {
        file_put_contents($path, $content);
        $changed++;
        $report[] = str_replace($root . DIRECTORY_SEPARATOR, '', $path);
    }
}

echo "Changed files: {$changed}\nReplacements: {$count}\n\n";
foreach ($report as $r) echo "  {$r}\n";
