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
                        {{ $activity->dateadded ? \Carbon\Carbon::parse($activity->dateadded)->diffForHumans() : '' }}
                    </span>
                </div>
                
                <div class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                    {{-- In a real app, this would use __($activity->description_key) --}}
                    <span class="font-semibold text-blue-600 dark:text-blue-400">{{ str_replace('_', ' ', strtolower($activity->description_key)) }}</span>
                    
                    @if($activity->additional_data)
                        @php
                            $data = json_decode($activity->additional_data, true);
                        @endphp
                        @if(isset($data['task_name']))
                            <span class="text-slate-900 dark:text-slate-200 font-medium"> - {{ $data['task_name'] }}</span>
                        @elseif(isset($data['discussion_subject']))
                            <span class="text-slate-900 dark:text-slate-200 font-medium"> - {{ $data['discussion_subject'] }}</span>
                        @elseif(isset($data['file_name']))
                            <span class="text-slate-900 dark:text-slate-200 font-medium"> - {{ $data['file_name'] }}</span>
                        @elseif(isset($data['note_title']))
                            <span class="text-slate-900 dark:text-slate-200 font-medium"> - {{ $data['note_title'] }}</span>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@else
<div class="flex flex-col items-center justify-center py-16 text-center">
    <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4">
        <i class="bi bi-activity text-slate-400 dark:text-slate-500 text-2xl"></i>
    </div>
    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">No activity recorded for this project yet.</p>
</div>
@endif
