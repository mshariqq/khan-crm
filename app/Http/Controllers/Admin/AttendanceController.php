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
        $isAdmin = Auth::user()->can('attendance_view_all');

        $query = StaffAttendance::with('staff');

        $query->when($request->filled('staff_id'), function ($q) use ($request) {
            return $q->where('staffid', $request->staff_id);
        });

        $query->when($request->filled('date_from'), function ($q) use ($request) {
            return $q->where('date', '>=', $request->date_from);
        });
        $query->when($request->filled('date_to'), function ($q) use ($request) {
            return $q->where('date', '<=', $request->date_to);
        });

        $query->when($request->filled('status'), function ($q) use ($request) {
            return $q->where('status', $request->status);
        });

        $query->when($request->filled('search'), function ($q) use ($request) {
            $search = $request->search;
            return $q->whereHas('staff', function ($sq) use ($search) {
                $sq->where('firstname', 'like', "%$search%")
                   ->orWhere('lastname', 'like', "%$search%")
                   ->orWhere('email', 'like', "%$search%");
            })->orWhere('ip_address', 'like', "%$search%");
        });

        if (!$isAdmin && !$request->filled('staff_id')) {
            $query->where('staffid', $staffId);
        }

        $perPage = SettingsHelper::get('tables_pagination_limit', 25);
        $attendances = $query->orderBy('date', 'desc')->paginate($perPage)->withQueryString();

        return view('admin.attendance.index', compact('attendances'));
    }

    public function clockIn(Request $request)
    {
        if (!Auth::user()->can('attendance_create')) abort(403);
        $staffId = Auth::id();
        $today = Carbon::today()->toDateString();

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

        $now = Carbon::now();
        $gracePeriod = (int) SettingsHelper::get('late_grace_period', 15);
        $shiftStartTime = SettingsHelper::get('shift_start_time', '09:00');
        $shiftStart = Carbon::createFromTimeString($shiftStartTime)->addMinutes($gracePeriod);

        $status = $now->greaterThan($shiftStart) ? 'Late' : SettingsHelper::get('attendance_default_status', 'Present');
        $recordIp = SettingsHelper::get('attendance_require_ip', '0') === '1';

        $attendance = StaffAttendance::create([
            'staffid' => $staffId,
            'date' => $today,
            'clock_in' => $now->toTimeString(),
            'status' => $status,
            'ip_address' => $recordIp ? $request->ip() : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Clocked in successfully!' . ($status === 'Late' ? ' (Late arrival detected)' : ''),
            'data' => $attendance
        ]);
    }

    public function clockOut(Request $request)
    {
        if (!Auth::user()->can('attendance_create')) abort(403);
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

        $now = Carbon::now();
        $shiftEndTime = SettingsHelper::get('shift_end_time', '18:00');
        $shiftEnd = Carbon::createFromTimeString($shiftEndTime);
        $gracePeriod = (int) SettingsHelper::get('late_grace_period', 15);
        $earlyDeparture = $now->lessThan($shiftEnd->subMinutes($gracePeriod));

        $status = $attendance->status;
        if ($earlyDeparture && $status !== 'Late') {
            $status = 'Early Departure';
        } elseif ($earlyDeparture) {
            $status = 'Late / Early Departure';
        }

        $attendance->update([
            'clock_out' => $now->toTimeString(),
            'status' => $status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Clocked out successfully!' . ($earlyDeparture ? ' (Early departure detected)' : ''),
            'data' => $attendance
        ]);
    }

    public function getStatus()
    {
        if (!Auth::user()->can('attendance_view')) abort(403);
        $staffId = Auth::id();
        $today = Carbon::today()->toDateString();

        $attendance = StaffAttendance::where('staffid', $staffId)
            ->where('date', $today)
            ->first();

        $workedDuration = null;
        $overtime = null;
        $expectedSeconds = (float) SettingsHelper::get('default_working_hours', 8) * 3600;

        if ($attendance && $attendance->clock_in && $attendance->clock_out) {
            $clockIn = Carbon::parse($attendance->date . ' ' . $attendance->clock_in);
            $clockOut = Carbon::parse($attendance->date . ' ' . $attendance->clock_out);
            $workedSeconds = $clockOut->diffInSeconds($clockIn);
            $workedDuration = gmdate('H:i:s', $workedSeconds);

            if ($workedSeconds > $expectedSeconds) {
                $overtimeSeconds = $workedSeconds - $expectedSeconds;
                $overtime = gmdate('H:i:s', $overtimeSeconds);
            }
        }

        return response()->json([
            'clocked_in' => $attendance ? true : false,
            'clocked_out' => $attendance && $attendance->clock_out ? true : false,
            'clock_in_time' => $attendance ? $attendance->date . ' ' . $attendance->clock_in : null,
            'clock_out_time' => $attendance ? $attendance->date . ' ' . $attendance->clock_out : null,
            'worked_duration' => $workedDuration,
            'overtime' => $overtime,
            'expected_hours' => SettingsHelper::get('default_working_hours', 8),
            'attendance' => $attendance
        ]);
    }
}
