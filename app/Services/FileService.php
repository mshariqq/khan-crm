<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use ZipArchive;

class FileService
{
    protected string $uploadDir = 'FILES';

    protected array $allowedExtensions = [
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp',
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv',
        'txt', 'zip', 'rar', '7z',
        'mp4', 'avi', 'mov', 'wmv', 'mkv', 'webm',
        'mp3', 'wav', 'ogg', 'aac', 'flac',
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
        'video/mp4', 'video/x-msvideo', 'video/quicktime', 'video/x-ms-wmv', 'video/x-matroska', 'video/webm',
        'audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/aac', 'audio/flac',
    ];

    protected int $maxFileSize = 10 * 1024 * 1024; // 10MB

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

        if ($file->getSize() > $this->maxFileSize) {
            throw new \InvalidArgumentException('File size exceeds the maximum limit of ' . ($this->maxFileSize / 1024 / 1024) . 'MB.');
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
            'file_size' => filesize($dir . '/' . $renamedName),
        ];
    }

    public function delete(string $filename): bool
    {
        $path = $this->getFilePath($filename);
        return $path && file_exists($path) && unlink($path);
    }

    public function url(string $filename): string
    {
        return url($this->uploadDir . '/' . $filename);
    }

    public function getFilePath(string $filename): ?string
    {
        $path = public_path($this->uploadDir . '/' . $filename);
        return file_exists($path) ? $path : null;
    }

    public function exists(string $filename): bool
    {
        return file_exists(public_path($this->uploadDir . '/' . $filename));
    }

    public function size(string $filename): ?int
    {
        $path = $this->getFilePath($filename);
        return $path ? filesize($path) : null;
    }

    public function rename(string $oldFilename, string $newFilename): bool
    {
        $oldPath = public_path($this->uploadDir . '/' . $oldFilename);
        $newPath = public_path($this->uploadDir . '/' . $newFilename);
        if (!file_exists($oldPath)) return false;
        return rename($oldPath, $newPath);
    }

    public function getAbsolutePath(string $filename): string
    {
        return public_path($this->uploadDir . '/' . $filename);
    }

    public function createZipFromFiles(array $fileRecords, string $zipName = 'files.zip'): string
    {
        $zipPath = public_path($this->uploadDir . '/' . $zipName);
        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
            throw new \RuntimeException('Cannot create zip archive');
        }

        foreach ($fileRecords as $file) {
            $filePath = $this->getFilePath($file->file_name);
            if ($filePath) {
                $zip->addFile($filePath, $file->original_file_name ?? $file->file_name);
            }
        }

        $zip->close();
        return $zipName;
    }
}
