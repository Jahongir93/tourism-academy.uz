<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Rich-text (TinyMCE) fields are base64-wrapped client-side before submit
 * so a server WAF (ModSecurity / OWASP CRS) does not see <img>, <script>,
 * src=, etc. in the request body and block the POST with 403.
 *
 * This middleware transparently decodes any field whose value begins with
 * the marker, so controllers receive the original HTML unchanged.
 */
class DecodeRichText
{
    public const MARKER = '@@B64@@';

    public function handle(Request $request, Closure $next)
    {
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH'], true)) {
            $input = $request->all();
            $this->decodeRecursive($input);
            $request->merge($input);
        }

        return $next($request);
    }

    private function decodeRecursive(array &$data): void
    {
        foreach ($data as &$value) {
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
}
