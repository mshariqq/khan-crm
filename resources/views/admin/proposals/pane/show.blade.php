@php
$pStatusMap = [
    1 => ['label' => 'Open', 'class' => 'bg-blue-100 text-blue-700'],
    2 => ['label' => 'Accepted', 'class' => 'bg-emerald-100 text-emerald-700'],
    3 => ['label' => 'Declined', 'class' => 'bg-red-100 text-red-700'],
];
$ps = $pStatusMap[$proposal->status] ?? ['label' => 'Unknown', 'class' => 'bg-slate-100 text-slate-600'];
@endphp

<div class="flex items-start justify-between mb-6">
    <div class="flex items-start gap-4">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-purple-200 flex-shrink-0">
            {{ strtoupper(substr($proposal->subject, 0, 1)) }}
        </div>
        <div>
            <div class="flex items-center gap-3 mb-1">
                <h1 class="text-xl font-bold text-slate-800">{{ $proposal->subject }}</h1>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $ps['class'] }}">{{ $ps['label'] }}</span>
            </div>
            <div class="flex items-center gap-3 text-xs text-slate-400">
                <span>{{ $proposal->proposal_to }}</span>
                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                <span>#{{ $proposal->id }}</span>
                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                <span>{{ \Carbon\Carbon::parse($proposal->datecreated)->format('M j, Y') }}</span>
            </div>
        </div>
    </div>
    <div class="flex items-center gap-2 flex-shrink-0">
        <button onclick="parent.openTaskPane('{{ route('admin.proposals.pane.edit', $proposal->id) }}')" class="px-3 py-2 bg-white border border-slate-200 text-slate-600 text-xs font-bold rounded-xl hover:bg-slate-50 transition-all flex items-center gap-1.5">
            <i class="bi bi-pencil"></i> Edit
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-5 gap-5">
    <div class="lg:col-span-3 space-y-5">
        {{-- Content --}}
        @if($proposal->content)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                <i class="bi bi-card-text text-slate-400"></i>
                <h3 class="text-sm font-semibold text-slate-800">Content</h3>
            </div>
            <div class="p-5 text-sm text-slate-600 leading-relaxed prose prose-slate max-w-none">{!! $proposal->content !!}</div>
        </div>
        @endif

        {{-- Items --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="bi bi-list-check text-blue-500"></i>
                    <h3 class="text-sm font-semibold text-slate-800">Items</h3>
                </div>
                <span class="px-2 py-0.5 bg-slate-100 text-slate-600 rounded-full text-[10px] font-bold">{{ $proposal->items->count() }}</span>
            </div>
            @if($proposal->items->count() > 0)
            <div class="p-5">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200">
                            <th class="px-3 py-2 text-left text-slate-500 font-medium">Item</th>
                            <th class="px-3 py-2 text-center text-slate-500 font-medium">Qty</th>
                            <th class="px-3 py-2 text-right text-slate-500 font-medium">Rate</th>
                            <th class="px-3 py-2 text-right text-slate-500 font-medium">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($proposal->items as $item)
                        <tr>
                            <td class="px-3 py-3 text-slate-700">{{ $item->description }}</td>
                            <td class="px-3 py-3 text-center text-slate-700">{{ $item->qty }}</td>
                            <td class="px-3 py-3 text-right text-slate-700">{{ app_format_money($item->rate, $proposal->currency) }}</td>
                            <td class="px-3 py-3 text-right font-medium text-slate-800">{{ app_format_money($item->amount, $proposal->currency) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="p-8 text-center">
                <p class="text-sm text-slate-400">No items on this proposal.</p>
            </div>
            @endif
        </div>

        {{-- Comments --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="bi bi-chat-dots text-cyan-600"></i>
                    <h3 class="text-sm font-semibold text-slate-800">Comments</h3>
                </div>
                <span class="px-2 py-0.5 bg-cyan-50 text-cyan-700 rounded-full text-[10px] font-bold">{{ $proposal->comments->count() }}</span>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($proposal->comments->sortByDesc('dateadded') as $comment)
                <div class="p-4 hover:bg-slate-50/50 transition-colors">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-slate-400 to-slate-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                            {{ $comment->staff ? strtoupper(substr($comment->staff->firstname, 0, 1)) : 'U' }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-0.5">
                                <span class="text-sm font-semibold text-slate-800">{{ $comment->staff ? $comment->staff->firstname . ' ' . $comment->staff->lastname : 'Unknown' }}</span>
                                <span class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($comment->dateadded)->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-slate-600 leading-relaxed">{{ $comment->content }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center">
                    <p class="text-sm text-slate-400">No comments yet.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="lg:col-span-2 space-y-4">
        {{-- Status --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                <i class="bi bi-info-circle text-slate-400"></i>
                <h3 class="text-sm font-semibold text-slate-800">Details</h3>
            </div>
            <div class="p-5 space-y-4">
                <div class="flex justify-between">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status</span>
                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold {{ $ps['class'] }}">{{ $ps['label'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">To</span>
                    <span class="text-sm font-medium text-slate-700">{{ $proposal->proposal_to }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Email</span>
                    <span class="text-sm font-medium text-slate-700">{{ $proposal->email ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Date</span>
                    <span class="text-sm font-medium text-slate-700">{{ \Carbon\Carbon::parse($proposal->date)->format('M j, Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Valid Till</span>
                    <span class="text-sm font-medium text-slate-700">{{ $proposal->open_till ? \Carbon\Carbon::parse($proposal->open_till)->format('M j, Y') : '-' }}</span>
                </div>
                @if($proposal->assignedStaff)
                <div class="flex justify-between">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Assigned</span>
                    <span class="text-sm font-medium text-slate-700">{{ $proposal->assignedStaff->firstname }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Financial --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                <i class="bi bi-cash text-emerald-500"></i>
                <h3 class="text-sm font-semibold text-slate-800">Financial</h3>
            </div>
            <div class="p-5 space-y-3">
                <div class="flex justify-between">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Subtotal</span>
                    <span class="text-sm font-medium text-slate-700">{{ app_format_money($proposal->subtotal ?? 0, $proposal->currency) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tax</span>
                    <span class="text-sm font-medium text-slate-700">{{ app_format_money($proposal->total_tax ?? 0, $proposal->currency) }}</span>
                </div>
                <div class="flex justify-between border-t border-slate-200 pt-3">
                    <span class="text-xs font-bold text-slate-800">Total</span>
                    <span class="text-sm font-bold text-indigo-600">{{ app_format_money($proposal->total ?? 0, $proposal->currency) }}</span>
                </div>
            </div>
        </div>

        {{-- Related --}}
        @if($proposal->estimate_id || $proposal->invoice_id || $proposal->project_id || $proposal->rel_type)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                <i class="bi bi-link-45deg text-slate-400"></i>
                <h3 class="text-sm font-semibold text-slate-800">Related</h3>
            </div>
            <div class="p-5 space-y-3">
                @if($proposal->estimate_id)
                <a href="{{ route('admin.estimates.show', $proposal->estimate_id) }}" data-pane="show" data-pane-type="estimate" data-pane-id="{{ $proposal->estimate_id }}" class="flex items-center gap-3 px-3 py-2.5 bg-amber-50 rounded-lg border border-amber-100 hover:bg-amber-100 transition-colors">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600 flex-shrink-0">
                        <i class="bi bi-file-text text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-[10px] font-bold text-amber-500 uppercase block">Estimate</span>
                        <span class="text-sm font-medium text-slate-800">#{{ $proposal->estimate_id }}</span>
                    </div>
                    <i class="bi bi-box-arrow-up-right text-amber-400 text-xs"></i>
                </a>
                @endif
                @if($proposal->invoice_id)
                <a href="{{ route('admin.invoices.show', $proposal->invoice_id) }}" class="flex items-center gap-3 px-3 py-2.5 bg-emerald-50 rounded-lg border border-emerald-100 hover:bg-emerald-100 transition-colors">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0">
                        <i class="bi bi-receipt text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-[10px] font-bold text-emerald-500 uppercase block">Invoice</span>
                        <span class="text-sm font-medium text-slate-800">#{{ $proposal->invoice_id }}</span>
                    </div>
                    <i class="bi bi-box-arrow-up-right text-emerald-400 text-xs"></i>
                </a>
                @endif
                @if($proposal->project_id)
                <a href="{{ route('admin.projects.show', $proposal->project_id) }}" class="flex items-center gap-3 px-3 py-2.5 bg-blue-50 rounded-lg border border-blue-100 hover:bg-blue-100 transition-colors">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600 flex-shrink-0">
                        <i class="bi bi-folder text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-[10px] font-bold text-blue-500 uppercase block">Project</span>
                        <span class="text-sm font-medium text-slate-800">{{ optional($proposal->project)->name ?? '#' . $proposal->project_id }}</span>
                    </div>
                    <i class="bi bi-box-arrow-up-right text-blue-400 text-xs"></i>
                </a>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>