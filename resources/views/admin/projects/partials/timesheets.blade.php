@php
$totalMinutes = $allTimers->where('end_time', '!=', null)->sum(function($t) {
    return \Carbon\Carbon::parse($t->start_time)->diffInMinutes(\Carbon\Carbon::parse($t->end_time));
});
$billableMinutes = $allTimers->where('end_time', '!=', null)->where('is_billable', 1)->sum(function($t) {
    return \Carbon\Carbon::parse($t->start_time)->diffInMinutes(\Carbon\Carbon::parse($t->end_time));
});
$staffCount = $allTimers->pluck('staff_id')->unique()->count();
$trackedTaskCount = $project->tasks->filter(fn($t) => $t->timers->count() > 0)->count();
@endphp

<div class="space-y-6">
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
                    <p class="text-2xl font-bold text-slate-800">{{ $trackedTaskCount }}</p>
                </div>
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="bi bi-list-ul text-amber-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Detailed Table --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-[10px] font-bold text-slate-500 uppercase tracking-wider">Staff Member</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold text-slate-500 uppercase tracking-wider">Task</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold text-slate-500 uppercase tracking-wider">Date</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold text-slate-500 uppercase tracking-wider">Start</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold text-slate-500 uppercase tracking-wider">End</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold text-slate-500 uppercase tracking-wider">Duration</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold text-slate-500 uppercase tracking-wider">Rate</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold text-slate-500 uppercase tracking-wider">Billable</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($allTimers as $timer)
                    @php
                        $start = \Carbon\Carbon::parse($timer->start_time);
                        $end = $timer->end_time ? \Carbon\Carbon::parse($timer->end_time) : null;
                        $duration = $end ? $start->diffInMinutes($end) : now()->diffInMinutes($start);
                        $hours = floor($duration / 60);
                        $minutes = $duration % 60;
                        $staf = $timer->staff;
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <div class="flex items-center gap-2.5">
                                <span class="w-7 h-7 rounded-full bg-slate-200 border border-slate-300 flex items-center justify-center text-slate-600 text-[10px] font-bold flex-shrink-0">
                                    {{ strtoupper(substr($staf->firstname ?? 'U', 0, 1)) }}{{ strtoupper(substr($staf->lastname ?? '', 0, 1)) }}
                                </span>
                                <span class="text-sm font-medium text-slate-900">{{ $staf->firstname ?? 'Unknown' }} {{ $staf->lastname ?? '' }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="text-sm text-slate-700">{{ $timer->task_name ?? 'Unknown Task' }}</div>
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap text-sm text-slate-600">{{ $start->format('M d, Y') }}</td>
                        <td class="px-5 py-3.5 whitespace-nowrap text-sm text-slate-600">{{ $start->format('h:i A') }}</td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            @if($end)
                            <span class="text-sm text-slate-600">{{ $end->format('h:i A') }}</span>
                            @else
                            <span class="text-xs font-semibold text-amber-600 flex items-center gap-1"><span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span> Running</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <span class="text-sm font-semibold text-slate-800">{{ sprintf('%02d:%02d', $hours, $minutes) }}</span>
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            @if($timer->is_billable)
                            <span class="text-sm font-semibold text-emerald-700">${{ number_format($timer->hourly_rate ?? 0, 2) }}/h</span>
                            @else
                            <span class="text-sm text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            @if($timer->is_billable)
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-800">Billable</span>
                            @else
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-100 text-slate-500">Non-billable</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center text-slate-500">
                            <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
                                <i class="bi bi-clock-history text-slate-300 text-xl"></i>
                            </div>
                            <p class="text-sm font-medium">No time logged yet</p>
                            <p class="text-xs text-slate-400 mt-1">Time tracked on project tasks will appear here</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
