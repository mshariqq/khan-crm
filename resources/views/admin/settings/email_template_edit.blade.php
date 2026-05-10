@extends('layouts.admin')

@section('breadcrumb', 'Edit Template')

@section('main')
<div class="p-4 space-y-4">
    <div class="flex items-center justify-between bg-white rounded-xl border border-slate-200 p-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.settings.email_templates.index') }}" class="p-2 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-800">{{ $template->name }}</h1>
                <p class="text-sm text-slate-500">Edit automated notification content</p>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.settings.email_templates.update', $template->emailtemplateid) }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @csrf
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-700">Email Subject</label>
                    <input type="text" name="subject" value="{{ $template->subject }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" required>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-700">Message Content (HTML Allowed)</label>
                    <textarea name="message" rows="15" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all font-mono text-sm" required>{{ $template->message }}</textarea>
                    <p class="text-xs text-slate-400 mt-2 italic">Tip: You can use HTML tags to format your message.</p>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
                <h3 class="text-sm font-black uppercase tracking-widest text-slate-400 border-b pb-3">Template Settings</h3>
                
                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-700">From Name</label>
                    <input type="text" name="fromname" value="{{ $template->fromname }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm" placeholder="e.g. {companyname} Support">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-700">Template Status</label>
                    <select name="active" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                        <option value="1" {{ $template->active ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !$template->active ? 'selected' : '' }}>Disabled</option>
                    </select>
                </div>

                <button type="submit" class="w-full py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-500/20 flex items-center justify-center gap-2">
                    <i class="bi bi-save"></i> Save Template
                </button>
            </div>

            <div class="bg-slate-900 rounded-2xl p-6 text-white shadow-xl">
                <h3 class="text-xs font-black uppercase tracking-widest text-slate-400 border-b border-slate-800 pb-3 mb-4">Available Placeholders</h3>
                <div class="space-y-3">
                    <div class="p-2 bg-slate-800/50 rounded-lg border border-slate-700/50">
                        <code class="text-blue-400 text-xs">{companyname}</code>
                        <p class="text-[10px] text-slate-400 mt-1">Your company name from settings</p>
                    </div>
                    <div class="p-2 bg-slate-800/50 rounded-lg border border-slate-700/50">
                        <code class="text-blue-400 text-xs">{crm_url}</code>
                        <p class="text-[10px] text-slate-400 mt-1">Your system URL</p>
                    </div>
                    <div class="p-2 bg-slate-800/50 rounded-lg border border-slate-700/50">
                        <code class="text-blue-400 text-xs">{email_signature}</code>
                        <p class="text-[10px] text-slate-400 mt-1">Your global email signature</p>
                    </div>
                    
                    @if($template->type == 'invoice')
                    <div class="p-2 bg-slate-800/50 rounded-lg border border-slate-700/50">
                        <code class="text-blue-400 text-xs">{invoice_number}</code>
                        <p class="text-[10px] text-slate-400 mt-1">Full invoice number with prefix</p>
                    </div>
                    <div class="p-2 bg-slate-800/50 rounded-lg border border-slate-700/50">
                        <code class="text-blue-400 text-xs">{invoice_link}</code>
                        <p class="text-[10px] text-slate-400 mt-1">Direct link to view invoice</p>
                    </div>
                    @endif

                    @if($template->type == 'project')
                    <div class="p-2 bg-slate-800/50 rounded-lg border border-slate-700/50">
                        <code class="text-blue-400 text-xs">{project_name}</code>
                        <p class="text-[10px] text-slate-400 mt-1">Name of the project</p>
                    </div>
                    <div class="p-2 bg-slate-800/50 rounded-lg border border-slate-700/50">
                        <code class="text-blue-400 text-xs">{project_link}</code>
                        <p class="text-[10px] text-slate-400 mt-1">Link to the project dashboard</p>
                    </div>
                    @endif

                    @if($template->type == 'tasks')
                    <div class="p-2 bg-slate-800/50 rounded-lg border border-slate-700/50">
                        <code class="text-blue-400 text-xs">{task_name}</code>
                        <p class="text-[10px] text-slate-400 mt-1">Name of the task</p>
                    </div>
                    <div class="p-2 bg-slate-800/50 rounded-lg border border-slate-700/50">
                        <code class="text-blue-400 text-xs">{task_priority}</code>
                        <p class="text-[10px] text-slate-400 mt-1">Priority (Low, Medium, High, Urgent)</p>
                    </div>
                    @endif

                    @if($template->type == 'ticket')
                    <div class="p-2 bg-slate-800/50 rounded-lg border border-slate-700/50">
                        <code class="text-blue-400 text-xs">{ticket_subject}</code>
                        <p class="text-[10px] text-slate-400 mt-1">Subject of the ticket</p>
                    </div>
                    <div class="p-2 bg-slate-800/50 rounded-lg border border-slate-700/50">
                        <code class="text-blue-400 text-xs">{ticket_public_url}</code>
                        <p class="text-[10px] text-slate-400 mt-1">Link to the ticket portal</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
