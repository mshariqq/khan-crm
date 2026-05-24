<div class="flex items-center justify-between mb-4">
    <h3 class="text-sm font-semibold text-slate-800">Feedback ({{ $project->feedback->count() }})</h3>
    <button onclick="document.getElementById('inlineFeedbackForm').classList.toggle('hidden')" class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 flex items-center gap-1">
        <i class="bi bi-plus"></i> Submit Feedback
    </button>
</div>

<form id="inlineFeedbackForm" action="{{ route('admin.projects.feedback.store', $project->id) }}" method="POST" class="hidden mb-4 p-4 bg-slate-50 rounded-lg border border-slate-200 space-y-3">
    @csrf
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Rating (1-5)</label>
            <select name="rating" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                <option value="">Select rating</option>
                @for($i = 1; $i <= 5; $i++)
                <option value="{{ $i }}">{{ $i }} {{ $i == 1 ? 'Star' : 'Stars' }}</option>
                @endfor
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">NPS Score (0-10)</label>
            <select name="nps_score" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Select score</option>
                @for($i = 0; $i <= 10; $i++)
                <option value="{{ $i }}">{{ $i }} {{ $i >= 9 ? '(Promoter)' : ($i >= 7 ? '(Passive)' : '(Detractor)') }}</option>
                @endfor
            </select>
        </div>
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-600 mb-1">Comment</label>
        <textarea name="comment" rows="3" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Share your experience..."></textarea>
    </div>
    <div class="flex justify-end gap-2">
        <button type="button" onclick="document.getElementById('inlineFeedbackForm').classList.add('hidden')" class="px-3 py-1.5 text-xs text-slate-600 hover:text-slate-800">Cancel</button>
        <button type="submit" class="px-4 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">Submit Feedback</button>
    </div>
</form>

@if($project->feedback->count() > 0)
<div class="space-y-3">
    @foreach($project->feedback as $feedback)
    <div class="p-4 bg-white border border-slate-200 rounded-lg hover:border-slate-300 transition-colors">
        <div class="flex items-start justify-between mb-2">
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-0.5">
                    @for($i = 1; $i <= 5; $i++)
                    <i class="bi {{ $i <= ($feedback->rating ?? 0) ? 'bi-star-fill text-amber-400' : 'bi-star text-slate-300' }} text-sm"></i>
                    @endfor
                </div>
                @if($feedback->nps_score !== null)
                <span class="text-[10px] px-2 py-0.5 rounded-full font-medium
                    @if($feedback->nps_score >= 9) bg-emerald-100 text-emerald-700
                    @elseif($feedback->nps_score >= 7) bg-blue-100 text-blue-700
                    @else bg-red-100 text-red-700 @endif">
                    NPS: {{ $feedback->nps_score }}
                </span>
                @endif
            </div>
            <div class="flex items-center gap-1">
                @if($feedback->submitter)
                <span class="text-[10px] text-slate-400">{{ $feedback->submitter->firstname ?? '' }} {{ $feedback->submitter->lastname ?? '' }}</span>
                @endif
                <span class="text-[10px] text-slate-400">{{ $feedback->created_at?->format('M j, Y') ?? '' }}</span>
            </div>
        </div>
        @if($feedback->comment)
        <p class="text-xs text-slate-600 leading-relaxed">{{ $feedback->comment }}</p>
        @endif
    </div>
    @endforeach
</div>
@else
<div class="text-center py-12">
    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
        <i class="bi bi-chat-square-quote text-slate-400 text-xl"></i>
    </div>
    <p class="text-sm text-slate-500">No feedback yet</p>
    <button onclick="document.getElementById('inlineFeedbackForm').classList.remove('hidden')" class="mt-3 inline-block px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">Submit Feedback</button>
</div>
@endif
