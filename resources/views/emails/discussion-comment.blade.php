<h2>New comment on "{{ $discussion->subject }}"</h2>

<p><strong>{{ $author }}</strong> commented on the discussion <strong>"{{ $discussion->subject }}"</strong> in project <strong>{{ $project->name }}</strong>.</p>

<div style="background: #f8fafc; border-left: 3px solid #2563eb; padding: 16px; margin: 16px 0; border-radius: 4px;">
    {!! nl2br(e($comment->content)) !!}
</div>

@if($comment->file_name)
<p style="color: #64748b; font-size: 14px;">
    <i>📎 {{ $comment->original_file_name ?? basename($comment->file_name) }} attached</i>
</p>
@endif

<p>
    <a href="{{ route('admin.projects.discussions', $project->id) }}" class="btn">View Discussion</a>
</p>

<div class="signature">
    <p>To reply to this discussion, simply reply to this email or visit the discussion page.</p>
</div>
