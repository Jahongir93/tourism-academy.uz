<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Local replacement for ui-avatars.com — generates an SVG avatar from a name's
 * initials so avatars work without any outbound internet (firewall-safe).
 *
 * Drop-in compatible with the ui-avatars query API:
 *   /avatar?name=John+Doe&background=10b981&color=fff&size=128
 */
class AvatarController extends Controller
{
    public function show(Request $request)
    {
        $name       = trim((string) $request->query('name', '?'));
        $size       = (int) $request->query('size', 128);
        $size       = max(16, min(512, $size));
        $color      = $this->sanitizeColor($request->query('color', 'fff'));
        $background = $request->query('background', 'random');

        // Build initials (max 2 letters)
        $parts = preg_split('/\s+/', $name, -1, PREG_SPLIT_NO_EMPTY) ?: ['?'];
        $initials = '';
        foreach (array_slice($parts, 0, 2) as $p) {
            $initials .= mb_strtoupper(mb_substr($p, 0, 1));
        }
        if ($initials === '') $initials = '?';

        // Deterministic background when "random" requested
        if ($background === 'random' || $background === '') {
            $palette = ['4F46E5','7C3AED','0EA5E9','10B981','F59E0B','F43F5E','F97316','14B8A6','EC4899','06B6D4'];
            $background = $palette[crc32($name) % count($palette)];
        } else {
            $background = $this->sanitizeColor($background);
        }

        $fontSize = round($size * 0.42, 1);

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$size}" height="{$size}" viewBox="0 0 {$size} {$size}">
  <rect width="{$size}" height="{$size}" fill="#{$background}"/>
  <text x="50%" y="50%" dy=".1em" fill="#{$color}" font-family="Inter, Arial, sans-serif"
        font-size="{$fontSize}" font-weight="600" text-anchor="middle" dominant-baseline="middle">{$initials}</text>
</svg>
SVG;

        return response($svg, 200, [
            'Content-Type'  => 'image/svg+xml',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    private function sanitizeColor(?string $c): string
    {
        $c = ltrim((string) $c, '#');
        return preg_match('/^[0-9a-fA-F]{3,8}$/', $c) ? $c : '888888';
    }
}
