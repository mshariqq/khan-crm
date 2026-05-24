@extends('layouts.client')

@section('title', $discussion->subject . ' - ' . $project->name)
@section('breadcrumb', 'Discussion')

@section('main')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('client.projects.show', $project) }}" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-200 rounded-xl text-slate-500 hover:text-blue-600 transition-colors">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ $discussion->subject }}</h1>
            <p class="text-sm text-slate-500">{{ $project->name }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs">
                {{ $discussion->staff ? substr($discussion->staff->firstname, 0, 1) . substr($discussion->staff->lastname, 0, 1) : 'UN' }}
            </div>
            <div>
                <p class="text-sm font-bold text-slate-800">{{ $discussion->staff->full_name ?? 'Unknown' }}</p>
                <p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($discussion->datecreated)->format('M d, Y h:i A') }}</p>
            </div>
        </div>
        <div class="prose prose-slate max-w-none text-slate-600 text-sm">
            {!! nl2br(e($discussion->description)) !!}
        </div>
    </div>

    <div class="space-y-4">
        <h3 class="text-lg font-bold text-slate-800">Comments ({{ $discussion->comments->count() }})</h3>

        @forelse($discussion->comments as $comment)
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 rounded-full {{ $comment->staff_id ? 'bg-purple-100 text-purple-600' : 'bg-blue-100 text-blue-600' }} flex items-center justify-center font-bold text-xs">
                    @if($comment->staff_id)
                        {{ substr($comment->staff->firstname ?? 'S', 0, 1) }}{{ substr($comment->staff->lastname ?? 'T', 0, 1) }}
                    @else
                        {{ substr($comment->contact->firstname ?? 'C', 0, 1) }}{{ substr($comment->contact->lastname ?? 'L', 0, 1) }}
                    @endif
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-800">{{ $comment->staff ? $comment->staff->full_name : ($comment->fullname ?? 'Client') }}</p>
                    <p class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($comment->created ?? $comment->created_at)->format('M d, Y h:i A') }}</p>
                </div>
            </div>
            <div class="text-sm text-slate-600">
                {{ $comment->content }}
            </div>
        </div>
        @empty
        <div class="text-center py-8 text-slate-400">
            <i class="bi bi-chat-dots text-2xl opacity-50 mb-2"></i>
            <p class="text-sm">No comments yet</p>
        </div>
        @endforelse

        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <h4 class="text-sm font-bold text-slate-800 mb-3">Add a Comment</h4>
            <form id="commentForm">
                @csrf
                <textarea name="content" rows="3" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500" placeholder="Write your comment..." required></textarea>
                <div class="flex justify-end mt-3">
                    <button type="submit" class="px-5 py-2 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all">
                        Post Comment
                    </button>
                </div>
            </form>
            <div id="commentSuccess" class="hidden mt-3 p-3 bg-emerald-50 rounded-lg text-emerald-700 text-sm">
                <i class="bi bi-check-circle"></i> Comment posted successfully!
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('commentForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const projectId = {{ $project->id }};
    const discussionId = {{ $discussion->id }};
    
    fetch('/client/projects/' + projectId + '/discussions/' + discussionId + '/comment', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('commentForm').classList.add('hidden');
            document.getElementById('commentSuccess').classList.remove('hidden');
            setTimeout(() => location.reload(), 1500);
        }
    })
    .catch(() => alert('Failed to post comment'));
});
</script>
@endsection
