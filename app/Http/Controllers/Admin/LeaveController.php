<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SettingsHelper;
use App\Http\Controllers\Controller;
use App\Models\StaffLeave;
use App\Models\LeaveType;
use App\Models\CompanyHoliday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->can('hr_leaves_view')) abort(403);

        $staffId = Auth::id();
        $isAdmin = Auth::user()->can('hr_leaves_manage');

        $query = StaffLeave::with(['staff', 'type', 'approver']);

        // Filter by Staff
        $query->when($request->filled('staff_id'), function ($q) use ($request) {
            return $q->where('staffid', $request->staff_id);
        });

        // Filter by Leave Type
        $query->when($request->filled('type_id'), function ($q) use ($request) {
            return $q->where('leave_type_id', $request->type_id);
        });

        // Filter by Date Range
        $query->when($request->filled('date_from'), function ($q) use ($request) {
            return $q->where('start_date', '>=', $request->date_from);
        });
        $query->when($request->filled('date_to'), function ($q) use ($request) {
            return $q->where('end_date', '<=', $request->date_to);
        });

        // Filter by Status
        $query->when($request->filled('status'), function ($q) use ($request) {
            return $q->where('status', $request->status);
        });

        // Search by Staff Name or Reason
        $query->when($request->filled('search'), function ($q) use ($request) {
            $search = $request->search;
            return $q->whereHas('staff', function ($sq) use ($search) {
                $sq->where('firstname', 'like', "%$search%")
                   ->orWhere('lastname', 'like', "%$search%")
                   ->orWhere('email', 'like', "%$search%");
            })->orWhere('reason', 'like', "%$search%");
        });

        // Handle Non-Admin view restriction
        if (!$isAdmin && !$request->filled('staff_id')) {
            $query->where('staffid', $staffId);
        }

        $perPage = SettingsHelper::get('tables_pagination_limit', 25);
        $leaves = $query->orderBy('start_date', 'desc')->paginate($perPage)->withQueryString();

        return view('admin.leaves.index', compact('leaves'));
    }

    public function create()
    {
        if (!Auth::user()->can('hr_leaves_view')) abort(403);
        $types = LeaveType::all();
        $staffId = Auth::id();
        $currentYear = date('Y');

        $balances = [];
        foreach ($types as $type) {
            $used = StaffLeave::where('staffid', $staffId)
                ->where('leave_type_id', $type->id)
                ->where('status', 'Approved')
                ->whereYear('start_date', $currentYear)
                ->get()
                ->sum(function ($l) {
                    return max(0, \Carbon\Carbon::parse($l->end_date)->diffInDays(\Carbon\Carbon::parse($l->start_date)) + 1);
                });
            $balances[$type->id] = [
                'used' => $used,
                'remaining' => max(0, $type->default_quota - $used),
            ];
        }

        return view('admin.leaves.create', compact('types', 'balances'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('hr_leaves_view')) abort(403);

        $backdateDays = (int) SettingsHelper::get('leave_allow_backdate_days', '0');
        $maxStartDate = $backdateDays > 0
            ? Carbon::today()->subDays($backdateDays)->toDateString()
            : Carbon::today()->toDateString();

        $request->validate([
            'leave_type_id' => 'required|integer|exists:tblleave_types,id',
            'start_date' => 'required|date|after_or_equal:' . $maxStartDate,
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
        ]);

        $staffId = Auth::id();
        $type = LeaveType::findOrFail($request->leave_type_id);

        $requestedDays = \Carbon\Carbon::parse($request->end_date)->diffInDays(\Carbon\Carbon::parse($request->start_date)) + 1;

        $usedDays = StaffLeave::where('staffid', $staffId)
            ->where('leave_type_id', $type->id)
            ->where('status', 'Approved')
            ->whereYear('start_date', date('Y'))
            ->get()
            ->sum(function ($l) {
                return max(0, \Carbon\Carbon::parse($l->end_date)->diffInDays(\Carbon\Carbon::parse($l->start_date)) + 1);
            });

        $remaining = $type->default_quota - $usedDays;

        if ($requestedDays > $remaining) {
            return redirect()->back()->withInput()->with('error', "Insufficient leave balance. You have only {$remaining} day(s) remaining for {$type->name}.");
        }

        $defaultStatus = SettingsHelper::get('leave_default_status', 'Pending');

        StaffLeave::create([
            'staffid' => $staffId,
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => $defaultStatus,
        ]);

        return redirect()->route('admin.leaves.index')->with('success', 'Leave request submitted successfully!');
    }

    public function updateStatus(Request $request, $id)
    {
        if (!Auth::user()->can('hr_leaves_manage')) abort(403);

        $request->validate([
            'status' => 'required|in:Approved,Rejected',
        ]);

        $leave = StaffLeave::findOrFail($id);
        $leave->update([
            'status' => $request->status,
            'approved_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Leave request has been {$request->status}."
        ]);
    }

    public function holidays()
    {
        if (!Auth::user()->can('hr_leaves_view')) abort(403);
        $perPage = SettingsHelper::get('tables_pagination_limit', 25);
        $holidays = CompanyHoliday::orderBy('date', 'asc')->paginate($perPage);
        return view('admin.leaves.holidays', compact('holidays'));
    }

    public function storeHoliday(Request $request)
    {
        if (!Auth::user()->can('hr_leaves_manage')) abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        CompanyHoliday::create($request->only(['name', 'date', 'description']));

        return redirect()->route('admin.leaves.holidays')->with('success', 'Holiday added successfully!');
    }

    public function destroyHoliday($id)
    {
        if (!Auth::user()->can('hr_leaves_manage')) abort(403);
        CompanyHoliday::findOrFail($id)->delete();
        return redirect()->route('admin.leaves.holidays')->with('success', 'Holiday deleted successfully!');
    }
}
