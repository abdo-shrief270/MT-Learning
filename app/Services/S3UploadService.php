<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class S3UploadService
{
    /**
     * Handle file upload and old file deletion (if applicable).
     *
     * @param array $data The input data containing the file.
     * @param string $parameter The key in the $data array for the file.
     * @param string $path The destination path in the storage disk.
     * @param Model $model The model instance for old file reference (optional).
     * @param string $primaryKey The primary key of the model (optional).
     * @param bool $haveOldFile Whether to delete the old file.
     * @return array The updated data with the new file path.
     * @throws \Exception If the file doesn't exist in the temporary location.
     */
    public static function upload(
        array $data,
        string $parameter,
        string $path,
        ?Model $model = null,
        bool $haveOldFile = false
    ): array {
        if(is_string($data[$parameter]))
            return $data;
        if (isset($data[$parameter])) {
            if (Storage::disk(env('FILESYSTEM_DISK'))->exists($data[$parameter]->getPathname())) {
                $newPath = $path .'/'. uniqid() . '.' . pathinfo($data[$parameter]->getPathname(), PATHINFO_EXTENSION);
                Storage::disk(env('FILESYSTEM_DISK'))->move($data[$parameter]->getPathname(), $newPath);
                $data[$parameter] = $newPath;
            } else {
                throw new \Exception("Temporary file does not exist or was already removed.");
            }
            if ($haveOldFile && $model) {
                $oldFile = $model->$parameter;
                if ($oldFile) {
                    Storage::disk(env('FILESYSTEM_DISK'))->delete($oldFile);
                }
            }
        }

        return $data;
    }
}
