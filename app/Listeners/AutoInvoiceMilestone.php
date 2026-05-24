<?php

namespace App\Listeners;

use App\Events\MilestoneCompleted;
use App\Models\Invoice;
use App\Models\Itemable;
use App\Models\ProjectActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AutoInvoiceMilestone
{
    public function handle(MilestoneCompleted $event): void
    {
        $milestone = $event->milestone;
        $project = $milestone->project;
        if (!$project) return;

        $billableTasks = $milestone->tasks()->where('billable', true)->get();
        if ($billableTasks->isEmpty()) return;

        $totalAmount = $billableTasks->sum(function ($task) {
            return $task->hourly_rate ?: 0;
        });

        if ($totalAmount <= 0) return;

        $invoice = Invoice::create([
            'clientid' => $project->clientid,
            'project_id' => $project->id,
            'date' => now(),
            'duedate' => now()->addDays(30),
            'status' => Invoice::STATUS_UNPAID,
            'addedfrom' => Auth::id() ?? 1,
            'total' => $totalAmount,
            'subtotal' => $totalAmount,
            'datecreated' => now(),
        ]);

        $description = 'Billable tasks from milestone: ' . $milestone->name;
        Itemable::create([
            'rel_id' => $invoice->id,
            'rel_type' => 'invoice',
            'description' => $description,
            'qty' => 1,
            'rate' => $totalAmount,
            'amount' => $totalAmount,
        ]);

        ProjectActivity::create([
            'project_id' => $project->id,
            'staff_id' => Auth::id() ?? 1,
            'description_key' => 'project_activity_invoice_from_milestone',
            'additional_data' => json_encode([
                'milestone_name' => $milestone->name,
                'invoice_id' => $invoice->id,
                'amount' => $totalAmount,
            ]),
            'dateadded' => now(),
        ]);
    }
}
