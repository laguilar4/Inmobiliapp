<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class S3Helper
{
    /**
     * Sube un archivo al bucket S3 en el directorio indicado.
     *
     * @param  string  $directory  Directorio destino dentro del bucket (ej: "imagenes/proyectos")
     * @param  UploadedFile|string  $file  Archivo subido o contenido en string
     * @param  string|null  $filename  Nombre del archivo. Si es null se genera uno único
     * @param  string  $visibility  "public" o "private"
     * @return string|false  Ruta del archivo en S3 o false en caso de error
     */
    public static function upload(string $directory, UploadedFile|string $file, ?string $filename = null, string $visibility = 'public'): string|false
    {
        $directory = trim($directory, '/');

        if ($file instanceof UploadedFile) {
            if ($filename) {
                $path = Storage::disk('s3')->putFileAs($directory, $file, $filename, $visibility);
            } else {
                $path = Storage::disk('s3')->putFile($directory, $file, $visibility);
            }
        } else {
            $filename = $filename ?? uniqid('file_', true);
            $path = $directory . '/' . $filename;
            $stored = Storage::disk('s3')->put($path, $file, $visibility);
            $path = $stored ? $path : false;
        }

        return $path;
    }

    /**
     * Obtiene la URL pública de un archivo en S3.
     *
     * @param  string  $directory  Directorio dentro del bucket
     * @param  string  $filename   Nombre del archivo
     * @return string  URL del archivo
     */
    public static function url(string $directory, string $filename): string
    {
        $directory = trim($directory, '/');

        return Storage::disk('s3')->url($directory . '/' . $filename);
    }

    /**
     * Genera una URL temporal (pre-signed) para un archivo privado en S3.
     *
     * @param  string  $directory   Directorio dentro del bucket
     * @param  string  $filename    Nombre del archivo
     * @param  int     $minutes     Minutos de validez de la URL (por defecto 60)
     * @return string  URL pre-signed
     */
    public static function temporaryUrl(string $directory, string $filename, int $minutes = 60): string
    {
        $directory = trim($directory, '/');

        return Storage::disk('s3')->temporaryUrl(
            $directory . '/' . $filename,
            now()->addMinutes($minutes)
        );
    }

    /**
     * Lee el contenido de un archivo en S3.
     *
     * @param  string  $directory  Directorio dentro del bucket
     * @param  string  $filename   Nombre del archivo
     * @return string|null  Contenido del archivo o null si no existe
     */
    public static function read(string $directory, string $filename): ?string
    {
        $directory = trim($directory, '/');
        $path = $directory . '/' . $filename;

        if (!Storage::disk('s3')->exists($path)) {
            return null;
        }

        return Storage::disk('s3')->get($path);
    }

    /**
     * Elimina un archivo del bucket S3.
     *
     * @param  string  $directory  Directorio dentro del bucket
     * @param  string  $filename   Nombre del archivo
     * @return bool  true si se eliminó correctamente
     */
    public static function delete(string $directory, string $filename): bool
    {
        $directory = trim($directory, '/');

        return Storage::disk('s3')->delete($directory . '/' . $filename);
    }

    /**
     * Elimina todos los archivos dentro de un directorio en S3.
     *
     * @param  string  $directory  Directorio a vaciar
     * @return bool  true si se eliminó correctamente
     */
    public static function deleteDirectory(string $directory): bool
    {
        $directory = trim($directory, '/');

        return Storage::disk('s3')->deleteDirectory($directory);
    }

    /**
     * Verifica si un archivo existe en S3.
     *
     * @param  string  $directory  Directorio dentro del bucket
     * @param  string  $filename   Nombre del archivo
     * @return bool
     */
    public static function exists(string $directory, string $filename): bool
    {
        $directory = trim($directory, '/');

        return Storage::disk('s3')->exists($directory . '/' . $filename);
    }

    /**
     * Lista los archivos dentro de un directorio en S3.
     *
     * @param  string  $directory  Directorio a listar
     * @return array  Lista de rutas de archivos
     */
    public static function list(string $directory): array
    {
        $directory = trim($directory, '/');

        return Storage::disk('s3')->files($directory);
    }
}
