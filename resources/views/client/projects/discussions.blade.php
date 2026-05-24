@extends('layouts.client')

@section('title', 'Discussions - ' . $project->name)
@section('breadcrumb', 'Project Discussions')

@section('main')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('client.projects.show', $project) }}" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-200 rounded-xl text-slate-500 hover:text-blue-600 transition-colors">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Discussions</h1>
                <p class="text-sm text-slate-500">{{ $project->name }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr class="text-[10px] uppercase tracking-wider font-bold text-slate-400">
                        <th class="px-6 py-4">Subject</th>
                        <th class="px-6 py-4">Started By</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Comments</th>
                        <th class="px-6 py-4">Last Activity</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($discussions as $discussion)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                @if($discussion->is_pinned)
                                <i class="bi bi-pin-angle-fill text-amber-500 text-xs"></i>
                                @endif
                                <a href="{{ route('client.projects.discussions.show', [$project, $discussion]) }}" class="text-sm font-bold text-slate-800 hover:text-blue-600 transition-colors">
                                    {{ $discussion->subject }}
                                </a>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 text-[10px] font-bold">
                                    {{ substr($discussion->staff->full_name ?? $discussion->created_by_name ?? 'U', 0, 1) }}
                                </span>
                                <span class="text-xs text-slate-600">{{ $discussion->staff->full_name ?? $discussion->created_by_name ?? 'Unknown' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-500">
                            {{ $discussion->datecreated ? \Carbon\Carbon::parse($discussion->datecreated)->format('M d, Y') : '' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-bold text-slate-600 flex items-center gap-1">
                                <i class="bi bi-chat"></i> {{ $discussion->comments->count() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-500">
                            {{ $discussion->last_activity ? \Carbon\Carbon::parse($discussion->last_activity)->diffForHumans() : '-' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('client.projects.discussions.show', [$project, $discussion]) }}" class="inline-flex items-center gap-2 text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="bi bi-chat-dots text-3xl text-slate-300"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800">No discussions yet</h3>
                            <p class="text-slate-500">No discussions have been shared with you for this project.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
