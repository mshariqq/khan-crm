@extends('layouts.admin')

@section('breadcrumb', 'Proposal Details')

@section('main')
<div class="h-[calc(100vh-64px)] flex flex-col">
    <div class="bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.proposals.index') }}" class="p-2 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-500 transition-all">
                <i class="bi bi-arrow-left text-lg"></i>
            </a>
            <div>
                <div class="flex items-center gap-2 mb-0.5">
                    <h1 class="text-xl font-bold text-slate-800">{{ $proposal->subject }}</h1>
                    @php
                        $pStatusMap = [
                            1 => ['label' => 'Open', 'class' => 'bg-blue-100 text-blue-700'],
                            2 => ['label' => 'Accepted', 'class' => 'bg-emerald-100 text-emerald-700'],
                            3 => ['label' => 'Declined', 'class' => 'bg-red-100 text-red-700'],
                        ];
                        $ps = $pStatusMap[$proposal->status] ?? ['label' => 'Unknown', 'class' => 'bg-slate-100 text-slate-600'];
                    @endphp
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider {{ $ps['class'] }}">{{ $ps['label'] }}</span>
                </div>
                <p class="text-sm text-slate-400">
                    {{ $proposal->proposal_to }} &middot; {{ \Carbon\Carbon::parse($proposal->datecreated)->format('M d, Y') }}
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if($proposal->total)
            <div class="text-right px-4 py-2 bg-indigo-50 rounded-lg border border-indigo-200">
                <p class="text-xs text-indigo-600 font-medium">Total</p>
                <p class="text-lg font-bold text-indigo-700">{{ app_format_money($proposal->total, $proposal->currency) }}</p>
            </div>
            @endif
            <div class="h-8 w-px bg-slate-200 mx-1"></div>
            <a href="{{ route('admin.proposals.edit', $proposal->id) }}" data-pane="edit" data-pane-type="proposal" data-pane-id="{{ $proposal->id }}" class="p-2 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-600 transition-all" title="Edit Proposal">
                <i class="bi bi-pencil-square"></i>
            </a>
            <form action="{{ route('admin.proposals.destroy', $proposal->id) }}" method="POST" onsubmit="return confirm('Delete this proposal?');" class="inline">
                @csrf @method('DELETE')
                <button type="submit" class="p-2 rounded-xl bg-red-50 hover:bg-red-100 text-red-500 transition-all" title="Delete Proposal">
                    <i class="bi bi-trash3"></i>
                </button>
            </form>
        </div>
    </div>

    <div class="flex-1 flex overflow-hidden">
        <div class="flex-1 overflow-y-auto bg-slate-50/50">
            <div class="max-w-4xl mx-auto p-8">
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                    <div class="flex border-b border-slate-200 overflow-x-auto px-4">
                        <button class="tab-btn active px-4 py-3 text-sm font-medium border-b-2 border-blue-500 text-blue-600 whitespace-nowrap" data-tab="overview">Overview</button>
                        <button class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 border-b-2 border-transparent whitespace-nowrap" data-tab="items">Items ({{ $proposal->items->count() }})</button>
                        <button class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 border-b-2 border-transparent whitespace-nowrap" data-tab="comments">Comments ({{ $proposal->comments->count() }})</button>
                        <button class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 border-b-2 border-transparent whitespace-nowrap" data-tab="notes">Notes ({{ $proposal->notes->count() }})</button>
                        <button class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 border-b-2 border-transparent whitespace-nowrap" data-tab="reminders">Reminders ({{ $proposal->reminders->count() }})</button>
                    </div>

                    <div class="p-6">
                        {{-- Overview Tab --}}
                        <div id="tab-overview" class="tab-content">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="bg-slate-50 rounded-xl p-5">
                                    <h3 class="text-sm font-semibold text-slate-700 mb-3">Proposal Details</h3>
                                    <div class="space-y-3 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-slate-500">Proposal To</span>
                                            <span class="text-slate-800 font-medium">{{ $proposal->proposal_to }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-slate-500">Email</span>
                                            <span class="text-slate-800">{{ $proposal->email ?? '-' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-slate-500">Phone</span>
                                            <span class="text-slate-800">{{ $proposal->phone ?? '-' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-slate-500">Valid Until</span>
                                            <span class="text-slate-800">{{ $proposal->open_till ? \Carbon\Carbon::parse($proposal->open_till)->format('M d, Y') : '-' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-slate-500">Assigned To</span>
                                            <span class="text-slate-800">{{ optional($proposal->assignedStaff)->firstname ?? 'Unassigned' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-slate-50 rounded-xl p-5">
                                    <h3 class="text-sm font-semibold text-slate-700 mb-3">Financial</h3>
                                    <div class="space-y-3 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-slate-500">Subtotal</span>
                                            <span class="text-slate-800 font-medium">{{ app_format_money($proposal->subtotal ?? 0, $proposal->currency) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-slate-500">Tax</span>
                                            <span class="text-slate-800">{{ app_format_money($proposal->total_tax ?? 0, $proposal->currency) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-slate-500">Discount</span>
                                            <span class="text-slate-800">{{ app_format_money($proposal->discount_total ?? 0, $proposal->currency) }}</span>
                                        </div>
                                        <div class="flex justify-between border-t border-slate-200 pt-3">
                                            <span class="text-slate-600 font-medium">Total</span>
                                            <span class="text-slate-800 font-bold">{{ app_format_money($proposal->total ?? 0, $proposal->currency) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if($proposal->content)
                            <div class="mt-6 bg-slate-50 rounded-xl p-5">
                                <h3 class="text-sm font-semibold text-slate-700 mb-2">Content</h3>
                                <div class="text-sm text-slate-600 prose prose-slate max-w-none">{!! $proposal->content !!}</div>
                            </div>
                            @endif
                            @if($proposal->address || $proposal->city)
                            <div class="mt-6 bg-slate-50 rounded-xl p-5">
                                <h3 class="text-sm font-semibold text-slate-700 mb-3">Address</h3>
                                <p class="text-sm text-slate-600">{{ $proposal->address }}, {{ $proposal->city }}{{ $proposal->state ? ', ' . $proposal->state : '' }}{{ $proposal->zip ? ' ' . $proposal->zip : '' }}</p>
                            </div>
                            @endif
                        </div>

                        {{-- Items Tab --}}
                        <div id="tab-items" class="tab-content hidden">
                            @if($proposal->items->count() > 0)
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
                            @else
                            <p class="text-sm text-slate-400 text-center py-8">No items on this proposal.</p>
                            @endif
                        </div>

                        {{-- Comments Tab --}}
                        <div id="tab-comments" class="tab-content hidden space-y-4">
                            <form action="{{ route('admin.proposals.store_comment', $proposal->id) }}" method="POST" class="bg-slate-50 rounded-xl p-4">
                                @csrf
                                <textarea name="content" rows="2" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm" placeholder="Add a comment..." required></textarea>
                                <div class="flex justify-end mt-2">
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Add Comment</button>
                                </div>
                            </form>
                            <div class="space-y-3">
                                @forelse($proposal->comments as $comment)
                                <div class="bg-slate-50 rounded-xl p-4 flex justify-between items-start">
                                    <div>
                                        <p class="text-sm text-slate-700">{{ $comment->content }}</p>
                                        <p class="text-xs text-slate-400 mt-1">{{ \Carbon\Carbon::parse($comment->datecreated)->format('M d, Y H:i') }}</p>
                                    </div>
                                    <form action="{{ route('admin.proposal_comments.destroy', $comment->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600 text-xs font-medium">Delete</button>
                                    </form>
                                </div>
                                @empty
                                <p class="text-sm text-slate-400 text-center py-8">No comments yet.</p>
                                @endforelse
                            </div>
                        </div>

                        {{-- Notes Tab --}}
                        <div id="tab-notes" class="tab-content hidden space-y-4">
                            <form action="{{ route('admin.proposals.store_note', $proposal->id) }}" method="POST" class="bg-slate-50 rounded-xl p-4">
                                @csrf
                                <div class="flex gap-2">
                                    <input type="text" name="content" class="flex-1 px-3 py-2 border border-slate-200 rounded-lg text-sm" placeholder="Add a note..." required>
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Add</button>
                                </div>
                            </form>
                            <div class="space-y-3">
                                @forelse($proposal->notes as $note)
                                <div class="bg-slate-50 rounded-xl p-4 flex justify-between items-start">
                                    <div>
                                        <p class="text-sm text-slate-700">{{ $note->description }}</p>
                                        <p class="text-xs text-slate-400 mt-1">{{ \Carbon\Carbon::parse($note->dateadded)->format('M d, Y H:i') }}</p>
                                    </div>
                                    <form action="{{ route('admin.notes.destroy', $note->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600 text-xs font-medium">Delete</button>
                                    </form>
                                </div>
                                @empty
                                <p class="text-sm text-slate-400 text-center py-8">No notes.</p>
                                @endforelse
                            </div>
                        </div>

                        {{-- Reminders Tab --}}
                        <div id="tab-reminders" class="tab-content hidden space-y-4">
                            <form action="{{ route('admin.proposals.store_reminder', $proposal->id) }}" method="POST" class="bg-slate-50 rounded-xl p-4">
                                @csrf
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                                    <input type="text" name="content" placeholder="Reminder content..." class="px-3 py-2 border border-slate-200 rounded-lg text-sm" required>
                                    <input type="datetime-local" name="date" class="px-3 py-2 border border-slate-200 rounded-lg text-sm" required>
                                </div>
                                <div class="flex justify-end">
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Add Reminder</button>
                                </div>
                            </form>
                            <div class="space-y-3">
                                @forelse($proposal->reminders as $reminder)
                                <div class="bg-slate-50 rounded-xl p-4 flex justify-between items-center">
                                    <div>
                                        <p class="text-sm text-slate-700">{{ $reminder->description }}</p>
                                        <p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($reminder->date)->format('M d, Y H:i') }}</p>
                                    </div>
                                    <form action="{{ route('admin.reminders.destroy', $reminder->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                                @empty
                                <p class="text-sm text-slate-400 text-center py-8">No reminders.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Sidebar --}}
        <div class="w-80 border-l border-slate-200 bg-white overflow-y-auto p-6 space-y-8 shrink-0">
            <div class="space-y-4">
                <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Proposal Status</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 ml-1">Status</label>
                        <div class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full {{ $proposal->status == 2 ? 'bg-emerald-500' : ($proposal->status == 3 ? 'bg-red-500' : 'bg-blue-500') }}"></span>
                            <span class="text-sm font-bold text-slate-700">{{ $ps['label'] }}</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 ml-1">Assigned To</label>
                        <div class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 text-[10px] font-bold">
                                {{ strtoupper(substr(optional($proposal->assignedStaff)->firstname ?? 'U', 0, 1)) }}
                            </div>
                            <span class="text-sm font-bold text-slate-700">{{ optional($proposal->assignedStaff)->firstname ?? 'Unassigned' }}</span>
                        </div>
                    </div>
                </div>
                <div class="space-y-2 pt-2">
                    @if($proposal->status == 1)
                    <form action="{{ route('admin.proposals.mark_accepted', $proposal->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2.5 bg-emerald-600 text-white text-sm font-bold rounded-xl hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-200 flex items-center gap-2 justify-center">
                            <i class="bi bi-check-circle"></i> Mark Accepted
                        </button>
                    </form>
                    <form action="{{ route('admin.proposals.mark_declined', $proposal->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2.5 bg-red-500 text-white text-sm font-bold rounded-xl hover:bg-red-600 transition-all shadow-lg shadow-red-200 flex items-center gap-2 justify-center">
                            <i class="bi bi-x-circle"></i> Mark Declined
                        </button>
                    </form>
                    @endif
                    <form action="{{ route('admin.proposals.send', $proposal->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2.5 bg-blue-100 text-blue-700 text-sm font-bold rounded-xl hover:bg-blue-200 transition-all flex items-center gap-2 justify-center">
                            <i class="bi bi-send"></i> Send to Client
                        </button>
                    </form>
                    <a href="{{ route('admin.proposals.print', $proposal->id) }}" class="block w-full px-4 py-2.5 bg-slate-100 text-slate-700 text-sm font-bold rounded-xl hover:bg-slate-200 transition-all flex items-center gap-2 justify-center">
                        <i class="bi bi-printer"></i> Print / PDF
                    </a>
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Proposal Details</h3>
                <div class="bg-slate-50 rounded-2xl p-4 space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-400">To</span>
                        <span class="font-medium text-slate-800">{{ $proposal->proposal_to }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Email</span>
                        <span class="font-medium text-slate-800">{{ $proposal->email ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Phone</span>
                        <span class="font-medium text-slate-800">{{ $proposal->phone ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Date</span>
                        <span class="font-medium text-slate-800">{{ \Carbon\Carbon::parse($proposal->date)->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Valid Till</span>
                        <span class="font-medium text-slate-800">{{ $proposal->open_till ? \Carbon\Carbon::parse($proposal->open_till)->format('M d, Y') : '-' }}</span>
                    </div>
                    @if($proposal->currency)
                    <div class="flex justify-between">
                        <span class="text-slate-400">Currency</span>
                        <span class="font-medium text-slate-800">{{ $proposal->currency->name ?? '-' }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Financial Summary</h3>
                <div class="bg-slate-50 rounded-2xl p-4 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Subtotal</span>
                        <span class="font-medium text-slate-800">{{ app_format_money($proposal->subtotal ?? 0, $proposal->currency) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Tax</span>
                        <span class="font-medium text-slate-800">{{ app_format_money($proposal->total_tax ?? 0, $proposal->currency) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Discount</span>
                        <span class="font-medium text-slate-800">-{{ app_format_money($proposal->discount_total ?? 0, $proposal->currency) }}</span>
                    </div>
                    <div class="flex justify-between border-t border-slate-200 pt-3">
                        <span class="font-bold text-slate-700">Total</span>
                        <span class="font-bold text-indigo-600">{{ app_format_money($proposal->total ?? 0, $proposal->currency) }}</span>
                    </div>
                </div>
            </div>

            @if($proposal->rel_type || $proposal->project_id || $proposal->estimate_id || $proposal->invoice_id)
            <div class="space-y-4">
                <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Related</h3>
                <div class="bg-slate-50 rounded-2xl p-4 space-y-3 text-sm">
                    @if($proposal->estimate_id)
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Estimate</span>
                        <a href="{{ route('admin.estimates.show', $proposal->estimate_id) }}" data-pane="show" data-pane-type="estimate" data-pane-id="{{ $proposal->estimate_id }}" class="font-medium text-blue-600 hover:text-blue-800">#{{ $proposal->estimate_id }}</a>
                    </div>
                    @endif
                    @if($proposal->invoice_id)
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Invoice</span>
                        <a href="{{ route('admin.invoices.show', $proposal->invoice_id) }}" class="font-medium text-blue-600 hover:text-blue-800">#{{ $proposal->invoice_id }}</a>
                    </div>
                    @endif
                    @if($proposal->rel_type && $proposal->rel_id)
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 capitalize">{{ $proposal->rel_type }}</span>
                        <a href="{{ $proposal->rel_type == 'lead' ? route('admin.leads.show', $proposal->rel_id) : '#' }}" class="font-medium text-blue-600 hover:text-blue-800">#{{ $proposal->rel_id }}</a>
                    </div>
                    @endif
                    @if($proposal->project_id)
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Project</span>
                        <a href="{{ route('admin.projects.show', $proposal->project_id) }}" class="font-medium text-blue-600 hover:text-blue-800">{{ optional($proposal->project)->name ?? '#' . $proposal->project_id }}</a>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('active', 'border-blue-500', 'text-blue-600');
                b.classList.add('text-slate-500', 'border-transparent');
            });
            btn.classList.add('active', 'border-blue-500', 'text-blue-600');
            btn.classList.remove('text-slate-500', 'border-transparent');

            document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
            document.getElementById('tab-' + btn.dataset.tab).classList.remove('hidden');
        });
    });
</script>
@endpush
