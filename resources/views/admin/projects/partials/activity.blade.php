@if($activities->count() > 0)
<div class="relative max-w-2xl mx-auto">
    {{-- Vertical Line --}}
    <div class="absolute left-4 top-0 bottom-0 w-px bg-slate-200 dark:bg-slate-700"></div>

    <div class="space-y-6">
        @foreach($activities as $activity)
        <div class="relative flex items-start gap-4 pl-8">
            {{-- Avatar/Dot --}}
            <div class="absolute left-0 w-8 h-8 rounded-full bg-white dark:bg-slate-900 border-2 border-slate-200 dark:border-slate-700 flex items-center justify-center text-white text-[10px] font-bold z-10 flex-shrink-0 overflow-hidden">
                @if($activity->staff)
                    <div class="w-full h-full bg-slate-500 flex items-center justify-center text-white uppercase">
                        {{ substr($activity->staff->firstname, 0, 1) }}
                    </div>
                @else
                    <div class="w-full h-full bg-slate-400 flex items-center justify-center text-white uppercase">
                        S
                    </div>
                @endif
            </div>

            {{-- Content Card --}}
            <div class="flex-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg p-3 shadow-sm transition-all hover:border-blue-300 dark:hover:border-blue-500/50">
                <div class="flex items-center justify-between gap-2 mb-1">
                    <span class="text-xs font-bold text-slate-900 dark:text-white">
                        {{ $activity->staff->firstname ?? 'System' }} {{ $activity->staff->lastname ?? '' }}
                    </span>
                    <span class="text-[10px] text-slate-400 dark:text-slate-500">
                        {{ $activity->dateadded?->diffForHumans() }}
                    </span>
                </div>
                
                <div class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                    @php
                        $labels = \App\Models\Project::activityDescriptionLabels();
                        $key = $activity->description_key;
                        $label = $labels[$key] ?? str_replace('_', ' ', strtolower($key));
                        $data = $activity->additional_data ? json_decode($activity->additional_data, true) : [];
                        foreach ($data as $k => $v) {
                            $label = str_replace(':' . $k, '<strong>' . e($v) . '</strong>', $label);
                        }
                    @endphp
                    <span class="font-semibold text-blue-600 dark:text-blue-400">{!! $label !!}</span>
                    @if(!empty($data['task_name']))
                        <span class="text-slate-900 dark:text-slate-200 font-medium"> — {{ $data['task_name'] }}</span>
                    @elseif(!empty($data['discussion_subject']))
                        <span class="text-slate-900 dark:text-slate-200 font-medium"> — {{ $data['discussion_subject'] }}</span>
                    @elseif(!empty($data['file_name']))
                        <span class="text-slate-900 dark:text-slate-200 font-medium"> — {{ $data['file_name'] }}</span>
                    @elseif(!empty($data['note_title']))
                        <span class="text-slate-900 dark:text-slate-200 font-medium"> — {{ $data['note_title'] }}</span>
                    @elseif(!empty($data['milestone_name']))
                        <span class="text-slate-900 dark:text-slate-200 font-medium"> — {{ $data['milestone_name'] }}</span>
                    @elseif(!empty($data['member_name']))
                        <span class="text-slate-900 dark:text-slate-200 font-medium"> — {{ $data['member_name'] }}</span>
                    @elseif(!empty($data['project_name']))
                        <span class="text-slate-900 dark:text-slate-200 font-medium"> — {{ $data['project_name'] }}</span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@else
<div class="flex flex-col items-center justify-center py-16 text-center">
    <div class="w-20 h-20 mx-auto mb-4 bg-slate-50 rounded-full flex items-center justify-center">
        <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <p class="text-sm font-medium text-slate-500 mb-1">No activity recorded yet</p>
    <p class="text-xs text-slate-400">Activity will appear here as the team works on the project</p>
</div>
@endif
