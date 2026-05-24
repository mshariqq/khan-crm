<?php

namespace App\Services;

use App\Models\Staff;
use App\Models\Project;
use App\Models\Task;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoicePaymentRecord;
use App\Models\Ticket;
use App\Models\Expense;
use App\Models\Lead;
use App\Models\LeadStatus;
use App\Models\ActivityLog;
use Carbon\Carbon;

class StaffService extends BaseService
{
    public function getStaffStats()
    {
        $now = Carbon::now();
        $prevMonth = Carbon::now()->subMonth();

        $revenueThisMonth = InvoicePaymentRecord::whereYear('date', $now->year)
            ->whereMonth('date', $now->month)->sum('amount');
        $revenuePrevMonth = InvoicePaymentRecord::whereYear('date', $prevMonth->year)
            ->whereMonth('date', $prevMonth->month)->sum('amount');

        $clientsThisMonth = Client::whereYear('datecreated', $now->year)
            ->whereMonth('datecreated', $now->month)->count();
        $clientsPrevMonth = Client::whereYear('datecreated', $prevMonth->year)
            ->whereMonth('datecreated', $prevMonth->month)->count();

        $totalProjects = Project::count();
        $activeProjects = Project::where('status', Project::STATUS_ACTIVE)->count();
        $completedProjects = Project::where('status', Project::STATUS_COMPLETED)->count();

        return [
            'total_revenue' => InvoicePaymentRecord::sum('amount'),
            'revenue_this_month' => $revenueThisMonth,
            'revenue_trend' => $revenuePrevMonth > 0 ? round((($revenueThisMonth - $revenuePrevMonth) / $revenuePrevMonth) * 100, 1) : ($revenueThisMonth > 0 ? 100 : 0),
            'total_projects' => $totalProjects,
            'active_projects' => $activeProjects,
            'completed_projects' => $completedProjects,
            'project_completion_rate' => $totalProjects > 0 ? round(($completedProjects / $totalProjects) * 100) : 0,
            'total_clients' => Client::count(),
            'new_clients_month' => $clientsThisMonth,
            'clients_trend' => $clientsPrevMonth > 0 ? round((($clientsThisMonth - $clientsPrevMonth) / $clientsPrevMonth) * 100, 1) : ($clientsThisMonth > 0 ? 100 : 0),
            'pending_tasks' => Task::where('status', Task::STATUS_BACKLOG)->count(),
            'in_progress_tasks' => Task::where('status', Task::STATUS_PENDING)->count(),
            'completed_tasks' => Task::where('status', Task::STATUS_COMPLETED)->count(),
            'overdue_invoices' => Invoice::where('status', Invoice::STATUS_UNPAID)->where('duedate', '<', $now)->count(),
            'open_tickets' => Ticket::where('status', 1)->count(),
            'expense_this_month' => Expense::whereYear('date', $now->year)
                ->whereMonth('date', $now->month)->sum('amount'),
            'profit_this_month' => $revenueThisMonth - Expense::whereYear('date', $now->year)
                ->whereMonth('date', $now->month)->sum('amount'),
            'total_leads' => Lead::count(),
            'converted_leads' => Lead::whereNotNull('date_converted')->count(),
            'lead_conversion_rate' => Lead::count() > 0 ? round((Lead::whereNotNull('date_converted')->count() / Lead::count()) * 100, 1) : 0,
        ];
    }

    public function getRevenueChartData()
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $revenue = (float) InvoicePaymentRecord::whereYear('date', $month->year)
                ->whereMonth('date', $month->month)->sum('amount');
            $data[] = [
                'month' => $month->format('M Y'),
                'revenue' => $revenue,
            ];
        }
        return $data;
    }

    public function getExpenseChartData()
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $expense = (float) Expense::whereYear('date', $month->year)
                ->whereMonth('date', $month->month)->sum('amount');
            $data[] = [
                'month' => $month->format('M Y'),
                'expense' => $expense,
            ];
        }
        return $data;
    }

    public function getTaskStatusDistribution()
    {
        $colors = ['#94a3b8', '#3b82f6', '#f59e0b', '#10b981'];
        $labels = ['To Do', 'In Progress', 'In Review', 'Done'];
        $statuses = [];
        foreach ([Task::STATUS_BACKLOG, Task::STATUS_PENDING, Task::STATUS_IN_PROGRESS, Task::STATUS_COMPLETED] as $i => $status) {
            $count = Task::where('status', $status)->count();
            if ($count > 0) {
                $statuses[] = [
                    'label' => $labels[$i],
                    'value' => $count,
                    'color' => $colors[$i],
                ];
            }
        }
        return $statuses;
    }

    public function getProjectStatusDistribution()
    {
        return [
            ['label' => 'Active', 'value' => Project::where('status', Project::STATUS_ACTIVE)->count(), 'color' => '#10b981'],
            ['label' => 'Completed', 'value' => Project::where('status', Project::STATUS_COMPLETED)->count(), 'color' => '#3b82f6'],
            ['label' => 'On Hold', 'value' => Project::where('status', Project::STATUS_ON_HOLD)->count(), 'color' => '#f59e0b'],
        ];
    }

    public function getTicketStatusDistribution()
    {
        return Ticket::with('statusRel')
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get()
            ->map(function ($ticket) {
                $colors = ['#94a3b8', '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];
                $idx = min($ticket->status, count($colors) - 1);
                return [
                    'label' => $ticket->statusRel->name ?? 'Status ' . $ticket->status,
                    'value' => $ticket->count,
                    'color' => $colors[$idx] ?? '#94a3b8',
                ];
            })->toArray();
    }

    public function getLeadStatusDistribution()
    {
        return Lead::with('statusRel')
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get()
            ->map(function ($lead) {
                return [
                    'label' => $lead->statusRel->name ?? 'Status ' . $lead->status,
                    'value' => $lead->count,
                    'color' => $lead->statusRel->color ?? '#94a3b8',
                ];
            })->toArray();
    }

    public function getClientGrowthData()
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $count = Client::whereYear('datecreated', $month->year)
                ->whereMonth('datecreated', $month->month)->count();
            $data[] = [
                'month' => $month->format('M Y'),
                'clients' => $count,
            ];
        }
        return $data;
    }

    public function getUpcomingDeadlines()
    {
        $now = Carbon::now();
        $nextWeek = Carbon::now()->addDays(7);

        $tasksDue = Task::where('status', '!=', Task::STATUS_COMPLETED)
            ->whereNotNull('duedate')
            ->whereBetween('duedate', [$now, $nextWeek])
            ->orderBy('duedate')
            ->take(5)
            ->get(['id', 'name as title', 'duedate', 'rel_id', 'rel_type']);

        $overdueInvoices = Invoice::where('status', Invoice::STATUS_UNPAID)
            ->where('duedate', '<', $now)
            ->orderBy('duedate')
            ->take(5)
            ->with('currency')
            ->get(['id', 'formatted_number as title', 'duedate', 'total', 'clientid']);

        return [
            'tasks_due' => $tasksDue,
            'overdue_invoices' => $overdueInvoices,
        ];
    }

    public function getTopClientsByRevenue()
    {
        return Client::selectRaw('tblclients.userid, tblclients.company, sum(tblinvoicepaymentrecords.amount) as total_revenue')
            ->join('tblinvoices', 'tblclients.userid', '=', 'tblinvoices.clientid')
            ->join('tblinvoicepaymentrecords', 'tblinvoices.id', '=', 'tblinvoicepaymentrecords.invoiceid')
            ->groupBy('tblclients.userid', 'tblclients.company')
            ->orderByDesc('total_revenue')
            ->take(5)
            ->get();
    }

    public function getRecentActivity()
    {
        return ActivityLog::orderBy('date', 'desc')->take(10)->get();
    }

    public function updateProfile(Staff $staff, array $data)
    {
        return $staff->update($data);
    }
}
