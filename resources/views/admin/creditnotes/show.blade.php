@extends('layouts.admin')

@section('breadcrumb', 'Credit Note Details')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ $creditNote->prefix }}{{ str_pad($creditNote->number, 4, '0', STR_PAD_LEFT) }}</h1>
            <p class="text-slate-500 text-sm">Issued to {{ $creditNote->client->company ?? 'N/A' }} on {{ \Carbon\Carbon::parse($creditNote->date)->format('M j, Y') }}</p>
        </div>
        <div class="flex items-center gap-3">
            @can('credit_notes_edit')
            <a href="{{ route('admin.creditnotes.edit', $creditNote->id) }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50 transition-all flex items-center gap-2">
                <i class="bi bi-pencil-square"></i> Edit
            </a>
            @endcan
            <a href="{{ route('admin.creditnotes.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-300 transition-all flex items-center gap-2">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="px-4 py-2 bg-green-100 text-green-700 rounded-lg text-sm font-medium">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="px-4 py-2 bg-red-100 text-red-700 rounded-lg text-sm font-medium">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <!-- Items Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <h3 class="font-bold text-slate-800">Items</h3>
                </div>
                <table class="w-full text-left">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr class="text-[10px] uppercase tracking-wider font-bold text-slate-400">
                            <th class="px-6 py-3">Description</th>
                            <th class="px-6 py-3 text-center">Qty</th>
                            <th class="px-6 py-3 text-right">Rate</th>
                            <th class="px-6 py-3 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($creditNote->items as $item)
                        <tr>
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-slate-800">{{ $item->description }}</p>
                                @if($item->long_description)
                                    <p class="text-xs text-slate-500 mt-1">{{ $item->long_description }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-slate-600">{{ $item->qty }}</td>
                            <td class="px-6 py-4 text-right text-sm text-slate-600">{{ number_format($item->rate, 2) }}</td>
                            <td class="px-6 py-4 text-right text-sm font-bold text-slate-800">{{ number_format($item->amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50/50">
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-right text-sm font-bold text-slate-500">Subtotal</td>
                            <td class="px-6 py-3 text-right text-sm font-bold text-slate-800">{{ number_format($creditNote->subtotal, 2) }}</td>
                        </tr>
                        @if($creditNote->total_tax > 0)
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-right text-sm font-bold text-slate-500">Tax</td>
                            <td class="px-6 py-3 text-right text-sm font-bold text-slate-800">{{ number_format($creditNote->total_tax, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="bg-slate-100/50">
                            <td colspan="3" class="px-6 py-4 text-right text-sm font-bold text-slate-800">Total Amount</td>
                            <td class="px-6 py-4 text-right text-xl font-black text-blue-600">{{ $creditNote->currencyRel->symbol ?? '$' }}{{ number_format($creditNote->total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Applied Credits -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <h3 class="font-bold text-slate-800">Applied Credits</h3>
                </div>
                <table class="w-full text-left">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr class="text-[10px] uppercase tracking-wider font-bold text-slate-400">
                            <th class="px-6 py-3">Invoice #</th>
                            <th class="px-6 py-3">Date Applied</th>
                            <th class="px-6 py-3 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($creditNote->credits as $credit)
                        <tr>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.invoices.show', $credit->invoice_id) }}" class="text-sm font-bold text-blue-600">
                                    {{ $credit->invoice->prefix }}{{ $credit->invoice->number }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500">
                                {{ \Carbon\Carbon::parse($credit->date_applied)->format('M j, Y') }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-bold text-slate-800">
                                {{ number_format($credit->amount, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-slate-400 text-sm italic">No credits applied to any invoice yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Summary Info -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-6 uppercase tracking-wider text-center border-b pb-4">Credit Summary</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-slate-50 rounded-xl">
                        <span class="text-xs font-bold text-slate-500 uppercase">Total Credit</span>
                        <span class="text-sm font-black text-slate-800">{{ number_format($creditNote->total, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-xl">
                        <span class="text-xs font-bold text-blue-500 uppercase">Total Applied</span>
                        <span class="text-sm font-black text-blue-700">{{ number_format($creditNote->totalApplied(), 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-emerald-50 rounded-xl border border-emerald-100">
                        <span class="text-xs font-bold text-emerald-600 uppercase">Credit Remaining</span>
                        <span class="text-lg font-black text-emerald-700">{{ number_format($creditNote->remainingCredit(), 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Apply to Invoice Form -->
            @if($creditNote->remainingCredit() > 0 && $availableInvoices->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-sm font-black text-slate-800 mb-4 uppercase tracking-widest border-b pb-2">Apply Credit to Invoice</h3>
                <form action="{{ route('admin.creditnotes.apply', $creditNote->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-500 uppercase">Select Invoice</label>
                        <select name="invoice_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all" required>
                            @foreach($availableInvoices as $inv)
                                <option value="{{ $inv->id }}">{{ $inv->prefix }}{{ $inv->number }} (Balance: {{ number_format($inv->amountDue(), 2) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-500 uppercase">Amount to Apply</label>
                        <input type="number" step="0.01" name="amount" max="{{ min($creditNote->remainingCredit(), $availableInvoices->first()->amountDue()) }}" value="{{ min($creditNote->remainingCredit(), $availableInvoices->first()->amountDue()) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all" required>
                    </div>
                    <button type="submit" class="w-full py-3 bg-blue-600 text-white font-black text-xs uppercase tracking-widest rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-500/20 transition-all">
                        Apply Credit
                    </button>
                </form>
            </div>
            @elseif($creditNote->remainingCredit() <= 0)
            <div class="bg-emerald-50 rounded-2xl p-6 border border-emerald-100 text-center">
                <i class="bi bi-check-circle-fill text-emerald-500 text-2xl mb-2"></i>
                <p class="text-sm font-bold text-emerald-800 uppercase tracking-wider">Fully Applied</p>
                <p class="text-[10px] text-emerald-600 mt-1 uppercase font-medium">This credit note has been completely used.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
