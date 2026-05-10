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
        
        if (!Auth::user()->can('delete')) {
            abort(403, 'Unauthorized action.');
        }
        
        $reminder->delete();

        return redirect()->back()->with('success', 'Reminder deleted successfully!');
    }
}