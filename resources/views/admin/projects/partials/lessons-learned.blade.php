<div class="flex items-center justify-between mb-4">
    <h3 class="text-sm font-semibold text-slate-800">Lessons Learned ({{ $project->lessonsLearned->count() }})</h3>
    <button onclick="document.getElementById('inlineLessonForm').classList.toggle('hidden')" class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 flex items-center gap-1">
        <i class="bi bi-plus"></i> Add Lesson
    </button>
</div>

<form id="inlineLessonForm" action="{{ route('admin.projects.lessons-learned.store', $project->id) }}" method="POST" class="hidden mb-4 p-4 bg-slate-50 rounded-lg border border-slate-200 space-y-3">
    @csrf
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Title</label>
            <input type="text" name="title" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Category</label>
            <select name="category" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="process">Process</option>
                <option value="technical">Technical</option>
                <option value="management">Management</option>
                <option value="communication">Communication</option>
                <option value="resource">Resource</option>
                <option value="risk">Risk</option>
                <option value="stakeholder">Stakeholder</option>
                <option value="other">Other</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Impact</label>
            <select name="impact" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="positive">Positive</option>
                <option value="neutral">Neutral</option>
                <option value="negative">Negative</option>
            </select>
        </div>
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-600 mb-1">Lesson Description</label>
        <textarea name="description" rows="3" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required></textarea>
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-600 mb-1">Recommendation</label>
        <textarea name="recommendation" rows="2" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
    </div>
    <div class="flex justify-end gap-2">
        <button type="button" onclick="document.getElementById('inlineLessonForm').classList.add('hidden')" class="px-3 py-1.5 text-xs text-slate-600 hover:text-slate-800">Cancel</button>
        <button type="submit" class="px-4 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">Add Lesson</button>
    </div>
</form>

@if($project->lessonsLearned->count() > 0)
<div class="space-y-3">
    @foreach($project->lessonsLearned as $lesson)
    <div class="p-4 bg-white border border-slate-200 rounded-lg hover:border-slate-300 transition-colors group">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <h4 class="text-sm font-semibold text-slate-800">{{ $lesson->title }}</h4>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 capitalize">{{ $lesson->category ?? 'general' }}</span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-medium flex items-center gap-1
                        @if($lesson->impact == 'positive') bg-emerald-100 text-emerald-700
                        @elseif($lesson->impact == 'negative') bg-red-100 text-red-700
                        @else bg-slate-100 text-slate-600 @endif">
                        <i class="bi 
                            @if($lesson->impact == 'positive') bi-emoji-smile
                            @elseif($lesson->impact == 'negative') bi-emoji-frown
                            @else bi-emoji-neutral
                            @endif"></i>
                        {{ ucfirst($lesson->impact ?? 'neutral') }}
                    </span>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed mb-2">{{ $lesson->description }}</p>
                @if($lesson->recommendation)
                <div class="p-2 bg-blue-50 rounded-lg border border-blue-100">
                    <p class="text-[10px] font-medium text-blue-700 mb-0.5">Recommendation:</p>
                    <p class="text-[11px] text-blue-600">{{ $lesson->recommendation }}</p>
                </div>
                @endif
            </div>
            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity ml-2">
                <form action="{{ route('admin.projects.lessons-learned.destroy', [$project->id, $lesson->id]) }}" method="POST" class="inline" onsubmit="event.preventDefault(); confirmDelete('Confirm Delete', 'Delete this lesson?', this.action, (this.querySelector('[name=_method]')?.value || 'POST'));">
                    @csrf @method('DELETE')
                    <button type="submit" class="p-1 text-slate-400 hover:text-red-600 rounded hover:bg-slate-200" title="Delete"><i class="bi bi-trash text-xs"></i></button>
                </form>
            </div>
        </div>
        <div class="flex items-center gap-2 mt-3 pt-2 border-t border-slate-100">
            @if($lesson->creator)
            <span class="w-5 h-5 rounded-full bg-slate-400 flex items-center justify-center text-white text-[8px] font-semibold">
                {{ substr($lesson->creator->firstname ?? 'U', 0, 1) }}
            </span>
            <span class="text-[10px] text-slate-500">{{ $lesson->creator->firstname ?? '' }} {{ $lesson->creator->lastname ?? '' }}</span>
            @endif
            <span class="text-[10px] text-slate-400 ml-auto">{{ $lesson->created_at?->format('M j, Y') ?? '' }}</span>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="text-center py-12">
    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
        <i class="bi bi-journal-bookmark-fill text-slate-400 text-xl"></i>
    </div>
    <p class="text-sm text-slate-500">No lessons learned yet</p>
    <button onclick="document.getElementById('inlineLessonForm').classList.remove('hidden')" class="mt-3 inline-block px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">Add Lesson</button>
</div>
@endif
