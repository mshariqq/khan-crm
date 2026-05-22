<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Edit Ticket</h1>
        <p class="text-slate-500 text-sm">#{{ $ticket->ticketid }} · Update ticket details.</p>
    </div>
</div>

<form action="{{ route('admin.tickets.update', $ticket->ticketid) }}" method="POST" data-pane-submit="1" data-pane-refresh="{{ route('admin.tickets.pane.show', $ticket->ticketid) }}" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    @csrf @method('PUT')
    <div class="p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-3">
                <div class="space-y-1">
                    <label class="block text-sm font-medium text-slate-700">Subject <span class="text-red-500">*</span></label>
                    <input type="text" name="subject" value="{{ old('subject', $ticket->subject) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" required>
                </div>
                <div class="space-y-1">
                    <label class="block text-sm font-medium text-slate-700">Name</label>
                    <input type="text" name="name" value="{{ old('name', $ticket->name) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                </div>
                <div class="space-y-1">
                    <label class="block text-sm font-medium text-slate-700">Email</label>
                    <input type="email" name="email" value="{{ old('email', $ticket->email) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                </div>
            </div>
            <div class="space-y-3">
                <div class="space-y-1">
                    <label class="block text-sm font-medium text-slate-700">Department <span class="text-red-500">*</span></label>
                    <select name="department" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" required>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->departmentid }}" {{ old('department', $ticket->department) == $dept->departmentid ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Priority <span class="text-red-500">*</span></label>
                        <select name="priority" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" required>
                            @foreach($priorities as $priority)
                                <option value="{{ $priority->priorityid }}" {{ old('priority', $ticket->priority) == $priority->priorityid ? 'selected' : '' }}>{{ $priority->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Status <span class="text-red-500">*</span></label>
                        <select name="status" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" required>
                            @foreach($statuses as $status)
                                <option value="{{ $status->ticketstatusid }}" {{ old('status', $ticket->status) == $status->ticketstatusid ? 'selected' : '' }}>{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="space-y-1">
                    <label class="block text-sm font-medium text-slate-700">Assigned Staff</label>
                    <select name="assigned" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                        <option value="">Unassigned</option>
                        @foreach($staff as $s)
                            <option value="{{ $s->staffid }}" {{ old('assigned', $ticket->assigned) == $s->staffid ? 'selected' : '' }}>{{ $s->firstname }} {{ $s->lastname }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="space-y-1">
                <label class="block text-sm font-medium text-slate-700">Client</label>
                <select name="userid" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    <option value="">No Client</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->userid }}" {{ old('userid', $ticket->userid) == $client->userid ? 'selected' : '' }}>{{ $client->company }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-1">
                <label class="block text-sm font-medium text-slate-700">Contact</label>
                <select name="contactid" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    <option value="">No Contact</option>
                    @foreach($contacts as $contact)
                        <option value="{{ $contact->id }}" {{ old('contactid', $ticket->contactid) == $contact->id ? 'selected' : '' }}>{{ $contact->firstname }} {{ $contact->lastname }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-1">
                <label class="block text-sm font-medium text-slate-700">Project</label>
                <select name="project_id" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    <option value="">None</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ old('project_id', $ticket->project_id) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="space-y-1">
            <label class="block text-sm font-medium text-slate-700">Message <span class="text-red-500">*</span></label>
            <textarea name="message" rows="5" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" required>{{ old('message', $ticket->message) }}</textarea>
        </div>
    </div>
    <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
        <button type="button" onclick="parent.closeTaskPane()" class="px-6 py-2 text-slate-600 font-semibold rounded-lg hover:bg-slate-200 transition-all">Cancel</button>
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Update Ticket</button>
    </div>
</form>
