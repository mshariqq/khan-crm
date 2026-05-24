@extends('layouts.admin')

@section('breadcrumb', 'Project Timesheets')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ $project->name }} - Timesheets</h1>
            <p class="text-slate-500 text-sm mt-1">Time tracking summary for all project tasks</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.projects.show', $project) }}" class="px-4 py-2.5 bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-all flex items-center gap-2">
                <i class="bi bi-arrow-left"></i> Back to Project
            </a>
            <button onclick="loadTimesheetSummary()" class="px-4 py-2.5 bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-all flex items-center gap-2">
                <i class="bi bi-bar-chart"></i> Summary
            </button>
            <a href="{{ route('admin.projects.timesheets.export', $project) }}" class="px-4 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 flex items-center gap-2">
                <i class="bi bi-download"></i> Export
            </a>
        </div>
    </div>

    @php
        $allTimers = $project->tasks->flatMap(function($task) {
            return $task->timers;
        })->sortByDesc('start_time');

        $totalMinutes = $allTimers->where('end_time', '!=', null)->sum(function($t) {
            return $t->start_time->diffInMinutes($t->end_time);
        });
        $billableMinutes = $allTimers->where('end_time', '!=', null)->where('is_billable', 1)->sum(function($t) {
            return $t->start_time->diffInMinutes($t->end_time);
        });
        $staffCount = $allTimers->pluck('staff_id')->unique()->count();
        $trackedTasksCount = $project->tasks->filter(fn($t) => $t->timers->count() > 0)->count();
        $pendingCount = $allTimers->filter(fn($t) => !$t->status || $t->status === 'pending')->count();
        $approvedCount = $allTimers->filter(fn($t) => $t->status === 'approved')->count();
        $rejectedCount = $allTimers->filter(fn($t) => $t->status === 'rejected')->count();
    @endphp

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Total Hours</p>
                    <p class="text-2xl font-bold text-slate-800">{{ number_format($totalMinutes / 60, 1) }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="bi bi-clock-history text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Billable Hours</p>
                    <p class="text-2xl font-bold text-emerald-600">{{ number_format($billableMinutes / 60, 1) }}</p>
                </div>
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="bi bi-currency-dollar text-emerald-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Staff Members</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $staffCount }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="bi bi-people text-purple-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Tasks Tracked</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $trackedTasksCount }}</p>
                </div>
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="bi bi-list-ul text-amber-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Approval Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-amber-50 rounded-xl border border-amber-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-amber-600 font-medium">Pending Approval</p>
                    <p class="text-2xl font-bold text-amber-800">{{ $pendingCount }}</p>
                </div>
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="bi bi-hourglass-split text-amber-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-emerald-50 rounded-xl border border-emerald-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-emerald-600 font-medium">Approved</p>
                    <p class="text-2xl font-bold text-emerald-800">{{ $approvedCount }}</p>
                </div>
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="bi bi-check-circle text-emerald-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-red-50 rounded-xl border border-red-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-red-600 font-medium">Rejected</p>
                    <p class="text-2xl font-bold text-red-800">{{ $rejectedCount }}</p>
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="bi bi-x-circle text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Bulk Actions --}}
    @can('projects_edit')
    <div class="flex items-center gap-3">
        <button onclick="bulkApprove()" id="bulkApproveBtn" disabled class="px-4 py-2 bg-emerald-600 text-white text-xs font-semibold rounded-lg hover:bg-emerald-700 transition-all shadow-sm flex items-center gap-1.5">
            <i class="bi bi-check-all"></i> Bulk Approve Selected
        </button>
        <span class="text-xs text-slate-400" id="selectedCount">0 selected</span>
    </div>
    @endcan

    {{-- Detailed Timesheet Table --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        @can('projects_edit')
                        <th class="px-3 py-3 text-left">
                            <input type="checkbox" id="selectAll" class="rounded border-slate-300">
                        </th>
                        @endcan
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Staff Member</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Task</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Start Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">End Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Duration</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Rate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Billable</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                        @can('projects_edit')
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                        @endcan
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($allTimers as $timer)
                        @php
                            $start = $timer->start_time;
                            $end = $timer->end_time;
                            $duration = $end ? $start->diffInMinutes($end) : now()->diffInMinutes($start);
                            $hours = floor($duration / 60);
                            $minutes = $duration % 60;
                            $timerStatus = $timer->status ?? 'pending';
                            $staf = $timer->staff;
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            @can('projects_edit')
                            <td class="px-3 py-4 whitespace-nowrap">
                                @if(!$timer->status || $timer->status === 'pending')
                                <input type="checkbox" class="timer-checkbox rounded border-slate-300" value="{{ $timer->id }}">
                                @endif
                            </td>
                            @endcan
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-xs font-bold">
                                        {{ substr($staf->full_name ?? 'U', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-slate-900">{{ $staf->full_name ?? 'Unknown' }}</div>
                                        <div class="text-sm text-slate-500">{{ $staf->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-slate-900">{{ $timer->task->name ?? 'Unknown Task' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">{{ $start->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">{{ $start->format('h:i A') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($end)
                                    <span class="text-sm text-slate-600">{{ $end->format('h:i A') }}</span>
                                @else
                                    <span class="text-sm text-amber-600 font-medium"><i class="bi bi-play-circle"></i> Running</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-slate-800">{{ sprintf('%02d:%02d', $hours, $minutes) }}</span>
                                @if($hours > 8)
                                    <span class="ml-1 px-1.5 py-0.5 text-[9px] font-bold rounded bg-red-100 text-red-700">OT</span>
                                @endif
                                @if($timer->rounding_minutes > 0)
                                    <span class="ml-1 px-1.5 py-0.5 text-[9px] font-bold rounded bg-purple-100 text-purple-700" title="Rounded to {{ $timer->rounding_minutes }} min intervals">R</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($timer->is_billable)
                                    <span class="text-sm font-semibold text-emerald-700">${{ $timer->hourly_rate ?? 0 }}/h</span>
                                @else
                                    <span class="text-sm text-slate-500">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($timer->is_billable)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-800">Billable</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-slate-100 text-slate-800">Non-billable</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($timerStatus === 'approved')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-800">Approved</span>
                                @elseif($timerStatus === 'rejected')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Rejected</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-800">Pending</span>
                                @endif
                            </td>
                            @can('projects_edit')
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    @if(!$timer->status || $timer->status === 'pending')
                                    <button onclick="approveTimer({{ $timer->id }})" class="px-2 py-1 text-[10px] font-semibold rounded bg-emerald-100 text-emerald-700 hover:bg-emerald-200 transition-colors" title="Approve">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                    <button onclick="rejectTimer({{ $timer->id }})" class="px-2 py-1 text-[10px] font-semibold rounded bg-red-100 text-red-700 hover:bg-red-200 transition-colors" title="Reject">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                    @endif
                                    <button onclick="showAuditLog({{ $timer->id }})" class="px-2 py-1 text-[10px] font-semibold rounded bg-blue-100 text-blue-700 hover:bg-blue-200 transition-colors" title="Audit Log">
                                        <i class="bi bi-clock-history"></i>
                                    </button>
                                </div>
                            </td>
                            @endcan
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()?->can('projects_edit') ? 11 : 10 }}" class="px-6 py-12 text-center text-slate-500">
                                <i class="bi bi-clock-history text-3xl opacity-50 mb-4"></i>
                                <p class="text-lg">No time logged yet</p>
                                <p class="text-sm mt-2">Time tracked on project tasks will appear here</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Audit Log Modal --}}
<div id="auditLogModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 max-h-[80vh] flex flex-col">
        <div class="flex items-center justify-between p-4 border-b border-slate-200">
            <h3 class="text-sm font-semibold text-slate-800">Timesheet Audit Log</h3>
            <button onclick="closeAuditLog()" class="p-1 text-slate-400 hover:text-slate-600 rounded">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div id="auditLogBody" class="p-4 overflow-y-auto space-y-3">
            <div class="text-center text-sm text-slate-400 py-8">Loading...</div>
        </div>
    </div>
</div>

{{-- Summary Modal --}}
<div id="summaryModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl mx-4 max-h-[80vh] flex flex-col">
        <div class="flex items-center justify-between p-4 border-b border-slate-200">
            <h3 class="text-sm font-semibold text-slate-800">Timesheet Summary</h3>
            <button onclick="closeSummaryModal()" class="p-1 text-slate-400 hover:text-slate-600 rounded">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div id="summaryBody" class="p-4 overflow-y-auto">
            <div class="text-center text-sm text-slate-400 py-8">Loading...</div>
        </div>
    </div>
</div>

<script>
function approveTimer(id) {
    if (!confirm('Approve this timesheet entry?')) return;
    fetch('/admin/timers/' + id + '/approve', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
    })
    .then(r => r.json())
    .then(d => { if (d.success) location.reload(); else alert(d.message); })
    .catch(() => alert('Failed to approve'));
}

function rejectTimer(id) {
    const reason = prompt('Enter rejection reason (optional):');
    if (reason === null) return;
    fetch('/admin/timers/' + id + '/reject', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        body: JSON.stringify({ reason: reason })
    })
    .then(r => r.json())
    .then(d => { if (d.success) location.reload(); else alert(d.message); })
    .catch(() => alert('Failed to reject'));
}

function showAuditLog(timerId) {
    document.getElementById('auditLogModal').classList.remove('hidden');
    document.getElementById('auditLogBody').innerHTML = '<div class="text-center text-sm text-slate-400 py-8">Loading...</div>';
    fetch('/admin/timers/' + timerId + '/audit-logs', {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        let html = '';
        if (data.logs && data.logs.length > 0) {
            data.logs.forEach(log => {
                const changes = log.changes ? JSON.stringify(log.changes) : '';
                html += '<div class="p-3 bg-slate-50 rounded-lg border border-slate-200">' +
                    '<div class="flex items-center justify-between mb-1">' +
                    '<span class="text-xs font-semibold ' + (log.action === 'approved' ? 'text-emerald-700' : log.action === 'rejected' ? 'text-red-700' : 'text-blue-700') + '">' +
                    ucfirst(log.action) + '</span>' +
                    '<span class="text-[10px] text-slate-400">' + (log.created_at ? new Date(log.created_at).toLocaleString() : '') + '</span>' +
                    '</div>' +
                    (changes ? '<p class="text-[10px] text-slate-500 mt-1">' + changes + '</p>' : '') +
                    '</div>';
            });
        } else {
            html = '<div class="text-center text-sm text-slate-400 py-8">No audit logs found</div>';
        }
        document.getElementById('auditLogBody').innerHTML = html;
    })
    .catch(() => {
        document.getElementById('auditLogBody').innerHTML = '<div class="text-center text-sm text-red-400 py-8">Failed to load audit logs</div>';
    });
}

function closeAuditLog() {
    document.getElementById('auditLogModal').classList.add('hidden');
}

function loadTimesheetSummary() {
    document.getElementById('summaryModal').classList.remove('hidden');
    document.getElementById('summaryBody').innerHTML = '<div class="text-center text-sm text-slate-400 py-8">Loading...</div>';
    fetch('{{ route("admin.projects.timesheets.summary", $project) }}', {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        let html = '';
        if (data.weekly) {
            html += '<h4 class="text-sm font-semibold text-slate-800 mb-3">Weekly Summary</h4>';
            html += '<div class="space-y-2 mb-6">';
            data.weekly.forEach(w => {
                html += '<div class="flex items-center justify-between p-2 bg-slate-50 rounded-lg border border-slate-200">' +
                    '<span class="text-xs text-slate-600">' + (w.week || '') + '</span>' +
                    '<span class="text-xs font-semibold text-slate-800">' + (w.hours || 0) + 'h</span>' +
                    '</div>';
            });
            html += '</div>';
        }
        if (data.daily) {
            html += '<h4 class="text-sm font-semibold text-slate-800 mb-3">Daily Summary</h4>';
            html += '<div class="space-y-2">';
            data.daily.forEach(d => {
                html += '<div class="flex items-center justify-between p-2 bg-slate-50 rounded-lg border border-slate-200">' +
                    '<span class="text-xs text-slate-600">' + (d.date || '') + '</span>' +
                    '<span class="text-xs font-semibold text-slate-800">' + (d.hours || 0) + 'h</span>' +
                    '</div>';
            });
            html += '</div>';
        }
        if (!data.weekly && !data.daily) {
            html = '<div class="text-center text-sm text-slate-400 py-8">No summary data available</div>';
        }
        document.getElementById('summaryBody').innerHTML = html;
    })
    .catch(() => {
        document.getElementById('summaryBody').innerHTML = '<div class="text-center text-sm text-red-400 py-8">Failed to load summary</div>';
    });
}

function closeSummaryModal() {
    document.getElementById('summaryModal').classList.add('hidden');
}

function ucfirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            document.querySelectorAll('.timer-checkbox').forEach(cb => cb.checked = this.checked);
            updateBulkApproveBtn();
        });
        document.querySelectorAll('.timer-checkbox').forEach(cb => {
            cb.addEventListener('change', updateBulkApproveBtn);
        });
    }
});

function updateBulkApproveBtn() {
    const checked = document.querySelectorAll('.timer-checkbox:checked');
    const btn = document.getElementById('bulkApproveBtn');
    const count = document.getElementById('selectedCount');
    if (btn) btn.disabled = checked.length === 0;
    if (count) count.textContent = checked.length + ' selected';
}

function bulkApprove() {
    const checked = document.querySelectorAll('.timer-checkbox:checked');
    if (checked.length === 0) return;
    if (!confirm('Approve ' + checked.length + ' selected timesheet entries?')) return;
    const ids = Array.from(checked).map(cb => parseInt(cb.value));
    fetch('/admin/timers/bulk-approve', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        body: JSON.stringify({ timer_ids: ids })
    })
    .then(r => r.json())
    .then(d => { if (d.success) location.reload(); else alert(d.message); })
    .catch(() => alert('Failed to bulk approve'));
}
</script>
@endsection
