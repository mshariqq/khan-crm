<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaskTimer;
use App\Models\TimesheetAuditLog;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskTimerController extends Controller
{
    /** Start a timer for a task */
    public function start(Request $request)
    {
        if (!Auth::user()->can('tasks_create')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'task_id' => 'required|integer|exists:tbltasks,id'
        ]);

        try {
            $runningTimer = TaskTimer::where('staff_id', Auth::id())
                ->whereNull('end_time')
                ->first();

            if ($runningTimer) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already have a timer running. Please stop it first.'
                ], 400);
            }

            $task = \App\Models\Task::findOrFail($request->task_id);

            $timer = TaskTimer::create([
                'task_id' => $request->task_id,
                'staff_id' => Auth::id(),
                'start_time' => now(),
                'is_billable' => $task->billable ?? false,
                'hourly_rate' => $task->hourly_rate ?? 0
            ]);

            TimesheetAuditLog::create([
                'timer_id' => $timer->id,
                'staff_id' => Auth::id(),
                'action' => 'created',
                'changes' => ['task_id' => $request->task_id, 'action' => 'timer_started'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Timer started successfully',
                'timer' => $timer
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** Stop a running timer */
    public function stop($timerId, Request $request)
    {
        if (!Auth::user()->can('tasks_create')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $timer = TaskTimer::findOrFail($timerId);

            if ($timer->staff_id !== Auth::id()) {
                return response()->json(['success' => false, 'message' => 'Cannot stop timer you did not start'], 403);
            }

            $oldEnd = $timer->end_time;
            $timer->update(['end_time' => now()]);

            TimesheetAuditLog::create([
                'timer_id' => $timer->id,
                'staff_id' => Auth::id(),
                'action' => 'stopped',
                'changes' => ['end_time' => $timer->fresh()->end_time->toIso8601String()],
            ]);

            $duration = $timer->start_time->diffInMinutes($timer->end_time);

            return response()->json([
                'success' => true,
                'message' => 'Timer stopped successfully',
                'timer' => $timer,
                'duration_minutes' => $duration
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** Manual timesheet entry (log time) */
    public function store(Request $request)
    {
        if (!Auth::user()->can('tasks_create')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'task_id' => 'required|integer|exists:tbltasks,id',
            'date' => 'required|date',
            'hours' => 'required|integer|min:0|max:23',
            'minutes' => 'required|integer|min:0|max:59',
            'note' => 'nullable|string|max:1000',
            'is_billable' => 'nullable|boolean',
            'rounding_minutes' => 'nullable|integer|in:0,5,10,15,30,60',
        ]);

        if ((int)$request->hours === 0 && (int)$request->minutes === 0) {
            return response()->json(['success' => false, 'message' => 'Duration must be greater than 0'], 422);
        }

        try {
            $task = \App\Models\Task::findOrFail($request->task_id);
            $startTime = \Carbon\Carbon::parse($request->date . ' 09:00:00');
            $endTime = (clone $startTime)->addHours((int)$request->hours)->addMinutes((int)$request->minutes);

            $timer = TaskTimer::create([
                'task_id' => $request->task_id,
                'staff_id' => Auth::id(),
                'start_time' => $startTime,
                'end_time' => $endTime,
                'hourly_rate' => $task->hourly_rate ?? 0,
                'note' => $request->note,
                'is_billable' => $request->boolean('is_billable', $task->billable ?? false),
                'rounding_minutes' => $request->input('rounding_minutes', 0),
            ]);

            TimesheetAuditLog::create([
                'timer_id' => $timer->id,
                'staff_id' => Auth::id(),
                'action' => 'created',
                'changes' => $timer->toArray(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Time logged successfully',
                'reload' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** Edit a manual timesheet entry */
    public function update(Request $request, $id)
    {
        $timer = TaskTimer::findOrFail($id);

        if ($timer->staff_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Cannot edit another user\'s entry'], 403);
        }

        $request->validate([
            'date' => 'required|date',
            'hours' => 'required|integer|min:0|max:23',
            'minutes' => 'required|integer|min:0|max:59',
            'note' => 'nullable|string|max:1000',
            'is_billable' => 'nullable|boolean',
            'rounding_minutes' => 'nullable|integer|in:0,5,10,15,30,60',
        ]);

        try {
            $oldData = $timer->toArray();
            $startTime = \Carbon\Carbon::parse($request->date . ' 09:00:00');
            $endTime = (clone $startTime)->addHours((int)$request->hours)->addMinutes((int)$request->minutes);

            $timer->update([
                'start_time' => $startTime,
                'end_time' => $endTime,
                'note' => $request->note,
                'is_billable' => $request->boolean('is_billable', $timer->is_billable),
                'rounding_minutes' => $request->input('rounding_minutes', $timer->rounding_minutes),
            ]);

            TimesheetAuditLog::create([
                'timer_id' => $timer->id,
                'staff_id' => Auth::id(),
                'action' => 'updated',
                'changes' => ['old' => $oldData, 'new' => $timer->fresh()->toArray()],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Timesheet updated successfully',
                'reload' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** Delete a timesheet entry */
    public function destroy($id)
    {
        $timer = TaskTimer::findOrFail($id);

        if ($timer->staff_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Cannot delete another user\'s entry'], 403);
        }

        try {
            TimesheetAuditLog::create([
                'timer_id' => $timer->id,
                'staff_id' => Auth::id(),
                'action' => 'deleted',
                'changes' => $timer->toArray(),
            ]);

            $timer->delete();

            return response()->json([
                'success' => true,
                'message' => 'Timesheet deleted successfully',
                'reload' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** Get all timers for a task */
    public function getByTask($taskId)
    {
        if (!Auth::user()->can('tasks_view')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $timers = TaskTimer::with('staff')
                ->where('task_id', $taskId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'timers' => $timers
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** Approve a timesheet entry */
    public function approve($timerId)
    {
        if (!Auth::user()->can('projects_edit')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $timer = TaskTimer::findOrFail($timerId);
        $timer->update([
            'status' => TaskTimer::STATUS_APPROVED,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        TimesheetAuditLog::create([
            'timer_id' => $timer->id,
            'staff_id' => Auth::id(),
            'action' => 'approved',
            'changes' => ['approved_by' => Auth::id(), 'approved_at' => now()->toIso8601String()],
        ]);

        Notification::createForStaff(
            $timer->staff_id,
            'Your timesheet entry for <strong>' . e($timer->task->name ?? 'Task') . '</strong> has been approved',
            route('admin.projects.show', $timer->task->rel_id ?? 0)
        );

        return response()->json(['success' => true, 'message' => 'Timesheet approved']);
    }

    /** Reject a timesheet entry */
    public function reject(Request $request, $timerId)
    {
        if (!Auth::user()->can('projects_edit')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate(['reason' => 'nullable|string|max:1000']);

        $timer = TaskTimer::findOrFail($timerId);
        $timer->update([
            'status' => TaskTimer::STATUS_REJECTED,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        TimesheetAuditLog::create([
            'timer_id' => $timer->id,
            'staff_id' => Auth::id(),
            'action' => 'rejected',
            'changes' => ['reason' => $request->reason],
        ]);

        Notification::createForStaff(
            $timer->staff_id,
            'Your timesheet entry for <strong>' . e($timer->task->name ?? 'Task') . '</strong> has been rejected' . ($request->reason ? ': ' . e($request->reason) : ''),
            route('admin.projects.show', $timer->task->rel_id ?? 0)
        );

        return response()->json(['success' => true, 'message' => 'Timesheet rejected']);
    }

    /** Bulk approve timesheet entries */
    public function bulkApprove(Request $request)
    {
        if (!Auth::user()->can('projects_edit')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate(['timer_ids' => 'required|array', 'timer_ids.*' => 'integer|exists:tbltaskstimers,id']);

        $count = 0;
        foreach ($request->timer_ids as $id) {
            $timer = TaskTimer::find($id);
            if ($timer && $timer->status === TaskTimer::STATUS_PENDING) {
                $timer->update([
                    'status' => TaskTimer::STATUS_APPROVED,
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                ]);
                TimesheetAuditLog::create([
                    'timer_id' => $timer->id,
                    'staff_id' => Auth::id(),
                    'action' => 'approved',
                    'changes' => ['bulk_approve' => true],
                ]);
                $count++;
            }
        }

        return response()->json(['success' => true, 'message' => "$count timesheets approved"]);
    }
}