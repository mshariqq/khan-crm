@extends('layouts.admin')

@section('breadcrumb', 'Estimate Details')

@section('main')
<div class="h-[calc(100vh-64px)] flex flex-col">
    <div class="bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.estimates.index') }}" class="p-2 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-500 transition-all">
                <i class="bi bi-arrow-left text-lg"></i>
            </a>
            <div>
                <div class="flex items-center gap-2 mb-0.5">
                    <h1 class="text-xl font-bold text-slate-800">#{{ $estimate->prefix ?? '' }}{{ $estimate->number }}</h1>
                    @php
                        $statusMap = [
                            1 => ['label' => 'Draft', 'class' => 'bg-slate-100 text-slate-700'],
                            2 => ['label' => 'Sent', 'class' => 'bg-blue-100 text-blue-700'],
                            3 => ['label' => 'Accepted', 'class' => 'bg-emerald-100 text-emerald-700'],
                            4 => ['label' => 'Declined', 'class' => 'bg-red-100 text-red-700'],
                            5 => ['label' => 'Converted', 'class' => 'bg-purple-100 text-purple-700'],
                        ];
                        $s = $statusMap[$estimate->status] ?? ['label' => 'Unknown', 'class' => 'bg-slate-100 text-slate-600'];
                    @endphp
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider {{ $s['class'] }}">{{ $s['label'] }}</span>
                </div>
                <p class="text-sm text-slate-400">
                    {{ optional($estimate->client)->company ?? 'No Client' }} &middot; {{ \Carbon\Carbon::parse($estimate->datecreated)->format('M d, Y') }}
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if($estimate->total)
            <div class="text-right px-4 py-2 bg-amber-50 rounded-lg border border-amber-200">
                <p class="text-xs text-amber-600 font-medium">Total</p>
                <p class="text-lg font-bold text-amber-700">{{ app_format_money($estimate->total, $estimate->currency) }}</p>
            </div>
            @endif
            <div class="h-8 w-px bg-slate-200 mx-1"></div>
            <a href="{{ route('admin.estimates.edit', $estimate->id) }}" class="p-2 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-600 transition-all" title="Edit Estimate" data-pane="edit" data-pane-type="estimate" data-pane-id="{{ $estimate->id }}">
                <i class="bi bi-pencil-square"></i>
            </a>
            <form action="{{ route('admin.estimates.destroy', $estimate->id) }}" method="POST" onsubmit="return confirm('Delete this estimate?');" class="inline">
                @csrf @method('DELETE')
                <button type="submit" class="p-2 rounded-xl bg-red-50 hover:bg-red-100 text-red-500 transition-all" title="Delete Estimate">
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
                        <button class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 border-b-2 border-transparent whitespace-nowrap" data-tab="items">Items ({{ $estimate->items->count() }})</button>
                        <button class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 border-b-2 border-transparent whitespace-nowrap" data-tab="notes">Notes ({{ $estimate->notes->count() }})</button>
                        <button class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 border-b-2 border-transparent whitespace-nowrap" data-tab="reminders">Reminders ({{ $estimate->reminders->count() }})</button>
                    </div>

                    <div class="p-6">
                        {{-- Overview Tab --}}
                        <div id="tab-overview" class="tab-content">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="bg-slate-50 rounded-xl p-5">
                                    <h3 class="text-sm font-semibold text-slate-700 mb-3">Estimate Details</h3>
                                    <div class="space-y-3 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-slate-500">Estimate #</span>
                                            <span class="text-slate-800 font-medium">{{ $estimate->prefix ?? '' }}{{ $estimate->number }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-slate-500">Date</span>
                                            <span class="text-slate-800">{{ \Carbon\Carbon::parse($estimate->date)->format('M d, Y') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-slate-500">Expiry Date</span>
                                            <span class="text-slate-800">{{ $estimate->expirydate ? \Carbon\Carbon::parse($estimate->expirydate)->format('M d, Y') : '-' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-slate-500">Created By</span>
                                            <span class="text-slate-800">{{ optional($estimate->creator)->firstname ?? '-' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-slate-500">Reference</span>
                                            <span class="text-slate-800">{{ $estimate->reference_no ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-slate-50 rounded-xl p-5">
                                    <h3 class="text-sm font-semibold text-slate-700 mb-3">Financial</h3>
                                    <div class="space-y-3 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-slate-500">Subtotal</span>
                                            <span class="text-slate-800 font-medium">{{ app_format_money($estimate->subtotal ?? 0, $estimate->currency) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-slate-500">Tax</span>
                                            <span class="text-slate-800">{{ app_format_money($estimate->total_tax ?? 0, $estimate->currency) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-slate-500">Discount</span>
                                            <span class="text-slate-800">{{ app_format_money($estimate->discount_total ?? 0, $estimate->currency) }}</span>
                                        </div>
                                        <div class="flex justify-between border-t border-slate-200 pt-3">
                                            <span class="text-slate-600 font-medium">Total</span>
                                            <span class="text-slate-800 font-bold">{{ app_format_money($estimate->total ?? 0, $estimate->currency) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if($estimate->adminnote || $estimate->clientnote)
                            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if($estimate->adminnote)
                                <div class="bg-slate-50 rounded-xl p-5">
                                    <h3 class="text-sm font-semibold text-slate-700 mb-2">Admin Note</h3>
                                    <p class="text-sm text-slate-600">{{ $estimate->adminnote }}</p>
                                </div>
                                @endif
                                @if($estimate->clientnote)
                                <div class="bg-slate-50 rounded-xl p-5">
                                    <h3 class="text-sm font-semibold text-slate-700 mb-2">Client Note</h3>
                                    <p class="text-sm text-slate-600">{{ $estimate->clientnote }}</p>
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>

                        {{-- Items Tab --}}
                        <div id="tab-items" class="tab-content hidden">
                            @if($estimate->items->count() > 0)
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
                                    @foreach($estimate->items as $item)
                                    <tr>
                                        <td class="px-3 py-3 text-slate-700">{{ $item->description ?? 'Item' }}</td>
                                        <td class="px-3 py-3 text-center text-slate-700">{{ $item->qty ?? 1 }}</td>
                                        <td class="px-3 py-3 text-right text-slate-700">{{ app_format_money($item->rate ?? 0, $estimate->currency) }}</td>
                                        <td class="px-3 py-3 text-right font-medium text-slate-800">{{ app_format_money(($item->qty ?? 1) * ($item->rate ?? 0), $estimate->currency) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @else
                            <p class="text-sm text-slate-400 text-center py-8">No items on this estimate.</p>
                            @endif
                        </div>

                        {{-- Notes Tab --}}
                        <div id="tab-notes" class="tab-content hidden space-y-4">
                            <form action="{{ route('admin.estimates.store_note', $estimate->id) }}" method="POST" class="bg-slate-50 rounded-xl p-4">
                                @csrf
                                <div class="flex gap-2">
                                    <input type="text" name="content" placeholder="Add a note..." class="flex-1 px-3 py-2 border border-slate-200 rounded-lg text-sm" required>
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Add</button>
                                </div>
                            </form>
                            <div class="space-y-3">
                                @forelse($estimate->notes as $note)
                                <div class="bg-slate-50 rounded-xl p-4 flex justify-between items-start">
                                    <div>
                                        <p class="text-sm text-slate-700">{{ $note->description }}</p>
                                        <p class="text-xs text-slate-400 mt-1">{{ optional($note->created_at)->format('M d, Y H:i') ?? '' }}</p>
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
                            <form action="{{ route('admin.estimates.store_reminder', $estimate->id) }}" method="POST" class="bg-slate-50 rounded-xl p-4">
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
                                @forelse($estimate->reminders as $reminder)
                                <div class="bg-slate-50 rounded-xl p-4 flex justify-between items-center">
                                    <div>
                                        <p class="text-sm text-slate-700">{{ $reminder->description ?? '' }}</p>
                                        <p class="text-xs text-slate-400">{{ $reminder->date ? \Carbon\Carbon::parse($reminder->date)->format('M d, Y H:i') : '-' }}</p>
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
                <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Estimate Status</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 ml-1">Status</label>
                        <div class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full {{ $estimate->status == 3 ? 'bg-emerald-500' : ($estimate->status == 4 ? 'bg-red-500' : ($estimate->status == 5 ? 'bg-purple-500' : ($estimate->status == 2 ? 'bg-blue-500' : 'bg-slate-400'))) }}"></span>
                            <span class="text-sm font-bold text-slate-700">{{ $s['label'] }}</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 ml-1">Sale Agent</label>
                        <div class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 text-[10px] font-bold">
                                {{ strtoupper(substr(optional($estimate->saleAgent)->firstname ?? 'U', 0, 1)) }}
                            </div>
                            <span class="text-sm font-bold text-slate-700">{{ optional($estimate->saleAgent)->firstname ?? 'Unassigned' }}</span>
                        </div>
                    </div>
                </div>
                <div class="space-y-2 pt-2">
                    @if(in_array($estimate->status, [1, 2]))
                    <form action="{{ route('admin.estimates.mark_accepted', $estimate->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2.5 bg-emerald-600 text-white text-sm font-bold rounded-xl hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-200 flex items-center gap-2 justify-center">
                            <i class="bi bi-check-circle"></i> Mark Accepted
                        </button>
                    </form>
                    <form action="{{ route('admin.estimates.mark_declined', $estimate->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2.5 bg-red-500 text-white text-sm font-bold rounded-xl hover:bg-red-600 transition-all shadow-lg shadow-red-200 flex items-center gap-2 justify-center">
                            <i class="bi bi-x-circle"></i> Mark Declined
                        </button>
                    </form>
                    @if(!$estimate->invoiceid)
                    <form action="{{ route('admin.estimates.convert_to_invoice', $estimate->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2.5 bg-amber-600 text-white text-sm font-bold rounded-xl hover:bg-amber-700 transition-all shadow-lg shadow-amber-200 flex items-center gap-2 justify-center">
                            <i class="bi bi-receipt"></i> Convert to Invoice
                        </button>
                    </form>
                    @endif
                    @endif
                    <a href="{{ route('admin.estimates.print', $estimate->id) }}" target="_blank" class="w-full px-4 py-2.5 bg-slate-100 text-slate-700 text-sm font-bold rounded-xl hover:bg-slate-200 transition-all flex items-center gap-2 justify-center">
                        <i class="bi bi-printer"></i> Print / PDF
                    </a>
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Client</h3>
                <div class="bg-slate-50 rounded-2xl p-4 space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400">
                            <i class="bi bi-building text-xl"></i>
                        </div>
                        <div>
                            @if($estimate->client)
                            <a href="{{ route('admin.clients.show', $estimate->client->userid) }}" class="text-sm font-bold text-slate-800 hover:text-blue-600">{{ $estimate->client->company }}</a>
                            <p class="text-[11px] text-slate-500">{{ $estimate->client->email }}</p>
                            @else
                            <p class="text-sm text-slate-400">No client</p>
                            @endif
                        </div>
                    </div>
                    @if($estimate->client && ($estimate->client->phonenumber || $estimate->client->address))
                    <div class="pt-3 border-t border-slate-200/60 space-y-2">
                        @if($estimate->client->phonenumber)
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-medium text-slate-400">Phone</span>
                            <span class="text-[11px] font-bold text-slate-700">{{ $estimate->client->phonenumber }}</span>
                        </div>
                        @endif
                        @if($estimate->client->address)
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-medium text-slate-400">Address</span>
                            <span class="text-[11px] font-bold text-slate-700 text-right max-w-[180px]">{{ $estimate->client->address }}</span>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Details</h3>
                <div class="space-y-3 px-1">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-400">Estimate #</span>
                        <span class="text-xs font-bold text-slate-700">{{ $estimate->prefix ?? '' }}{{ $estimate->number }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-400">Date Created</span>
                        <span class="text-xs font-bold text-slate-700">{{ \Carbon\Carbon::parse($estimate->datecreated)->format('M d, Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-400">Expiry Date</span>
                        <span class="text-xs font-bold text-slate-700">{{ $estimate->expirydate ? \Carbon\Carbon::parse($estimate->expirydate)->format('M d, Y') : '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-400">Reference</span>
                        <span class="text-xs font-bold text-slate-700">{{ $estimate->reference_no ?? '-' }}</span>
                    </div>
                    @if($estimate->project_id)
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-400">Project</span>
                        <a href="{{ route('admin.projects.show', $estimate->project_id) }}" class="text-xs font-bold text-blue-600 hover:text-blue-800">{{ optional($estimate->project)->name ?? '#' . $estimate->project_id }}</a>
                    </div>
                    @endif
                </div>
            </div>
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
