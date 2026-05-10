<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class FileService
{
    protected string $uploadDir = 'FILES';

    public function upload(UploadedFile $file, ?string $prefix = null): array
    {
        $dir = public_path($this->uploadDir);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $filetype = $file->getMimeType();
        $renamedName = ($prefix ?: 'file') . '_' . time() . '_' . uniqid() . '.' . $extension;

        $file->move($dir, $renamedName);

        return [
            'file_name' => $renamedName,
            'original_file_name' => $originalName,
            'filetype' => $filetype,
        ];
    }

    public function delete(string $filename): bool
    {
        $path = public_path($this->uploadDir . '/' . $filename);
        return file_exists($path) && unlink($path);
    }

    public function url(string $filename): string
    {
        return url($this->uploadDir . '/' . $filename);
    }
}
