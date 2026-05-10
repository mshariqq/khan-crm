@extends('layouts.admin')

@section('main')
<div class="p-6 max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.settings.ticket_email_integration.index') }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 transition-all">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Edit Email Integration</h1>
            <p class="text-slate-500 text-sm">Update IMAP settings for {{ $integration->email }}.</p>
        </div>
    </div>

    <form action="{{ route('admin.settings.ticket_email_integration.update', $integration->id) }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @csrf
        @method('PUT')
        <div class="p-8 space-y-8">
            <!-- Connection Settings -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <h3 class="text-sm font-bold text-slate-800 border-b pb-2 mb-4">IMAP Server Settings</h3>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-700">Email Address</label>
                    <input type="email" name="email" value="{{ $integration->email }}" required class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-700">IMAP Server</label>
                    <input type="text" name="imap_server" value="{{ $integration->imap_server }}" required class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-700">Password</label>
                    <input type="password" name="password" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="Leave blank to keep current password">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700">Port</label>
                        <input type="number" name="port" value="{{ $integration->port }}" required class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700">Encryption</label>
                        <select name="encryption" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            <option value="ssl" {{ $integration->encryption == 'ssl' ? 'selected' : '' }}>SSL</option>
                            <option value="tls" {{ $integration->encryption == 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="" {{ $integration->encryption == '' ? 'selected' : '' }}>None</option>
                        </select>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-700">Mailbox Folder</label>
                    <input type="text" name="folder" value="{{ $integration->folder }}" required class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-700">Check Every (Minutes)</label>
                    <input type="number" name="check_every" value="{{ $integration->check_every }}" required class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                </div>
            </div>

            <!-- Ticket Default Settings -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <h3 class="text-sm font-bold text-slate-800 border-b pb-2 mb-4">Ticket Defaults</h3>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-700">Department</label>
                    <select name="department_id" required class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                        @foreach($departments as $dept)
                            <option value="{{ $dept->departmentid }}" {{ $integration->department_id == $dept->departmentid ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-700">Priority</label>
                    <select name="priority_id" required class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                        @foreach($priorities as $priority)
                            <option value="{{ $priority->priorityid }}" {{ $integration->priority_id == $priority->priorityid ? 'selected' : '' }}>{{ $priority->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Options -->
            <div class="space-y-4 pt-4">
                <h3 class="text-sm font-bold text-slate-800 border-b pb-2 mb-4">Processing Options</h3>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="active" value="1" {{ $integration->active ? 'checked' : '' }} id="active" class="w-5 h-5 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                    <label for="active" class="text-sm font-medium text-slate-700">Active (Processing enabled)</label>
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="only_loop_on_unseen_emails" value="1" {{ $integration->only_loop_on_unseen_emails ? 'checked' : '' }} id="unseen" class="w-5 h-5 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                    <label for="unseen" class="text-sm font-medium text-slate-700">Only process unread emails</label>
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="delete_after_import" value="1" {{ $integration->delete_after_import ? 'checked' : '' }} id="delete" class="w-5 h-5 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                    <label for="delete" class="text-sm font-medium text-slate-700">Delete emails from server after import</label>
                </div>
            </div>
        </div>

        <div class="p-8 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
            <a href="{{ route('admin.settings.ticket_email_integration.index') }}" class="px-6 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-xl text-sm font-bold hover:bg-slate-50 transition-all">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">Update Integration</button>
        </div>
    </form>
</div>
@endsection
