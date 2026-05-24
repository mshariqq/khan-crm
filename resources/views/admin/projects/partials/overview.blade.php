@php use App\Models\Project; @endphp
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
    <div class="p-3 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl border border-blue-200">
        <div class="flex items-center justify-between mb-1">
            <i class="bi bi-list-check text-blue-500 text-sm"></i>
        </div>
        <p class="text-xl font-bold text-blue-700">{{ $project->tasks->count() }}</p>
        <p class="text-[10px] text-blue-600">Tasks</p>
    </div>
    <div class="p-3 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl border border-emerald-200">
        <div class="flex items-center justify-between mb-1">
            <i class="bi bi-receipt text-emerald-500 text-sm"></i>
        </div>
        <p class="text-xl font-bold text-emerald-700">{{ app_format_money($project->invoices->sum('total')) }}</p>
        <p class="text-[10px] text-emerald-600">Invoiced</p>
    </div>
    <div class="p-3 bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl border border-amber-200">
        <div class="flex items-center justify-between mb-1">
            <i class="bi bi-cash-stack text-amber-500 text-sm"></i>
        </div>
        <p class="text-xl font-bold text-amber-700">{{ app_format_money($project->expenses->sum('amount')) }}</p>
        <p class="text-[10px] text-amber-600">Expenses</p>
    </div>
    <div class="p-3 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl border border-purple-200">
        <div class="flex items-center justify-between mb-1">
            <i class="bi bi-flag text-purple-500 text-sm"></i>
        </div>
        <p class="text-xl font-bold text-purple-700">{{ $project->milestones->count() }}</p>
        <p class="text-[10px] text-purple-600">Milestones</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="col-span-2 space-y-4">
        <div class="p-4 bg-slate-50 rounded-xl">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i class="bi bi-building text-blue-600 text-sm"></i>
                </div>
                <h3 class="text-sm font-semibold text-slate-800">Client</h3>
            </div>
            @if($project->client)
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-slate-200 flex items-center justify-center">
                    <i class="bi bi-building text-slate-500"></i>
                </div>
                <div>
                    <a href="{{ route('admin.clients.show', $project->client->userid) }}" class="text-sm font-medium text-slate-800 hover:text-blue-600">{{ $project->client->company }}</a>
                    <p class="text-xs text-slate-500">{{ $project->client->email }}</p>
                </div>
            </div>
            @else
            <p class="text-xs text-slate-400">No client assigned</p>
            @endif
        </div>

        <div class="p-4 bg-slate-50 rounded-xl">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                    <i class="bi bi-calendar-event text-indigo-600 text-sm"></i>
                </div>
                <h3 class="text-sm font-semibold text-slate-800">Project Details</h3>
            </div>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between py-2 border-b border-slate-200/50">
                    <span class="text-slate-500">Start Date</span>
                    <span class="text-slate-700 font-medium">{{ $project->start_date ? $project->start_date->format('M j, Y') : 'Not set' }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-200/50">
                    <span class="text-slate-500">Deadline</span>
                    <span class="text-slate-700 font-medium">{{ $project->deadline ? $project->deadline->format('M j, Y') : 'Not set' }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-200/50">
                    <span class="text-slate-500">Project Cost</span>
                    <span class="text-slate-700 font-medium">{{ app_format_money($project->project_cost ?? 0) }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-slate-500">Hourly Rate</span>
                    <span class="text-slate-700 font-medium">{{ app_format_money($project->hourly_rate ?? 0) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-4">
        <div class="p-4 bg-slate-50 rounded-xl">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg bg-cyan-100 flex items-center justify-center">
                    <i class="bi bi-people text-cyan-600 text-sm"></i>
                </div>
                <h3 class="text-sm font-semibold text-slate-800">Team</h3>
            </div>
            @if($project->staff->count() > 0)
            <div class="space-y-2">
                @foreach($project->staff as $member)
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-slate-400 flex items-center justify-center text-white text-[10px] font-semibold">
                        {{ substr($member->firstname, 0, 1) }}
                    </span>
                    <span class="text-xs text-slate-700">{{ $member->firstname }} {{ $member->lastname }}</span>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-xs text-slate-400 py-4 text-center">No team members</p>
            @endif
        </div>

        <div class="p-4 bg-slate-50 rounded-xl">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <i class="bi bi-wallet2 text-emerald-600 text-sm"></i>
                </div>
                <h3 class="text-sm font-semibold text-slate-800">Financial</h3>
            </div>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between py-2 border-b border-slate-200/50">
                    <span class="text-slate-500">Total Invoiced</span>
                    <span class="text-slate-700 font-medium">{{ app_format_money($project->invoices->sum('total')) }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-200/50">
                    <span class="text-slate-500">Total Expenses</span>
                    <span class="text-slate-700 font-medium">{{ app_format_money($project->expenses->sum('amount')) }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-slate-500">Profit</span>
                    <span class="text-emerald-600 font-medium">{{ app_format_money(($project->project_cost ?? 0) - $project->expenses->sum('amount')) }}</span>
                </div>
                @if($project->budget_utilization !== null)
                <div class="flex justify-between py-2 border-t border-slate-200/50">
                    <span class="text-slate-500">Budget Used</span>
                    <span class="font-medium {{ $project->budget_utilization > 100 ? 'text-red-600' : ($project->budget_utilization > 80 ? 'text-amber-600' : 'text-emerald-600') }}">
                        {{ $project->budget_utilization }}%
                    </span>
                </div>
                @endif
            </div>
        </div>

        <div class="p-4 bg-slate-50 rounded-xl">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                    <i class="bi bi-tags text-amber-600 text-sm"></i>
                </div>
                <h3 class="text-sm font-semibold text-slate-800">Status</h3>
            </div>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                @if($project->status === Project::STATUS_ACTIVE) bg-emerald-100 text-emerald-700
                @elseif($project->status === Project::STATUS_COMPLETED) bg-indigo-100 text-indigo-700
                @elseif($project->status === Project::STATUS_ON_HOLD) bg-amber-100 text-amber-700
                @else bg-red-100 text-red-700 @endif">
                @if($project->status === Project::STATUS_ACTIVE) Active
                @elseif($project->status === Project::STATUS_COMPLETED) Completed
                @elseif($project->status === Project::STATUS_ON_HOLD) On Hold
                @else Cancelled @endif
            </span>
        </div>
    </div>
</div>