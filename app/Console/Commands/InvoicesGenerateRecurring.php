<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InvoicesGenerateRecurring extends Command
{
    protected $signature = 'invoices:generate-recurring';
    protected $description = 'Generate recurring invoices that are due';

    public function handle(): int
    {
        $this->warn('Recurring invoice generation not yet implemented — requires Invoice recurrence logic.');
        return Command::SUCCESS;
    }
}
