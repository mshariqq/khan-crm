@extends('layouts.admin')

@section('breadcrumb', 'Edit Ticket')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
<style>
    .ql-editor { font-size: 14px; padding: 15px; min-height: 200px; }
    .ql-toolbar { border: 1px solid #e2e8f0 !important; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem; background: #f8fafc; }
    .ql-container { border: 1px solid #e2e8f0 !important; border-top: none !important; border-bottom-left-radius: 0.5rem; border-bottom-right-radius: 0.5rem; }
</style>
@endpush

@section('main')
<div class="p-4 space-y-4">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.tickets.index') }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 transition-all">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-lg font-bold text-slate-800">Edit Ticket</h1>
                <p class="text-xs text-slate-400">#{{ $ticket->ticketid }}</p>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.tickets.update', $ticket->ticketid) }}" method="POST" class="bg-white rounded-lg shadow-sm border border-slate-200">
        @csrf
        @method('PUT')
        <div class="p-4 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-3">
                    <div>
                        <label class="text-xs text-slate-500 uppercase tracking-wide">Subject <span class="text-red-500">*</span></label>
                        <input type="text" name="subject" value="{{ old('subject', $ticket->subject) }}" class="w-full px-3 py-2 mt-1 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" required>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-slate-500 uppercase tracking-wide">Department <span class="text-red-500">*</span></label>
                            <select name="department" class="w-full px-3 py-2 mt-1 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" required>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->departmentid }}" {{ old('department', $ticket->department) == $dept->departmentid ? 'selected' : '' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-slate-500 uppercase tracking-wide">Priority <span class="text-red-500">*</span></label>
                            <select name="priority" class="w-full px-3 py-2 mt-1 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" required>
                                @foreach($priorities as $priority)
                                    <option value="{{ $priority->priorityid }}" {{ old('priority', $ticket->priority) == $priority->priorityid ? 'selected' : '' }}>{{ $priority->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 uppercase tracking-wide">Status <span class="text-red-500">*</span></label>
                        <select name="status" class="w-full px-3 py-2 mt-1 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" required>
                            @foreach($statuses as $status)
                                <option value="{{ $status->ticketstatusid }}" {{ old('status', $ticket->status) == $status->ticketstatusid ? 'selected' : '' }}>{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="space-y-3">
                    <div>
                        <label class="text-xs text-slate-500 uppercase tracking-wide">Client</label>
                        <select name="userid" class="w-full px-3 py-2 mt-1 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                            <option value="">No Client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->userid }}" {{ old('userid', $ticket->userid) == $client->userid ? 'selected' : '' }}>{{ $client->company }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 uppercase tracking-wide">Contact</label>
                        <select name="contactid" class="w-full px-3 py-2 mt-1 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                            <option value="">No Contact</option>
                            @foreach($contacts as $contact)
                                <option value="{{ $contact->id }}" {{ old('contactid', $ticket->contactid) == $contact->id ? 'selected' : '' }}>{{ $contact->firstname }} {{ $contact->lastname }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-slate-500 uppercase tracking-wide">Assigned Staff</label>
                            <select name="assigned" class="w-full px-3 py-2 mt-1 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                                <option value="">Unassigned</option>
                                @foreach($staff as $s)
                                    <option value="{{ $s->staffid }}" {{ old('assigned', $ticket->assigned) == $s->staffid ? 'selected' : '' }}>{{ $s->firstname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-slate-500 uppercase tracking-wide">Project</label>
                            <select name="project_id" class="w-full px-3 py-2 mt-1 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                                <option value="">None</option>
                                @foreach($projects as $p)
                                    <option value="{{ $p->id }}" {{ old('project_id', $ticket->project_id) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label class="text-xs text-slate-500 uppercase tracking-wide">Message <span class="text-red-500">*</span></label>
                <div id="editor" class="mt-1">{!! old('message', $ticket->message) !!}</div>
                <input type="hidden" name="message" id="message" required>
                @error('message') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @endif
            </div>
        </div>

        <div class="px-4 py-3 bg-slate-50 border-t border-slate-200 flex justify-end gap-2">
            <a href="{{ route('admin.tickets.index') }}" class="px-4 py-2 text-slate-600 text-sm font-medium rounded-lg hover:bg-slate-200 transition-all">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-all">Update Ticket</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Describe the issue...',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link', 'clean']
                ]
            }
        });

        const form = document.querySelector('form');
        const messageInput = document.querySelector('#message');

        form.onsubmit = function() {
            messageInput.value = quill.root.innerHTML;
            if (quill.getText().trim().length === 0) {
                alert('Please enter a message');
                return false;
            }
        };
    });
</script>
@endpush