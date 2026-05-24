@extends('layouts.client')

@section('title', 'Activity - ' . $project->name)
@section('breadcrumb', 'Project Activity')

@section('main')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('client.projects.show', $project) }}" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-200 rounded-xl text-slate-500 hover:text-blue-600 transition-colors">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Project Activity</h1>
                <p class="text-sm text-slate-500">{{ $project->name }}</p>
            </div>
        </div>
    </div>

    @if($activities->count() > 0)
    <div class="relative max-w-2xl mx-auto">
        <div class="absolute left-4 top-0 bottom-0 w-px bg-slate-200"></div>

        <div class="space-y-6">
            @foreach($activities as $activity)
            <div class="relative flex items-start gap-4 pl-8">
                <div class="absolute left-0 w-8 h-8 rounded-full bg-white border-2 border-slate-200 flex items-center justify-center text-white text-[10px] font-bold z-10 flex-shrink-0 overflow-hidden">
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

                <div class="flex-1 bg-white border border-slate-200 rounded-lg p-3 shadow-sm transition-all hover:border-blue-300">
                    <div class="flex items-center justify-between gap-2 mb-1">
                        <span class="text-xs font-bold text-slate-900">
                            {{ $activity->staff->firstname ?? 'System' }} {{ $activity->staff->lastname ?? '' }}
                        </span>
                        <span class="text-[10px] text-slate-400">
                            {{ $activity->dateadded ? \Carbon\Carbon::parse($activity->dateadded)->diffForHumans() : '' }}
                        </span>
                    </div>

                    <div class="text-xs text-slate-600 leading-relaxed">
                        @php
                            $labels = \App\Models\Project::activityDescriptionLabels();
                            $key = $activity->description_key;
                            $label = $labels[$key] ?? str_replace('_', ' ', strtolower($key));
                            $data = $activity->additional_data ? json_decode($activity->additional_data, true) : [];
                            foreach ($data as $k => $v) {
                                $label = str_replace(':' . $k, '<strong>' . e($v) . '</strong>', $label);
                            }
                        @endphp
                        <span class="font-semibold text-blue-600">{!! $label !!}</span>
                        @if(!empty($data['task_name']))
                            <span class="text-slate-900 font-medium"> — {{ $data['task_name'] }}</span>
                        @elseif(!empty($data['discussion_subject']))
                            <span class="text-slate-900 font-medium"> — {{ $data['discussion_subject'] }}</span>
                        @elseif(!empty($data['file_name']))
                            <span class="text-slate-900 font-medium"> — {{ $data['file_name'] }}</span>
                        @elseif(!empty($data['note_title']))
                            <span class="text-slate-900 font-medium"> — {{ $data['note_title'] }}</span>
                        @elseif(!empty($data['milestone_name']))
                            <span class="text-slate-900 font-medium"> — {{ $data['milestone_name'] }}</span>
                        @elseif(!empty($data['member_name']))
                            <span class="text-slate-900 font-medium"> — {{ $data['member_name'] }}</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    @if(method_exists($activities, 'hasPages') && $activities->hasPages())
    <div class="mt-8">
        {{ $activities->links() }}
    </div>
    @endif
    @else
    <div class="flex flex-col items-center justify-center py-20 bg-white rounded-2xl border border-dashed border-slate-300">
        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
            <i class="bi bi-activity text-3xl text-slate-300"></i>
        </div>
        <h3 class="text-lg font-bold text-slate-800">No activity yet</h3>
        <p class="text-slate-500">No activity has been recorded for this project yet.</p>
    </div>
    @endif
</div>
@endsection
