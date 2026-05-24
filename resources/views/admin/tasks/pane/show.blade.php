@php
$statusLabels = ['Backlog', 'To Do', 'In Progress', 'Done'];
$statusColors = ['bg-slate-100 text-slate-700', 'bg-amber-100 text-amber-700', 'bg-blue-100 text-blue-700', 'bg-emerald-100 text-emerald-700'];
$priorityLabels = ['Low', 'Medium', 'High'];
$priorityColors = ['bg-emerald-100 text-emerald-700', 'bg-amber-100 text-amber-700', 'bg-red-100 text-red-700'];
$completed = $task->checklistItems->where('finished', 1)->count();
$total = $task->checklistItems->count();
$progress = $total > 0 ? round(($completed / $total) * 100) : 0;
$totalMinutes = $task->timers->reduce(function($carry, $t) {
    if ($t->end_time) return $carry + $t->start_time->diffInMinutes($t->end_time);
    return $carry;
}, 0);
@endphp

<div class="flex items-start justify-between mb-6">
    <div class="flex items-start gap-4">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-blue-200 flex-shrink-0">
            {{ strtoupper(substr($task->name, 0, 1)) }}
        </div>
        <div>
            <div class="flex items-center gap-3 mb-1">
                <h1 class="text-xl font-bold text-slate-800">{{ $task->name }}</h1>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $statusColors[$task->status] ?? $statusColors[0] }}">{{ $statusLabels[$task->status] ?? 'Unknown' }}</span>
            </div>
            <div class="flex items-center gap-3 text-xs text-slate-400">
                <span>#{{ $task->id }}</span>
                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                <span>Created {{ $task->created_at->format('M j, Y') }}</span>
                @if($task->creator)
                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                <span class="flex items-center gap-1.5">
                    <span class="w-4 h-4 rounded-full bg-slate-400 flex items-center justify-center text-white text-[6px] font-bold">{{ strtoupper(substr($task->creator->firstname, 0, 1)) }}</span>
                    {{ $task->creator->firstname }}
                </span>
                @endif
                @if($task->project)
                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                <a href="{{ route('admin.projects.show', $task->project->id) }}" class="text-blue-600 hover:underline">{{ $task->project->name }}</a>
                @endif
            </div>
        </div>
    </div>
    <div class="flex items-center gap-2 flex-shrink-0">
        @if($task->status != 3)
        <button onclick="paneUpdateTaskStatus({{ $task->id }}, 3)" class="px-4 py-2 bg-emerald-500 text-white text-xs font-bold rounded-xl hover:bg-emerald-600 transition-all flex items-center gap-1.5 shadow-lg shadow-emerald-200">
            <i class="bi bi-check2-circle"></i> Mark Done
        </button>
        @endif
        <button onclick="parent.openTaskPane('{{ route('admin.tasks.pane.edit', $task->id) }}')" class="px-3 py-2 bg-white border border-slate-200 text-slate-600 text-xs font-bold rounded-xl hover:bg-slate-50 transition-all flex items-center gap-1.5">
            <i class="bi bi-pencil"></i> Edit
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-5 gap-5">
    {{-- Main Content --}}
    <div class="lg:col-span-3 space-y-5">

        {{-- Description --}}
        @if($task->description)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                <i class="bi bi-card-text text-slate-400"></i>
                <h3 class="text-sm font-semibold text-slate-800">Description</h3>
            </div>
            <div class="p-5 text-sm text-slate-600 leading-relaxed prose prose-slate max-w-none">{!! $task->description !!}</div>
        </div>
        @endif

        {{-- Checklist --}}
        @if($total > 0)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="bi bi-check2-square text-purple-500"></i>
                    <h3 class="text-sm font-semibold text-slate-800">Checklist</h3>
                </div>
                <span class="text-xs font-medium text-slate-500">{{ $completed }}/{{ $total }}</span>
            </div>
            <div class="p-5">
                <div class="w-full bg-slate-100 rounded-full h-2 mb-4">
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-2 rounded-full transition-all" style="width: {{ $progress }}%"></div>
                </div>
                <div class="space-y-1.5">
                    @foreach($task->checklistItems as $item)
                    <div class="flex items-center gap-3 px-3 py-2 rounded-lg {{ $item->finished ? 'bg-emerald-50/50' : 'hover:bg-slate-50' }} transition-colors">
                        <div class="w-5 h-5 rounded-md border-2 flex items-center justify-center flex-shrink-0 transition-all {{ $item->finished ? 'bg-emerald-500 border-emerald-500' : 'border-slate-300' }}">
                            @if($item->finished)<i class="bi bi-check text-white text-[10px]"></i>@endif
                        </div>
                        <span class="text-sm {{ $item->finished ? 'line-through text-slate-400' : 'text-slate-700' }}">{{ $item->description }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Comments --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="bi bi-chat-dots text-cyan-600"></i>
                    <h3 class="text-sm font-semibold text-slate-800">Comments</h3>
                </div>
                <span class="px-2 py-0.5 bg-cyan-50 text-cyan-700 rounded-full text-[10px] font-bold">{{ $task->comments->count() }}</span>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($task->comments->sortByDesc('dateadded') as $comment)
                <div class="p-4 hover:bg-slate-50/50 transition-colors">
                    <div class="flex items-start gap-3">
                        @php
                            $cfn = $comment->addedFrom ? $comment->addedFrom->firstname : 'U';
                            $cln = $comment->addedFrom ? $comment->addedFrom->lastname : '';
                            $cInitial = strtoupper(substr($cfn, 0, 1));
                        @endphp
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-slate-400 to-slate-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                            {{ $cInitial }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-0.5">
                                <span class="text-sm font-semibold text-slate-800">{{ $cfn }} {{ $cln }}</span>
                                <span class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($comment->dateadded)->diffForHumans() }}</span>
                            </div>
                            <div class="comment-content text-sm text-slate-600 leading-relaxed">{!! $comment->content !!}</div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center">
                    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
                        <i class="bi bi-chat-dots text-slate-300 text-xl"></i>
                    </div>
                    <p class="text-sm text-slate-500 mb-1">No comments yet</p>
                    <p class="text-xs text-slate-400">Start the conversation below</p>
                </div>
                @endforelse
            </div>
            <div class="px-5 py-4 bg-slate-50/50 border-t border-slate-100">
                <form id="paneCommentForm">
                    @csrf
                    <input type="hidden" name="task_id" value="{{ $task->id }}">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                            {{ strtoupper(substr(auth()->user()->firstname ?? 'U', 0, 1)) }}
                        </div>
                        <div class="flex-1 space-y-2">
                            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden focus-within:ring-2 focus-within:ring-blue-500/20 focus-within:border-blue-300 transition-all">
                                <div id="paneCommentEditor" style="min-height: 90px;"></div>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="px-5 py-2 bg-blue-600 text-white text-xs font-bold rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-200 flex items-center gap-1.5">
                                    <i class="bi bi-send-fill"></i> Post Comment
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- Task Info --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                <i class="bi bi-info-circle text-slate-400"></i>
                <h3 class="text-sm font-semibold text-slate-800">Task Info</h3>
            </div>
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status</span>
                        <div class="mt-1.5">
                            <span class="px-3 py-1.5 rounded-lg text-xs font-bold {{ $statusColors[$task->status] ?? $statusColors[0] }}">{{ $statusLabels[$task->status] ?? 'Unknown' }}</span>
                        </div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Priority</span>
                        <div class="mt-1.5">
                            <span class="px-3 py-1.5 rounded-lg text-xs font-bold {{ $priorityColors[$task->priority-1] ?? $priorityColors[0] }}">
                                @if($task->priority == 3)<i class="bi bi-arrow-up-short mr-0.5"></i>
                                @elseif($task->priority == 2)<i class="bi bi-dash mr-0.5"></i>
                                @else<i class="bi bi-arrow-down-short mr-0.5"></i>@endif
                                {{ $priorityLabels[$task->priority-1] ?? 'Unknown' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Start Date</span>
                        <p class="mt-1.5 text-sm font-medium text-slate-700">{{ $task->startdate ? \Carbon\Carbon::parse($task->startdate)->format('M j, Y') : '-' }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Due Date</span>
                        <p class="mt-1.5 text-sm font-medium {{ $task->duedate && \Carbon\Carbon::parse($task->duedate)->isPast() && $task->status != 3 ? 'text-red-600' : 'text-slate-700' }}">
                            {{ $task->duedate ? \Carbon\Carbon::parse($task->duedate)->format('M j, Y') : '-' }}
                            @if($task->duedate && \Carbon\Carbon::parse($task->duedate)->isPast() && $task->status != 3)
                                <span class="text-[10px] text-red-500 ml-1">(Overdue)</span>
                            @elseif($task->duedate && !\Carbon\Carbon::parse($task->duedate)->isPast() && $task->status != 3)
                                <span class="text-[10px] text-slate-400 ml-1">({{ \Carbon\Carbon::parse($task->duedate)->diffInDays(now()) }}d left)</span>
                            @endif
                        </p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Billable</span>
                        <div class="mt-1.5 flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full {{ $task->billable ? 'bg-emerald-500' : 'bg-slate-300' }}"></div>
                            <span class="text-sm font-medium text-slate-700">{{ $task->billable ? 'Yes' : 'No' }}</span>
                        </div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Visible to Client</span>
                        <div class="mt-1.5 flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full {{ $task->visible_to_client ? 'bg-emerald-500' : 'bg-slate-300' }}"></div>
                            <span class="text-sm font-medium text-slate-700">{{ $task->visible_to_client ? 'Yes' : 'No' }}</span>
                        </div>
                    </div>
                </div>
                @if($task->datefinished)
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Date Finished</span>
                    <p class="mt-1.5 text-sm font-medium text-emerald-600"><i class="bi bi-check-circle-fill mr-1 text-[10px]"></i>{{ \Carbon\Carbon::parse($task->datefinished)->format('M j, Y · g:i A') }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Team --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                <i class="bi bi-people text-slate-400"></i>
                <h3 class="text-sm font-semibold text-slate-800">Team</h3>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2.5">Assigned To</span>
                    @if($task->assignedStaff->count() > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($task->assignedStaff as $staff)
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 rounded-lg border border-blue-100">
                            <span class="w-6 h-6 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-[9px] font-bold">
                                {{ strtoupper(substr($staff->firstname, 0, 1)) }}{{ strtoupper(substr($staff->lastname ?? '', 0, 1)) }}
                            </span>
                            <span class="text-xs font-medium text-blue-700">{{ $staff->firstname }} {{ $staff->lastname ?? '' }}</span>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-sm text-slate-400 italic">No one assigned</p>
                    @endif
                </div>
                @if($task->followers->count() > 0)
                <div class="pt-3 border-t border-slate-100">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2.5">Followers</span>
                    <div class="flex flex-wrap gap-2">
                        @foreach($task->followers as $follower)
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-50 rounded-lg border border-slate-200">
                            <span class="w-6 h-6 rounded-full bg-gradient-to-br from-slate-400 to-slate-600 flex items-center justify-center text-white text-[9px] font-bold">
                                {{ strtoupper(substr($follower->firstname, 0, 1)) }}{{ strtoupper(substr($follower->lastname ?? '', 0, 1)) }}
                            </span>
                            <span class="text-xs font-medium text-slate-600">{{ $follower->firstname }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Attachments --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="bi bi-paperclip text-slate-400"></i>
                    <h3 class="text-sm font-semibold text-slate-800">Attachments</h3>
                </div>
                <span class="px-2 py-0.5 bg-slate-100 text-slate-600 rounded-full text-[10px] font-bold">{{ $task->attachments->count() }}</span>
            </div>
            <div class="p-4 space-y-3">
                @if($task->attachments->count() > 0)
                <div class="space-y-1.5 max-h-56 overflow-y-auto">
                    @foreach($task->attachments as $att)
                    @php
                        $ext = strtolower(pathinfo($att->original_file_name ?: $att->file_name, PATHINFO_EXTENSION));
                        $isImage = $att->filetype && str_starts_with($att->filetype, 'image/');
                        $iconMap = [
                            'pdf' => 'bi-filetype-pdf',
                            'doc' => 'bi-filetype-docx', 'docx' => 'bi-filetype-docx',
                            'xls' => 'bi-filetype-xlsx', 'xlsx' => 'bi-filetype-xlsx', 'csv' => 'bi-filetype-csv',
                            'zip' => 'bi-file-zip', 'rar' => 'bi-file-zip', '7z' => 'bi-file-zip',
                            'txt' => 'bi-filetype-txt',
                            'ppt' => 'bi-filetype-pptx', 'pptx' => 'bi-filetype-pptx',
                            'mp4' => 'bi-film', 'avi' => 'bi-film', 'mov' => 'bi-film',
                            'mp3' => 'bi-music-note', 'wav' => 'bi-music-note',
                            'json' => 'bi-filetype-json', 'xml' => 'bi-filetype-xml',
                            'html' => 'bi-filetype-html', 'css' => 'bi-filetype-css', 'js' => 'bi-filetype-js',
                        ];
                        $icon = $iconMap[$ext] ?? 'bi-file-earmark';
                    @endphp
                    <div class="flex items-center justify-between gap-2 px-3 py-2 rounded-lg hover:bg-slate-50 group transition-colors border border-transparent hover:border-slate-200">
                        <div class="flex items-center gap-2.5 min-w-0 flex-1">
                            @if($isImage)
                            <div class="w-10 h-10 rounded-lg overflow-hidden flex-shrink-0 border border-slate-200 bg-slate-50">
                                <img src="{{ $att->fileurl }}" alt="{{ $att->original_file_name ?: $att->file_name }}" class="w-full h-full object-cover" loading="lazy">
                            </div>
                            @else
                            <div class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 flex-shrink-0">
                                <i class="bi {{ $icon }} text-base"></i>
                            </div>
                            @endif
                            <div class="min-w-0">
                                <a href="{{ $att->fileurl }}" target="_blank" class="text-sm font-medium text-slate-700 hover:text-blue-600 truncate block">{{ $att->original_file_name ?: $att->file_name }}</a>
                                <span class="text-[10px] text-slate-400">{{ $att->staff ? $att->staff->firstname . ' ' . $att->staff->lastname : 'Unknown' }} · {{ \Carbon\Carbon::parse($att->dateadded)->format('M j, Y') }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-1 flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="{{ $att->fileurl }}" target="_blank" class="w-7 h-7 rounded-md flex items-center justify-center text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all" title="Download">
                                <i class="bi bi-download text-xs"></i>
                            </a>
                            <button onclick="deleteTaskAttachment({{ $att->id }}, this)" class="w-7 h-7 rounded-md flex items-center justify-center text-slate-400 hover:text-red-600 hover:bg-red-50 transition-all" title="Delete">
                                <i class="bi bi-trash3 text-xs"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-2">
                        <i class="bi bi-paperclip text-slate-300"></i>
                    </div>
                    <p class="text-sm text-slate-400">No attachments yet</p>
                </div>
                @endif

                <form action="{{ route('admin.tasks.attachments.store', $task->id) }}" method="POST" data-pane-submit="1" data-pane-refresh="{{ route('admin.tasks.pane.show', $task->id) }}" enctype="multipart/form-data" class="pt-3 border-t border-slate-100">
                    @csrf
                    <div class="flex items-center gap-2">
                        <label class="flex-1 flex items-center gap-2 px-3 py-2 rounded-lg border border-dashed border-slate-300 bg-slate-50 cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-all text-sm text-slate-500">
                            <i class="bi bi-upload text-blue-500"></i>
                            <span class="truncate" id="attachment-file-label">Choose file...</span>
                            <input type="file" name="file" class="hidden" onchange="document.getElementById('attachment-file-label').textContent = this.files[0]?.name || 'Choose file...'">
                        </label>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-xs font-bold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-200 flex items-center gap-1.5">
                            <i class="bi bi-upload"></i> Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Related --}}
        @if($task->relatedEntity || $task->milestoneRelation)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                <i class="bi bi-link-45deg text-slate-400"></i>
                <h3 class="text-sm font-semibold text-slate-800">Related</h3>
            </div>
            <div class="p-5 space-y-3">
                @if($task->relatedEntity)
                <div class="flex items-center gap-3 px-3 py-2.5 bg-blue-50 rounded-lg border border-blue-100">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600 flex-shrink-0">
                        <i class="bi bi-folder text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        @php
                            $relRouteNames = ['project' => 'admin.projects.show', 'invoice' => 'admin.invoices.show', 'estimate' => 'admin.estimates.show', 'proposal' => 'admin.proposals.show', 'lead' => 'admin.leads.show', 'ticket' => 'admin.tickets.show', 'client' => 'admin.clients.show'];
                            $relRoute = $relRouteNames[$task->rel_type] ?? null;
                            $paneTypeMap = ['estimate' => 'estimate', 'proposal' => 'proposal', 'ticket' => 'ticket'];
                            $paneType = $paneTypeMap[$task->rel_type] ?? null;
                        @endphp
                        <span class="text-[10px] font-bold text-blue-500 uppercase block">{{ $task->rel_type }}</span>
                        @if($relRoute)
                        <a href="{{ route($relRoute, $task->rel_id) }}" @if($paneType) data-pane="show" data-pane-type="{{ $paneType }}" data-pane-id="{{ $task->rel_id }}" @endif class="text-sm font-medium text-slate-800 hover:text-blue-600 truncate block">{{ $task->relatedEntity->name ?? $task->relatedEntity->number ?? 'N/A' }}</a>
                        @else
                        <span class="text-sm font-medium text-slate-800">{{ $task->relatedEntity->name ?? $task->relatedEntity->number ?? 'N/A' }}</span>
                        @endif
                    </div>
                </div>
                @endif
                @if($task->milestoneRelation)
                <div class="flex items-center gap-3 px-3 py-2.5 bg-indigo-50 rounded-lg border border-indigo-100">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600 flex-shrink-0">
                        <i class="bi bi-flag text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-[10px] font-bold text-indigo-500 uppercase block">Milestone</span>
                        <span class="text-sm font-medium text-slate-800">{{ $task->milestoneRelation->name }}</span>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Time Tracking --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="bi bi-clock-history text-orange-500"></i>
                    <h3 class="text-sm font-semibold text-slate-800">Time Tracking</h3>
                </div>
                <div class="flex items-center gap-2">
                    @if($task->timers->count() > 0)
                    <span class="text-xs font-bold text-orange-600">{{ number_format($totalMinutes / 60, 1) }}h total</span>
                    @endif
                    <button onclick="toggleTimesheetForm()" class="px-3 py-1.5 bg-orange-500 text-white text-[10px] font-bold rounded-lg hover:bg-orange-600 transition-all flex items-center gap-1 shadow-lg shadow-orange-200">
                        <i class="bi bi-plus-lg"></i> Log Time
                    </button>
                </div>
            </div>
            <div class="p-5 space-y-4">

                {{-- Log Time Form --}}
                <div id="timesheet-form" class="hidden p-4 bg-orange-50 rounded-xl border border-orange-200 space-y-3">
                    <div class="flex items-center gap-2 mb-1">
                        <i class="bi bi-clock text-orange-500 text-sm"></i>
                        <span class="text-xs font-bold text-orange-700 uppercase">Log Time</span>
                    </div>
                    <form action="{{ route('admin.timers.store') }}" method="POST" data-pane-submit="1" data-pane-refresh="{{ route('admin.tasks.pane.show', $task->id) }}">
                        @csrf
                        <input type="hidden" name="task_id" value="{{ $task->id }}">
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="text-[10px] font-medium text-slate-500">Date</label>
                                <input type="date" name="date" value="{{ date('Y-m-d') }}" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-orange-500 outline-none">
                            </div>
                            <div>
                                <label class="text-[10px] font-medium text-slate-500">Hours</label>
                                <input type="number" name="hours" min="0" max="23" value="0" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-orange-500 outline-none">
                            </div>
                            <div>
                                <label class="text-[10px] font-medium text-slate-500">Minutes</label>
                                <input type="number" name="minutes" min="0" max="59" value="0" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-orange-500 outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-medium text-slate-500">Note</label>
                            <textarea name="note" rows="1" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-orange-500 outline-none" placeholder="What did you work on?"></textarea>
                        </div>
                        <div class="flex justify-end gap-1.5">
                            <button type="button" onclick="toggleTimesheetForm()" class="px-3 py-1.5 text-[10px] font-semibold text-slate-500 hover:text-slate-700 transition-all">Cancel</button>
                            <button type="submit" class="px-4 py-1.5 bg-orange-500 text-white text-[10px] font-bold rounded-lg hover:bg-orange-600 transition-all shadow">Save Entry</button>
                        </div>
                    </form>
                </div>

                {{-- Timer List --}}
                @if($task->timers->count() > 0)
                <div class="space-y-2">
                    @foreach($task->timers as $timer)
                    @php
                        $durationMinutes = $timer->end_time ? $timer->start_time->diffInMinutes($timer->end_time) : 0;
                        $isOwner = auth()->id() === $timer->staff_id;
                        $timerDate = $timer->start_time instanceof \Carbon\Carbon ? $timer->start_time->format('Y-m-d') : date('Y-m-d');
                        $timerHours = floor($durationMinutes / 60);
                        $timerMins = $durationMinutes % 60;
                    @endphp

                    {{-- Display row --}}
                    <div id="timesheet-display-{{ $timer->id }}" class="flex items-center justify-between py-2 px-3 bg-slate-50 rounded-lg border border-slate-100 group">
                        <div class="flex items-center gap-2.5 min-w-0 flex-1">
                            <span class="w-7 h-7 rounded-full bg-slate-200 border border-slate-300 flex items-center justify-center text-slate-600 text-[9px] font-bold flex-shrink-0">
                                {{ strtoupper(substr($timer->staff->firstname ?? 'U', 0, 1)) }}{{ strtoupper(substr($timer->staff->lastname ?? '', 0, 1)) }}
                            </span>
                            <div class="min-w-0">
                                <span class="text-xs font-medium text-slate-700">{{ $timer->staff->firstname ?? 'Unknown' }} {{ $timer->staff->lastname ?? '' }}</span>
                                <span class="text-[10px] text-slate-400 block">
                                    {{ $timer->start_time instanceof \Carbon\Carbon ? $timer->start_time->format('M j, Y') : $timer->start_time }}
                                    @if($timer->note)
                                    · <span class="italic">{{ $timer->note }}</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 flex-shrink-0">
                            @if($timer->end_time)
                            <span class="text-xs font-bold text-slate-800">{{ $timerHours > 0 ? $timerHours . 'h ' : '' }}{{ $timerMins > 0 ? $timerMins . 'm' : '' }}</span>
                            @if($isOwner)
                            <div class="flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button onclick="editTimesheet({{ $timer->id }})" class="w-6 h-6 rounded-md flex items-center justify-center text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all" title="Edit"><i class="bi bi-pencil text-[10px]"></i></button>
                                <button onclick="deleteTimesheet({{ $timer->id }})" class="w-6 h-6 rounded-md flex items-center justify-center text-slate-400 hover:text-red-600 hover:bg-red-50 transition-all" title="Delete"><i class="bi bi-trash3 text-[10px]"></i></button>
                            </div>
                            @endif
                            @else
                            <span class="text-xs font-bold text-orange-600 flex items-center gap-1.5">
                                <span class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></span> Running
                            </span>
                            @endif
                        </div>
                    </div>

                    {{-- Edit form --}}
                    @if($isOwner)
                    <div id="timesheet-edit-{{ $timer->id }}" class="hidden p-3 bg-white rounded-xl border border-orange-200 space-y-2">
                        <form onsubmit="event.preventDefault(); updateTimesheet({{ $timer->id }})">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="task_id" value="{{ $task->id }}">
                            <div class="grid grid-cols-3 gap-2 mb-2">
                                <div>
                                    <label class="text-[10px] font-medium text-slate-500">Date</label>
                                    <input type="date" name="date" value="{{ $timerDate }}" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-orange-500 outline-none">
                                </div>
                                <div>
                                    <label class="text-[10px] font-medium text-slate-500">Hours</label>
                                    <input type="number" name="hours" min="0" max="23" value="{{ $timerHours }}" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-orange-500 outline-none">
                                </div>
                                <div>
                                    <label class="text-[10px] font-medium text-slate-500">Minutes</label>
                                    <input type="number" name="minutes" min="0" max="59" value="{{ $timerMins }}" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-orange-500 outline-none">
                                </div>
                            </div>
                            <div class="mb-2">
                                <label class="text-[10px] font-medium text-slate-500">Note</label>
                                <textarea name="note" rows="1" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-orange-500 outline-none" placeholder="What did you work on?">{{ $timer->note }}</textarea>
                            </div>
                            <div class="flex justify-end gap-1.5">
                                <button type="button" onclick="cancelEditTimesheet({{ $timer->id }})" class="px-3 py-1.5 text-[10px] font-semibold text-slate-500 hover:text-slate-700 transition-all">Cancel</button>
                                <button type="submit" class="px-4 py-1.5 bg-orange-500 text-white text-[10px] font-bold rounded-lg hover:bg-orange-600 transition-all shadow">Update</button>
                            </div>
                        </form>
                    </div>
                    @endif
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-2">
                        <i class="bi bi-clock text-slate-300"></i>
                    </div>
                    <p class="text-sm text-slate-400">No time logged yet</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Billing --}}
        @if(($task->hourly_rate ?? 0) > 0 || $task->billed || $task->invoice)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                <i class="bi bi-currency-dollar text-emerald-500"></i>
                <h3 class="text-sm font-semibold text-slate-800">Billing</h3>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-2 gap-4">
                    @if(($task->hourly_rate ?? 0) > 0)
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Hourly Rate</span>
                        <p class="text-lg font-bold text-emerald-600">{{ app_format_money($task->hourly_rate) }}<span class="text-xs text-slate-400 font-medium">/hr</span></p>
                    </div>
                    @endif
                    @if($task->billed)
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Status</span>
                        <p class="text-sm font-bold text-emerald-600 flex items-center gap-1"><i class="bi bi-check-circle-fill text-[10px]"></i> Billed</p>
                    </div>
                    @endif
                    @if($task->invoice)
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Invoice</span>
                        <a href="{{ route('admin.invoices.show', $task->invoice_id) }}" class="text-sm font-bold text-blue-600 hover:underline">#{{ $task->invoice->number }}</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
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
#paneCommentEditor .ql-editor { font-size: 13px; padding: 12px; min-height: 70px; }
#paneCommentEditor .ql-toolbar { border: none; border-bottom: 1px solid #eef2f6; background: #f8fafc; border-radius: 0; }
#paneCommentEditor .ql-container { border: none; }
.ql-snow .ql-stroke { stroke: #64748b; }
.ql-snow .ql-fill { fill: #64748b; }
.ql-snow .ql-picker { color: #64748b; }
</style>

<script>
try {
    if (window.paneCommentQuill) window.paneCommentQuill.destroy();
    window.paneCommentQuill = new Quill('#paneCommentEditor', {
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
} catch(e) {}

document.getElementById('paneCommentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    if (!window.paneCommentQuill) { parent.showToast('Editor not available', 'error'); return; }
    var content = window.paneCommentQuill.root.innerHTML;
    if (window.paneCommentQuill.getText().trim() === '') {
        parent.showToast('Please enter a comment', 'error');
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
            parent.showToast(data.message, 'success');
            window.paneCommentQuill.setContents([]);
            parent.openTaskPane('{{ route('admin.tasks.pane.show', $task->id) }}', 'Task');
        }
    });
});

function paneUpdateTaskStatus(id, status) {
    fetch('/admin/tasks/' + id + '/status', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ status: status === 3 ? 'completed' : 'in_progress' })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            parent.showToast('Task marked as done', 'success');
            parent.closeTaskPane();
        }
    });
}

function deleteTaskAttachment(id, btn) {
    if (!confirm('Delete this attachment?')) return;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin text-xs"></i>';
    fetch('/admin/tasks/attachments/' + id, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            parent.showToast(data.message, 'success');
            parent.openTaskPane('{{ route('admin.tasks.pane.show', $task->id) }}', 'Task');
        } else {
            parent.showToast(data.message || 'Delete failed', 'error');
        }
    })
    .catch(() => {
        parent.showToast('Network error', 'error');
    });
}

function toggleTimesheetForm() {
    document.getElementById('timesheet-form').classList.toggle('hidden');
}

function editTimesheet(id) {
    document.getElementById('timesheet-display-' + id).classList.add('hidden');
    document.getElementById('timesheet-edit-' + id).classList.remove('hidden');
}

function cancelEditTimesheet(id) {
    document.getElementById('timesheet-display-' + id).classList.remove('hidden');
    document.getElementById('timesheet-edit-' + id).classList.add('hidden');
}

function updateTimesheet(id) {
    const form = document.getElementById('timesheet-edit-' + id).querySelector('form');
    const formData = new FormData(form);
    fetch('/admin/timers/' + id, {
        method: 'POST',
        body: formData,
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            parent.showToast(data.message, 'success');
            parent.openTaskPane('{{ route('admin.tasks.pane.show', $task->id) }}', 'Task');
        } else {
            parent.showToast(data.message || 'Update failed', 'error');
        }
    })
    .catch(() => {
        parent.showToast('Network error', 'error');
    });
}

function deleteTimesheet(id) {
    if (!confirm('Delete this timesheet entry?')) return;
    fetch('/admin/timers/' + id, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            parent.showToast(data.message, 'success');
            parent.openTaskPane('{{ route('admin.tasks.pane.show', $task->id) }}', 'Task');
        } else {
            parent.showToast(data.message || 'Delete failed', 'error');
        }
    })
    .catch(() => {
        parent.showToast('Network error', 'error');
    });
}
</script>
