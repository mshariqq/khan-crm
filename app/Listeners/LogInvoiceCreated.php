<?php

namespace App\Listeners;

use App\Events\InvoiceCreated;
use App\Services\AuditService;

class LogInvoiceCreated
{
    public function __construct(private AuditService $audit) {}

    public function handle(InvoiceCreated $event): void
    {
        $this->audit->log(
            'invoice_created',
            "Invoice #{$event->invoice->id} created",
            'Invoice',
            $event->invoice->id,
            null,
            ['total' => $event->invoice->total, 'client_id' => $event->invoice->userid],
            $event->staffId,
        );
    }
}
