<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Notification extends Model
{
    protected $table = 'tblnotifications';
    public $timestamps = true;
    protected $fillable = ['id', 'isread', 'isread_inline', 'date', 'description', 'fromuserid', 'fromclientid', 'from_fullname', 'touserid', 'fromcompany', 'link', 'additional_data'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'date' => 'datetime'
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

    public static function createForStaff(int $touserid, string $description, ?string $link = null, ?int $fromuserid = null, ?string $fromFullname = null): self
    {
        $user = Auth::user();
        return static::create([
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
        ]);
    }

    public static function createForMultipleStaff(array $touserids, string $description, ?string $link = null, ?int $fromuserid = null, ?string $fromFullname = null): void
    {
        $user = Auth::user();
        $fromId = $fromuserid ?? ($user ? $user->staffid : 0);
        $fromName = $fromFullname ?? ($user ? ($user->firstname ?? '') . ' ' . ($user->lastname ?? '') : 'System');
        $date = now();

        $records = [];
        foreach (array_unique($touserids) as $touserid) {
            if (!$touserid) continue;
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
            ];
        }

        if (!empty($records)) {
            static::insert($records);
        }
    }
}
