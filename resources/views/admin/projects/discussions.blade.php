@extends('layouts.admin')

@section('breadcrumb', 'Project Discussions')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ $project->name }} - Discussions</h1>
            <p class="text-slate-500 text-sm mt-1">Project discussions and team collaboration</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.projects.show', $project) }}" class="px-4 py-2.5 bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-all flex items-center gap-2">
                <i class="bi bi-arrow-left"></i> Back to Project
            </a>
            <a href="{{ route('admin.projects.discussions.create', $project) }}" class="px-4 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 flex items-center gap-2">
                <i class="bi bi-plus-lg"></i> Start Discussion
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4">
        @forelse($project->discussions as $discussion)
            <div class="bg-white rounded-xl border border-slate-200 p-6 hover:border-slate-300 transition-all shadow-sm">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-slate-800 mb-2">{{ $discussion->title }}</h3>
                        <div class="flex items-center gap-4 text-sm text-slate-600 mb-3">
                            <span><i class="bi bi-person-circle"></i> {{ $discussion->staff->full_name ?? 'Unknown' }}</span>
                            <span><i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($discussion->created_at)->format('M d, Y h:i A') }}</span>
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ 
                                $discussion->status == 1 ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' 
                            }}">
                                {{ $discussion->status == 1 ? 'Open' : 'Closed' }}
                            </span>
                        </div>
                    </div>
                </div>
                
                @if($discussion->description)
                <div class="bg-slate-50 rounded-xl p-4 mb-4">
                    <h4 class="text-sm font-semibold text-slate-800 mb-2">Description</h4>
                    <p class="text-slate-700 text-sm leading-relaxed">{{ $discussion->description }}</p>
                </div>
                @endif

                @if($discussion->comments->count() > 0)
                <div class="bg-slate-50 rounded-xl p-4">
                    <h4 class="text-sm font-semibold text-slate-800 mb-3">Comments ({{ $discussion->comments->count() }})</h4>
                    <div class="space-y-3">
                        @foreach($discussion->comments as $comment)
                        <div class="flex items-start gap-3 p-3 bg-white rounded-lg border border-slate-200">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                {{ substr($comment->staff->full_name ?? 'U', 0, 1) }}
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-medium text-slate-800">{{ $comment->staff->full_name }}</span>
                                    <span class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($comment->created_at)->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-slate-700">{{ $comment->content }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="flex items-center justify-between pt-4 mt-4 border-t border-slate-200">
                    <div class="flex items-center gap-4 text-sm text-slate-600">
                        <span><i class="bi bi-chat-dots"></i> {{ $discussion->comments->count() }} Comments</span>
                        <span><i class="bi bi-eye"></i> {{ $discussion->views ?? 0 }} Views</span>
                    </div>
                    <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-700">View Discussion →</a>
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-chat-dots text-slate-300 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-slate-800 mb-2">No discussions yet</h3>
                <p class="text-slate-500 mb-6">Start a discussion to collaborate with your team.</p>
                <a href="{{ route('admin.projects.discussions.create', $project) }}" class="px-5 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all inline-flex items-center gap-2">
                    <i class="bi bi-plus-lg"></i> Start Discussion
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection