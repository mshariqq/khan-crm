<?php

namespace App\Listeners;

use App\Events\EstimateConvertedToInvoice;
use App\Services\AuditService;

class LogEstimateConverted
{
    public function __construct(private AuditService $audit) {}

    public function handle(EstimateConvertedToInvoice $event): void
    {
        $this->audit->log(
            'estimate_converted',
            "Estimate #{$event->estimate->id} converted to invoice #{$event->invoice->id}",
            'Estimate',
            $event->estimate->id,
            null,
            ['estimate_id' => $event->estimate->id, 'invoice_id' => $event->invoice->id],
            $event->staffId,
        );
    }
}
