<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TicketImapService;

class FetchTicketEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:fetch-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and process emails for ticket integration';

    /**
     * Execute the console command.
     */
    public function handle(TicketImapService $imapService)
    {
        $this->info('Starting ticket email fetch...');
        $imapService->fetchEmails();
        $this->info('Ticket email fetch completed.');
    }
}
