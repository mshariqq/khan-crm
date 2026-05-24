<?php

namespace App\Listeners;

use App\Events\PaymentRecorded;
use App\Services\AuditService;

class LogPaymentRecorded
{
    public function __construct(private AuditService $audit) {}

    public function handle(PaymentRecorded $event): void
    {
        $p = $event->payment;
        $this->audit->log(
            'payment_recorded',
            "Payment {$p->amount} recorded on invoice #{$p->invoice_id}",
            'InvoicePaymentRecord',
            $p->id,
            null,
            $p->toArray(),
            $event->staffId,
        );
    }
}
