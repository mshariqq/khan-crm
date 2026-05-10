<div id="discussions-app">
    <style>
    .discussion-list-item.active { background: #eff6ff; border-color: #93c5fd; }
    .discussion-list-item:not(.active):hover { background: #f1f5f9; }
    .chat-bubble { border-radius: 1rem 1rem 1rem 0.25rem; }
    .chat-messages { scrollbar-width: thin; scrollbar-color: #e2e8f0 transparent; }
    .chat-messages::-webkit-scrollbar { width: 4px; }
    .chat-messages::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }
    #chat-empty-state { display: none; }
    @media (min-width: 1024px) { #chat-empty-state.dshown { display: flex; } }
    </style>

    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-semibold text-slate-800">Discussions</h3>
        <button onclick="openNewDiscussionModal()" class="px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition-all flex items-center gap-1.5">
            <i class="bi bi-plus-lg"></i> New Discussion
        </button>
    </div>

    <div class="flex flex-col lg:flex-row gap-4">
        {{-- Discussion List --}}
        <div id="discussion-list" class="lg:w-2/5 flex-shrink-0">
            @forelse($discussions as $discussion)
            <div class="discussion-list-item p-3 rounded-xl cursor-pointer transition-all border mb-1.5 bg-white border-slate-200 hover:border-slate-300"
                 data-id="{{ $discussion->id }}"
                 onclick="selectDiscussion({{ $discussion->id }})">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full bg-slate-300 flex items-center justify-center text-white text-[10px] font-bold flex-shrink-0">
                        {{ substr($discussion->staff->full_name ?? 'U', 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-800 truncate">{{ $discussion->subject }}</span>
                            <span class="text-[10px] text-slate-400 flex-shrink-0 ml-2">{{ $discussion->last_activity ? \Carbon\Carbon::parse($discussion->last_activity)->diffForHumans() : '' }}</span>
                        </div>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-[10px] text-slate-500 truncate">{{ $discussion->staff->full_name ?? 'Unknown' }}</span>
                            <span class="text-[10px] text-slate-400 flex items-center gap-1">
                                <i class="bi bi-chat"></i> {{ $discussion->comments->count() }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
                    <i class="bi bi-chat-dots text-slate-400 text-xl"></i>
                </div>
                <p class="text-sm text-slate-500">No discussions yet</p>
            </div>
            @endforelse
        </div>

        {{-- Chat Area --}}
        <div id="discussion-chat-area" class="flex-1 min-w-0">
            @foreach($discussions as $discussion)
            <div id="chat-panel-{{ $discussion->id }}" class="chat-panel flex-col h-[500px] hidden border border-slate-200 rounded-xl p-4">
                {{-- Chat Header --}}
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 mb-3">
                    <div class="flex items-center gap-2">
                        <button onclick="selectDiscussion(null)" class="lg:hidden p-1 text-slate-400 hover:text-slate-600">
                            <i class="bi bi-arrow-left"></i>
                        </button>
                        <div>
                            <h4 class="text-sm font-semibold text-slate-800">{{ $discussion->subject }}</h4>
                            <p class="text-[10px] text-slate-400">Started by {{ $discussion->staff->full_name ?? 'Unknown' }} &middot; {{ $discussion->datecreated ? \Carbon\Carbon::parse($discussion->datecreated)->format('M j, Y') : '' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Messages --}}
                <div id="chat-messages-{{ $discussion->id }}" class="chat-messages flex-1 overflow-y-auto space-y-3 pr-1 mb-3">
                    @if($discussion->description)
                    <div class="flex items-start gap-2.5">
                        <div class="w-7 h-7 rounded-full bg-slate-300 flex items-center justify-center text-white text-[10px] font-bold flex-shrink-0 mt-0.5">
                            {{ substr($discussion->staff->full_name ?? 'U', 0, 1) }}
                        </div>
                        <div class="bg-blue-50 rounded-2xl rounded-tl-none px-3.5 py-2.5 max-w-[80%]">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-[11px] font-semibold text-blue-700">{{ $discussion->staff->full_name ?? 'Unknown' }}</span>
                                <span class="text-[10px] text-blue-400">{{ $discussion->datecreated ? \Carbon\Carbon::parse($discussion->datecreated)->diffForHumans() : '' }}</span>
                            </div>
                            <p class="text-xs text-slate-700 leading-relaxed">{{ $discussion->description }}</p>
                        </div>
                    </div>
                    @endif

                    @foreach($discussion->comments as $comment)
                    <div class="flex items-start gap-2.5">
                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-slate-400 to-slate-500 flex items-center justify-center text-white text-[10px] font-bold flex-shrink-0 mt-0.5">
                            {{ substr($comment->staff->full_name ?? 'U', 0, 1) }}
                        </div>
                        <div class="bg-white border border-slate-200 rounded-2xl rounded-tl-none px-3.5 py-2.5 max-w-[80%] shadow-sm">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-[11px] font-semibold text-slate-700">{{ $comment->staff->full_name ?? 'Unknown' }}</span>
                                <span class="text-[10px] text-slate-400">{{ $comment->created ? \Carbon\Carbon::parse($comment->created)->diffForHumans() : ($comment->created_at ? \Carbon\Carbon::parse($comment->created_at)->diffForHumans() : '') }}</span>
                            </div>
                            <p class="text-xs text-slate-700 leading-relaxed">{{ $comment->content }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Input --}}
                <form onsubmit="return sendComment({{ $discussion->id }})" class="flex items-center gap-2 pt-3 border-t border-slate-200">
                    <input type="text" id="comment-input-{{ $discussion->id }}" class="flex-1 px-4 py-2 text-sm border border-slate-200 rounded-full focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Type a message..." required>
                    <button type="submit" class="w-9 h-9 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700 transition-all flex-shrink-0">
                        <i class="bi bi-send text-xs"></i>
                    </button>
                </form>
            </div>
            @endforeach

            {{-- No discussion selected --}}
            <div id="chat-empty-state" class="flex-col items-center justify-center h-[500px] text-center">
                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                    <i class="bi bi-chat-dots text-slate-300 text-2xl"></i>
                </div>
                <h4 class="text-sm font-semibold text-slate-700 mb-1">Select a discussion</h4>
                <p class="text-xs text-slate-400">Choose a discussion from the list to view its conversation</p>
            </div>
        </div>
    </div>

    {{-- New Discussion Modal --}}
    <div id="newDiscussionModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4">
            <div class="flex items-center justify-between p-4 border-b border-slate-200">
                <h3 class="text-sm font-semibold text-slate-800">Start New Discussion</h3>
                <button onclick="closeNewDiscussionModal()" class="p-1 text-slate-400 hover:text-slate-600 rounded">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <form id="newDiscussionForm" class="p-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Subject</label>
                    <input type="text" name="subject" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Discussion subject..." required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Description</label>
                    <textarea name="description" rows="4" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="What's this about?"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeNewDiscussionModal()" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Start Discussion</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
window.activeDiscussionId = window.activeDiscussionId || null;

function selectDiscussion(id) {
    window.activeDiscussionId = id;
    activeDiscussionId = id;

    document.querySelectorAll('.discussion-list-item').forEach(el => {
        el.classList.toggle('active', parseInt(el.dataset.id) === id);
    });

    const list = document.getElementById('discussion-list');
    document.querySelectorAll('.chat-panel').forEach(el => el.classList.add('hidden'));

    if (id) {
        if (window.innerWidth < 1024) list.classList.add('hidden');
        else list.classList.remove('hidden');

        const panel = document.getElementById('chat-panel-' + id);
        if (panel) {
            panel.classList.remove('hidden');
            panel.classList.add('flex');
        }
    } else {
        list.classList.remove('hidden');
    }

    const emptyState = document.getElementById('chat-empty-state');
    if (emptyState) {
        emptyState.classList.toggle('dshown', !id);
    }

    if (id) {
        setTimeout(() => {
            const msgs = document.getElementById('chat-messages-' + id);
            if (msgs) msgs.scrollTop = msgs.scrollHeight;
        }, 50);
    }
}

function openNewDiscussionModal() {
    document.getElementById('newDiscussionModal').classList.remove('hidden');
}

function closeNewDiscussionModal() {
    document.getElementById('newDiscussionModal').classList.add('hidden');
}

document.getElementById('newDiscussionForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('/admin/projects/{{ $project->id }}/discussions', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData,
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closeNewDiscussionModal();
            loadTab('discussions');
        } else {
            alert(data.message || 'Failed to create discussion');
        }
    })
    .catch(() => alert('Failed to create discussion'));
});

function sendComment(discussionId) {
    const input = document.getElementById('comment-input-' + discussionId);
    if (!input || !input.value.trim()) return false;

    fetch('/admin/projects/{{ $project->id }}/discussions/' + discussionId + '/comment', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ content: input.value }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            loadTab('discussions');
        } else {
            alert(data.message || 'Failed to send message');
        }
    })
    .catch(() => alert('Failed to send message'));

    return false;
}

// Auto-restore active discussion and scroll to bottom
(function() {
    if (window.activeDiscussionId) {
        selectDiscussion(window.activeDiscussionId);
    }
    @foreach($discussions as $discussion)
    (function(id) {
        const el = document.getElementById('chat-messages-' + id);
        if (el) el.scrollTop = el.scrollHeight;
    })({{ $discussion->id }});
    @endforeach
})();
</script>
