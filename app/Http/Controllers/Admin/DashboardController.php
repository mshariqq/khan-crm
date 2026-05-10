<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\StaffService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $staffService;

    public function __construct(StaffService $staffService)
    {
        $this->staffService = $staffService;
    }

    public function index()
    {
        $user = auth()->guard('staff')->user();
        $stats = $this->staffService->getStaffStats();
        // Load recent items for quick overview
        $latestProjects = \App\Models\Project::orderBy('created_at', 'desc')->take(5)->get();
        $latestTickets = \App\Models\Ticket::with(['statusRel'])->orderBy('created_at', 'desc')->take(5)->get();
        $latestTasks = \App\Models\Task::orderBy('created_at', 'desc')->take(5)->get();
        $latestInvoices = \App\Models\Invoice::orderBy('created_at', 'desc')->take(5)->get();

        return view('admin.dashboard', [
            'stats' => $stats,
            'user' => $user,
            'projects' => $latestProjects,
            'tickets' => $latestTickets,
            'tasks' => $latestTasks,
            'invoices' => $latestInvoices,
        ]);
    }
}
