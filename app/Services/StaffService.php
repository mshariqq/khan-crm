<?php

namespace App\Services;

use App\Models\Staff;

class StaffService extends BaseService
{
    public function getStaffStats()
    {
        return [
            'total_projects' => \App\Models\Project::count(),
            'pending_tasks' => \App\Models\Task::where('status', 0)->count(),
            'total_clients' => \App\Models\Client::count(),
            'active_projects' => \App\Models\Project::where('status', 1)->count(),
            'overdue_invoices' => \App\Models\Invoice::where('status', 1)
                ->where('duedate', '<', now())
                ->count(),
            'open_tickets' => \App\Models\Ticket::where('status', 1)->count(),
            'revenue_this_month' => \App\Models\Invoice::where('status', 3)
                ->whereMonth('datecreated', now()->month)
                ->sum('total'),
        ];
    }

    public function updateProfile(Staff $staff, array $data)
    {
        return $staff->update($data);
    }
}
