<?php

namespace App\Models;

use App\Events\NewNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Notification extends Model
{
    protected $table = 'tblnotifications';
    public $timestamps = true;
    protected $fillable = [
        'id', 'isread', 'isread_inline', 'date', 'description', 'fromuserid',
        'fromclientid', 'from_fullname', 'touserid', 'fromcompany', 'link',
        'additional_data', 'notification_type', 'grouping_key', 'group_count',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'date' => 'datetime',
        'isread' => 'integer',
        'isread_inline' => 'boolean',
        'group_count' => 'integer',
    ];

    public function receiver()
    {
        return $this->belongsTo(Staff::class, 'touserid', 'staffid');
    }

    public function senderStaff()
    {
        return $this->belongsTo(Staff::class, 'fromuserid', 'staffid');
    }

    public function senderClient()
    {
        return $this->belongsTo(Client::class, 'fromclientid', 'userid');
    }

    public static function createForStaff(
        int $touserid,
        string $description,
        ?string $link = null,
        ?int $fromuserid = null,
        ?string $fromFullname = null,
        ?string $notificationType = null,
        ?string $groupingKey = null,
    ): self {
        $user = Auth::user();

        if ($notificationType && !self::recipientWantsInApp($touserid, $notificationType)) {
            $notif = new self();
            $notif->id = 0;
            return $notif;
        }

        $description = strip_tags($description);

        $groupCount = 1;
        if ($groupingKey && $touserid) {
            $existing = static::where('touserid', $touserid)
                ->where('grouping_key', $groupingKey)
                ->where('isread', 0)
                ->where('date', '>=', now()->subHours(24))
                ->first();

            if ($existing) {
                $existing->increment('group_count');
                $existing->update([
                    'date' => now(),
                    'description' => $description,
                    'link' => $link,
                    'fromuserid' => $fromuserid ?? ($user ? $user->staffid : 0),
                    'from_fullname' => $fromFullname ?? ($user ? ($user->firstname ?? '') . ' ' . ($user->lastname ?? '') : 'System'),
                ]);
                return $existing;
            }
        }

        $notif = static::create([
            'isread' => 0,
            'isread_inline' => 0,
            'date' => now(),
            'description' => $description,
            'fromuserid' => $fromuserid ?? ($user ? $user->staffid : 0),
            'fromclientid' => 0,
            'from_fullname' => $fromFullname ?? ($user ? ($user->firstname ?? '') . ' ' . ($user->lastname ?? '') : 'System'),
            'touserid' => $touserid,
            'fromcompany' => null,
            'link' => $link,
            'additional_data' => null,
            'notification_type' => $notificationType,
            'grouping_key' => $groupingKey,
            'group_count' => $groupCount,
        ]);

        if (config('broadcasting.default') !== 'null' && config('broadcasting.default') !== 'log') {
            try {
                $unreadCount = static::where('touserid', $touserid)->where('isread', 0)->count();
                NewNotification::dispatch($notif, $unreadCount);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::debug('Broadcast dispatch failed: ' . $e->getMessage());
            }
        }

        return $notif;
    }

    public static function createForMultipleStaff(
        array $touserids,
        string $description,
        ?string $link = null,
        ?int $fromuserid = null,
        ?string $fromFullname = null,
        ?string $notificationType = null,
        ?string $groupingKey = null,
    ): void {
        $user = Auth::user();
        $fromId = $fromuserid ?? ($user ? $user->staffid : 0);
        $fromName = $fromFullname ?? ($user ? ($user->firstname ?? '') . ' ' . ($user->lastname ?? '') : 'System');
        $date = now();
        $description = strip_tags($description);

        $records = [];
        foreach (array_unique($touserids) as $touserid) {
            if (!$touserid) continue;

            if ($notificationType && !self::recipientWantsInApp((int)$touserid, $notificationType)) {
                continue;
            }

            if ($groupingKey) {
                $existing = static::where('touserid', (int)$touserid)
                    ->where('grouping_key', $groupingKey)
                    ->where('isread', 0)
                    ->where('date', '>=', now()->subHours(24))
                    ->first();

                if ($existing) {
                    $existing->increment('group_count');
                    $existing->update([
                        'date' => now(),
                        'description' => $description,
                        'link' => $link,
                        'fromuserid' => $fromId,
                        'from_fullname' => $fromName,
                    ]);
                    continue;
                }
            }

            $records[] = [
                'isread' => 0,
                'isread_inline' => 0,
                'date' => $date,
                'description' => $description,
                'fromuserid' => $fromId,
                'fromclientid' => 0,
                'from_fullname' => $fromName,
                'touserid' => (int)$touserid,
                'fromcompany' => null,
                'link' => $link,
                'additional_data' => null,
                'notification_type' => $notificationType,
                'grouping_key' => $groupingKey,
                'group_count' => 1,
            ];
        }

        if (!empty($records)) {
            static::insert($records);

            if (config('broadcasting.default') !== 'null' && config('broadcasting.default') !== 'log') {
                try {
                    foreach (array_unique($touserids) as $touserid) {
                        if (!$touserid) continue;
                        $unreadCount = static::where('touserid', (int)$touserid)->where('isread', 0)->count();
                        $notif = static::where('touserid', (int)$touserid)->latest()->first();
                        if ($notif) {
                            NewNotification::dispatch($notif, $unreadCount);
                        }
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::debug('Broadcast dispatch failed: ' . $e->getMessage());
                }
            }
        }
    }

    private static function recipientWantsInApp(int $staffId, string $type): bool
    {
        $pref = StaffNotificationPreference::where('staffid', $staffId)
            ->where('notification_type', $type)
            ->first();

        if ($pref) {
            return $pref->in_app;
        }

        return true;
    }

    public static function recipientWantsEmail(int $staffId, string $type): bool
    {
        $pref = StaffNotificationPreference::where('staffid', $staffId)
            ->where('notification_type', $type)
            ->first();

        if ($pref) {
            return $pref->email;
        }

        return true;
    }
}
