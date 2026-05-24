<div class="flex items-start gap-2.5 group" id="comment-{{ $comment->id }}">
    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-[10px] font-bold flex-shrink-0 mt-0.5">
        {{ substr($comment->staff->full_name ?? $comment->fullname ?? 'U', 0, 1) }}
    </div>
    <div class="flex-1 min-w-0">
        <div class="bg-white rounded-2xl rounded-tl-none px-3.5 py-2.5 border border-slate-100 shadow-sm {{ $depth > 0 ? 'bg-slate-50' : '' }}">
            <div class="flex items-center justify-between mb-1">
                <div class="flex items-center gap-2">
                    <span class="text-[11px] font-semibold text-slate-800">{{ $comment->staff->full_name ?? $comment->fullname ?? 'Unknown' }}</span>
                    <span class="text-[10px] text-slate-400">{{ ($comment->created ?? $comment->created_at)?->diffForHumans() }}</span>
                </div>
                <div class="flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button onclick="replyToComment({{ $discussion->id }}, {{ $comment->id }}, '{{ $comment->staff->firstname ?? '' }}')" class="p-1 rounded text-slate-400 hover:text-blue-600" title="Reply">
                        <i class="bi bi-reply text-[10px]"></i>
                    </button>
                    @if(($comment->staff_id && $comment->staff_id == auth()->id()) || auth()->user()?->can('projects_edit'))
                    <button onclick="editComment({{ $discussion->id }}, {{ $comment->id }}, '{{ str_replace("'", "\'", $comment->content) }}')" class="p-1 rounded text-slate-400 hover:text-blue-600" title="Edit">
                        <i class="bi bi-pencil text-[10px]"></i>
                    </button>
                    <button onclick="deleteComment({{ $discussion->id }}, {{ $comment->id }})" class="p-1 rounded text-slate-400 hover:text-red-600" title="Delete">
                        <i class="bi bi-trash text-[10px]"></i>
                    </button>
                    @endif
                </div>
            </div>
            <p class="text-xs text-slate-700 leading-relaxed">{!! nl2br(e($comment->content)) !!}</p>

            @if($comment->mentions)
            <div class="flex items-center gap-1 mt-1.5">
                <i class="bi bi-at text-[10px] text-amber-500"></i>
                @foreach(json_decode($comment->mentions, true) ?? [] as $mentionedId)
                @php $mentioned = \App\Models\Staff::find($mentionedId); @endphp
                @if($mentioned)
                <span class="text-[10px] bg-amber-50 text-amber-700 px-1.5 py-0.5 rounded-full">{{ $mentioned->full_name }}</span>
                @endif
                @endforeach
            </div>
            @endif

            @if($comment->file_name)
            <div class="mt-2 pt-2 border-t border-slate-100">
                <a href="{{ asset('storage/' . $comment->file_name) }}" target="_blank" class="flex items-center gap-1.5 text-[10px] text-blue-600 hover:text-blue-700">
                    <i class="bi bi-file-earmark"></i>
                    <span>{{ $comment->original_file_name ?? basename($comment->file_name) }}</span>
                    @if($comment->file_size)
                    <span class="text-slate-400">({{ number_format($comment->file_size / 1024, 1) }} KB)</span>
                    @endif
                    <i class="bi bi-box-arrow-up-right"></i>
                </a>
            </div>
            @endif
        </div>

        @if($comment->replies->count() > 0)
        <div class="space-y-2 mt-2 ml-2.5 border-l-2 border-slate-100 pl-2.5">
            @foreach($comment->replies as $reply)
            @include('admin.projects.partials.discussion-comment', ['comment' => $reply, 'discussion' => $discussion, 'depth' => $depth + 1])
            @endforeach
        </div>
        @endif
    </div>
</div>
