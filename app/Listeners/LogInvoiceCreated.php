<?php

namespace App\Listeners;

use App\Events\InvoiceCreated;
use App\Models\Notification;
use App\Models\Staff;
use App\Services\AuditService;
use App\Helpers\SettingsHelper;

class LogInvoiceCreated
{
    public function __construct(private AuditService $audit) {}

    public function handle(InvoiceCreated $event): void
    {
        $invoice = $event->invoice;

        $this->audit->log(
            'invoice_created',
            "Invoice #{$invoice->id} created",
            'Invoice',
            $invoice->id,
            null,
            ['total' => $invoice->total, 'client_id' => $invoice->userid],
            $event->staffId,
        );

        $highValueThreshold = (float) SettingsHelper::get('high_value_invoice_threshold', 10000);
        if ($invoice->total >= $highValueThreshold) {
            $admins = Staff::where('admin', 1)->pluck('staffid')->toArray();
            $staffIds = array_unique(array_filter($admins));

            if (!empty($staffIds)) {
                Notification::createForMultipleStaff(
                    $staffIds,
                    'High-value invoice created: ' . e(number_format($invoice->total, 2)) . ' for ' . e($invoice->client->company ?? 'Client #' . $invoice->userid),
                    route('admin.invoices.show', $invoice->id),
                    notificationType: 'invoice',
                    groupingKey: 'high_value_invoice_' . $invoice->id
                );
            }
        }
    }
}
