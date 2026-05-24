<?php

namespace App\Listeners;

use App\Events\InvoicePaid;
use App\Services\AuditService;

class LogInvoicePaid
{
    public function __construct(private AuditService $audit) {}

    public function handle(InvoicePaid $event): void
    {
        $this->audit->log(
            'invoice_paid',
            "Invoice #{$event->invoice->id} paid: {$event->amount}",
            'Invoice',
            $event->invoice->id,
            null,
            ['amount' => $event->amount, 'status' => $event->invoice->status],
            $event->staffId,
        );
    }
}
