<?php

namespace App\Listeners;

use App\Events\PaymentRecorded;
use App\Models\Notification;
use App\Models\Staff;
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

        $admins = Staff::where('admin', 1)->pluck('staffid')->toArray();
        $staffIds = array_unique(array_filter($admins));

        if (!empty($staffIds)) {
            Notification::createForMultipleStaff(
                $staffIds,
                'Payment ' . e(number_format($p->amount, 2)) . ' recorded on invoice',
                $p->invoice_id ? route('admin.invoices.show', $p->invoice_id) : null,
                notificationType: 'payment',
                groupingKey: 'payment_recorded_' . $p->invoice_id
            );
        }
    }
}
