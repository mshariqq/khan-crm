<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProjectFile;
use App\Services\FileService;
use App\Models\ProjectActivity;

class FileCleanupExpired extends Command
{
    protected $signature = 'files:cleanup-expired';

    protected $description = 'Delete expired project files';

    public function handle(FileService $fileService)
    {
        $this->info('Checking for expired files...');

        $expiredFiles = ProjectFile::expired()->get();
        $count = 0;

        foreach ($expiredFiles as $file) {
            $fileService->delete($file->file_name);
            ProjectActivity::create([
                'project_id' => $file->project_id,
                'staff_id' => $file->staffid,
                'description_key' => 'project_activity_file_expired',
                'additional_data' => json_encode(['file_name' => $file->original_file_name]),
                'dateadded' => now(),
            ]);
            $file->delete();
            $count++;
        }

        $this->info("Deleted {$count} expired file(s).");
        return Command::SUCCESS;
    }
}
