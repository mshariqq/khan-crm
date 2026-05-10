@props(['comments' => [], 'taskId' => null, 'projectId' => null])

<div class="space-y-3" id="comments-container">
    @forelse($comments as $comment)
        <div class="flex gap-3 p-4 bg-slate-50 rounded-xl border border-slate-200">
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                {{ substr($comment->addedFrom->full_name ?? 'U', 0, 1) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-1">
                    <p class="text-sm font-semibold text-slate-800">
                        {{ $comment->addedFrom->full_name ?? 'Unknown User' }}
                    </p>
                    <span class="text-xs text-slate-500">
                        {{ $comment->created_at->diffForHumans() }}
                    </span>
                </div>
                <p class="text-slate-700 text-sm leading-relaxed whitespace-pre-wrap">{{ $comment->content }}</p>
            </div>
        </div>
    @empty
        <div class="text-center py-8 text-slate-500">
            <i class="bi bi-chat-dots text-3xl opacity-50 mb-2"></i>
            <p class="text-sm">No comments yet</p>
        </div>
    @endforelse
</div>

@if($taskId || $projectId)
    <form id="comment-form" class="mt-6">
        @csrf
        <div class="space-y-3">
            <label class="block text-sm font-medium text-slate-600">Add Comment</label>
            <textarea 
                id="comment-content" 
                rows="3" 
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-sm resize-none"
                placeholder="Write your comment..."
            ></textarea>
            <div class="flex items-center gap-2">
                <button 
                    type="submit" 
                    class="px-4 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all flex items-center gap-2"
                >
                    <i class="bi bi-send"></i> Post Comment
                </button>
                <button 
                    type="button" 
                    id="cancel-comment" 
                    class="px-4 py-2.5 bg-slate-100 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-200 transition-all hidden"
                >
                    Cancel
                </button>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        $(document).ready(function() {
            $('#comment-form').on('submit', function(e) {
                e.preventDefault();
                
                const content = $('#comment-content').val().trim();
                if (!content) {
                    alert('Please enter a comment');
                    return;
                }

                const formData = {
                    content: content,
                    task_id: {{ $taskId ?? 'null' }},
                    project_id: {{ $projectId ?? 'null' }},
                    _token: '{{ csrf_token() }}'
                };

                $.ajax({
                    url: '{{ route("admin.comments.store") }}',
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#comment-content').val('');
                            location.reload();
                        } else {
                            alert('Failed to add comment: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);
                        alert('Failed to add comment');
                    }
                });
            });
        });
    </script>
    @endpush
@endif