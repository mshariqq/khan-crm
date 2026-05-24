<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Proposals for {{ $lead->name }}</h1>
        <p class="text-slate-500 text-sm">{{ $lead->proposals->count() }} proposal(s) linked to this lead</p>
    </div>
    <a href="{{ route('admin.leads.pane.proposals.create', $lead->id) }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all flex items-center gap-2">
        <i class="bi bi-plus-lg"></i> New Proposal
    </a>
</div>

@if($lead->proposals->count() > 0)
    <div class="space-y-3">
        @foreach($lead->proposals->sortByDesc('datecreated') as $proposal)
            @php
                $pStatusMap = [
                    1 => ['label' => 'Open', 'class' => 'bg-blue-100 text-blue-700'],
                    2 => ['label' => 'Accepted', 'class' => 'bg-emerald-100 text-emerald-700'],
                    3 => ['label' => 'Declined', 'class' => 'bg-red-100 text-red-700'],
                ];
                $ps = $pStatusMap[$proposal->status] ?? ['label' => 'Unknown', 'class' => 'bg-slate-100 text-slate-600'];
            @endphp
            <div class="bg-white rounded-xl border border-slate-200 p-4 hover:border-blue-200 transition-all cursor-pointer" onclick="parent.openTaskPane('{{ route('admin.proposals.pane.show', $proposal->id) }}', 'Proposal')">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center text-white font-bold text-sm shadow-sm flex-shrink-0">
                            {{ strtoupper(substr($proposal->subject, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-800">{{ $proposal->subject }}</p>
                            <p class="text-xs text-slate-400">{{ app_format_money($proposal->total) }} &middot; {{ \Carbon\Carbon::parse($proposal->datecreated)->format('M d, Y') }}</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $ps['class'] }}">{{ $ps['label'] }}</span>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="text-center py-12">
        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
            <i class="bi bi-file-earmark-text text-slate-400 text-2xl"></i>
        </div>
        <p class="text-slate-500 text-sm mb-4">No proposals yet for this lead.</p>
        <a href="{{ route('admin.leads.pane.proposals.create', $lead->id) }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all inline-flex items-center gap-2">
            <i class="bi bi-plus-lg"></i> Create First Proposal
        </a>
    </div>
@endif
