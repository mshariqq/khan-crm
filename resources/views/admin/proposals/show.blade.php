@extends('layouts.admin')

@section('breadcrumb', 'Proposal Details')

@section('main')
<div class="p-4 space-y-4">
    {{-- Compact Header --}}
    <div class="flex items-center justify-between bg-white rounded-xl border border-slate-200 p-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                <i class="bi bi-file-earmark-text"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-800">{{ $proposal->subject }}</h1>
                <p class="text-sm text-slate-500">{{ $proposal->proposal_to }} • {{ \Carbon\Carbon::parse($proposal->datecreated)->format('M d, Y') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if($proposal->total)
            <div class="text-right px-4 py-2 bg-indigo-50 rounded-lg border border-indigo-200">
                <p class="text-xs text-indigo-600 font-medium">Total</p>
                <p class="text-lg font-bold text-indigo-700">{{ $proposal->currency->symbol ?? '$' }}{{ number_format($proposal->total, 2) }}</p>
            </div>
            @endif
            @php
                $pStatusMap = [
                    1 => ['label' => 'Open', 'class' => 'bg-blue-100 text-blue-700'],
                    2 => ['label' => 'Accepted', 'class' => 'bg-emerald-100 text-emerald-700'],
                    3 => ['label' => 'Declined', 'class' => 'bg-red-100 text-red-700'],
                ];
                $ps = $pStatusMap[$proposal->status] ?? ['label' => 'Unknown', 'class' => 'bg-slate-100'];
            @endphp
            <span class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ $ps['class'] }}">{{ $ps['label'] }}</span>
            <a href="{{ route('admin.proposals.edit', $proposal->id) }}" class="p-2.5 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200">
                <i class="bi bi-pencil"></i>
            </a>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-4 gap-3">
        <div class="bg-white rounded-lg border border-slate-200 p-3 text-center">
            <p class="text-2xl font-bold text-slate-800">{{ $proposal->items->count() }}</p>
            <p class="text-xs text-slate-500">Items</p>
        </div>
        <div class="bg-white rounded-lg border border-slate-200 p-3 text-center">
            <p class="text-2xl font-bold text-slate-800">{{ $proposal->comments->count() }}</p>
            <p class="text-xs text-slate-500">Comments</p>
        </div>
        <div class="bg-white rounded-lg border border-slate-200 p-3 text-center">
            <p class="text-2xl font-bold text-slate-800">{{ $proposal->notes->count() }}</p>
            <p class="text-xs text-slate-500">Notes</p>
        </div>
        <div class="bg-white rounded-lg border border-slate-200 p-3 text-center">
            <p class="text-2xl font-bold text-slate-800">{{ $proposal->allow_comments ? 'Yes' : 'No' }}</p>
            <p class="text-xs text-slate-500">Comments Allowed</p>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
        <div class="lg:col-span-3 space-y-4">
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="flex border-b border-slate-200 overflow-x-auto">
                    <button class="tab-btn active px-4 py-3 text-sm font-medium border-b-2 border-blue-500 text-blue-600 whitespace-nowrap" data-tab="overview">Overview</button>
                    <button class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 border-b-2 border-transparent whitespace-nowrap" data-tab="items">Items ({{ $proposal->items->count() }})</button>
                    <button class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 border-b-2 border-transparent whitespace-nowrap" data-tab="comments">Comments ({{ $proposal->comments->count() }})</button>
                    <button class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 border-b-2 border-transparent whitespace-nowrap" data-tab="notes">Notes ({{ $proposal->notes->count() }})</button>
                    <button class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 border-b-2 border-transparent whitespace-nowrap" data-tab="reminders">Reminders ({{ $proposal->reminders->count() }})</button>
                </div>

                <div class="p-4">
                    {{-- Overview Tab --}}
                    <div id="tab-overview" class="tab-content">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-slate-50 rounded-lg p-4">
                                <h3 class="text-sm font-semibold text-slate-700 mb-3">Proposal Details</h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Proposal To</span>
                                        <span class="text-slate-800">{{ $proposal->proposal_to }}</span>
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
                                </div>
                            </div>
                            <div class="bg-slate-50 rounded-lg p-4">
                                <h3 class="text-sm font-semibold text-slate-700 mb-3">Financial</h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Subtotal</span>
                                        <span class="text-slate-800">{{ $proposal->currency->symbol ?? '$' }}{{ number_format($proposal->subtotal ?? 0, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Tax</span>
                                        <span class="text-slate-800">{{ $proposal->currency->symbol ?? '$' }}{{ number_format($proposal->total_tax ?? 0, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Discount</span>
                                        <span class="text-slate-800">{{ $proposal->currency->symbol ?? '$' }}{{ number_format($proposal->discount_total ?? 0, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between border-t pt-2">
                                        <span class="text-slate-500 font-medium">Total</span>
                                        <span class="text-slate-800 font-bold">{{ $proposal->currency->symbol ?? '$' }}{{ number_format($proposal->total ?? 0, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if($proposal->content)
                        <div class="mt-4 bg-slate-50 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-slate-700 mb-2">Content</h3>
                            <p class="text-sm text-slate-600 whitespace-pre-wrap">{{ $proposal->content }}</p>
                        </div>
                        @endif
                    </div>

                    {{-- Items Tab --}}
                    <div id="tab-items" class="tab-content hidden">
                        @if($proposal->items->count() > 0)
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-slate-500">Item</th>
                                    <th class="px-3 py-2 text-center text-slate-500">Qty</th>
                                    <th class="px-3 py-2 text-right text-slate-500">Rate</th>
                                    <th class="px-3 py-2 text-right text-slate-500">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($proposal->items as $item)
                                <tr>
                                    <td class="px-3 py-2">{{ $item->description }}</td>
                                    <td class="px-3 py-2 text-center">{{ $item->qty }}</td>
                                    <td class="px-3 py-2 text-right">{{ $proposal->currency->symbol ?? '$' }}{{ number_format($item->rate, 2) }}</td>
                                    <td class="px-3 py-2 text-right font-medium">{{ $proposal->currency->symbol ?? '$' }}{{ number_format($item->amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <p class="text-sm text-slate-400 text-center py-4">No items</p>
                        @endif
                    </div>

                    {{-- Comments Tab --}}
                    <div id="tab-comments" class="tab-content hidden">
                        <form action="{{ route('admin.proposals.store_comment', $proposal->id) }}" method="POST" class="mb-4">
                            @csrf
                            <textarea name="content" rows="2" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm" placeholder="Add a comment..." required></textarea>
                            <button type="submit" class="mt-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Add Comment</button>
                        </form>
                        @if($proposal->comments->count() > 0)
                        <div class="space-y-2">
                            @foreach($proposal->comments as $comment)
                            <div class="bg-slate-50 rounded-lg p-3 flex justify-between items-start">
                                <div>
                                    <p class="text-sm text-slate-700">{{ $comment->content }}</p>
                                    <p class="text-xs text-slate-400 mt-1">{{ \Carbon\Carbon::parse($comment->datecreated)->format('M d, Y H:i') }}</p>
                                </div>
                                <form action="{{ route('admin.proposal_comments.destroy', $comment->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs">Delete</button>
                                </form>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-sm text-slate-400 text-center py-4">No comments</p>
                        @endif
                    </div>

                    {{-- Notes Tab --}}
                    <div id="tab-notes" class="tab-content hidden">
                        <form action="{{ route('admin.proposals.store_note', $proposal->id) }}" method="POST" class="mb-4">
                            @csrf
                            <div class="flex gap-2">
                                <input type="text" name="content" class="flex-1 px-3 py-2 border border-slate-200 rounded-lg text-sm" placeholder="Add a note..." required>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Add</button>
                            </div>
                        </form>
                        @if($proposal->notes->count() > 0)
                        <div class="space-y-2">
                            @foreach($proposal->notes as $note)
                            <div class="bg-slate-50 rounded-lg p-3 flex justify-between items-start">
                                <div>
                                    <p class="text-sm text-slate-700">{{ $note->content }}</p>
                                    <p class="text-xs text-slate-400 mt-1">{{ \Carbon\Carbon::parse($note->dateadded)->format('M d, Y H:i') }}</p>
                                </div>
                                <form action="{{ route('admin.notes.destroy', $note->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs">Delete</button>
                                </form>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-sm text-slate-400 text-center py-4">No notes</p>
                        @endif
                    </div>

                    {{-- Reminders Tab --}}
                    <div id="tab-reminders" class="tab-content hidden">
                        <form action="{{ route('admin.proposals.store_reminder', $proposal->id) }}" method="POST" class="mb-4">
                            @csrf
                            <div class="grid grid-cols-2 gap-2 mb-2">
                                <input type="text" name="content" placeholder="Reminder content..." class="px-3 py-2 border border-slate-200 rounded-lg text-sm" required>
                                <input type="datetime-local" name="date" class="px-3 py-2 border border-slate-200 rounded-lg text-sm" required>
                            </div>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Add Reminder</button>
                        </form>
                        @if($proposal->reminders->count() > 0)
                        <div class="space-y-3">
                            @foreach($proposal->reminders as $reminder)
                            <div class="p-3 bg-slate-50 rounded-lg flex justify-between items-center">
                                <div>
                                    <p class="text-sm text-slate-700">{{ $reminder->description }}</p>
                                    <p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($reminder->date)->format('M d, Y H:i') }}</p>
                                </div>
                                <form action="{{ route('admin.reminders.destroy', $reminder->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs">Delete</button>
                                </form>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-sm text-slate-400 text-center py-4">No reminders</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Sidebar --}}
        <div class="space-y-4">
            <div class="bg-white rounded-xl border border-slate-200 p-4">
                <h3 class="text-sm font-semibold text-slate-700 mb-3">Actions</h3>
                <div class="space-y-2">
                    @if($proposal->status == 1)
                    <form action="{{ route('admin.proposals.mark_accepted', $proposal->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-3 py-2 bg-emerald-600 text-white rounded-lg text-sm hover:bg-emerald-700 text-left flex items-center gap-2">
                            <i class="bi bi-check-circle"></i> Mark Accepted
                        </button>
                    </form>
                    <form action="{{ route('admin.proposals.mark_declined', $proposal->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-3 py-2 bg-red-500 text-white rounded-lg text-sm hover:bg-red-600 text-left flex items-center gap-2">
                            <i class="bi bi-x-circle"></i> Mark Declined
                        </button>
                    </form>
                    @endif
                    <form action="{{ route('admin.proposals.send', $proposal->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-3 py-2 bg-blue-100 text-blue-700 rounded-lg text-sm hover:bg-blue-200 text-left flex items-center gap-2">
                            <i class="bi bi-send"></i> Send to Client
                        </button>
                    </form>
                    <button onclick="window.print()" class="w-full px-3 py-2 bg-slate-100 text-slate-700 rounded-lg text-sm hover:bg-slate-200 text-left flex items-center gap-2">
                        <i class="bi bi-printer"></i> Print / PDF
                    </button>
                    <form action="{{ route('admin.proposals.destroy', $proposal->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete proposal?')" class="w-full px-3 py-2 bg-red-50 text-red-600 rounded-lg text-sm hover:bg-red-100 text-left flex items-center gap-2">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-4">
                <h3 class="text-sm font-semibold text-slate-700 mb-3">Related</h3>
                <div class="space-y-2 text-sm">
                    @if($proposal->rel_id && $proposal->rel_type)
                    <div>
                        <span class="text-slate-500">Linked to</span>
                        <p class="text-slate-800 capitalize">{{ $proposal->rel_type }} #{{ $proposal->rel_id }}</p>
                    </div>
                    @endif
                    @if($proposal->project_id)
                    <div>
                        <span class="text-slate-500">Project</span>
                        <p class="text-slate-800">{{ optional($proposal->project)->name ?? '-' }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <a href="{{ route('admin.proposals.index') }}" class="flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-100 text-slate-600 rounded-lg text-sm hover:bg-slate-200">
                <i class="bi bi-arrow-left"></i> Back to Proposals
            </a>
        </div>
    </div>
</div>

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
@endsection