<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Fallback file server for storage/app/public.
 *
 * On servers where `php artisan storage:link` cannot create a symlink
 * (symlinks disabled, restrictive hosting), requests to /storage/* fall
 * through to this route and the file is streamed by PHP instead — so
 * uploaded images/files always load regardless of symlink support.
 *
 * When the symlink DOES exist, the web server serves /storage/* directly
 * and this controller is never reached.
 */
class StorageController extends Controller
{
    public function show(Request $request, string $path)
    {
        // Prevent directory traversal
        $path = str_replace('\\', '/', $path);
        if (str_contains($path, '..')) {
            abort(404);
        }

        // New location (inside public/) first, then the legacy storage/app/public
        $candidates = [
            public_path('storage/' . $path),
            storage_path('app/public/' . $path),
        ];

        foreach ($candidates as $full) {
            if (File::exists($full) && File::isFile($full)) {
                return response()->file($full, [
                    'Cache-Control' => 'public, max-age=2592000',
                ]);
            }
        }

        abort(404);
    }
}
