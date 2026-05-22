<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaskChecklistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskChecklistController extends Controller
{
    /** Store a new checklist item */
    public function store(Request $request)
    {
        if (!Auth::user()->can('tasks_create')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'task_id' => 'required|integer|exists:tbltasks,id',
            'description' => 'required|string|max:500'
        ]);

        try {
            $maxOrder = TaskChecklistItem::where('taskid', $request->task_id)
                ->max('list_order');

            $item = TaskChecklistItem::create([
                'taskid' => $request->task_id,
                'description' => $request->description,
                'finished' => 0,
                'dateadded' => now(),
                'addedfrom' => Auth::id(),
                'list_order' => ($maxOrder ?? 0) + 1
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Checklist item added successfully',
                'item' => $item
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** Update a checklist item */
    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('tasks_edit')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'finished' => 'required|boolean'
        ]);

        try {
            $item = TaskChecklistItem::findOrFail($id);

            // Check if user has permission to modify checklist
            if (!Auth::user()->can('tasks_edit_all')) {
                // Check if user is assigned to task
                $task = \App\Models\Task::findOrFail($item->taskid);
                if (!$task->assignedStaff()->where('staffid', Auth::id())->exists()) {
                    return response()->json(['success' => false, 'message' => 'Not assigned to this task'], 403);
                }
            }

            $item->update([
                'finished' => $request->finished
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Checklist item updated successfully',
                'item' => $item
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** Delete a checklist item */
    public function destroy($id)
    {
        if (!Auth::user()->can('tasks_delete')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $item = TaskChecklistItem::findOrFail($id);

            // Check permissions
            if (!Auth::user()->can('tasks_delete_all')) {
                // Users can only delete their own checklist items (if we want to add ownership later)
                return response()->json(['success' => false, 'message' => 'Cannot delete checklist item'], 403);
            }

            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Checklist item deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** Get checklist items for a task */
    public function getByTask($taskId)
    {
        if (!Auth::user()->can('tasks_view')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $items = TaskChecklistItem::where('taskid', $taskId)
                ->orderBy('list_order')
                ->get();

            return response()->json([
                'success' => true,
                'items' => $items
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** Update checklist item order for drag & drop */
    public function updateOrder(Request $request)
    {
        if (!Auth::user()->can('tasks_edit')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'order' => 'required|array',
            'order.*.id' => 'required|integer|exists:tbltasks_checklist_items,id',
            'order.*.order' => 'required|integer|min:0'
        ]);

        try {
            foreach ($request->order as $item) {
                TaskChecklistItem::where('id', $item['id'])
                    ->update(['list_order' => $item['order']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Checklist order updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}