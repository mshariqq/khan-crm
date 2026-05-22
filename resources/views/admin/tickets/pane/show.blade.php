@php
$statusColors = [
    1 => 'bg-emerald-100 text-emerald-700 border-emerald-200',
    2 => 'bg-blue-100 text-blue-700 border-blue-200',
    3 => 'bg-amber-100 text-amber-700 border-amber-200',
    4 => 'bg-red-100 text-red-700 border-red-200',
    5 => 'bg-slate-100 text-slate-600 border-slate-200',
];
$priorityColors = ['bg-emerald-500', 'bg-amber-500', 'bg-red-500'];
$priorityLabels = ['Low', 'Medium', 'High'];
$sc = $statusColors[$ticket->status] ?? 'bg-slate-100 text-slate-600 border-slate-200';
$pc = $priorityColors[$ticket->priorityRel->priorityid ?? 1] ?? 'bg-slate-500';
$pl = $priorityLabels[$ticket->priorityRel->priorityid ?? 1] ?? 'Unknown';
@endphp

<div class="flex items-start justify-between mb-5">
    <div class="flex items-start gap-3 min-w-0 flex-1">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-indigo-200 flex-shrink-0">
            {{ strtoupper(substr($ticket->subject, 0, 1)) }}
        </div>
        <div class="min-w-0">
            <div class="flex items-center gap-2.5 mb-0.5 flex-wrap">
                <h1 class="text-lg font-bold text-slate-800 truncate">{{ $ticket->subject }}</h1>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $sc }}">{{ $ticket->statusRel->name ?? 'Unknown' }}</span>
            </div>
            <div class="flex items-center gap-2 text-xs text-slate-400 flex-wrap">
                <span>#{{ $ticket->ticketid }}</span>
                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                <span>Created {{ \Carbon\Carbon::parse($ticket->date)->diffForHumans() }}</span>
                @if($ticket->name)
                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                <span class="flex items-center gap-1">by {{ $ticket->name }}</span>
                @endif
                @if($ticket->lastreply)
                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                <span class="text-amber-600 font-medium">Last reply {{ \Carbon\Carbon::parse($ticket->lastreply)->diffForHumans() }}</span>
                @endif
            </div>
        </div>
    </div>
    <button onclick="parent.openTicketPane('{{ route('admin.tickets.pane.edit', $ticket->ticketid) }}')" class="px-3 py-1.5 bg-white border border-slate-200 text-slate-600 text-xs font-semibold rounded-lg hover:bg-slate-50 transition-all flex items-center gap-1.5 flex-shrink-0">
        <i class="bi bi-pencil"></i> Edit
    </button>
</div>

<div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
    {{-- Main Content --}}
    <div class="lg:col-span-3 space-y-4">

        {{-- Original Message --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center text-white text-[9px] font-bold">{{ strtoupper(substr($ticket->name ?? 'U', 0, 1)) }}</span>
                <span class="text-sm font-semibold text-slate-700">{{ $ticket->name ?? 'Unknown' }}</span>
                <span class="text-[10px] text-slate-400 ml-auto">{{ \Carbon\Carbon::parse($ticket->date)->format('M j, Y · g:i A') }}</span>
            </div>
            <div class="p-4 text-sm text-slate-600 leading-relaxed prose prose-slate max-w-none">{!! $ticket->message !!}</div>
        </div>

        {{-- Replies --}}
        @if($ticket->replies->count() > 0)
        <div class="space-y-3">
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                <i class="bi bi-chat-dots"></i>
                <span>Replies ({{ $ticket->replies->count() }})</span>
            </div>
            @foreach($ticket->replies as $reply)
            @php $isStaff = $reply->admin == 1; @endphp
            <div class="flex gap-3 {{ $isStaff ? 'flex-row-reverse' : '' }}">
                <div class="flex-shrink-0">
                    <span class="w-7 h-7 rounded-full flex items-center justify-center text-white text-[9px] font-bold {{ $isStaff ? 'bg-blue-500' : 'bg-slate-400' }}">
                        {{ strtoupper(substr($reply->name ?? 'U', 0, 1)) }}
                    </span>
                </div>
                <div class="flex-1 min-w-0 max-w-[85%]">
                    <div class="rounded-xl border border-slate-200 overflow-hidden {{ $isStaff ? 'bg-blue-50/50' : 'bg-white' }}">
                        <div class="px-4 py-2.5 border-b border-slate-100 flex items-center gap-2 {{ $isStaff ? 'border-blue-100' : '' }}">
                            <span class="text-xs font-semibold text-slate-700">{{ $reply->name }}</span>
                            @if($isStaff)
                            <span class="px-1.5 py-0.5 bg-blue-100 text-blue-700 rounded text-[9px] font-bold uppercase">Staff</span>
                            @endif
                            <span class="text-[10px] text-slate-400 ml-auto">{{ \Carbon\Carbon::parse($reply->date)->format('M j, g:i A') }}</span>
                        </div>
                        <div class="p-4 text-sm text-slate-600 leading-relaxed prose prose-slate max-w-none">{!! $reply->message !!}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Attachments --}}
        @if($ticket->attachments->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                <i class="bi bi-paperclip text-slate-400"></i>
                <h3 class="text-xs font-semibold text-slate-700">Attachments</h3>
                <span class="px-1.5 py-0.5 bg-slate-200 text-slate-600 rounded text-[9px] font-bold ml-auto">{{ $ticket->attachments->count() }}</span>
            </div>
            <div class="p-3 space-y-1">
                @foreach($ticket->attachments as $att)
                @php
                    $ext = strtolower(pathinfo($att->file_name, PATHINFO_EXTENSION));
                    $isImage = $att->filetype && str_starts_with($att->filetype, 'image/');
                    $iconMap = [
                        'pdf' => 'bi-filetype-pdf', 'doc' => 'bi-filetype-docx', 'docx' => 'bi-filetype-docx',
                        'xls' => 'bi-filetype-xlsx', 'xlsx' => 'bi-filetype-xlsx', 'csv' => 'bi-filetype-csv',
                        'zip' => 'bi-file-zip', 'rar' => 'bi-file-zip', 'txt' => 'bi-filetype-txt',
                        'png' => 'bi-filetype-png', 'jpg' => 'bi-filetype-jpg', 'jpeg' => 'bi-filetype-jpg', 'gif' => 'bi-filetype-gif',
                    ];
                    $icon = $iconMap[$ext] ?? 'bi-file-earmark';
                @endphp
                <div class="flex items-center gap-2.5 px-3 py-2 rounded-lg hover:bg-slate-50 transition-colors">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 flex-shrink-0">
                        <i class="bi {{ $icon }} text-sm"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <span class="text-sm text-slate-700 truncate block">{{ $att->file_name }}</span>
                        <span class="text-[10px] text-slate-400">{{ $att->filetype ?: 'Unknown type' }} · {{ \Carbon\Carbon::parse($att->dateadded)->format('M j, Y') }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Reply Composer --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                <i class="bi bi-reply text-blue-500"></i>
                <h3 class="text-xs font-semibold text-slate-700">Reply</h3>
            </div>
            <div class="p-4">
                <form id="ticketPaneReplyForm">
                    @csrf
                    <input type="hidden" name="ticketid" value="{{ $ticket->ticketid }}">
                    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden focus-within:ring-2 focus-within:ring-blue-500/20 focus-within:border-blue-300 transition-all mb-3">
                        <div id="ticketPaneReplyEditor" style="min-height: 120px;"></div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <label class="px-3 py-1.5 bg-slate-100 text-slate-500 text-xs font-medium rounded-lg cursor-pointer hover:bg-slate-200 transition-all">
                                <i class="bi bi-paperclip"></i> Attach
                                <input type="file" name="attachments[]" multiple class="hidden">
                            </label>
                        </div>
                        <button type="submit" class="px-5 py-2 bg-blue-600 text-white text-xs font-bold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-200 flex items-center gap-1.5">
                            <i class="bi bi-send-fill"></i> Send Reply
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- Ticket Info --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                <i class="bi bi-info-circle text-slate-400"></i>
                <h3 class="text-xs font-semibold text-slate-700">Ticket Info</h3>
            </div>
            <div class="p-4 space-y-4">
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Status</label>
                    <select onchange="updateTicketStatus(this, {{ $ticket->ticketid }})" class="w-full px-3 py-2 text-xs font-medium rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                        @foreach($statuses as $s)
                        <option value="{{ $s->ticketstatusid }}" {{ $ticket->status == $s->ticketstatusid ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Priority</label>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full {{ $pc }}"></span>
                        <span class="text-sm font-medium text-slate-700">{{ $pl }}</span>
                    </div>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Assigned To</label>
                    <select onchange="updateTicketAssigned(this, {{ $ticket->ticketid }})" class="w-full px-3 py-2 text-xs font-medium rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                        <option value="">Unassigned</option>
                        @foreach($staff as $s)
                        <option value="{{ $s->staffid }}" {{ $ticket->assigned == $s->staffid ? 'selected' : '' }}>{{ $s->firstname }} {{ $s->lastname }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Customer --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                <i class="bi bi-person text-slate-400"></i>
                <h3 class="text-xs font-semibold text-slate-700">Customer</h3>
            </div>
            <div class="p-4 space-y-3">
                @if($ticket->name)
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-full bg-gradient-to-br from-slate-400 to-slate-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">{{ strtoupper(substr($ticket->name, 0, 1)) }}</span>
                    <div>
                        <p class="text-sm font-medium text-slate-800">{{ $ticket->name }}</p>
                        @if($ticket->email)<p class="text-xs text-slate-400">{{ $ticket->email }}</p>@endif
                    </div>
                </div>
                @endif
                @if($ticket->client)
                <div class="flex items-center gap-2.5 px-3 py-2 bg-slate-50 rounded-lg border border-slate-100">
                    <i class="bi bi-building text-slate-400"></i>
                    <a href="{{ route('admin.clients.show', $ticket->client->userid) }}" class="text-sm font-medium text-blue-600 hover:underline truncate">{{ $ticket->client->company }}</a>
                </div>
                @endif
                @if($ticket->contact)
                <div class="flex items-center gap-2.5 px-3 py-2 bg-slate-50 rounded-lg border border-slate-100">
                    <i class="bi bi-person-badge text-slate-400"></i>
                    <span class="text-sm text-slate-700 truncate">{{ $ticket->contact->firstname }} {{ $ticket->contact->lastname }}</span>
                </div>
                @endif
                @if($ticket->project)
                <div class="flex items-center gap-2.5 px-3 py-2 bg-slate-50 rounded-lg border border-slate-100">
                    <i class="bi bi-folder text-slate-400"></i>
                    <a href="{{ route('admin.projects.show', $ticket->project->id) }}" class="text-sm font-medium text-blue-600 hover:underline truncate">{{ $ticket->project->name }}</a>
                </div>
                @endif
            </div>
        </div>

        {{-- Ticket Meta --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                <i class="bi bi-grid-3x3-gap text-slate-400"></i>
                <h3 class="text-xs font-semibold text-slate-700">Details</h3>
            </div>
            <div class="p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-500">Department</span>
                    <span class="text-xs font-medium text-slate-700">{{ $ticket->departmentRel->name ?? 'N/A' }}</span>
                </div>
                <div class="border-t border-slate-100"></div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-500">Last Reply</span>
                    <span class="text-xs font-medium text-slate-700">{{ $ticket->lastreply ? \Carbon\Carbon::parse($ticket->lastreply)->diffForHumans() : 'No replies' }}</span>
                </div>
                <div class="border-t border-slate-100"></div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-500">Created</span>
                    <span class="text-xs font-medium text-slate-700">{{ \Carbon\Carbon::parse($ticket->date)->format('M j, Y') }}</span>
                </div>
                @if($ticket->replyingStaff)
                <div class="border-t border-slate-100"></div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-500">Last Replied By</span>
                    <span class="text-xs font-medium text-slate-700">{{ $ticket->replyingStaff->firstname }} {{ $ticket->replyingStaff->lastname }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
#ticketPaneReplyEditor .ql-editor { font-size: 13px; padding: 12px; min-height: 100px; }
#ticketPaneReplyEditor .ql-toolbar { border: none; border-bottom: 1px solid #eef2f6; background: #f8fafc; border-radius: 0; }
#ticketPaneReplyEditor .ql-container { border: none; }
.ql-snow .ql-stroke { stroke: #64748b; }
.ql-snow .ql-fill { fill: #64748b; }
.ql-snow .ql-picker { color: #64748b; }
.prose { max-width: none; }
.prose p { margin: 0 0 6px 0; }
.prose p:last-child { margin-bottom: 0; }
.prose ul, .prose ol { margin: 4px 0; padding-left: 18px; }
.prose ul { list-style-type: disc; }
.prose ol { list-style-type: decimal; }
</style>

<script>
var ticketPaneReplyQuill = null;
try {
    if (window.ticketPaneReplyQuill) window.ticketPaneReplyQuill.destroy();
    window.ticketPaneReplyQuill = new Quill('#ticketPaneReplyEditor', {
        theme: 'snow',
        placeholder: 'Type your reply here...',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['link', 'clean']
            ]
        }
    });
} catch(e) {}

document.getElementById('ticketPaneReplyForm').addEventListener('submit', function(e) {
    e.preventDefault();
    if (!window.ticketPaneReplyQuill) { parent.showToast('Editor not available', 'error'); return; }
    var content = window.ticketPaneReplyQuill.root.innerHTML;
    if (window.ticketPaneReplyQuill.getText().trim() === '') {
        parent.showToast('Please enter a reply', 'error');
        return;
    }
    var btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i> Sending...';

    var formData = new FormData();
    formData.append('message', content);
    formData.append('ticketid', {{ $ticket->ticketid }});

    fetch('/admin/ticket-replies', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-send-fill"></i> Send Reply';
        if (data.success) {
            parent.showToast(data.message, 'success');
            window.ticketPaneReplyQuill.setContents([]);
            parent.openTicketPane('{{ route('admin.tickets.pane.show', $ticket->ticketid) }}');
        } else {
            parent.showToast(data.message || 'Reply failed', 'error');
        }
    })
    .catch(function() {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-send-fill"></i> Send Reply';
        parent.showToast('Network error', 'error');
    });
});

function updateTicketStatus(select, id) {
    select.disabled = true;
    fetch('/admin/tickets/' + id + '/change-status', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ status: select.value })
    })
    .then(res => res.json())
    .then(data => {
        select.disabled = false;
        if (data.success) {
            parent.showToast(data.message, 'success');
            parent.openTicketPane('{{ route('admin.tickets.pane.show', $ticket->ticketid) }}');
        } else {
            parent.showToast(data.message || 'Update failed', 'error');
        }
    })
    .catch(function() {
        select.disabled = false;
        parent.showToast('Network error', 'error');
    });
}

function updateTicketAssigned(select, id) {
    select.disabled = true;
    fetch('/admin/tickets/' + id + '/change-assigned', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ assigned: select.value || '' })
    })
    .then(res => res.json())
    .then(data => {
        select.disabled = false;
        if (data.success) {
            parent.showToast(data.message, 'success');
            parent.openTicketPane('{{ route('admin.tickets.pane.show', $ticket->ticketid) }}');
        } else {
            parent.showToast(data.message || 'Update failed', 'error');
        }
    })
    .catch(function() {
        select.disabled = false;
        parent.showToast('Network error', 'error');
    });
}
</script>
