<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function fetch()
    {
        $notifications = Notification::where('touserid', Auth::id())
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'description' => $n->description,
                    'from_fullname' => $n->from_fullname,
                    'date' => $n->date,
                    'isread' => $n->isread,
                    'link' => $n->link,
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

    public function markAllAsRead()
    {
        Notification::where('touserid', Auth::id())->where('isread', 0)->update(['isread' => 1]);
        return response()->json(['success' => true]);
    }
}
