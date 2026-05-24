<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\StaffService;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\Task;
use App\Models\Invoice;
use App\Models\Currency;

class DashboardController extends Controller
{
    protected $staffService;

    public function __construct(StaffService $staffService)
    {
        $this->staffService = $staffService;
    }

    public function index()
    {
        if (!auth()->user()->can('dashboard_view')) abort(403);
        $user = auth()->guard('staff')->user();
        $stats = $this->staffService->getStaffStats();
        $projects = Project::with('client')->orderBy('created_at', 'desc')->take(5)->get();
        $tickets = Ticket::with(['statusRel', 'assignedStaff'])->orderBy('date', 'desc')->take(5)->get();
        $tasks = Task::with(['assignedStaff', 'project'])->orderBy('dateadded', 'desc')->take(5)->get();
        $invoices = Invoice::with('client', 'currency')->orderBy('datecreated', 'desc')->take(5)->get();

        $revenueChart = $this->staffService->getRevenueChartData();
        $expenseChart = $this->staffService->getExpenseChartData();
        $taskStatusDist = $this->staffService->getTaskStatusDistribution();
        $projectStatusDist = $this->staffService->getProjectStatusDistribution();
        $leadStatusDist = $this->staffService->getLeadStatusDistribution();
        $upcomingDeadlines = $this->staffService->getUpcomingDeadlines();
        $topClients = $this->staffService->getTopClientsByRevenue();
        $recentActivity = $this->staffService->getRecentActivity();
        $baseCurrency = Currency::where('isdefault', 1)->first();

        return view('admin.dashboard', compact(
            'stats', 'user', 'projects', 'tickets', 'tasks', 'invoices',
            'revenueChart', 'expenseChart', 'taskStatusDist', 'projectStatusDist',
            'leadStatusDist', 'upcomingDeadlines',
            'topClients', 'recentActivity', 'baseCurrency'
        ));
    }
}
