<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Services\InvoiceNotificationService;
use Illuminate\Console\Command;

class InvoicesCheckOverdue extends Command
{
    protected $signature = 'invoices:check-overdue';
    protected $description = 'Check for overdue invoices and send notifications';

    public function handle(InvoiceNotificationService $notificationService): int
    {
        $overdueInvoices = Invoice::whereNotNull('duedate')
            ->where('duedate', '<', now())
            ->whereIn('status', [Invoice::STATUS_UNPAID, Invoice::STATUS_PARTIALLY_PAID])
            ->get();

        $count = 0;
        foreach ($overdueInvoices as $invoice) {
            $notificationService->send('invoice-overdue', $invoice->id);
            $notificationService->send('invoice-overdue-staff', $invoice->id);

            $invoice->update([
                'status' => Invoice::STATUS_OVERDUE,
                'last_overdue_reminder' => now(),
            ]);

            $count++;
        }

        $overdueInvoicesDueToday = Invoice::whereNotNull('duedate')
            ->whereDate('duedate', now()->format('Y-m-d'))
            ->where('status', Invoice::STATUS_UNPAID)
            ->get();

        foreach ($overdueInvoicesDueToday as $invoice) {
            $notificationService->send('invoice-due-today', $invoice->id);
        }

        $this->info("Processed {$count} overdue invoices.");

        return Command::SUCCESS;
    }
}
