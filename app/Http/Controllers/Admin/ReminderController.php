<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReminderController extends Controller
{
    public function destroy($id)
    {
        $reminder = Reminder::findOrFail($id);

        $permissionMap = [
            'estimate' => 'estimates_edit',
            'proposal' => 'proposals_edit',
            'project' => 'projects_edit',
            'task' => 'tasks_edit',
            'invoice' => 'invoices_edit',
            'lead' => 'leads_edit',
        ];

        $permission = $permissionMap[$reminder->rel_type] ?? null;
        if (!$permission || !Auth::user()->can($permission)) {
            abort(403, 'Unauthorized action.');
        }

        $reminder->delete();

        return redirect()->back()->with('success', 'Reminder deleted successfully!');
    }
}
