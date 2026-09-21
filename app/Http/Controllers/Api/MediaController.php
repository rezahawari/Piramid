<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MediaController extends Controller
{
    /**
     * Serve static images/storage files explicitly with permissive CORS headers.
     */
    public function serve(string $path): BinaryFileResponse|\Illuminate\Http\Response
    {
        $fullPath = public_path($path);

        if (! File::exists($fullPath)) {
            // Cek jika di storage/app/public
            $storagePath = storage_path('app/public/'.$path);
            if (File::exists($storagePath)) {
                $fullPath = $storagePath;
            } else {
                return response('File not found', 404);
            }
        }

        $mimeType = File::mimeType($fullPath) ?: 'application/octet-stream';

        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, HEAD, OPTIONS',
            'Access-Control-Allow-Headers' => '*',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
