<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SettingsHelper;
use App\Http\Controllers\Controller;
use App\Models\StaffAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->can('attendance_view')) abort(403);

        $staffId = Auth::id();
        $isAdmin = Auth::user()->admin;

        $query = StaffAttendance::with('staff');

        // Filter by Staff
        $query->when($request->filled('staff_id'), function ($q) use ($request) {
            return $q->where('staffid', $request->staff_id);
        });

        // Filter by Date Range
        $query->when($request->filled('date_from'), function ($q) use ($request) {
            return $q->where('date', '>=', $request->date_from);
        });
        $query->when($request->filled('date_to'), function ($q) use ($request) {
            return $q->where('date', '<=', $request->date_to);
        });

        // Filter by Status
        $query->when($request->filled('status'), function ($q) use ($request) {
            return $q->where('status', $request->status);
        });

        // Search by Staff Name or IP
        $query->when($request->filled('search'), function ($q) use ($request) {
            $search = $request->search;
            return $q->whereHas('staff', function ($sq) use ($search) {
                $sq->where('firstname', 'like', "%$search%")
                   ->orWhere('lastname', 'like', "%$search%")
                   ->orWhere('email', 'like', "%$search%");
            })->orWhere('ip_address', 'like', "%$search%");
        });

        // Handle Non-Admin view restriction
        if (!$isAdmin && !$request->filled('staff_id')) {
            $query->where('staffid', $staffId);
        }

        $perPage = SettingsHelper::get('tables_pagination_limit', 25);
        $attendances = $query->orderBy('date', 'desc')->paginate($perPage)->withQueryString();

        return view('admin.attendance.index', compact('attendances'));
    }

    public function clockIn(Request $request)
    {
        $staffId = Auth::id();
        $today = Carbon::today()->toDateString();

        // Validation: Already clocked in today
        $existing = StaffAttendance::where('staffid', $staffId)
            ->where('date', $today)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'You have already clocked in for today.',
                'data' => $existing
            ], 422);
        }

        $defaultStatus = SettingsHelper::get('attendance_default_status', 'Present');
        $recordIp = SettingsHelper::get('attendance_require_ip', '0') === '1';

        $attendance = StaffAttendance::create([
            'staffid' => $staffId,
            'date' => $today,
            'clock_in' => Carbon::now()->toTimeString(),
            'status' => $defaultStatus,
            'ip_address' => $recordIp ? $request->ip() : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Clocked in successfully!',
            'data' => $attendance
        ]);
    }

    public function clockOut(Request $request)
    {
        $staffId = Auth::id();
        $today = Carbon::today()->toDateString();

        $attendance = StaffAttendance::where('staffid', $staffId)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'No clock-in record found for today. Please clock in first.',
            ], 422);
        }

        if ($attendance->clock_out) {
            return response()->json([
                'success' => false,
                'message' => 'You have already clocked out for today.',
            ], 422);
        }

        $attendance->update([
            'clock_out' => Carbon::now()->toTimeString(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Clocked out successfully!',
            'data' => $attendance
        ]);
    }

    public function getStatus()
    {
        $staffId = Auth::id();
        $today = Carbon::today()->toDateString();

        $attendance = StaffAttendance::where('staffid', $staffId)
            ->where('date', $today)
            ->first();

        $workedDuration = null;
        if ($attendance && $attendance->clock_in && $attendance->clock_out) {
            $clockIn = Carbon::parse($attendance->date . ' ' . $attendance->clock_in);
            $clockOut = Carbon::parse($attendance->date . ' ' . $attendance->clock_out);
            $workedDuration = $clockOut->diff($clockIn)->format('%H:%I:%S');
        }

        return response()->json([
            'clocked_in' => $attendance ? true : false,
            'clocked_out' => $attendance && $attendance->clock_out ? true : false,
            'clock_in_time' => $attendance ? $attendance->date . ' ' . $attendance->clock_in : null,
            'clock_out_time' => $attendance ? $attendance->date . ' ' . $attendance->clock_out : null,
            'worked_duration' => $workedDuration,
            'attendance' => $attendance
        ]);
    }
}
