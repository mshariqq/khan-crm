<?php

namespace App\Listeners;

use App\Events\LeadConverted;
use App\Services\AuditService;

class LogLeadConverted
{
    public function __construct(private AuditService $audit) {}

    public function handle(LeadConverted $event): void
    {
        $this->audit->log(
            'lead_converted',
            "Lead '{$event->lead->name}' converted to client",
            'Lead',
            $event->lead->id,
            null,
            ['lead_name' => $event->lead->name, 'client_id' => $event->client->userid],
            $event->staffId,
        );
    }
}
