<?php

namespace App\Services;

use App\Models\ProjectFile;
use Illuminate\Support\Facades\Log;

class FileVirusScanService
{
    public function scan(ProjectFile $file): string
    {
        $path = app(FileService::class)->getFilePath($file->file_name);
        if (!$path) {
            $file->update(['virus_scan_status' => 'error', 'scan_result' => 'File not found on disk']);
            return 'error';
        }

        try {
            $result = $this->runScanner($path);
            $file->update([
                'virus_scan_status' => $result['clean'] ? 'clean' : 'infected',
                'virus_scan_at' => now(),
                'scan_result' => $result['message'],
            ]);
            return $file->virus_scan_status;
        } catch (\Exception $e) {
            Log::error("Virus scan failed for file {$file->id}: " . $e->getMessage());
            $file->update([
                'virus_scan_status' => 'error',
                'virus_scan_at' => now(),
                'scan_result' => $e->getMessage(),
            ]);
            return 'error';
        }
    }

    protected function runScanner(string $path): array
    {
        if (!$this->isClamAvAvailable()) {
            return ['clean' => true, 'message' => 'No virus scanner configured; marked clean by default'];
        }

        $cmd = escapeshellcmd("clamscan --no-summary " . escapeshellarg($path));
        $output = shell_exec($cmd . ' 2>&1');
        $clean = $output === null || str_contains($output, 'OK');

        return [
            'clean' => $clean,
            'message' => $clean ? 'Clean' : 'Infected: ' . trim($output ?? 'Unknown error'),
        ];
    }

    protected function isClamAvAvailable(): bool
    {
        $output = shell_exec('which clamscan 2>&1') ?: shell_exec('where clamscan 2>&1');
        return !empty($output);
    }
}
