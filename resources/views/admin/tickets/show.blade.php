@extends('layouts.admin')

@section('breadcrumb', 'Ticket Detail')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
<style>
    .ql-editor { font-size: 14px; padding: 15px; min-height: 150px; }
    .ql-toolbar { border: none !important; border-bottom: 1px solid #eef2f6 !important; background: #f8fafc; }
    .ql-container { border: none !important; }
    .ql-snow .ql-stroke { stroke: #64748b; }
    
    .message-bubble { position: relative; }
    .message-bubble::before {
        content: "";
        position: absolute;
        top: 0;
        bottom: 0;
        left: 20px;
        width: 2px;
        background: #f1f5f9;
        z-index: 0;
    }
    .message-item { position: relative; z-index: 1; }
</style>
@endpush

@section('main')
<div class="h-[calc(100vh-64px)] flex flex-col">
    <!-- Header -->
    <div class="bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.tickets.index') }}" class="p-2 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-500 transition-all">
                <i class="bi bi-arrow-left text-lg"></i>
            </a>
            <div>
                <div class="flex items-center gap-2 mb-0.5">
                    <h1 class="text-xl font-bold text-slate-800">{{ $ticket->subject }}</h1>
                    <span class="px-2.5 py-0.5 bg-slate-100 text-slate-600 rounded-full text-[11px] font-bold uppercase tracking-wider">#{{ $ticket->ticketid }}</span>
                </div>
                <p class="text-sm text-slate-400">
                    Created by <span class="text-slate-600 font-medium">{{ $ticket->name }}</span> · {{ \Carbon\Carbon::parse($ticket->date)->diffForHumans() }}
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 bg-slate-50 p-1 rounded-xl border border-slate-200">
                <button onclick="switchTab('conversation')" class="tab-btn px-4 py-1.5 text-sm font-medium rounded-lg bg-white shadow-sm text-blue-600" data-tab="conversation">Conversation</button>
                <button onclick="switchTab('notes')" class="tab-btn px-4 py-1.5 text-sm font-medium rounded-lg text-slate-500 hover:text-slate-700" data-tab="notes">Private Notes</button>
            </div>
            <div class="h-8 w-px bg-slate-200 mx-2"></div>
            <a href="{{ route('admin.tickets.edit', $ticket->ticketid) }}" class="p-2 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-600 transition-all" title="Edit Ticket">
                <i class="bi bi-pencil-square"></i>
            </a>
            <form action="{{ route('admin.tickets.destroy', $ticket->ticketid) }}" method="POST" onsubmit="return confirm('Delete ticket?');" class="inline">
                @csrf @method('DELETE')
                <button type="submit" class="p-2 rounded-xl bg-red-50 hover:bg-red-100 text-red-500 transition-all" title="Delete Ticket">
                    <i class="bi bi-trash3"></i>
                </button>
            </form>
        </div>
    </div>

    <div class="flex-1 flex overflow-hidden">
        <!-- Main Conversation Area -->
        <div class="flex-1 overflow-y-auto bg-slate-50/50">
            <div id="tab-conversation" class="tab-content max-w-4xl mx-auto p-8">
                <div class="message-bubble space-y-8">
                    <!-- Original Message -->
                    <div class="message-item flex gap-4">
                        <div class="w-10 h-10 rounded-2xl bg-slate-800 flex items-center justify-center text-white font-bold shrink-0 shadow-lg shadow-slate-200">
                            {{ strtoupper(substr($ticket->name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                                <div class="px-5 py-3 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
                                    <span class="text-sm font-bold text-slate-700">{{ $ticket->name }}</span>
                                    <span class="text-[11px] text-slate-400 font-medium">{{ \Carbon\Carbon::parse($ticket->date)->format('M d, Y · g:i A') }}</span>
                                </div>
                                <div class="p-6 text-slate-600 text-[15px] leading-relaxed prose prose-slate max-w-none">
                                    {!! $ticket->message !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    @foreach($ticket->replies as $reply)
                    <div class="message-item flex gap-4 {{ $reply->admin ? 'flex-row-reverse' : '' }}">
                        <div class="w-10 h-10 rounded-2xl {{ $reply->admin ? 'bg-blue-600' : 'bg-slate-800' }} flex items-center justify-center text-white font-bold shrink-0 shadow-lg {{ $reply->admin ? 'shadow-blue-100' : 'shadow-slate-100' }}">
                            {{ strtoupper(substr($reply->name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <div class="bg-white rounded-2xl shadow-sm border {{ $reply->admin ? 'border-blue-100' : 'border-slate-200' }} overflow-hidden">
                                <div class="px-5 py-3 border-b {{ $reply->admin ? 'border-blue-50 bg-blue-50/30' : 'border-slate-50 bg-slate-50/30' }} flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-bold text-slate-700">{{ $reply->name }}</span>
                                        @if($reply->admin)
                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-[10px] font-bold uppercase tracking-tight">Staff</span>
                                        @endif
                                    </div>
                                    <span class="text-[11px] text-slate-400 font-medium">{{ \Carbon\Carbon::parse($reply->date)->format('M d, Y · g:i A') }}</span>
                                </div>
                                <div class="p-6 text-slate-600 text-[15px] leading-relaxed prose prose-slate max-w-none">
                                    {!! $reply->message !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Reply Editor -->
                <div class="mt-12 bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-200 overflow-hidden ring-4 ring-slate-50">
                    <div id="replyEditor"></div>
                    <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="insertTemplate()" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700 hover:bg-white rounded-xl transition-all border border-transparent hover:border-slate-200">
                                <i class="bi bi-lightning-charge-fill mr-1"></i> Canned Response
                            </button>
                            <label class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700 hover:bg-white rounded-xl transition-all border border-transparent hover:border-slate-200 cursor-pointer">
                                <i class="bi bi-paperclip mr-1"></i> Attach Files
                                <input type="file" class="hidden" multiple>
                            </label>
                        </div>
                        <button type="button" onclick="submitReply()" class="px-6 py-2.5 bg-blue-600 text-white text-sm font-bold rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-200 flex items-center gap-2">
                            <span>Send Reply</span>
                            <i class="bi bi-send-fill text-xs opacity-70"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div id="tab-notes" class="tab-content hidden max-w-4xl mx-auto p-8">
                <div class="bg-amber-50 rounded-2xl border border-amber-100 p-8 text-center">
                    <div class="w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center text-amber-600 mx-auto mb-4">
                        <i class="bi bi-journal-text text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-amber-900 mb-2">Private Staff Notes</h3>
                    <p class="text-amber-700/70 text-sm max-w-sm mx-auto">Internal notes are only visible to staff members and will never be seen by the customer.</p>
                    <button class="mt-6 px-6 py-2.5 bg-amber-600 text-white text-sm font-bold rounded-xl hover:bg-amber-700 transition-all shadow-lg shadow-amber-200">
                        Add Internal Note
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="w-80 border-l border-slate-200 bg-white overflow-y-auto p-6 space-y-8 shrink-0">
            <!-- Ticket Status Section -->
            <div class="space-y-4">
                <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Ticket Status</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 ml-1">Status</label>
                        <select id="statusChange" onchange="updateStatus(this.value)" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 transition-all">
                            @foreach($statuses ?? [] as $status)
                            <option value="{{ $status->ticketstatusid }}" {{ $ticket->status == $status->ticketstatusid ? 'selected' : '' }}>{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 ml-1">Priority</label>
                        @php $p = $ticket->priorityRel->priorityid ?? 1; @endphp
                        <div class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full {{ $p == 3 ? 'bg-red-500' : ($p == 2 ? 'bg-amber-500' : 'bg-emerald-500') }}"></span>
                            <span class="text-sm font-bold text-slate-700">{{ $ticket->priorityRel->name ?? 'Normal' }}</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 ml-1">Assigned To</label>
                        <select id="assignedChange" onchange="updateAssigned(this.value)" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 transition-all">
                            <option value="">Unassigned</option>
                            @foreach($staff ?? [] as $s)
                            <option value="{{ $s->staffid }}" {{ $ticket->assigned == $s->staffid ? 'selected' : '' }}>{{ $s->firstname }} {{ $s->lastname }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Customer Details -->
            <div class="space-y-4">
                <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Customer Details</h3>
                <div class="bg-slate-50 rounded-2xl p-4 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400">
                            <i class="bi bi-person text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800">{{ $ticket->name }}</p>
                            <p class="text-[11px] text-slate-500">{{ $ticket->email }}</p>
                        </div>
                    </div>
                    @if($ticket->client)
                    <div class="pt-4 border-t border-slate-200/60 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-medium text-slate-400">Company</span>
                            <a href="{{ route('admin.clients.show', $ticket->client->userid) }}" class="text-[11px] font-bold text-slate-700 hover:text-blue-600">{{ $ticket->client->company }}</a>
                        </div>
                        @if($ticket->project)
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-medium text-slate-400">Project</span>
                            <a href="{{ route('admin.projects.show', $ticket->project->id) }}" class="text-[11px] font-bold text-blue-600 hover:text-blue-800">{{ $ticket->project->name }}</a>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Ticket Meta -->
            <div class="space-y-4">
                <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Ticket Info</h3>
                <div class="space-y-3 px-1">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-400">Department</span>
                        <span class="text-xs font-bold text-slate-700">{{ $ticket->departmentRel->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-400">Last Response</span>
                        <span class="text-xs font-bold text-slate-700">{{ $ticket->lastreply ? \Carbon\Carbon::parse($ticket->lastreply)->diffForHumans() : 'No replies' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
    let quill;
    document.addEventListener('DOMContentLoaded', function() {
        quill = new Quill('#replyEditor', {
            theme: 'snow',
            placeholder: 'Write your response here...',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link', 'clean']
                ]
            }
        });
    });

    function insertTemplate() {
        // Basic implementation for now, can be expanded to show a modal with canned responses
        const template = prompt("Enter template text:");
        if (template) {
            const range = quill.getSelection();
            quill.insertText(range ? range.index : 0, template);
        }
    }

    function switchTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(el => {
            el.classList.remove('bg-white', 'shadow-sm', 'text-blue-600');
            el.classList.add('text-slate-500');
        });
        
        document.getElementById('tab-' + tabName).classList.remove('hidden');
        const activeBtn = document.querySelector(`[data-tab="${tabName}"]`);
        activeBtn.classList.add('bg-white', 'shadow-sm', 'text-blue-600');
        activeBtn.classList.remove('text-slate-500');
    }

    function submitReply() {
        const message = quill.root.innerHTML;
        if (quill.getText().trim().length === 0) {
            alert('Please enter a message');
            return;
        }

        const btn = event.currentTarget;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin mr-2"></i> Sending...';

        fetch("{{ route('admin.tickets.reply.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                message: message,
                ticketid: {{ $ticket->ticketid }}
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Error sending reply');
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to send reply');
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }

    function updateStatus(statusId) {
        fetch("{{ route('admin.tickets.change_status', $ticket->ticketid) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                status: statusId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Optional: show a small toast
            } else {
                alert(data.message || 'Error updating status');
            }
        });
    }

    function updateAssigned(staffId) {
        fetch("{{ route('admin.tickets.change_assigned', $ticket->ticketid) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                assigned: staffId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Optional: show a small toast
            } else {
                alert(data.message || 'Error updating assignment');
            }
        });
    }
</script>
@endpush
