<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;

class FileService
{
    protected string $uploadDir = 'FILES';

    protected array $allowedExtensions = [
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp',
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv',
        'txt', 'zip', 'rar', '7z',
    ];

    protected array $allowedMimeTypes = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml', 'image/bmp',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/csv', 'text/plain',
        'application/zip', 'application/x-rar-compressed', 'application/x-7z-compressed',
    ];

    public function upload(UploadedFile $file, ?string $prefix = null): array
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, $this->allowedExtensions)) {
            throw new \InvalidArgumentException('File type .' . $extension . ' is not allowed for upload.');
        }

        $mime = $file->getMimeType();
        if (!in_array($mime, $this->allowedMimeTypes)) {
            throw new \InvalidArgumentException('File MIME type ' . $mime . ' is not allowed.');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $magicMime = finfo_file($finfo, $file->getPathname());
        finfo_close($finfo);

        if ($magicMime !== $mime) {
            throw new \InvalidArgumentException('File content does not match declared type. Potential spoofing detected.');
        }

        $dir = public_path($this->uploadDir);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $originalName = $file->getClientOriginalName();
        $renamedName = ($prefix ?: 'file') . '_' . time() . '_' . uniqid() . '.' . $extension;

        $file->move($dir, $renamedName);

        return [
            'file_name' => $renamedName,
            'original_file_name' => $originalName,
            'filetype' => $mime,
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
