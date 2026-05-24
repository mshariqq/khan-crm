<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailTemplateController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->can('settings_view')) {
            abort(403);
        }

        $type = $request->type ?? 'client';

        $templates = EmailTemplate::where('type', $type)->orderBy('name')->get();
        $types = EmailTemplate::select('type')->distinct()->pluck('type');
        $currentType = $type;

        if ($request->ajax() || $request->wantsJson()) {
            return view('admin.settings.email_templates_table', compact('templates', 'currentType'));
        }

        return view('admin.settings.email_templates', compact('templates', 'types', 'currentType'));
    }

    public function edit($id)
    {
        if (!Auth::user()->can('settings_edit')) {
            abort(403);
        }

        $template = EmailTemplate::findOrFail($id);
        return view('admin.settings.email_template_edit', compact('template'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('settings_edit')) {
            abort(403);
        }

        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'fromname' => 'nullable|string|max:255',
            'active' => 'required|boolean',
        ]);

        $template = EmailTemplate::findOrFail($id);
        $template->update([
            'subject' => $request->subject,
            'message' => $request->message,
            'fromname' => $request->fromname ?? '',
            'active' => $request->active,
        ]);

        return redirect()->route('admin.settings.email_templates.index')->with('success', 'Template updated!');
    }
}
