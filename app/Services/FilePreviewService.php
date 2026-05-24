<?php

namespace App\Services;

use App\Models\ProjectFile;

class FilePreviewService
{
    public function getPreviewData(ProjectFile $file): array
    {
        return match ($file->preview_type) {
            'image' => $this->imagePreview($file),
            'pdf' => $this->pdfPreview($file),
            'video' => $this->videoPreview($file),
            'audio' => $this->audioPreview($file),
            'office' => $this->officePreview($file),
            default => ['type' => 'none', 'message' => 'Preview not available for this file type'],
        };
    }

    protected function imagePreview(ProjectFile $file): array
    {
        return [
            'type' => 'image',
            'url' => app(FileService::class)->url($file->file_name),
            'alt' => $file->original_file_name,
        ];
    }

    protected function pdfPreview(ProjectFile $file): array
    {
        return [
            'type' => 'pdf',
            'url' => app(FileService::class)->url($file->file_name),
        ];
    }

    protected function videoPreview(ProjectFile $file): array
    {
        return [
            'type' => 'video',
            'url' => app(FileService::class)->url($file->file_name),
            'mime' => $file->filetype,
        ];
    }

    protected function audioPreview(ProjectFile $file): array
    {
        return [
            'type' => 'audio',
            'url' => app(FileService::class)->url($file->file_name),
            'mime' => $file->filetype,
        ];
    }

    protected function officePreview(ProjectFile $file): array
    {
        $ext = $file->extension;
        $service = app(FileService::class);

        if (in_array($ext, ['csv'])) {
            $path = $service->getFilePath($file->file_name);
            $rows = [];
            if ($path && ($handle = fopen($path, 'r')) !== false) {
                $header = fgetcsv($handle);
                while (($row = fgetcsv($handle)) !== false) {
                    $rows[] = $row;
                }
                fclose($handle);
            }
            return ['type' => 'csv', 'header' => $header ?? [], 'rows' => $rows];
        }

        return [
            'type' => 'office',
            'url' => $service->url($file->file_name),
            'filename' => $file->original_file_name,
        ];
    }
}
