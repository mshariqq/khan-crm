<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Notification $notification,
        public int $unreadCount,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('user.' . $this->notification->touserid),
        ];
    }

    public function broadcastAs(): string
    {
        return 'new-notification';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->notification->id,
            'description' => $this->notification->description,
            'from_fullname' => $this->notification->from_fullname,
            'link' => $this->notification->link,
            'time_ago' => $this->notification->date->diffForHumans(),
            'group_count' => $this->notification->group_count > 1 ? $this->notification->group_count : null,
            'unread_count' => $this->unreadCount,
        ];
    }
}
