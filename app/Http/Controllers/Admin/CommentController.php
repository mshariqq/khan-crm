<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaskComment;
use App\Models\Task;
use App\Models\ProjectDiscussionComment;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /** Store a new comment for a task */
    public function store(Request $request)
    {
        \Log::info('Comment store called', ['task_id' => $request->task_id, 'content' => $request->content]);
        
        if (!Auth::user()->can('tasks_view')) {
            \Log::warning('Unauthorized comment attempt');
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'content' => 'required|string|max:2000',
            'task_id' => 'required|integer|exists:tbltasks,id',
        ]);

        try {
            \Log::info('Comment store request', [
                'content' => $request->content,
                'task_id' => $request->task_id,
                'staffid' => Auth::id()
            ]);

            $comment = TaskComment::create([
                'content' => $request->content,
                'taskid' => $request->task_id,
                'staffid' => Auth::id(),
                'dateadded' => now()
            ]);

            // Notify assigned staff about the comment (except the commenter)
            if ($request->task_id) {
                $task = Task::with('assignedStaff')->find($request->task_id);
                if ($task) {
                    foreach ($task->assignedStaff as $staffMember) {
                        if ($staffMember->staffid !== Auth::id() && $staffMember->email) {
                            $this->emailService->send($staffMember->email, 'task-commented', [
                                'staff_firstname' => $staffMember->firstname,
                                'task_name' => $task->name,
                                'task_comment' => $request->content,
                                'task_link' => route('admin.tasks.show', $task->id),
                            ]);
                        }
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully',
                'comment' => $comment->load('addedFrom')
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** Update an existing comment */
    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('tasks_edit')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'content' => 'required|string|max:2000'
        ]);

        try {
            $comment = TaskComment::findOrFail($id);

            // Check if user owns the comment or has permission to edit others
            if ($comment->staffid !== Auth::id() && !Auth::user()->can('tasks_edit_all')) {
                return response()->json(['success' => false, 'message' => 'Cannot edit others comments'], 403);
            }

            $comment->update([
                'content' => $request->content
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Comment updated successfully',
                'comment' => $comment
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** Delete a comment */
    public function destroy($id)
    {
        if (!Auth::user()->can('tasks_delete_all')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $comment = TaskComment::findOrFail($id);
            $comment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}