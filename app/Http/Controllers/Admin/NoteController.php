<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    public function destroy($id)
    {
        $note = Note::findOrFail($id);
        
        if (!Auth::user()->can('projects_edit')) {
            abort(403, 'Unauthorized action.');
        }
        
        $note->delete();

        return redirect()->back()->with('success', 'Note deleted successfully!');
    }
}