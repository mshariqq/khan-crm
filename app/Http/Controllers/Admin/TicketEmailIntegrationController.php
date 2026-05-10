<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketEmailIntegration;
use App\Models\Department;
use App\Models\TicketPriority;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketEmailIntegrationController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('settings_view')) {
            abort(403);
        }

        $integrations = TicketEmailIntegration::with(['department', 'priority'])->get();
        return view('admin.settings.tickets_email_integration', compact('integrations'));
    }

    public function create()
    {
        if (!Auth::user()->can('settings_edit')) {
            abort(403);
        }

        $departments = Department::all();
        $priorities = TicketPriority::all();
        return view('admin.settings.tickets_email_integration_create', compact('departments', 'priorities'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('settings_edit')) {
            abort(403);
        }

        $validated = $request->validate([
            'email' => 'required|email|max:100',
            'imap_server' => 'required|string|max:100',
            'password' => 'required|string',
            'port' => 'required|integer',
            'encryption' => 'nullable|string|max:10',
            'folder' => 'required|string|max:100',
            'department_id' => 'required|integer',
            'priority_id' => 'required|integer',
            'check_every' => 'required|integer|min:1',
            'active' => 'boolean',
            'only_loop_on_unseen_emails' => 'boolean',
            'delete_after_import' => 'boolean',
        ]);

        TicketEmailIntegration::create($validated);

        return redirect()->route('admin.settings.ticket_email_integration.index')->with('success', 'Integration created successfully!');
    }

    public function edit($id)
    {
        if (!Auth::user()->can('settings_edit')) {
            abort(403);
        }

        $integration = TicketEmailIntegration::findOrFail($id);
        $departments = Department::all();
        $priorities = TicketPriority::all();
        return view('admin.settings.tickets_email_integration_edit', compact('integration', 'departments', 'priorities'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('settings_edit')) {
            abort(403);
        }

        $validated = $request->validate([
            'email' => 'required|email|max:100',
            'imap_server' => 'required|string|max:100',
            'password' => 'nullable|string',
            'port' => 'required|integer',
            'encryption' => 'nullable|string|max:10',
            'folder' => 'required|string|max:100',
            'department_id' => 'required|integer',
            'priority_id' => 'required|integer',
            'check_every' => 'required|integer|min:1',
            'active' => 'boolean',
            'only_loop_on_unseen_emails' => 'boolean',
            'delete_after_import' => 'boolean',
        ]);

        $integration = TicketEmailIntegration::findOrFail($id);
        
        if (empty($request->password)) {
            unset($validated['password']);
        }

        $integration->update($validated);

        return redirect()->route('admin.settings.ticket_email_integration.index')->with('success', 'Integration updated successfully!');
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('settings_edit')) {
            abort(403);
        }

        $integration = TicketEmailIntegration::findOrFail($id);
        $integration->delete();

        return redirect()->route('admin.settings.ticket_email_integration.index')->with('success', 'Integration deleted successfully!');
    }
}
