<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaskTimer;
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
            // Check if there's already a running timer for this user
            $runningTimer = TaskTimer::where('staff_id', Auth::id())
                ->whereNull('end_time')
                ->first();

            if ($runningTimer) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already have a timer running. Please stop it first.'
                ], 400);
            }

            // Get the task to check if it's billable
            $task = \App\Models\Task::findOrFail($request->task_id);

            $timer = TaskTimer::create([
                'task_id' => $request->task_id,
                'staff_id' => Auth::id(),
                'start_time' => now(),
                'is_billable' => $task->billable ?? false,
                'hourly_rate' => $task->hourly_rate ?? 0
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

            // Check if you own this timer
            if ($timer->staff_id !== Auth::id()) {
                return response()->json(['success' => false, 'message' => 'Cannot stop timer you did not start'], 403);
            }

            // Stop the timer
            $timer->update([
                'end_time' => now()
            ]);

            // Calculate the duration
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
}