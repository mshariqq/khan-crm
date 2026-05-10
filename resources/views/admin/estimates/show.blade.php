@extends('layouts.admin')

@section('breadcrumb', 'Estimate Details')

@section('main')
<div class="p-4 space-y-4">
    {{-- Compact Header --}}
    <div class="flex items-center justify-between bg-white rounded-xl border border-slate-200 p-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                <i class="bi bi-file-earmark-ruled"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-800">#{{ $estimate->number }}</h1>
                <p class="text-sm text-slate-500">{{ optional($estimate->client)->company ?? 'No Client' }} • {{ \Carbon\Carbon::parse($estimate->datecreated)->format('M d, Y') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if($estimate->total)
            <div class="text-right px-4 py-2 bg-amber-50 rounded-lg border border-amber-200">
                <p class="text-xs text-amber-600 font-medium">Total</p>
                <p class="text-lg font-bold text-amber-700">{{ $estimate->currency->symbol ?? '$' }}{{ number_format($estimate->total, 2) }}</p>
            </div>
            @endif
            @php
                $statusMap = [
                    1 => ['label' => 'Draft', 'class' => 'bg-slate-100 text-slate-700'],
                    2 => ['label' => 'Sent', 'class' => 'bg-blue-100 text-blue-700'],
                    3 => ['label' => 'Accepted', 'class' => 'bg-emerald-100 text-emerald-700'],
                    4 => ['label' => 'Declined', 'class' => 'bg-red-100 text-red-700'],
                ];
                $s = $statusMap[$estimate->status] ?? ['label' => 'Unknown', 'class' => 'bg-slate-100'];
            @endphp
            <span class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ $s['class'] }}">{{ $s['label'] }}</span>
            <a href="{{ route('admin.estimates.edit', $estimate->id) }}" class="p-2.5 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200">
                <i class="bi bi-pencil"></i>
            </a>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-4 gap-3">
        <div class="bg-white rounded-lg border border-slate-200 p-3 text-center">
            <p class="text-2xl font-bold text-slate-800">{{ $estimate->items->count() }}</p>
            <p class="text-xs text-slate-500">Items</p>
        </div>
        <div class="bg-white rounded-lg border border-slate-200 p-3 text-center">
            <p class="text-2xl font-bold text-slate-800">{{ $estimate->currency->symbol ?? '$' }}{{ number_format($estimate->subtotal ?? 0, 0) }}</p>
            <p class="text-xs text-slate-500">Subtotal</p>
        </div>
        <div class="bg-white rounded-lg border border-slate-200 p-3 text-center">
            <p class="text-2xl font-bold text-slate-800">{{ $estimate->currency->symbol ?? '$' }}{{ number_format($estimate->total_tax ?? 0, 0) }}</p>
            <p class="text-xs text-slate-500">Tax</p>
        </div>
        <div class="bg-white rounded-lg border border-slate-200 p-3 text-center">
            <p class="text-2xl font-bold text-slate-800">{{ number_format($estimate->discount_total ?? 0, 0) }}</p>
            <p class="text-xs text-slate-500">Discount</p>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
        <div class="lg:col-span-3 space-y-4">
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
<div class="flex border-b border-slate-200 overflow-x-auto">
                    <button class="tab-btn active px-4 py-3 text-sm font-medium border-b-2 border-blue-500 text-blue-600 whitespace-nowrap" data-tab="overview">Overview</button>
                    <button class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 border-b-2 border-transparent whitespace-nowrap" data-tab="items">Items ({{ $estimate->items->count() }})</button>
                    <button class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 border-b-2 border-transparent whitespace-nowrap" data-tab="notes">Notes ({{ $estimate->notes->count() }})</button>
                    <button class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 border-b-2 border-transparent whitespace-nowrap" data-tab="reminders">Reminders ({{ $estimate->reminders->count() }})</button>
                </div>

                <div class="p-4">
                    {{-- Overview Tab --}}
                    <div id="tab-overview" class="tab-content">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-slate-50 rounded-lg p-4">
                                <h3 class="text-sm font-semibold text-slate-700 mb-3">Estimate Details</h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Estimate #</span>
                                        <span class="text-slate-800 font-medium">{{ $estimate->prefix ?? '' }}{{ $estimate->number }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Date</span>
                                        <span class="text-slate-800">{{ \Carbon\Carbon::parse($estimate->datecreated)->format('M d, Y') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Expiry Date</span>
                                        <span class="text-slate-800">{{ $estimate->expirydate ? \Carbon\Carbon::parse($estimate->expirydate)->format('M d, Y') : '-' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Created By</span>
                                        <span class="text-slate-800">{{ optional($estimate->creator)->firstname ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-slate-50 rounded-lg p-4">
                                <h3 class="text-sm font-semibold text-slate-700 mb-3">Financial</h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Subtotal</span>
                                        <span class="text-slate-800">{{ $estimate->currency->symbol ?? '$' }}{{ number_format($estimate->subtotal ?? 0, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Tax</span>
                                        <span class="text-slate-800">{{ $estimate->currency->symbol ?? '$' }}{{ number_format($estimate->total_tax ?? 0, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Discount</span>
                                        <span class="text-slate-800">{{ $estimate->currency->symbol ?? '$' }}{{ number_format($estimate->discount_total ?? 0, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between border-t pt-2">
                                        <span class="text-slate-500 font-medium">Total</span>
                                        <span class="text-slate-800 font-bold">{{ $estimate->currency->symbol ?? '$' }}{{ number_format($estimate->total ?? 0, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Items Tab --}}
                    <div id="tab-items" class="tab-content hidden">
                        @if($estimate->items->count() > 0)
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
                                @foreach($estimate->items as $item)
                                <tr>
                                    <td class="px-3 py-2">{{ $item->description ?? 'Item' }}</td>
                                    <td class="px-3 py-2 text-center">{{ $item->qty ?? 1 }}</td>
                                    <td class="px-3 py-2 text-right">{{ $estimate->currency->symbol ?? '$' }}{{ number_format($item->rate ?? 0, 2) }}</td>
                                    <td class="px-3 py-2 text-right font-medium">{{ $estimate->currency->symbol ?? '$' }}{{ number_format(($item->qty ?? 1) * ($item->rate ?? 0), 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <p class="text-sm text-slate-400 text-center py-4">No items</p>
                        @endif
                    </div>

                    {{-- Notes Tab --}}
                    <div id="tab-notes" class="tab-content hidden">
                        <form action="{{ route('admin.estimates.store_note', $estimate->id) }}" method="POST" class="mb-4">
                            @csrf
                            <div class="flex gap-2">
                                <input type="text" name="content" placeholder="Add a note..." class="flex-1 px-3 py-2 border border-slate-200 rounded-lg text-sm" required>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Add</button>
                            </div>
                        </form>
                        @if($estimate->notes->count() > 0)
                        <div class="space-y-3">
                            @foreach($estimate->notes as $note)
                            <div class="p-3 bg-slate-50 rounded-lg">
                                <p class="text-sm text-slate-700">{{ $note->content }}</p>
                                <p class="text-xs text-slate-400 mt-1">{{ $note->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-sm text-slate-400 text-center py-4">No notes</p>
                        @endif
                    </div>

                    {{-- Reminders Tab --}}
                    <div id="tab-reminders" class="tab-content hidden">
                        <form action="{{ route('admin.estimates.store_reminder', $estimate->id) }}" method="POST" class="mb-4">
                            @csrf
                            <div class="grid grid-cols-2 gap-2 mb-2">
                                <input type="text" name="content" placeholder="Reminder content..." class="px-3 py-2 border border-slate-200 rounded-lg text-sm" required>
                                <input type="datetime-local" name="date" class="px-3 py-2 border border-slate-200 rounded-lg text-sm" required>
                            </div>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Add Reminder</button>
                        </form>
                        @if($estimate->reminders->count() > 0)
                        <div class="space-y-3">
                            @foreach($estimate->reminders as $reminder)
                            <div class="p-3 bg-slate-50 rounded-lg flex justify-between items-center">
                                <div>
                                    <p class="text-sm text-slate-700">{{ $reminder->content }}</p>
                                    <p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($reminder->date)->format('M d, Y H:i') }}</p>
                                </div>
                                <form action="{{ route('admin.reminders.destroy', $reminder->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700"><i class="bi bi-trash"></i></button>
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
                    @if(in_array($estimate->status, [1, 2]))
                    <form action="{{ route('admin.estimates.mark_accepted', $estimate->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-3 py-2 bg-emerald-600 text-white rounded-lg text-sm hover:bg-emerald-700 text-left flex items-center gap-2">
                            <i class="bi bi-check-circle"></i> Mark Accepted
                        </button>
                    </form>
                    <form action="{{ route('admin.estimates.mark_declined', $estimate->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-3 py-2 bg-red-500 text-white rounded-lg text-sm hover:bg-red-600 text-left flex items-center gap-2">
                            <i class="bi bi-x-circle"></i> Mark Declined
                        </button>
                    </form>
                    @if(!$estimate->invoice_id)
                    <form action="{{ route('admin.estimates.convert_to_invoice', $estimate->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-3 py-2 bg-amber-600 text-white rounded-lg text-sm hover:bg-amber-700 text-left flex items-center gap-2">
                            <i class="bi bi-receipt"></i> Convert to Invoice
                        </button>
                    </form>
                    @endif
                    @endif
                    <button onclick="window.print()" class="w-full px-3 py-2 bg-slate-100 text-slate-700 rounded-lg text-sm hover:bg-slate-200 text-left flex items-center gap-2">
                        <i class="bi bi-printer"></i> Print / PDF
                    </button>
                    <form action="{{ route('admin.estimates.destroy', $estimate->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete estimate?')" class="w-full px-3 py-2 bg-red-50 text-red-600 rounded-lg text-sm hover:bg-red-100 text-left flex items-center gap-2">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-4">
                <h3 class="text-sm font-semibold text-slate-700 mb-3">Client</h3>
                @if($estimate->client)
                <p class="font-medium text-slate-800">{{ $estimate->client->company }}</p>
                <p class="text-sm text-slate-500">{{ $estimate->client->email }}</p>
                @else
                <p class="text-sm text-slate-400">No client</p>
                @endif
            </div>

            <a href="{{ route('admin.estimates.index') }}" class="flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-100 text-slate-600 rounded-lg text-sm hover:bg-slate-200">
                <i class="bi bi-arrow-left"></i> Back to Estimates
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