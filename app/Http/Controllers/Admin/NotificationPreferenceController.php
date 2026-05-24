<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaffNotificationPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationPreferenceController extends Controller
{
    public function index()
    {
        $staffId = Auth::id();
        $prefs = StaffNotificationPreference::where('staffid', $staffId)->get()->keyBy('notification_type');

        $allTypes = [
            'task_assigned' => ['label' => 'Task Assigned', 'desc' => 'When a task is assigned to you'],
            'task_completed' => ['label' => 'Task Completed', 'desc' => 'When a task is marked complete'],
            'task_commented' => ['label' => 'Task Comment', 'desc' => 'When someone comments on a task'],
            'project_created' => ['label' => 'Project Created', 'desc' => 'When a new project is created'],
            'project_updated' => ['label' => 'Project Updated', 'desc' => 'When project details change'],
            'project_status' => ['label' => 'Project Status', 'desc' => 'When project status changes'],
            'project_member' => ['label' => 'Team Changes', 'desc' => 'When you are added/removed from a project'],
            'milestone' => ['label' => 'Milestone Updates', 'desc' => 'When milestones are created or completed'],
            'discussion_comment' => ['label' => 'Discussion Comments', 'desc' => 'When someone comments in a discussion'],
            'discussion_mention' => ['label' => 'Discussion Mentions', 'desc' => 'When someone @mentions you in a discussion'],
            'file_upload' => ['label' => 'File Uploads', 'desc' => 'When files are uploaded to projects'],
            'note_added' => ['label' => 'Note Additions', 'desc' => 'When notes are added to projects'],
            'invoice' => ['label' => 'Invoice Alerts', 'desc' => 'Invoice-related notifications'],
            'proposal' => ['label' => 'Proposal Alerts', 'desc' => 'Proposal-related notifications'],
            'payment' => ['label' => 'Payment Alerts', 'desc' => 'Payment-related notifications'],
        ];

        return view('admin.notifications.preferences', compact('prefs', 'allTypes'));
    }

    public function update(Request $request)
    {
        $staffId = Auth::id();

        $request->validate([
            'preferences' => 'array',
            'preferences.*' => 'array',
            'preferences.*.notification_type' => 'required|string|max:50',
            'preferences.*.in_app' => 'boolean',
            'preferences.*.email' => 'boolean',
        ]);

        foreach (($request->preferences ?? []) as $pref) {
            StaffNotificationPreference::updateOrCreate(
                [
                    'staffid' => $staffId,
                    'notification_type' => $pref['notification_type'],
                ],
                [
                    'in_app' => filter_var($pref['in_app'] ?? true, FILTER_VALIDATE_BOOLEAN),
                    'email' => filter_var($pref['email'] ?? true, FILTER_VALIDATE_BOOLEAN),
                ]
            );
        }

        // Handle toggle for individual preferences
        if ($request->has('type') && $request->has('field')) {
            $type = $request->type;
            $field = $request->field;
            $value = $request->boolean('value', true);

            StaffNotificationPreference::updateOrCreate(
                ['staffid' => $staffId, 'notification_type' => $type],
                [$field => $value]
            );

            if ($request->wantsJson()) {
                return response()->json(['success' => true]);
            }
        }

        return redirect()->route('admin.notifications.preferences.index')
            ->with('success', 'Notification preferences updated!');
    }
}
