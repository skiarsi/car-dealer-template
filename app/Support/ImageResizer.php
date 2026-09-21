<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageResizer
{
    public static function store(UploadedFile $file, string $directory, int $maxWidth = 800): string
    {
        $realpath = self::realpath($file);
        $contents = $realpath !== null ? @file_get_contents($realpath) : false;
        $source = $contents ? @imagecreatefromstring($contents) : false;

        if ($source === false) {
            return $file->store($directory, 'public');
        }

        $source = $realpath !== null ? self::orient($source, $realpath) : $source;
        $width = imagesx($source);
        $height = imagesy($source);

        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int) max(1, round($height * ($maxWidth / $width)));
            $canvas = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($canvas, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($source);
            $source = $canvas;
        }

        ob_start();
        imagejpeg($source, null, 82);
        $blob = ob_get_clean();
        imagedestroy($source);

        $path = trim($directory, '/').'/'.Str::uuid().'.jpg';
        Storage::disk('public')->put($path, $blob);

        return $path;
    }

    private static function realpath(UploadedFile $file): ?string
    {
        $path = $file->getRealPath() ?: $file->getPathname();

        return is_string($path) && $path !== '' && is_readable($path) ? $path : null;
    }

    private static function orient(\GdImage $image, string $path): \GdImage
    {
        $exif = @exif_read_data($path);
        $orientation = $exif['Orientation'] ?? 1;

        return match ($orientation) {
            3 => imagerotate($image, 180, 0) ?: $image,
            6 => imagerotate($image, -90, 0) ?: $image,
            8 => imagerotate($image, 90, 0) ?: $image,
            default => $image,
        };
    }
}
