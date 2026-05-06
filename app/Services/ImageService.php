<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageService
{
    private ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Guarda una imagen optimizada como WebP en storage.
     *
     * @param  UploadedFile  $file
     * @param  string        $folder   Carpeta dentro de storage/app/public
     * @param  int           $maxWidth Ancho máximo en px
     * @return string        Ruta relativa guardada
     */
    /**
     * Guarda una imagen desde base64 como WebP en storage.
     *
     * @param  string  $base64   Cadena base64 (con o sin data URI prefix)
     * @param  string  $folder   Carpeta dentro de storage/app/public
     * @param  int     $maxWidth Ancho máximo en px
     * @return string  Ruta relativa guardada
     */
    public function storeBase64AsWebp(string $base64, string $folder, int $maxWidth = 1200): string
    {
        // Eliminar el prefijo data URI si existe
        $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $base64);
        $base64 = str_replace(' ', '+', $base64);

        $image = $this->manager->read(base64_decode($base64));

        if ($image->width() > $maxWidth) {
            $image->scaleDown(width: $maxWidth);
        }

        $filename = Str::uuid() . '.webp';
        $path     = $folder . '/' . $filename;

        Storage::disk('public')->put(
            $path,
            $image->toWebp(quality: 82)->toString()
        );

        return $path;
    }

    public function storeAsWebp(UploadedFile $file, string $folder, int $maxWidth = 1200): string
    {
        $image = $this->manager->read($file->getRealPath());

        // Redimensionar solo si supera el ancho máximo, manteniendo proporción
        if ($image->width() > $maxWidth) {
            $image->scaleDown(width: $maxWidth);
        }

        $filename = Str::uuid() . '.webp';
        $path     = $folder . '/' . $filename;

        Storage::disk('public')->put(
            $path,
            $image->toWebp(quality: 82)->toString()
        );

        return $path;
    }
}
