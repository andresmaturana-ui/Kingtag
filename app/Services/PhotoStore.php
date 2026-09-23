<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Guarda las fotos subidas desde el celular: las endereza, las achica y las
 * vuelve a codificar como JPEG. Al recodificar se pierden los datos EXIF
 * (GPS, modelo del celular, hora), que no queremos publicar.
 */
class PhotoStore
{
    public const MAX_SIDE = 1600;

    public const THUMB_SIDE = 400;

    /**
     * @return array{path: string, thumb: string}
     */
    public function store(UploadedFile $file, string $folder = 'photos'): array
    {
        $image = @imagecreatefromstring((string) file_get_contents($file->getRealPath()));

        if ($image === false) {
            throw new RuntimeException('No se pudo leer la imagen.');
        }

        $image = $this->orient($image, $file->getRealPath());

        $name = $folder.'/'.now()->format('Y/m').'/'.Str::uuid();
        $path = $name.'.jpg';
        $thumb = $name.'_t.jpg';

        Storage::disk('public')->put($path, $this->encode($this->fit($image, self::MAX_SIDE)));
        Storage::disk('public')->put($thumb, $this->encode($this->square($image, self::THUMB_SIDE)));

        return ['path' => $path, 'thumb' => $thumb];
    }

    /**
     * Gira la imagen según la orientación EXIF que guardan los celulares.
     */
    private function orient(\GdImage $image, string $realPath): \GdImage
    {
        if (! function_exists('exif_read_data')) {
            return $image;
        }

        $exif = @exif_read_data($realPath);
        $rotation = match ((int) ($exif['Orientation'] ?? 1)) {
            3 => 180,
            6 => -90,
            8 => 90,
            default => 0,
        };

        return $rotation === 0 ? $image : imagerotate($image, $rotation, 0);
    }

    private function fit(\GdImage $image, int $maxSide): \GdImage
    {
        $w = imagesx($image);
        $h = imagesy($image);
        $scale = min(1, $maxSide / max($w, $h));

        if ($scale === 1) {
            return $image;
        }

        return imagescale($image, (int) round($w * $scale), (int) round($h * $scale));
    }

    private function square(\GdImage $image, int $side): \GdImage
    {
        $w = imagesx($image);
        $h = imagesy($image);
        $crop = min($w, $h);

        $thumb = imagecreatetruecolor($side, $side);
        imagecopyresampled($thumb, $image, 0, 0, (int) (($w - $crop) / 2), (int) (($h - $crop) / 2), $side, $side, $crop, $crop);

        return $thumb;
    }

    private function encode(\GdImage $image): string
    {
        ob_start();
        imagejpeg($image, null, 82);

        return (string) ob_get_clean();
    }
}
