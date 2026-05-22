@extends('layouts.admin')

@section('breadcrumb', 'Task Detail')

@push('styles')
<style>
#commentEditor .ql-editor { font-size: 13px; padding: 10px; min-height: 80px; }
#commentEditor .ql-toolbar { border: none; border-bottom: 1px solid #e2e8f0; background: #f8fafc; }
#commentEditor .ql-container { border: none; }
.ql-snow .ql-stroke { stroke: #64748b; }
.ql-snow .ql-fill { fill: #64748b; }
.ql-snow .ql-picker { color: #64748b; }
.comment-content { font-size: 12px; line-height: 1.5; }
.comment-content p { margin: 0 0 4px 0; }
.comment-content p:last-child { margin-bottom: 0; }
.comment-content > ul, .comment-content > ol { margin: 4px 0; padding-left: 18px; }
.comment-content ul { list-style-type: disc; }
.comment-content ol { list-style-type: decimal; }
.comment-content li { margin: 2px 0; }
.comment-content li p { margin: 0; }
.comment-content a { color: #3b82f6; text-decoration: underline; }
.comment-content strong { font-weight: 600; }
.comment-content em { font-style: italic; }
.comment-content u { text-decoration: underline; }
.comment-content ul li::marker { color: #64748b; }
.comment-content ol li::marker { color: #64748b; }
.comment-content blockquote { border-left: 2px solid #cbd5e1; padding-left: 8px; margin: 4px 0; color: #475569; }
</style>
@endpush

@section('main')
<div class="p-4 space-y-4">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.tasks.index') }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 transition-all">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-lg font-bold text-slate-800">{{ $task->name }}</h1>
                <p class="text-xs text-slate-400">Created {{ $task->created_at->format('M j, Y') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if($task->status != 3)
            <button onclick="updateTaskStatus(3)" class="px-3 py-1.5 bg-emerald-500 text-white text-xs font-medium rounded-lg hover:bg-emerald-600 transition-all flex items-center gap-1">
                <i class="bi bi-check2-circle"></i> Mark Done
            </button>
            @endif
            <a href="{{ route('admin.tasks.edit', $task->id) }}" class="px-3 py-1.5 bg-amber-500 text-white text-xs font-medium rounded-lg hover:bg-amber-600 transition-all">Edit</a>
            <form action="{{ route('admin.tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Delete task?');" class="inline">
                @csrf @method('DELETE')
                <button type="submit" class="px-3 py-1.5 bg-red-500 text-white text-xs font-medium rounded-lg hover:bg-red-600 transition-all">Delete</button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 space-y-4">
            @if($task->description)
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
                <div class="flex items-center gap-2 mb-3">
                    <i class="bi bi-card-text text-slate-400"></i>
                    <h3 class="text-sm font-semibold text-slate-800">Description</h3>
                </div>
                <div class="text-sm text-slate-600 leading-relaxed">{!! $task->description !!}</div>
            </div>
            @endif

            @if($task->checklistItems->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-check2-square text-purple-500"></i>
                        <h3 class="text-sm font-semibold text-slate-800">Checklist</h3>
                    </div>
                    @php $completed = $task->checklistItems->where('finished', 1)->count(); $total = $task->checklistItems->count(); @endphp
                    <span class="text-xs px-2 py-0.5 bg-slate-100 rounded text-slate-600">{{ $completed }}/{{ $total }}</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 mb-3">
                    <div class="bg-purple-500 h-1.5 rounded-full" style="width: {{ $total ? ($completed/$total)*100 : 0 }}%"></div>
                </div>
                <div class="space-y-2">
                    @foreach($task->checklistItems as $item)
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded border-2 flex items-center justify-center flex-shrink-0 {{ $item->finished ? 'bg-purple-500 border-purple-500' : 'border-slate-300' }}">
                            @if($item->finished)<i class="bi bi-check text-white text-[10px]"></i>@endif
                        </div>
                        <span class="text-sm text-slate-700 {{ $item->finished ? 'line-through text-slate-400' : '' }}">{{ $item->description }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
                <div class="flex items-center gap-2 mb-4">
                    <i class="bi bi-info-circle text-slate-400"></i>
                    <h3 class="text-sm font-semibold text-slate-800">Details</h3>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div class="space-y-2">
                        <div>
                            <span class="text-[10px] text-slate-400 uppercase">Status</span>
                            @php $statusClasses = ['bg-slate-100 text-slate-600', 'bg-amber-100 text-amber-700', 'bg-blue-100 text-blue-700', 'bg-emerald-100 text-emerald-700']; $statusLabels = ['Backlog', 'To Do', 'In Progress', 'Done']; @endphp
                            <div class="mt-1 px-2 py-1 rounded text-xs font-medium {{ $statusClasses[$task->status] ?? $statusClasses[0] }}">{{ $statusLabels[$task->status] ?? 'Unknown' }}</div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div>
                            <span class="text-[10px] text-slate-400 uppercase">Priority</span>
                            @php $priorityClasses = ['bg-emerald-100 text-emerald-700', 'bg-amber-100 text-amber-700', 'bg-red-100 text-red-700']; $priorityLabels = ['Low', 'Medium', 'High']; @endphp
                            <div class="mt-1 px-2 py-1 rounded text-xs font-medium {{ $priorityClasses[$task->priority-1] ?? $priorityClasses[0] }}">{{ $priorityLabels[$task->priority-1] ?? 'Unknown' }}</div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div>
                            <span class="text-[10px] text-slate-400 uppercase">Billable</span>
                            <div class="mt-1 text-xs text-slate-700">{{ $task->billable ? 'Yes' : 'No' }}</div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div>
                            <span class="text-[10px] text-slate-400 uppercase">Start Date</span>
                            <div class="mt-1 text-xs text-slate-700">{{ $task->startdate ? \Carbon\Carbon::parse($task->startdate)->format('M j, Y') : '-' }}</div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div>
                            <span class="text-[10px] text-slate-400 uppercase">Due Date</span>
                            <div class="mt-1 text-xs {{ $task->duedate && \Carbon\Carbon::parse($task->duedate)->isPast() && $task->status != 3 ? 'text-red-600 font-medium' : 'text-slate-700' }}">{{ $task->duedate ? \Carbon\Carbon::parse($task->duedate)->format('M j, Y') : '-' }}</div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div>
                            <span class="text-[10px] text-slate-400 uppercase">Created By</span>
                            <div class="mt-1 text-xs text-slate-700">{{ $task->creator->firstname ?? '-' }} {{ $task->creator->lastname ?? '' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
                <div class="flex items-center gap-2 mb-4">
                    <i class="bi bi-people text-slate-400"></i>
                    <h3 class="text-sm font-semibold text-slate-800">Team</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <span class="text-[10px] text-slate-400 uppercase block mb-2">Assigned To</span>
                        @if($task->assignedStaff->count() > 0)
                        <div class="flex flex-wrap gap-2">
                            @foreach($task->assignedStaff as $staff)
                            <span class="inline-flex items-center gap-1.5 px-2 py-1 bg-blue-50 rounded text-xs text-blue-700">
                                <span class="w-5 h-5 rounded-full bg-slate-400 flex items-center justify-center text-white text-[8px] font-semibold">
                                    {{ strtoupper(substr($staff->firstname, 0, 1)) }}
                                </span>
                                {{ $staff->firstname }} {{ $staff->lastname ?? '' }}
                            </span>
                            @endforeach
                        </div>
                        @else
                        <span class="text-xs text-slate-400">No one assigned</span>
                        @endif
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 uppercase block mb-2">Followers</span>
                        @if($task->followers->count() > 0)
                        <div class="flex flex-wrap gap-2">
                            @foreach($task->followers as $follower)
                            <span class="inline-flex items-center gap-1.5 px-2 py-1 bg-slate-100 rounded text-xs text-slate-700">
                                <span class="w-5 h-5 rounded-full bg-gradient-to-br from-slate-400 to-slate-600 flex items-center justify-center text-white text-[8px] font-semibold">
                                    {{ strtoupper(substr($follower->firstname, 0, 1)) }}
                                </span>
                                {{ $follower->firstname }}
                            </span>
                            @endforeach
                        </div>
                        @else
                        <span class="text-xs text-slate-400">No followers</span>
                        @endif
                    </div>
                </div>
            </div>

            @if($task->relatedEntity || $task->milestoneRelation)
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
                <div class="flex items-center gap-2 mb-4">
                    <i class="bi bi-link-45deg text-slate-400"></i>
                    <h3 class="text-sm font-semibold text-slate-800">Related</h3>
                </div>
                <div class="space-y-2">
                    @if($task->relatedEntity)
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] text-slate-400 uppercase w-16">Related To</span>
                        <span class="px-2 py-1 bg-blue-50 rounded text-xs text-blue-700">
                            <i class="bi bi-folder text-[10px] mr-1"></i>
                            @php
                                $relRouteNames = ['project' => 'admin.projects.show', 'invoice' => 'admin.invoices.show', 'estimate' => 'admin.estimates.show', 'proposal' => 'admin.proposals.show', 'lead' => 'admin.leads.show', 'ticket' => 'admin.tickets.show', 'client' => 'admin.clients.show'];
                                $relRoute = $relRouteNames[$task->rel_type] ?? null;
                            @endphp
                            @if($relRoute)
                            <a href="{{ route($relRoute, $task->rel_id) }}" class="hover:text-blue-700">{{ ucfirst($task->rel_type) }}: {{ $task->relatedEntity->name ?? $task->relatedEntity->number ?? 'N/A' }}</a>
                            @else
                            {{ ucfirst($task->rel_type) }}: {{ $task->relatedEntity->name ?? $task->relatedEntity->number ?? 'N/A' }}
                            @endif
                        </span>
                    </div>
                    @endif
                    @if($task->milestoneRelation)
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] text-slate-400 uppercase w-16">Milestone</span>
                        <span class="px-2 py-1 bg-indigo-50 rounded text-xs text-indigo-700">
                            <i class="bi bi-flag text-[10px] mr-1"></i>
                            {{ $task->milestoneRelation->name }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            @if($task->timers->count() > 0)
            @php
                $totalMinutes = $task->timers->reduce(function($carry, $t) {
                    if ($t->end_time) return $carry + $t->start_time->diffInMinutes($t->end_time);
                    return $carry;
                }, 0);
            @endphp
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-clock-history text-orange-500"></i>
                        <h3 class="text-sm font-semibold text-slate-800">Time Tracking</h3>
                    </div>
                    <span class="text-xs font-medium text-slate-600">
                        Total: {{ number_format($totalMinutes / 60, 1) }}h
                    </span>
                </div>
                <div class="space-y-2">
                    @foreach($task->timers as $timer)
                    @php $durationMinutes = $timer->end_time ? $timer->start_time->diffInMinutes($timer->end_time) : 0; @endphp
                    <div class="flex items-center justify-between py-1.5 px-3 bg-slate-50 rounded">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-gradient-to-br from-orange-400 to-amber-500 flex items-center justify-center text-white text-[8px] font-semibold">
                                {{ strtoupper(substr($timer->staff->firstname ?? 'U', 0, 1)) }}
                            </span>
                            <span class="text-xs text-slate-600">{{ $timer->staff->firstname ?? 'Unknown' }}</span>
                        </div>
                        @if($timer->end_time)
                        <span class="text-xs font-medium text-slate-800">{{ number_format($durationMinutes / 60, 1) }}h</span>
                        @else
                        <span class="text-xs font-medium text-orange-600 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 bg-orange-500 rounded-full animate-pulse"></span> Running
                        </span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($task->hourly_rate > 0 || $task->invoice)
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
                <div class="flex items-center gap-2 mb-4">
                    <i class="bi bi-currency-dollar text-slate-400"></i>
                    <h3 class="text-sm font-semibold text-slate-800">Billing</h3>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    @if($task->hourly_rate > 0)
                    <div>
                        <span class="text-[10px] text-slate-400 uppercase">Hourly Rate</span>
                        <div class="text-xs text-slate-700 font-medium">${{ number_format($task->hourly_rate, 2) }}/hr</div>
                    </div>
                    @endif
                    @if($task->billed)
                    <div>
                        <span class="text-[10px] text-slate-400 uppercase">Billed</span>
                        <div class="text-xs text-emerald-600 font-medium">Yes</div>
                    </div>
                    @endif
                    @if($task->invoice)
                    <div>
                        <span class="text-[10px] text-slate-400 uppercase">Invoice</span>
                        <div class="text-xs text-blue-600 font-medium">#{{ $task->invoice->number }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <div class="space-y-4">
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-chat-dots text-cyan-600"></i>
                        <span class="text-sm font-semibold text-slate-800">Comments</span>
                    </div>
                    <span class="text-xs text-slate-400">{{ $task->comments->count() }}</span>
                </div>

                @if($task->comments->count() > 0)
                <div class="max-h-80 overflow-y-auto divide-y divide-slate-100">
                    @foreach($task->comments->sortByDesc('dateadded') as $comment)
                    <div class="p-3 hover:bg-slate-50">
                        <div class="flex items-start gap-2">
                            @php 
                                $firstName = $comment->addedFrom ? $comment->addedFrom->firstname : 'U';
                                $initial = strtoupper(substr($firstName, 0, 1));
                            @endphp
                            <div class="w-7 h-7 rounded-full bg-slate-400 flex items-center justify-center text-white text-xs font-semibold flex-shrink-0">
                                {{ $initial }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-xs font-semibold text-slate-800">{{ $comment->addedFrom->firstname ?? 'Unknown' }}</span>
                                    <span class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($comment->dateadded)->diffForHumans() }}</span>
                                </div>
                                <div class="text-xs text-slate-600 mt-1 leading-snug comment-content">{!! $comment->content !!}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="p-4 text-center">
                    <p class="text-xs text-slate-400">No comments</p>
                </div>
                @endif

                <div class="p-3 bg-slate-50 border-t border-slate-100">
                    <form id="commentForm">
                        @csrf
                        <input type="hidden" name="task_id" value="{{ $task->id }}">
                        <div class="flex items-start gap-2 mb-2">
                            <div class="w-7 h-7 rounded-full bg-slate-400 flex items-center justify-center text-white text-xs font-semibold flex-shrink-0">
                                {{ strtoupper(substr(Auth::user()->firstname ?? 'U', 0, 1)) }}
                            </div>
                            <div class="flex-1 bg-white rounded-lg border border-slate-200 overflow-hidden">
                                <div id="commentEditor" style="min-height: 100px; width: 100%;"></div>
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition-all flex items-center gap-1">
                                <i class="bi bi-send-fill"></i> Post
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
var quill = new Quill('#commentEditor', {
    theme: 'snow',
    placeholder: 'Write a comment...',
    modules: {
        toolbar: [
            ['bold', 'italic', 'underline'],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            ['link', 'clean']
        ]
    }
});

document.getElementById('commentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var content = quill.root.innerHTML;
    if (quill.getText().trim() === '') {
        showToast('Please enter a comment', 'error');
        return;
    }

    var formData = new FormData();
    formData.append('content', content);
    formData.append('task_id', {{ $task->id }});

    fetch('/admin/comments', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            quill.setContents([]);
            setTimeout(() => location.reload(), 500);
        }
    });
});

function updateTaskStatus(status) {
    fetch('/admin/tasks/{{ $task->id }}/status', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ status: status === 3 ? 'completed' : 'in_progress' })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast('Task marked as done', 'success');
            setTimeout(() => location.reload(), 500);
        }
    });
}
</script>
@endpush