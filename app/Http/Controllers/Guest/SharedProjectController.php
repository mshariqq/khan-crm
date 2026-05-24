<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\ProjectShare;
use Illuminate\Http\Request;

class SharedProjectController extends Controller
{
    public function view($token)
    {
        $share = ProjectShare::with('project.client', 'project.milestones', 'project.tasks')
            ->where('token', $token)
            ->where('is_active', true)
            ->firstOrFail();

        if ($share->expires_at && $share->expires_at->isPast()) {
            abort(410, 'This shared link has expired.');
        }

        $share->increment('access_count');
        $share->update(['last_accessed_at' => now()]);

        $project = $share->project;

        if ($share->access_level === 'edit') {
            return view('guest.projects.show', compact('project', 'share'));
        }

        return view('guest.projects.show', compact('project', 'share'));
    }
}
