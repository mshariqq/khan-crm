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

        $permissionMap = [
            'estimate' => 'estimates_edit',
            'proposal' => 'proposals_edit',
            'project' => 'projects_edit',
            'invoice' => 'invoices_edit',
            'client' => 'clients_edit',
            'contact' => 'contacts_edit',
            'lead' => 'leads_edit',
        ];

        $permission = $permissionMap[$note->rel_type] ?? null;
        if (!$permission || !Auth::user()->can($permission)) {
            abort(403, 'Unauthorized action.');
        }

        $note->delete();

        return redirect()->back()->with('success', 'Note deleted successfully!');
    }
}
