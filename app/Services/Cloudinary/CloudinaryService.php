<?php

namespace App\Services\Cloudinary;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Illuminate\Http\UploadedFile;
use RuntimeException;

class CloudinaryService
{
    public function isConfigured(): bool
    {
        return config('cloudinary.cloud_name') !== ''
            && config('cloudinary.api_key') !== ''
            && config('cloudinary.api_secret') !== '';
    }

    /**
     * Signature payload for signed direct-browser uploads, so large
     * photos/videos go straight to Cloudinary without passing through PHP.
     */
    public function generateUploadSignature(string $folder, array $extraParams = []): array
    {
        $this->ensureConfigured();

        $timestamp = now()->timestamp;

        $params = array_merge($extraParams, [
            'folder' => $folder,
            'timestamp' => $timestamp,
        ]);
        ksort($params);

        $toSign = collect($params)
            ->map(fn ($value, $key) => "{$key}={$value}")
            ->implode('&');

        return [
            'signature' => sha1($toSign.config('cloudinary.api_secret')),
            'timestamp' => $timestamp,
            'api_key' => config('cloudinary.api_key'),
            'cloud_name' => config('cloudinary.cloud_name'),
            'folder' => $folder,
        ];
    }

    /**
     * Server-proxy upload fallback. Fine for small images (bukti transfer);
     * large media should use the signed direct-browser path instead.
     *
     * @return array{secure_url: string, public_id: string, resource_type: string}
     */
    public function uploadFile(UploadedFile $file, string $folder): array
    {
        $this->ensureConfigured();

        $result = $this->client()->uploadApi()->upload($file->getRealPath(), [
            'folder' => $folder,
            'resource_type' => 'auto',
        ]);

        return [
            'secure_url' => (string) $result['secure_url'],
            'public_id' => (string) $result['public_id'],
            'resource_type' => (string) $result['resource_type'],
        ];
    }

    public function delete(string $publicId, string $resourceType = 'image'): void
    {
        $this->ensureConfigured();

        $this->client()->uploadApi()->destroy($publicId, [
            'resource_type' => $resourceType,
        ]);
    }

    private function client(): Cloudinary
    {
        return new Cloudinary(Configuration::instance([
            'cloud' => [
                'cloud_name' => config('cloudinary.cloud_name'),
                'api_key' => config('cloudinary.api_key'),
                'api_secret' => config('cloudinary.api_secret'),
            ],
            'url' => ['secure' => true],
        ]));
    }

    private function ensureConfigured(): void
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Cloudinary belum dikonfigurasi. Isi CLOUDINARY_* di .env.');
        }
    }
}
