<?php

namespace App\Listeners;

use App\Events\InvoicePaid;
use App\Models\Notification;
use App\Models\Staff;
use App\Services\AuditService;
use Illuminate\Support\Facades\Auth;

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

        $admins = Staff::where('admin', 1)->pluck('staffid')->toArray();
        $staffIds = array_unique(array_filter($admins));

        if (!empty($staffIds)) {
            $invoice = $event->invoice;
            Notification::createForMultipleStaff(
                $staffIds,
                'Invoice ' . e($invoice->number ?? $invoice->id) . ' fully paid: ' . number_format($event->amount, 2),
                route('admin.invoices.show', $invoice->id),
                notificationType: 'payment',
                groupingKey: 'invoice_paid_' . $invoice->id
            );
        }
    }
}
