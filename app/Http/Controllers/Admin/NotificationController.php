<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\StaffNotificationPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function fetch()
    {
        $notifications = Notification::where('touserid', Auth::id())
            ->orderBy('date', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($n) {
                $desc = $n->description;
                $desc = strip_tags($desc, '<strong><em><b><i><u><a>');

                return [
                    'id' => $n->id,
                    'description' => $desc,
                    'from_fullname' => $n->from_fullname,
                    'date' => $n->date,
                    'isread' => $n->isread,
                    'link' => $n->link,
                    'group_count' => $n->group_count > 1 ? $n->group_count : null,
                    'notification_type' => $n->notification_type,
                    'time_ago' => \Carbon\Carbon::parse($n->date)->diffForHumans(),
                ];
            });

        return response()->json($notifications);
    }

    public function unreadCount()
    {
        $count = Notification::where('touserid', Auth::id())->where('isread', 0)->count();
        return response()->json(['count' => $count]);
    }

    public function markAsRead($id)
    {
        $notif = Notification::where('id', $id)->where('touserid', Auth::id())->first();
        if ($notif) {
            $notif->update(['isread' => 1]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }

    public function markAsUnread($id)
    {
        $notif = Notification::where('id', $id)->where('touserid', Auth::id())->first();
        if ($notif) {
            $notif->update(['isread' => 0]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }

    public function markAllAsRead()
    {
        Notification::where('touserid', Auth::id())->where('isread', 0)->update(['isread' => 1]);
        return response()->json(['success' => true]);
    }

    public function preferences()
    {
        $staffId = Auth::id();
        $prefs = StaffNotificationPreference::where('staffid', $staffId)->get()->keyBy('notification_type');

        $allTypes = [
            'task_assigned' => 'Task Assigned',
            'task_completed' => 'Task Completed',
            'task_commented' => 'Task Comment',
            'project_created' => 'Project Created',
            'project_updated' => 'Project Updated',
            'project_status' => 'Project Status Change',
            'project_member' => 'Project Member Changes',
            'milestone' => 'Milestone Updates',
            'discussion_comment' => 'Discussion Comments',
            'discussion_mention' => 'Discussion Mentions',
            'file_upload' => 'File Uploads',
            'note_added' => 'Note Additions',
            'invoice' => 'Invoice Notifications',
            'proposal' => 'Proposal Notifications',
            'payment' => 'Payment Notifications',
        ];

        return response()->json([
            'preferences' => $prefs,
            'all_types' => $allTypes,
        ]);
    }

    public function updatePreferences(Request $request)
    {
        $staffId = Auth::id();
        $data = $request->validate([
            'preferences' => 'required|array',
            'preferences.*.notification_type' => 'required|string|max:50',
            'preferences.*.in_app' => 'boolean',
            'preferences.*.email' => 'boolean',
        ]);

        foreach ($data['preferences'] as $pref) {
            StaffNotificationPreference::updateOrCreate(
                [
                    'staffid' => $staffId,
                    'notification_type' => $pref['notification_type'],
                ],
                [
                    'in_app' => $pref['in_app'] ?? true,
                    'email' => $pref['email'] ?? true,
                ]
            );
        }

        return response()->json(['success' => true]);
    }

    public function fetchGrouped()
    {
        $notifications = Notification::where('touserid', Auth::id())
            ->orderBy('date', 'desc')
            ->limit(50)
            ->get()
            ->groupBy(function ($n) {
                return $n->grouping_key ?: 'nokey_' . $n->id;
            })
            ->map(function ($group) {
                $first = $group->first();
                $count = $group->sum('group_count');

                return [
                    'id' => $first->id,
                    'description' => strip_tags($first->description, '<strong><em><b><i><u><a>'),
                    'from_fullname' => $first->from_fullname,
                    'date' => $first->date,
                    'isread' => $group->every(fn($n) => $n->isread) ? 1 : 0,
                    'link' => $first->link,
                    'group_count' => $count > 1 ? $count : null,
                    'time_ago' => \Carbon\Carbon::parse($first->date)->diffForHumans(),
                ];
            })
            ->values();

        return response()->json($notifications);
    }
}
