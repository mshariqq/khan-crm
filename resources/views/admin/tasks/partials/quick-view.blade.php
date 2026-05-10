<!-- Quick View Modal Header -->
<div class="flex items-center justify-between p-6 border-b border-slate-100">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
            <i class="bi bi-check2-square text-white text-lg"></i>
        </div>
        <div>
            <h3 class="text-xl font-bold text-slate-900">{{ $task->name }}</h3>
            <p class="text-sm text-slate-500">Task #{{ $task->id }}</p>
        </div>
    </div>
    <button onclick="closeTaskModal()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:text-slate-700 hover:bg-slate-200 transition-all flex items-center justify-center">
        <i class="bi bi-x-lg"></i>
    </button>
</div>

<!-- Quick View Modal Content -->
<div class="p-6 space-y-6 max-h-[70vh] overflow-y-auto">
    
    <!-- Task Overview Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="text-center p-4 bg-white/70 rounded-xl border border-slate-100">
            <i class="bi bi-flag text-slate-400 text-xl mb-1"></i>
            <p class="text-xs text-slate-500 mb-1">Priority</p>
            <p class="text-sm font-bold text-slate-900">
                {{ is_numeric($task->priority) ? 
                    ($task->priority == 3 ? 'High' : ($task->priority == 2 ? 'Medium' : 'Low')) 
                    : ucfirst($task->priority) }}
            </p>
        </div>
        <div class="text-center p-4 bg-white/70 rounded-xl border border-slate-100">
            <i class="bi bi-hourglass-split text-slate-400 text-xl mb-1"></i>
            <p class="text-xs text-slate-500 mb-1">Status</p>
            <p class="text-sm font-bold text-slate-900">
                {{ $task->status == 3 ? 'Done' : ($task->status == 2 ? 'In Progress' : 'To Do') }}
            </p>
        </div>
        <div class="text-center p-4 bg-white/70 rounded-xl border border-slate-100">
            <i class="bi bi-calendar-check text-slate-400 text-xl mb-1"></i>
            <p class="text-xs text-slate-500 mb-1">Due Date</p>
            <p class="text-sm font-bold text-slate-900">
                {{ $task->duedate ? \Carbon\Carbon::parse($task->duedate)->format('M j, Y') : '-' }}
            </p>
        </div>
        <div class="text-center p-4 bg-white/70 rounded-xl border border-slate-100">
            <i class="bi bi-percent text-slate-400 text-xl mb-1"></i>
            <p class="text-xs text-slate-500 mb-1">Progress</p>
            <p class="text-sm font-bold text-slate-900">{{ $task->progress ?? 0 }}%</p>
        </div>
    </div>

    <!-- Task Details -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h4 class="text-lg font-semibold text-slate-900 flex items-center gap-2">
                <i class="bi bi-card-text text-slate-400"></i>
                Task Details
            </h4>
            <span class="px-3 py-1 text-xs font-bold rounded-full bg-slate-100 text-slate-600">
                Created {{ \Carbon\Carbon::parse($task->dateadded)->diffForHumans() }}
            </span>
        </div>
        
        @if($task->description)
            <div class="bg-white/70 rounded-xl p-4 border border-slate-100">
                <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-wrap">{{ $task->description }}</p>
            </div>
        @else
            <div class="text-center py-6 bg-slate-50 rounded-xl">
                <i class="bi bi-file-text text-slate-300 text-2xl mb-2"></i>
                <p class="text-sm text-slate-400">No description provided</p>
            </div>
        @endif
    </div>

    <!-- Project & Milestone -->
    @if($task->project || $task->milestoneRelation)
    <div class="space-y-4">
        <h4 class="text-lg font-semibold text-slate-900 flex items-center gap-2">
            <i class="bi bi-folder2 text-slate-400"></i>
            Project & Milestone
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @if($task->project)
            <div class="bg-white/70 rounded-xl p-4 border border-slate-100">
                <p class="text-xs text-slate-500 uppercase tracking-wide mb-1">Project</p>
                <p class="text-sm font-semibold text-slate-900">{{ $task->project->name }}</p>
            </div>
            @endif
            @if($task->milestoneRelation)
            <div class="bg-white/70 rounded-xl p-4 border border-slate-100">
                <p class="text-xs text-slate-500 uppercase tracking-wide mb-1">Milestone</p>
                <p class="text-sm font-semibold text-slate-900">{{ $task->milestoneRelation->name }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Assignees -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h4 class="text-lg font-semibold text-slate-900 flex items-center gap-2">
                <i class="bi bi-people text-slate-400"></i>
                Assignees
            </h4>
            <span class="text-sm text-slate-500">{{ $task->assignedStaff->count() }} assigned</span>
        </div>
        
        @if($task->assignedStaff->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($task->assignedStaff as $assignee)
                <div class="flex items-center gap-3 bg-white/70 rounded-xl p-3 border border-slate-100">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold">
                        {{ substr($assignee->firstname, 0, 1) }}{{ substr($assignee->lastname, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-900 truncate">{{ $assignee->firstname }} {{ $assignee->lastname }}</p>
                        <p class="text-xs text-slate-500">{{ $assignee->email }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-6 bg-slate-50 rounded-xl">
                <i class="bi bi-person-x text-slate-300 text-2xl mb-2"></i>
                <p class="text-sm text-slate-400">No team members assigned yet</p>
                <button class="mt-3 px-4 py-2 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 transition-all">
                    <i class="bi bi-person-plus"></i> Assign Team
                </button>
            </div>
        @endif
    </div>

    <!-- Recent Comments -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h4 class="text-lg font-semibold text-slate-900 flex items-center gap-2">
                <i class="bi bi-chat-dots text-slate-400"></i>
                Recent Comments
            </h4>
            <span class="px-2 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-700">
                {{ $task->comments->count() }}
            </span>
        </div>
        
        @if($task->comments->count() > 0)
            <div class="space-y-3">
                @foreach($task->comments->take(3) as $comment)
                <div class="bg-white/70 rounded-xl p-4 border border-slate-100">
                    <div class="flex items-start gap-3 mb-2">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white text-xs font-bold">
                            {{ substr($comment->creator->firstname ?? 'U', 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-900">
                                {{ $comment->creator->firstname ?? 'Unknown' }} {{ $comment->creator->lastname ?? '' }}
                            </p>
                            <p class="text-xs text-slate-500">
                                {{ \Carbon\Carbon::parse($comment->dateadded)->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                    <div class="text-sm text-slate-700 whitespace-pre-wrap pl-11">{{ $comment->content }}</div>
                </div>
                @endforeach
            </div>
           
            @if($task->comments->count() > 3)
                <div class="text-center pt-2">
                    <button class="text-sm text-blue-600 hover:text-blue-700 font-semibold">
                        View all {{ $task->comments->count() }} comments
                    </button>
                </div>
            @endif
        @else
            <div class="text-center py-6 bg-slate-50 rounded-xl">
                <i class="bi bi-chat-square text-slate-300 text-2xl mb-2"></i>
                <p class="text-sm text-slate-400">No comments yet</p>
                <p class="text-xs text-slate-400 mt-1">Start the conversation</p>
            </div>
        @endif
    </div>

    <!-- Time Tracking Summary -->
    @if($task->timers->count() > 0)
    <div class="space-y-4">
        <h4 class="text-lg font-semibold text-slate-900 flex items-center gap-2">
            <i class="bi bi-clock-history text-slate-400"></i>
            Time Tracking
        </h4>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center p-4 bg-white/70 rounded-xl border border-slate-100">
                <p class="text-lg font-bold text-slate-900">{{ number_format($task->timers->sum('total_time_hours'), 1) }}h</p>
                <p class="text-xs text-slate-500">Total Time</p>
            </div>
            <div class="text-center p-4 bg-white/70 rounded-xl border border-slate-100">
                <p class="text-lg font-bold text-slate-900">{{ $task->timers->count() }}</p>
                <p class="text-xs text-slate-500">Sessions</p>
            </div>
            <div class="text-center p-4 bg-white/70 rounded-xl border border-slate-100">
                <p class="text-lg font-bold text-slate-900">{{ number_format($task->timers->avg('total_time_hours'), 1) }}h</p>
                <p class="text-xs text-slate-500">Avg Session</p>
            </div>
            <div class="text-center p-4 bg-white/70 rounded-xl border border-slate-100">
                <p class="text-lg font-bold text-slate-900">{{ $task->hourly_rate ? '$' . number_format($task->hourly_rate, 2) : '-' }}</p>
                <p class="text-xs text-slate-500">Hourly Rate</p>
            </div>
        </div>
    </div>
    @endif

</div>

<!-- Quick View Modal Footer -->
<div class="flex items-center justify-between p-6 border-t border-slate-100 bg-slate-50/50">
    <div class="text-sm text-slate-500">
        <i class="bi bi-person-circle"></i> Created by {{ $task->creator->firstname ?? 'System' }} {{ $task->creator->lastname ?? '' }}
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.tasks.show', $task) }}" 
           class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">
            <i class="bi bi-eye"></i> Full Details
        </a>
        <a href="{{ route('admin.tasks.edit', $task) }}" 
           class="px-4 py-2 bg-amber-500 text-white text-sm font-semibold rounded-xl hover:bg-amber-600 transition-all">
            <i class="bi bi-pencil"></i> Edit
        </a>
    </div>
</div>
