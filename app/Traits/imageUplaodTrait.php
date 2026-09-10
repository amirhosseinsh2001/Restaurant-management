<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait imageUplaodTrait
{
    private string $disk = 'public';
    protected function saveImage($file, string $folder): string
    {
        $fileName = $file->hashName();
        Storage::disk($this->disk)->putFileAs($folder, $file, $fileName);
        return $folder . '/' . $fileName;
    }
    protected function deleteFile(array|object $data, string $fieldName): bool
    {
        if (is_object($data) && method_exists($data, 'getRawOriginal')) {
            $filePath = $data->getRawOriginal($fieldName);
        } else {
            $filePath = data_get($data, $fieldName);
        }
        return $this->deleteFileByPath($filePath);
    }

    protected function deleteFileByPath(?string $path): bool
    {
        if ($path && Storage::disk($this->disk)->exists($path)) {
            return Storage::disk($this->disk)->delete($path);
        }

        return false;
    }
}
