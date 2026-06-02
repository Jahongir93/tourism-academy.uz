<?php
/**
 * One-off repair: the external-image localizer wrapped some URLs that live
 * inside @php arrays with blade `{{ asset(...) }}`, which is invalid PHP and
 * caused "syntax error, unexpected identifier" on render.
 *
 * Convert PHP-context occurrences  '{{ asset('X') }}'  →  asset('X')
 * but leave HTML/JS attribute occurrences (preceded by =) untouched, e.g.
 *   onerror="this.src='{{ asset('X') }}'"   (these render fine).
 */
$root = dirname(__DIR__) . '/resources/views';
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
$fixed = 0; $files = 0;

// Match  '{{ asset('images/ext/FILE') }}'  NOT preceded by '='
$pattern = "/(?<![=])'\\{\\{ asset\\('(images\\/ext\\/[^']+)'\\) \\}\\}'/";

foreach ($rii as $f) {
    if ($f->getExtension() !== 'php') continue;
    $p = $f->getPathname();
    $c = file_get_contents($p);
    $new = preg_replace($pattern, "asset('$1')", $c, -1, $n);
    if ($new !== $c && $n > 0) {
        file_put_contents($p, $new);
        $fixed += $n; $files++;
        echo str_replace($root . DIRECTORY_SEPARATOR, '', $p) . " ({$n})\n";
    }
}
echo "\nFixed: {$fixed} occurrences in {$files} files\n";
