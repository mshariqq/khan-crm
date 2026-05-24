<?php

namespace App\Listeners;

use App\Events\ProjectCompleted;
use App\Models\Invoice;
use App\Models\ProjectActivity;

class FinalizeProjectInvoices
{
    public function handle(ProjectCompleted $event): void
    {
        $project = $event->project;

        $project->invoices()
            ->whereIn('status', [Invoice::STATUS_UNPAID, Invoice::STATUS_PARTIALLY_PAID])
            ->update(['status' => Invoice::STATUS_PAID]);

        ProjectActivity::create([
            'project_id' => $project->id,
            'staff_id' => 1,
            'description_key' => 'project_activity_invoices_finalized',
            'additional_data' => json_encode(['auto_finalized' => true]),
            'dateadded' => now(),
        ]);
    }
}
