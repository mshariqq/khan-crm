<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DiscussionEmailService;
use App\Helpers\SettingsHelper;

class ProcessDiscussionEmails extends Command
{
    protected $signature = 'discussions:fetch-emails';

    protected $description = 'Fetch and process email replies to discussions';

    public function handle(DiscussionEmailService $service, SettingsHelper $settings)
    {
        if (!$settings->get('discussion_email_integration_enabled', false)) {
            $this->warn('Discussion email integration is disabled.');
            return Command::SUCCESS;
        }

        $this->info('Starting discussion email fetch...');

        $mbox = $settings->get('discussion_email_mailbox', '{imap.example.com:993/imap/ssl}INBOX');
        $username = $settings->get('discussion_email_username', '');
        $password = $settings->get('discussion_email_password', '');

        if (empty($username) || empty($password)) {
            $this->error('Discussion email credentials not configured.');
            return Command::FAILURE;
        }

        $result = $service->processIncomingEmail($mbox, $username, decrypt($password));

        $this->info($result['message']);

        return Command::SUCCESS;
    }
}
