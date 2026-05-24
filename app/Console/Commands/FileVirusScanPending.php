<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProjectFile;
use App\Services\FileVirusScanService;

class FileVirusScanPending extends Command
{
    protected $signature = 'files:scan-virus';

    protected $description = 'Scan pending project files for viruses';

    public function handle(FileVirusScanService $scanner)
    {
        $this->info('Scanning pending files...');

        $files = ProjectFile::where('virus_scan_status', 'pending')
            ->orWhereNull('virus_scan_status')
            ->limit(50)
            ->get();

        $scanned = 0;
        foreach ($files as $file) {
            $result = $scanner->scan($file);
            $this->line("File #{$file->id}: {$result}");
            $scanned++;
        }

        $this->info("Scanned {$scanned} file(s).");
        return Command::SUCCESS;
    }
}
