<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    private const SIZES = [
        'thumb' => [150, 150],
        'medium' => [500, 500],
        'large' => [1200, 1200],
    ];

    public function storeProductImages(UploadedFile $file, int $productId): array
    {
        $baseName = time() . '_' . Str::random(8);
        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $directory = "products/{$productId}";

        $originalPath = $file->storeAs($directory, "{$baseName}.{$extension}", 'public');

        $paths = [
            'image' => $originalPath,
            'thumb' => null,
            'medium' => null,
            'large' => null,
        ];

        if (! extension_loaded('gd')) {
            return $paths;
        }

        $sourcePath = Storage::disk('public')->path($originalPath);
        $source = $this->createImageResource($sourcePath, $extension);

        if (! $source) {
            return $paths;
        }

        foreach (self::SIZES as $sizeName => [$width, $height]) {
            $resized = $this->resizeImage($source, $width, $height);
            if (! $resized) {
                continue;
            }

            $sizePath = "{$directory}/{$baseName}_{$sizeName}.{$extension}";
            $this->saveImage($resized, Storage::disk('public')->path($sizePath), $extension);
            imagedestroy($resized);
            $paths[$sizeName] = $sizePath;
        }

        imagedestroy($source);

        return $paths;
    }

    public function storeCategoryImage(UploadedFile $file, string $type = 'image'): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $filename = time() . '_' . Str::random(8) . ".{$extension}";

        return $file->storeAs("categories/{$type}", $filename, 'public');
    }

    public function storeSettingImage(UploadedFile $file, string $key): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $filename = time() . '_' . Str::random(8) . ".{$extension}";

        return $file->storeAs("settings/{$key}", $filename, 'public');
    }

    public function storeSectionBackgroundImage(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $filename = time() . '_' . Str::random(8) . ".{$extension}";

        return $file->storeAs('sections/backgrounds', $filename, 'public');
    }

    public function deletePaths(?string ...$paths): void
    {
        $filtered = array_filter($paths);

        if ($filtered) {
            Storage::disk('public')->delete($filtered);
        }
    }

    private function createImageResource(string $path, string $extension)
    {
        return match ($extension) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($path),
            'png' => @imagecreatefrompng($path),
            'webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            'gif' => @imagecreatefromgif($path),
            default => false,
        };
    }

    private function resizeImage($source, int $maxWidth, int $maxHeight)
    {
        $srcWidth = imagesx($source);
        $srcHeight = imagesy($source);

        if ($srcWidth <= 0 || $srcHeight <= 0) {
            return false;
        }

        $ratio = min($maxWidth / $srcWidth, $maxHeight / $srcHeight, 1);
        $newWidth = (int) max(1, round($srcWidth * $ratio));
        $newHeight = (int) max(1, round($srcHeight * $ratio));

        $dest = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($dest, false);
        imagesavealpha($dest, true);

        imagecopyresampled($dest, $source, 0, 0, 0, 0, $newWidth, $newHeight, $srcWidth, $srcHeight);

        return $dest;
    }

    private function saveImage($image, string $path, string $extension): void
    {
        match ($extension) {
            'jpg', 'jpeg' => imagejpeg($image, $path, 85),
            'png' => imagepng($image, $path, 6),
            'webp' => function_exists('imagewebp') ? imagewebp($image, $path, 85) : imagejpeg($image, $path, 85),
            'gif' => imagegif($image, $path),
            default => imagejpeg($image, $path, 85),
        };
    }
}
