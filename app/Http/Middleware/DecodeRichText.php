<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

/**
 * WAF-safe request decoding.
 *
 * A server Web Application Firewall (ModSecurity / OWASP CRS) blocks POSTs
 * whose body contains <img>, <script>, src=, or raw binary file uploads
 * (flagged as XSS / malicious upload). To make the app work behind such a
 * firewall, the client wraps risky data as plain base64 text:
 *
 *   - Rich-text fields  → value prefixed with self::MARKER
 *   - File uploads      → hidden field "__wffile_<name>" holding JSON
 *                         {"n":filename,"t":mime,"d":base64}, and the real
 *                         <input type=file> is disabled so no binary is sent.
 *
 * This middleware transparently restores both so controllers/validation see
 * the original HTML and the normal $request->file('<name>').
 */
class DecodeRichText
{
    public const MARKER      = '@@B64@@';
    public const FILE_PREFIX = '__wffile_';

    public function handle(Request $request, Closure $next)
    {
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH'], true)) {
            // 1) Reconstruct files FIRST — before any $request->all() call, which
            //    would memoize an empty converted-files cache and hide our files.
            $this->reconstructFiles($request);

            // 2) Decode base64-wrapped text fields
            $input = $request->all();
            $this->decodeRecursive($input);
            $request->merge($input);
        }

        return $next($request);
    }

    private function decodeRecursive(array &$data): void
    {
        foreach ($data as $key => &$value) {
            if (str_starts_with((string) $key, self::FILE_PREFIX)) {
                continue; // handled separately
            }
            if (is_array($value)) {
                $this->decodeRecursive($value);
            } elseif (is_string($value) && str_starts_with($value, self::MARKER)) {
                $decoded = base64_decode(substr($value, strlen(self::MARKER)), true);
                if ($decoded !== false) {
                    $value = $decoded;
                }
            }
        }
    }

    private function reconstructFiles(Request $request): void
    {
        // Use the raw POST bag (not $request->all()) so we don't trigger the
        // converted-files memoization before our files are in place.
        foreach ($request->request->all() as $key => $value) {
            if (!is_string($key) || !str_starts_with($key, self::FILE_PREFIX)) {
                continue;
            }
            $target = substr($key, strlen(self::FILE_PREFIX)); // original input name
            if (!is_string($value) || $value === '') {
                continue;
            }

            $meta = json_decode($value, true);
            if (!is_array($meta) || empty($meta['d'])) {
                continue;
            }

            $binary = base64_decode($meta['d'], true);
            if ($binary === false) {
                continue;
            }

            $tmp = tempnam(sys_get_temp_dir(), 'wf_');
            if ($tmp === false) {
                continue;
            }
            file_put_contents($tmp, $binary);

            $file = new UploadedFile(
                $tmp,
                $meta['n'] ?? 'upload',
                $meta['t'] ?? 'application/octet-stream',
                null,
                true // test mode: skip is_uploaded_file() check (file came from base64)
            );

            $request->files->set($target, $file);
            $request->request->remove($key); // drop the base64 blob from input bag
        }
    }
}
