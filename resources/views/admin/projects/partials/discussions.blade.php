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
    .nested-comment { margin-left: 2.5rem; border-left: 2px solid #e2e8f0; padding-left: 1rem; }
    </style>

    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <h3 class="text-sm font-semibold text-slate-800">Discussions</h3>
            <div class="relative">
                <input type="text" id="discussionSearchInput" placeholder="Search discussions..." class="pl-7 pr-2 py-1 bg-slate-50 border border-slate-200 rounded text-xs focus:ring-1 focus:ring-blue-500 outline-none w-40">
                <i class="bi bi-search absolute left-2 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
            </div>
            <select id="discussionCategoryFilter" onchange="filterByCategory()" class="px-2 py-1 bg-slate-50 border border-slate-200 rounded text-xs focus:ring-1 focus:ring-blue-500 outline-none">
                <option value="">All Categories</option>
                @foreach(\App\Models\ProjectDiscussionCategory::all() as $cat)
                <option value="{{ $cat->id }}" style="color: {{ $cat->color }}">{{ $cat->name }}</option>
                @endforeach
            </select>
            @if(auth()->user()?->can('projects_edit'))
            <button onclick="openCategoryModal()" class="px-2 py-1 bg-slate-50 border border-slate-200 rounded text-xs text-slate-500 hover:text-slate-700 hover:bg-slate-100" title="Manage Categories">
                <i class="bi bi-gear"></i>
            </button>
            @endif
        </div>
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
                 data-pinned="{{ $discussion->is_pinned ? '1' : '0' }}"
                 data-category="{{ $discussion->category_id ?? '' }}"
                 onclick="selectDiscussion({{ $discussion->id }})">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full bg-slate-300 flex items-center justify-center text-white text-[10px] font-bold flex-shrink-0">
                        {{ substr($discussion->staff->full_name ?? 'U', 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-800 truncate flex items-center gap-1">
                                @if($discussion->is_pinned)<i class="bi bi-pin-angle-fill text-amber-500 text-[10px]"></i>@endif
                                {{ $discussion->subject }}
                            </span>
                            <span class="text-[10px] text-slate-400 flex-shrink-0 ml-2">{{ $discussion->last_activity?->diffForHumans() ?? '' }}</span>
                        </div>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-[10px] text-slate-500 truncate">{{ $discussion->staff->full_name ?? 'Unknown' }}</span>
                            @if($discussion->category)
                            <span class="text-[10px] px-1 rounded" style="background: {{ $discussion->category->color }}20; color: {{ $discussion->category->color }}">
                                {{ $discussion->category->name }}
                            </span>
                            @endif
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
            <div id="chat-panel-{{ $discussion->id }}" class="chat-panel flex-col h-[500px] hidden border border-slate-200 rounded-xl p-4" data-subject="{{ $discussion->subject }}" data-description="{{ $discussion->description }}">
                {{-- Chat Header --}}
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 mb-3">
                    <div class="flex items-center gap-2">
                        <button onclick="selectDiscussion(null)" class="lg:hidden p-1 text-slate-400 hover:text-slate-600">
                            <i class="bi bi-arrow-left"></i>
                        </button>
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-sm font-semibold text-slate-800">{{ $discussion->subject }}</h4>
                                @if($discussion->is_pinned)<i class="bi bi-pin-angle-fill text-amber-500 text-xs"></i>@endif
                            </div>
                            <p class="text-[10px] text-slate-400">
                                Started by {{ $discussion->staff->full_name ?? 'Unknown' }}
                                &middot; {{ $discussion->datecreated?->format('M j, Y') ?? '' }}
                                @if($discussion->category) &middot; <span style="color: {{ $discussion->category->color }}">{{ $discussion->category->name }}</span> @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1">
                        <button onclick="togglePinDiscussion({{ $discussion->id }})" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-amber-600" title="{{ $discussion->is_pinned ? 'Unpin' : 'Pin' }}">
                            <i class="bi {{ $discussion->is_pinned ? 'bi-pin-angle-fill text-amber-500' : 'bi-pin-angle' }}"></i>
                        </button>
                        <button onclick="editDiscussion({{ $discussion->id }})" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-blue-600" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button onclick="deleteDiscussion({{ $discussion->id }})" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-red-600" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>

                {{-- Description --}}
                @if($discussion->description)
                <div class="flex items-start gap-2.5 mb-3 pb-3 border-b border-slate-100">
                    <div class="w-7 h-7 rounded-full bg-slate-300 flex items-center justify-center text-white text-[10px] font-bold flex-shrink-0 mt-0.5">
                        {{ substr($discussion->staff->full_name ?? 'U', 0, 1) }}
                    </div>
                    <div class="bg-blue-50 rounded-2xl rounded-tl-none px-3.5 py-2.5 max-w-[80%]">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[11px] font-semibold text-blue-700">{{ $discussion->staff->full_name ?? 'Unknown' }}</span>
                            <span class="text-[10px] text-blue-400">{{ $discussion->datecreated?->diffForHumans() ?? '' }}</span>
                        </div>
                        <p class="text-xs text-slate-700 leading-relaxed">{!! nl2br(e($discussion->description)) !!}</p>
                    </div>
                </div>
                @endif

                {{-- Messages --}}
                <div id="chat-messages-{{ $discussion->id }}" class="chat-messages flex-1 overflow-y-auto space-y-3 pr-1 mb-3">
                    @foreach($discussion->comments->where('parent', null) as $comment)
                    @include('admin.projects.partials.discussion-comment', ['comment' => $comment, 'discussion' => $discussion, 'depth' => 0])
                    @endforeach
                </div>

                {{-- Input with file upload --}}
                <form id="comment-form-{{ $discussion->id }}" onsubmit="return sendComment({{ $discussion->id }})" class="pt-3 border-t border-slate-200">
                    <input type="hidden" name="parent_id" id="parent-input-{{ $discussion->id }}" value="">
                    <div class="flex items-center gap-2">
                        <div class="flex-1 relative">
                            <input type="text" id="comment-input-{{ $discussion->id }}" class="w-full px-4 py-2 text-sm border border-slate-200 rounded-full focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10" placeholder="Type a message... @mention someone" required>
                            <label class="absolute right-2 top-1/2 -translate-y-1/2 cursor-pointer text-slate-400 hover:text-blue-600">
                                <i class="bi bi-paperclip"></i>
                                <input type="file" id="file-input-{{ $discussion->id }}" class="hidden" accept="image/*,.pdf,.doc,.docx,.txt">
                            </label>
                        </div>
                        <button type="submit" class="w-9 h-9 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700 transition-all flex-shrink-0">
                            <i class="bi bi-send text-xs"></i>
                        </button>
                    </div>
                    <div id="file-preview-{{ $discussion->id }}" class="hidden mt-2 flex items-center gap-2 text-xs text-slate-500 bg-slate-50 rounded-lg px-3 py-1.5">
                        <i class="bi bi-file-earmark"></i>
                        <span id="file-name-{{ $discussion->id }}"></span>
                        <button type="button" onclick="clearFile({{ $discussion->id }})" class="text-red-400 hover:text-red-600"><i class="bi bi-x"></i></button>
                    </div>
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

    {{-- New/Edit Discussion Modal --}}
    <div id="discussionModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4">
            <div class="flex items-center justify-between p-4 border-b border-slate-200">
                <h3 id="discussionModalTitle" class="text-sm font-semibold text-slate-800">Start New Discussion</h3>
                <button onclick="closeDiscussionModal()" class="p-1 text-slate-400 hover:text-slate-600 rounded">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <form id="discussionForm" class="p-4 space-y-4">
                @csrf
                <input type="hidden" id="discussionId" value="">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Subject</label>
                    <input type="text" name="subject" id="discussionSubject" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Discussion subject..." required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Description</label>
                    <textarea name="description" id="discussionDescription" rows="4" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="What's this about?"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Category</label>
                    <select name="category_id" id="discussionCategory" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">No Category</option>
                        @foreach(\App\Models\ProjectDiscussionCategory::all() as $cat)
                        <option value="{{ $cat->id }}" style="color: {{ $cat->color }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="show_to_customer" id="discussionShowToCustomer" value="1" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <label class="text-xs text-slate-600">Visible to customer</label>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeDiscussionModal()" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Comment Modal --}}
    <div id="editCommentModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4">
            <div class="flex items-center justify-between p-4 border-b border-slate-200">
                <h3 class="text-sm font-semibold text-slate-800">Edit Comment</h3>
                <button onclick="closeEditCommentModal()" class="p-1 text-slate-400 hover:text-slate-600 rounded">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <form id="editCommentForm" class="p-4 space-y-4">
                @csrf @method('PUT')
                <input type="hidden" id="editCommentId" value="">
                <input type="hidden" id="editDiscussionId" value="">
                <textarea id="editCommentContent" rows="4" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500" required></textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeEditCommentModal()" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Update</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Category Management Modal --}}
    <div id="categoryModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between p-4 border-b border-slate-200">
                <h3 class="text-sm font-semibold text-slate-800">Manage Categories</h3>
                <button onclick="closeCategoryModal()" class="p-1 text-slate-400 hover:text-slate-600 rounded">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="p-4 space-y-3">
                <form id="categoryForm" class="flex items-center gap-2">
                    @csrf
                    <input type="text" name="name" id="categoryNameInput" class="flex-1 px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Category name..." required>
                    <input type="color" name="color" id="categoryColorInput" class="w-9 h-9 p-0.5 border border-slate-200 rounded-lg cursor-pointer" value="#6b7280">
                    <button type="submit" class="px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Add</button>
                </form>
                <div id="categoryList" class="space-y-1 max-h-48 overflow-y-auto">
                    @foreach(\App\Models\ProjectDiscussionCategory::all() as $cat)
                    <div class="flex items-center justify-between px-3 py-2 bg-slate-50 rounded-lg" data-cat-id="{{ $cat->id }}">
                        <span class="text-sm flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full inline-block" style="background: {{ $cat->color }}"></span>
                            {{ $cat->name }}
                        </span>
                        <button onclick="deleteCategory({{ $cat->id }})" class="text-red-400 hover:text-red-600 text-xs">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let discussionEditMode = false;
const staffList = @json(\App\Models\Staff::where('active', 1)->get(['staffid', 'firstname', 'lastname'])->map(fn($s) => ['staffid' => $s->staffid, 'firstname' => $s->firstname, 'lastname' => $s->lastname, 'full_name' => $s->full_name]));

document.getElementById('discussionSearchInput')?.addEventListener('input', debounce(function() {
    const query = this.value.trim();
    const categoryId = document.getElementById('discussionCategoryFilter').value;
    if (query.length < 2) {
        document.querySelectorAll('.discussion-list-item').forEach(el => el.style.display = '');
        return;
    }
    const params = new URLSearchParams({ q: query });
    if (categoryId) params.append('category_id', categoryId);
    fetch('/admin/projects/{{ $project->id }}/discussions/search?' + params.toString())
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const visibleIds = data.discussions.map(d => d.id);
                document.querySelectorAll('.discussion-list-item').forEach(el => {
                    el.style.display = visibleIds.includes(parseInt(el.dataset.id)) ? '' : 'none';
                });
            }
        });
}, 300));

function filterByCategory() {
    const catId = document.getElementById('discussionCategoryFilter').value;
    document.querySelectorAll('.discussion-list-item').forEach(el => {
        el.style.display = !catId || el.dataset.category === catId ? '' : 'none';
    });
}

// File input handling
document.querySelectorAll('[id^="file-input-"]').forEach(input => {
    input.addEventListener('change', function() {
        const id = this.id.replace('file-input-', '');
        const preview = document.getElementById('file-preview-' + id);
        const name = document.getElementById('file-name-' + id);
        if (this.files.length > 0) {
            preview.classList.remove('hidden');
            name.textContent = this.files[0].name;
        } else {
            preview.classList.add('hidden');
        }
    });
});

function clearFile(id) {
    document.getElementById('file-input-' + id).value = '';
    document.getElementById('file-preview-' + id).classList.add('hidden');
}

// Mention autocomplete
document.querySelectorAll('[id^="comment-input-"]').forEach(input => {
    input.addEventListener('input', function() {
        const val = this.value;
        const atIndex = val.lastIndexOf('@');
        if (atIndex >= 0 && !val.slice(atIndex).includes(' ')) {
            // Could add mention dropdown here
        }
    });
});

// Rest of the functions
function selectDiscussion(id) {
    window.activeDiscussionId = id;
    document.querySelectorAll('.discussion-list-item').forEach(el => {
        el.classList.toggle('active', parseInt(el.dataset.id) === id);
    });
    const list = document.getElementById('discussion-list');
    document.querySelectorAll('.chat-panel').forEach(el => el.classList.add('hidden'));
    if (id) {
        if (window.innerWidth < 1024) list.classList.add('hidden');
        else list.classList.remove('hidden');
        const panel = document.getElementById('chat-panel-' + id);
        if (panel) { panel.classList.remove('hidden'); panel.classList.add('flex'); }
    } else {
        list.classList.remove('hidden');
    }
    const emptyState = document.getElementById('chat-empty-state');
    if (emptyState) emptyState.classList.toggle('dshown', !id);
    if (id) {
        setTimeout(() => {
            const msgs = document.getElementById('chat-messages-' + id);
            if (msgs) msgs.scrollTop = msgs.scrollHeight;
        }, 50);
    }
}

function openNewDiscussionModal() {
    discussionEditMode = false;
    document.getElementById('discussionModalTitle').textContent = 'Start New Discussion';
    document.getElementById('discussionId').value = '';
    document.getElementById('discussionSubject').value = '';
    document.getElementById('discussionDescription').value = '';
    document.getElementById('discussionCategory').value = '';
    document.getElementById('discussionShowToCustomer').checked = false;
    document.getElementById('discussionModal').classList.remove('hidden');
}

function editDiscussion(id) {
    discussionEditMode = true;
    document.getElementById('discussionModalTitle').textContent = 'Edit Discussion';
    document.getElementById('discussionId').value = id;
    const panel = document.getElementById('chat-panel-' + id);
    document.getElementById('discussionSubject').value = panel.dataset.subject || '';
    document.getElementById('discussionDescription').value = panel.dataset.description || '';
    document.getElementById('discussionModal').classList.remove('hidden');
}

function closeDiscussionModal() {
    document.getElementById('discussionModal').classList.add('hidden');
}

function closeEditCommentModal() {
    document.getElementById('editCommentModal').classList.add('hidden');
}

function togglePinDiscussion(id) {
    fetch('/admin/projects/{{ $project->id }}/discussions/' + id + '/pin', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    }).then(r => r.json()).then(data => {
        if (data.success) loadTab('discussions');
    });
}

function deleteDiscussion(id) {
    if (!confirm('Delete this discussion and all its comments?')) return;
    fetch('/admin/projects/{{ $project->id }}/discussions/' + id, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    }).then(r => r.json()).then(data => {
        if (data.success) loadTab('discussions');
    });
}

document.getElementById('discussionForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('discussionId').value;
    const formData = new FormData(this);
    if (id) formData.append('_method', 'PUT');
    const url = id
        ? '/admin/projects/{{ $project->id }}/discussions/' + id
        : '/admin/projects/{{ $project->id }}/discussions';
    fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' },
        body: formData,
    }).then(r => r.json()).then(data => {
        if (data.success) { closeDiscussionModal(); loadTab('discussions'); }
        else alert(data.message || 'Failed');
    }).catch(() => alert('Failed'));
});

function sendComment(discussionId) {
    const input = document.getElementById('comment-input-' + discussionId);
    const fileInput = document.getElementById('file-input-' + discussionId);
    const parentInput = document.getElementById('parent-input-' + discussionId);
    if (!input.value.trim() && !fileInput.files.length) return false;
    const formData = new FormData();
    formData.append('content', input.value.trim());
    if (parentInput.value) formData.append('parent_id', parentInput.value);
    if (fileInput.files.length) formData.append('file', fileInput.files[0]);
    fetch('/admin/projects/{{ $project->id }}/discussions/' + discussionId + '/comment', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' },
        body: formData,
    }).then(r => r.json()).then(data => {
        if (data.success) {
            input.value = '';
            parentInput.value = '';
            clearFile(discussionId);
            loadTab('discussions');
        } else alert(data.message || 'Failed');
    }).catch(() => alert('Failed'));
    return false;
}

function replyToComment(discussionId, commentId, username) {
    const input = document.getElementById('comment-input-' + discussionId);
    const parentInput = document.getElementById('parent-input-' + discussionId);
    parentInput.value = commentId;
    input.value = '@' + username + ' ';
    input.focus();
}

function editComment(discussionId, commentId, content) {
    document.getElementById('editCommentId').value = commentId;
    document.getElementById('editDiscussionId').value = discussionId;
    document.getElementById('editCommentContent').value = content;
    document.getElementById('editCommentModal').classList.remove('hidden');
}

function deleteComment(discussionId, commentId) {
    if (!confirm('Delete this comment?')) return;
    fetch('/admin/projects/{{ $project->id }}/discussions/' + discussionId + '/comment/' + commentId, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    }).then(r => r.json()).then(data => {
        if (data.success) loadTab('discussions');
    });
}

document.getElementById('editCommentForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const commentId = document.getElementById('editCommentId').value;
    const discussionId = document.getElementById('editDiscussionId').value;
    const content = document.getElementById('editCommentContent').value;
    fetch('/admin/projects/{{ $project->id }}/discussions/' + discussionId + '/comment/' + commentId, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ content, _method: 'PUT' }),
    }).then(r => r.json()).then(data => {
        if (data.success) { closeEditCommentModal(); loadTab('discussions'); }
        else alert('Failed');
    }).catch(() => alert('Failed'));
});

function debounce(fn, delay) {
    let timer;
    return function(...args) {
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(this, args), delay);
    };
}

// Category Management
function openCategoryModal() {
    document.getElementById('categoryModal').classList.remove('hidden');
}

function closeCategoryModal() {
    document.getElementById('categoryModal').classList.add('hidden');
}

document.getElementById('categoryForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const name = document.getElementById('categoryNameInput').value;
    const color = document.getElementById('categoryColorInput').value;
    fetch('/admin/discussion-categories', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ name, color }),
    }).then(r => r.json()).then(data => {
        if (data.success) {
            document.getElementById('categoryNameInput').value = '';
            loadTab('discussions');
            closeCategoryModal();
        }
    });
});

function deleteCategory(id) {
    if (!confirm('Delete this category?')) return;
    fetch('/admin/discussion-categories/' + id, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    }).then(r => r.json()).then(data => {
        if (data.success) loadTab('discussions');
    });
}

// Mention autocomplete
let mentionDropdown = null;
document.querySelectorAll('[id^="comment-input-"]').forEach(input => {
    input.addEventListener('input', function() {
        const val = this.value;
        const atIndex = val.lastIndexOf('@');
        if (atIndex >= 0 && !val.slice(atIndex).includes(' ')) {
            const query = val.slice(atIndex + 1).toLowerCase();
            if (mentionDropdown) mentionDropdown.remove();
            if (query.length > 0) {
                const matches = staffList.filter(s =>
                    s.full_name.toLowerCase().includes(query) ||
                    s.firstname.toLowerCase().includes(query) ||
                    s.lastname.toLowerCase().includes(query)
                ).slice(0, 8);
                if (matches.length === 0) return;
                mentionDropdown = document.createElement('div');
                mentionDropdown.className = 'absolute bottom-full left-0 mb-1 bg-white border border-slate-200 rounded-lg shadow-lg z-50 max-h-32 overflow-y-auto w-48';
                mentionDropdown.id = 'mention-dropdown-' + this.id.replace('comment-input-', '');
                matches.forEach(s => {
                    const item = document.createElement('button');
                    item.type = 'button';
                    item.className = 'block w-full text-left px-3 py-1.5 text-xs hover:bg-slate-50 flex items-center gap-2';
                    item.innerHTML = '<span class="w-5 h-5 rounded-full bg-slate-300 flex items-center justify-center text-white text-[8px] font-bold flex-shrink-0">' + (s.full_name?.[0] || '?') + '</span> ' + (s.full_name || s.firstname + ' ' + s.lastname);
                    item.addEventListener('mousedown', (e) => {
                        e.preventDefault();
                        const before = val.slice(0, atIndex);
                        this.value = before + '@' + s.firstname + '.' + s.lastname + ' ';
                        if (mentionDropdown) mentionDropdown.remove();
                        this.focus();
                    });
                    mentionDropdown.appendChild(item);
                });
                this.parentElement.style.position = 'relative';
                this.parentElement.appendChild(mentionDropdown);
                return;
            }
        }
        if (mentionDropdown) { mentionDropdown.remove(); mentionDropdown = null; }
    });
    input.addEventListener('blur', function() {
        setTimeout(() => { if (mentionDropdown) { mentionDropdown.remove(); mentionDropdown = null; } }, 200);
    });
});

(function() {
    if (window.activeDiscussionId) selectDiscussion(window.activeDiscussionId);
    @foreach($discussions as $discussion)
    (function(id) {
        const el = document.getElementById('chat-messages-' + id);
        if (el) el.scrollTop = el.scrollHeight;
    })({{ $discussion->id }});
    @endforeach
})();
</script>
