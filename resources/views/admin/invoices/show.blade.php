@extends('layouts.admin')

@section('breadcrumb', 'Invoice Details')

@section('main')
<div class="p-4 space-y-4">
    {{-- Compact Header --}}
    <div class="flex items-center justify-between bg-white rounded-xl border border-slate-200 p-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                <i class="bi bi-receipt"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-800">#{{ $invoice->prefix ?? '' }}{{ $invoice->number }}</h1>
                <p class="text-sm text-slate-500">{{ optional($invoice->client)->company ?? 'No Client' }} • {{ \Carbon\Carbon::parse($invoice->date)->format('M d, Y') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div class="text-right px-4 py-2 bg-emerald-50 rounded-lg border border-emerald-200">
                <p class="text-xs text-emerald-600 font-medium">Total</p>
                <p class="text-lg font-bold text-emerald-700">{{ $invoice->currencyRel->symbol ?? '$' }}{{ number_format($invoice->total, 2) }}</p>
            </div>
            @php
                $statusMap = [
                    1 => ['label' => 'Unpaid', 'class' => 'bg-red-100 text-red-700'],
                    2 => ['label' => 'Partial', 'class' => 'bg-blue-100 text-blue-700'],
                    3 => ['label' => 'Paid', 'class' => 'bg-emerald-100 text-emerald-700'],
                    4 => ['label' => 'Cancelled', 'class' => 'bg-slate-100 text-slate-600'],
                ];
                $s = $statusMap[$invoice->status] ?? ['label' => 'Unknown', 'class' => 'bg-gray-100'];
            @endphp
            <span class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ $s['class'] }}">{{ $s['label'] }}</span>
            <a href="{{ route('admin.invoices.edit', $invoice->id) }}" class="p-2.5 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200">
                <i class="bi bi-pencil"></i>
            </a>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-4 gap-3">
        <div class="bg-white rounded-lg border border-slate-200 p-3 text-center">
            <p class="text-2xl font-bold text-slate-800">{{ $invoice->items->count() }}</p>
            <p class="text-xs text-slate-500">Items</p>
        </div>
        <div class="bg-white rounded-lg border border-slate-200 p-3 text-center">
            <p class="text-2xl font-bold text-slate-800">{{ number_format($invoice->totalPaid(), 0) }}</p>
            <p class="text-xs text-slate-500">Paid</p>
        </div>
        <div class="bg-white rounded-lg border border-slate-200 p-3 text-center">
            <p class="text-2xl font-bold text-red-600">{{ number_format($invoice->amountDue(), 0) }}</p>
            <p class="text-xs text-slate-500">Due</p>
        </div>
        <div class="bg-white rounded-lg border border-slate-200 p-3 text-center">
            <p class="text-2xl font-bold text-slate-800">{{ $invoice->notesList()->count() }}</p>
            <p class="text-xs text-slate-500">Notes</p>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
        <div class="lg:col-span-3 space-y-4">
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="flex border-b border-slate-200 overflow-x-auto">
                    <button class="tab-btn active px-4 py-3 text-sm font-medium border-b-2 border-blue-500 text-blue-600 whitespace-nowrap" data-tab="overview">Overview</button>
                    <button class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 border-b-2 border-transparent whitespace-nowrap" data-tab="items">Items ({{ $invoice->items->count() }})</button>
                    <button class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 border-b-2 border-transparent whitespace-nowrap" data-tab="payments">Payments ({{ $invoice->payments()->count() }})</button>
                    <button class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 border-b-2 border-transparent whitespace-nowrap" data-tab="credits">Credits</button>
                    <button class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 border-b-2 border-transparent whitespace-nowrap" data-tab="notes">Notes ({{ $invoice->notesList()->count() }})</button>
                    <button class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 border-b-2 border-transparent whitespace-nowrap" data-tab="reminders">Reminders ({{ $invoice->remindersList()->count() }})</button>
                </div>

                <div class="p-4">
                    {{-- Overview Tab --}}
                    <div id="tab-overview" class="tab-content">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-slate-50 rounded-lg p-4">
                                <h3 class="text-sm font-semibold text-slate-700 mb-3">From</h3>
                                <div class="space-y-1 text-sm text-slate-600">
                                    <p class="font-bold text-slate-800">{{ $global_settings['companyname'] ?? 'CRM System' }}</p>
                                    @if(!empty($global_settings['companyaddress']))
                                        <p>{!! nl2br(e($global_settings['companyaddress'])) !!}</p>
                                    @endif
                                    @if(!empty($global_settings['companyphone']))
                                        <p><i class="bi bi-telephone mr-1"></i> {{ $global_settings['companyphone'] }}</p>
                                    @endif
                                    @if(!empty($global_settings['company_email']))
                                        <p><i class="bi bi-envelope mr-1"></i> {{ $global_settings['company_email'] }}</p>
                                    @endif
                                    @if(!empty($global_settings['company_vat']))
                                        <p class="mt-2 text-xs uppercase tracking-wider text-slate-400">VAT: {{ $global_settings['company_vat'] }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="bg-slate-50 rounded-lg p-4">
                                <h3 class="text-sm font-semibold text-slate-700 mb-3">Bill To</h3>
                                <div class="space-y-1 text-sm text-slate-600">
                                    <p class="font-bold text-slate-800">{{ optional($invoice->client)->company ?? 'No Client' }}</p>
                                    <p>{{ $invoice->billing_street ?? optional($invoice->client)->address }}</p>
                                    <p>{{ $invoice->billing_city ?? optional($invoice->client)->city }}, {{ $invoice->billing_state ?? optional($invoice->client)->state }} {{ $invoice->billing_zip ?? optional($invoice->client)->zip }}</p>
                                    @if(optional($invoice->client)->phonenumber)
                                        <p><i class="bi bi-telephone mr-1"></i> {{ $invoice->client->phonenumber }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="bg-slate-50 rounded-lg p-4">
                                <h3 class="text-sm font-semibold text-slate-700 mb-3">Invoice Details</h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Invoice #</span>
                                        <span class="text-slate-800 font-medium">{{ $invoice->prefix ?? '' }}{{ $invoice->number }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Date</span>
                                        <span class="text-slate-800">{{ \Carbon\Carbon::parse($invoice->date)->format('M d, Y') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Due Date</span>
                                        <span class="text-slate-800">{{ $invoice->duedate ? \Carbon\Carbon::parse($invoice->duedate)->format('M d, Y') : '-' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Sent</span>
                                        <span class="text-slate-800">{{ $invoice->sent ? 'Yes' : 'No' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Created By</span>
                                        <span class="text-slate-800">{{ optional($invoice->creator)->firstname ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-slate-50 rounded-lg p-4">
                                <h3 class="text-sm font-semibold text-slate-700 mb-3">Financial</h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Subtotal</span>
                                        <span class="text-slate-800">{{ $invoice->currencyRel->symbol ?? '$' }}{{ number_format($invoice->subtotal, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Tax</span>
                                        <span class="text-slate-800">{{ $invoice->currencyRel->symbol ?? '$' }}{{ number_format($invoice->total_tax, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Discount</span>
                                        <span class="text-slate-800">{{ $invoice->currencyRel->symbol ?? '$' }}{{ number_format($invoice->discount_total ?? 0, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between border-t pt-2">
                                        <span class="text-slate-500 font-medium">Total</span>
                                        <span class="text-slate-800 font-bold">{{ $invoice->currencyRel->symbol ?? '$' }}{{ number_format($invoice->total, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-slate-500">Paid</span>
                                        <span class="font-bold text-emerald-600">{{ $invoice->currencyRel->symbol ?? '$' }}{{ number_format($invoice->totalPaid(), 2) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-slate-500">Credits Applied</span>
                                        <span class="font-bold text-blue-600">{{ $invoice->currencyRel->symbol ?? '$' }}{{ number_format($invoice->totalCredits(), 2) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center p-3 bg-slate-50 rounded-xl mt-4">
                                        <span class="text-sm font-bold text-slate-700">Amount Due</span>
                                        <span class="text-lg font-black text-rose-600">{{ $invoice->currencyRel->symbol ?? '$' }}{{ number_format($invoice->amountDue(), 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if($invoice->clientnote || $invoice->adminnote)
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if($invoice->clientnote)
                            <div class="bg-blue-50 rounded-lg p-4 border-l-3 border-blue-400">
                                <h3 class="text-sm font-semibold text-slate-700 mb-2">Client Note</h3>
                                <p class="text-sm text-slate-600">{{ $invoice->clientnote }}</p>
                            </div>
                            @endif
                            @if($invoice->adminnote)
                            <div class="bg-amber-50 rounded-lg p-4 border-l-3 border-amber-400">
                                <h3 class="text-sm font-semibold text-slate-700 mb-2">Internal Note</h3>
                                <p class="text-sm text-slate-600">{{ $invoice->adminnote }}</p>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>

                    {{-- Items Tab --}}
                    <div id="tab-items" class="tab-content hidden">
                        @if($invoice->items->count() > 0)
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-slate-500">Item</th>
                                    <th class="px-3 py-2 text-center text-slate-500">Qty</th>
                                    <th class="px-3 py-2 text-right text-slate-500">Rate</th>
                                    <th class="px-3 py-2 text-right text-slate-500">Tax</th>
                                    <th class="px-3 py-2 text-right text-slate-500">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($invoice->items as $item)
                                <tr>
                                    <td class="px-3 py-2">
                                        <p class="font-medium text-slate-800">{{ $item->description }}</p>
                                    </td>
                                    <td class="px-3 py-2 text-center">{{ $item->qty }}</td>
                                    <td class="px-3 py-2 text-right">{{ $invoice->currency->symbol ?? '$' }}{{ number_format($item->rate, 2) }}</td>
                                    <td class="px-3 py-2 text-right">{{ $invoice->currency->symbol ?? '$' }}{{ number_format($item->tax_rate, 2) }}</td>
                                    <td class="px-3 py-2 text-right font-medium">{{ $invoice->currency->symbol ?? '$' }}{{ number_format($item->amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <p class="text-sm text-slate-400 text-center py-4">No items</p>
                        @endif
                    </div>

                    {{-- Payments Tab --}}
                    <div id="tab-payments" class="tab-content hidden">
                        @php
                            $allPayments = $invoice->payments()->orderBy('date', 'desc')->get();
                            $totalPaid = $allPayments->sum('amount');
                            $paymentCount = $allPayments->count();
                            $lastPayment = $allPayments->first();
                            $modeNames = $paymentModes->pluck('name', 'id');
                        @endphp

                        {{-- Summary Cards --}}
                        <div class="grid grid-cols-3 gap-3 mb-4">
                            <div class="bg-emerald-50 rounded-xl border border-emerald-200 p-4 text-center">
                                <p class="text-2xl font-black text-emerald-700">{{ $invoice->currencyRel->symbol ?? '$' }}{{ number_format($totalPaid, 2) }}</p>
                                <p class="text-xs font-medium text-emerald-600 mt-1">Total Paid</p>
                            </div>
                            <div class="bg-blue-50 rounded-xl border border-blue-200 p-4 text-center">
                                <p class="text-2xl font-black text-blue-700">{{ $paymentCount }}</p>
                                <p class="text-xs font-medium text-blue-600 mt-1">Payments</p>
                            </div>
                            <div class="bg-slate-50 rounded-xl border border-slate-200 p-4 text-center">
                                <p class="text-2xl font-black text-slate-700">{{ $lastPayment ? \Carbon\Carbon::parse($lastPayment->date)->format('M d, Y') : '-' }}</p>
                                <p class="text-xs font-medium text-slate-600 mt-1">Last Payment</p>
                            </div>
                        </div>

                        {{-- Add Payment Form --}}
                        <form action="{{ route('admin.invoices.payments.store', $invoice->id) }}" method="POST" class="mb-4 p-4 bg-gradient-to-r from-emerald-50 to-emerald-100/50 rounded-xl border border-emerald-200">
                            @csrf
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-7 h-7 rounded-lg bg-emerald-500 flex items-center justify-center text-white">
                                    <i class="bi bi-plus-lg text-xs"></i>
                                </div>
                                <h4 class="text-sm font-bold text-emerald-800">Record Payment</h4>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
                                <div>
                                    <label class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block mb-1">Amount</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-emerald-500 text-sm font-medium">{{ $invoice->currencyRel->symbol ?? '$' }}</span>
                                        <input type="number" step="0.01" name="amount" class="w-full pl-8 pr-3 py-2 rounded-lg border border-emerald-300 text-sm font-bold focus:ring-2 focus:ring-emerald-500 outline-none" placeholder="0.00" required>
                                    </div>
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block mb-1">Mode</label>
                                    <select name="paymentmode" class="w-full px-3 py-2 rounded-lg border border-emerald-300 text-sm focus:ring-2 focus:ring-emerald-500 outline-none" required>
                                        <option value="">Select...</option>
                                        @foreach($paymentModes as $mode)
                                            <option value="{{ $mode->id }}">{{ $mode->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block mb-1">Date</label>
                                    <input type="date" name="date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-lg border border-emerald-300 text-sm focus:ring-2 focus:ring-emerald-500 outline-none" required>
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block mb-1">Transaction ID</label>
                                    <input type="text" name="transactionid" class="w-full px-3 py-2 rounded-lg border border-emerald-300 text-sm focus:ring-2 focus:ring-emerald-500 outline-none" placeholder="Optional">
                                </div>
                                <div class="flex items-end">
                                    <button type="submit" class="w-full px-4 py-2 bg-emerald-600 text-white font-bold rounded-lg text-sm hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-200 flex items-center justify-center gap-1.5">
                                        <i class="bi bi-check2-circle"></i> Add
                                    </button>
                                </div>
                            </div>
                            <div class="mt-2">
                                <input type="text" name="note" class="w-full px-3 py-2 rounded-lg border border-emerald-300 text-sm focus:ring-2 focus:ring-emerald-500 outline-none" placeholder="Note (optional)...">
                            </div>
                        </form>

                        {{-- Payments List --}}
                        @if($paymentCount > 0)
                        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-200">
                                        <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Date</th>
                                        <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Amount</th>
                                        <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Mode</th>
                                        <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Transaction ID</th>
                                        <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Note</th>
                                        <th class="px-4 py-3 text-right text-[10px] font-bold text-slate-400 uppercase tracking-wider">Recorded</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($allPayments as $payment)
                                    @php
                                        $modeName = $modeNames[$payment->paymentmode] ?? $payment->paymentmode;
                                    @endphp
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="font-medium text-slate-700">{{ \Carbon\Carbon::parse($payment->date)->format('M d, Y') }}</span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="font-bold text-emerald-700">{{ $invoice->currencyRel->symbol ?? '$' }}{{ number_format($payment->amount, 2) }}</span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @if($modeName)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs font-medium">
                                                <i class="bi bi-credit-card text-slate-400"></i>
                                                {{ $modeName }}
                                            </span>
                                            @else
                                            <span class="text-xs text-slate-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="text-xs text-slate-600 font-mono">{{ $payment->transactionid ?: '-' }}</span>
                                        </td>
                                        <td class="px-4 py-3 max-w-[200px]">
                                            @if($payment->note)
                                            <span class="text-xs text-slate-500 truncate block" title="{{ $payment->note }}">{{ $payment->note }}</span>
                                            @else
                                            <span class="text-xs text-slate-300 italic">No note</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right whitespace-nowrap">
                                            <span class="text-xs text-slate-400">{{ $payment->daterecorded ? \Carbon\Carbon::parse($payment->daterecorded)->format('M d, Y H:i') : '-' }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="py-12 text-center bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200">
                            <div class="w-14 h-14 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="bi bi-cash-coin text-emerald-300 text-2xl"></i>
                            </div>
                            <p class="text-sm font-medium text-slate-500 mb-1">No payments recorded yet</p>
                            <p class="text-xs text-slate-400">Use the form above to record a payment for this invoice</p>
                        </div>
                        @endif
                    </div>

                    {{-- Credits Tab --}}
                    <div id="tab-credits" class="tab-content hidden">
                        @if($invoice->credits()->count() > 0)
                        <div class="space-y-4">
                            @foreach($invoice->credits as $credit)
                            <div class="bg-blue-50 rounded-xl p-4 flex justify-between items-center border border-blue-100">
                                <div>
                                    <p class="text-sm font-bold text-blue-800">
                                        <a href="{{ route('admin.creditnotes.show', $credit->credit_id) }}" class="hover:underline">
                                            {{ $credit->creditNote->prefix }}{{ str_pad($credit->creditNote->number, 4, '0', STR_PAD_LEFT) }}
                                        </a>
                                    </p>
                                    <p class="text-xs text-blue-500 font-medium mt-1">Applied on {{ \Carbon\Carbon::parse($credit->date_applied)->format('M d, Y') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-black text-blue-800">{{ $invoice->currencyRel->symbol ?? '$' }}{{ number_format($credit->amount, 2) }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="py-10 text-center">
                            <i class="bi bi-receipt-cutoff text-slate-300 text-4xl mb-3 block"></i>
                            <p class="text-sm text-slate-400 font-medium">No credits applied to this invoice.</p>
                        </div>
                        @endif
                    </div>

                    {{-- Notes Tab --}}
                    <div id="tab-notes" class="tab-content hidden">
                        <form action="{{ route('admin.invoices.notes.store', $invoice->id) }}" method="POST" class="mb-4">
                            @csrf
                            <div class="flex gap-2">
                                <input type="text" name="description" class="flex-1 px-3 py-2 border border-slate-200 rounded-lg text-sm" placeholder="Add a note..." required>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Add</button>
                            </div>
                        </form>
                        @if($invoice->notesList()->count() > 0)
                        <div class="space-y-2">
                            @foreach($invoice->notesList()->orderBy('dateadded', 'desc')->get() as $note)
                            <div class="bg-slate-50 rounded-lg p-3">
                                <p class="text-sm text-slate-700">{{ $note->description }}</p>
                                <p class="text-xs text-slate-400 mt-1">{{ \Carbon\Carbon::parse($note->dateadded)->format('M d, Y H:i') }}</p>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-sm text-slate-400 text-center py-4">No notes</p>
                        @endif
                    </div>

                    {{-- Reminders Tab --}}
                    <div id="tab-reminders" class="tab-content hidden">
                        <form action="{{ route('admin.invoices.reminders.store', $invoice->id) }}" method="POST" class="mb-4 p-4 bg-blue-50 rounded-xl border border-blue-100">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-1 block">Reminder Date</label>
                                    <input type="datetime-local" name="date" class="w-full px-3 py-2 border border-blue-200 rounded-lg text-sm" required>
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-1 block">Assign To</label>
                                    <select name="staff" class="w-full px-3 py-2 border border-blue-200 rounded-lg text-sm" required>
                                        <option value="">Select Staff</option>
                                        @foreach($staff as $member)
                                            <option value="{{ $member->staffid }}">{{ $member->firstname }} {{ $member->lastname }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-1 block">Description</label>
                                <textarea name="description" rows="2" class="w-full px-3 py-2 border border-blue-200 rounded-lg text-sm" placeholder="What needs to be done?" required></textarea>
                            </div>
                            <div class="flex items-center justify-between">
                                <label class="flex items-center gap-2 text-sm text-blue-700 cursor-pointer">
                                    <input type="checkbox" name="notify_by_email" value="1" checked class="rounded border-blue-300 text-blue-600">
                                    Notify by Email
                                </label>
                                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold hover:bg-blue-700 transition shadow-md shadow-blue-500/20">
                                    Set Reminder
                                </button>
                            </div>
                        </form>

                        @if($invoice->remindersList()->count() > 0)
                        <div class="space-y-3">
                            @foreach($invoice->remindersList()->orderBy('date', 'desc')->get() as $reminder)
                            <div class="bg-white border border-slate-100 rounded-xl p-4 shadow-sm hover:shadow-md transition">
                                <div class="flex justify-between items-start">
                                    <div class="flex gap-3">
                                        <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500">
                                            <i class="bi bi-bell"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-800">{{ $reminder->description }}</p>
                                            <div class="flex items-center gap-3 mt-1 text-xs text-slate-500">
                                                <span class="flex items-center gap-1">
                                                    <i class="bi bi-calendar-event"></i>
                                                    {{ \Carbon\Carbon::parse($reminder->date)->format('M d, Y H:i') }}
                                                </span>
                                                <span class="flex items-center gap-1">
                                                    <i class="bi bi-person"></i>
                                                    {{ $reminder->staff->firstname ?? 'Unknown' }}
                                                </span>
                                                @if($reminder->isnotified)
                                                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded text-[10px] font-black uppercase">Notified</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <form action="{{ route('admin.reminders.destroy', $reminder->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-300 hover:text-red-500 transition-colors">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-10 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200">
                            <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center text-slate-300 mx-auto mb-3">
                                <i class="bi bi-bell-slash text-xl"></i>
                            </div>
                            <p class="text-sm text-slate-400">No reminders set for this invoice</p>
                        </div>
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
                    @if($invoice->status != 3 && $invoice->status != 4)
                    <form action="{{ route('admin.invoices.mark_paid', $invoice->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-3 py-2 bg-emerald-600 text-white rounded-lg text-sm hover:bg-emerald-700 text-left flex items-center gap-2">
                            <i class="bi bi-check-circle"></i> Mark as Paid
                        </button>
                    </form>
                    @endif
                    @if($invoice->status != 4)
                    <form action="{{ route('admin.invoices.mark_cancelled', $invoice->id) }}" method="POST">
                        @csrf
                        <button type="submit" onclick="return confirm('Cancel invoice?')" class="w-full px-3 py-2 bg-slate-500 text-white rounded-lg text-sm hover:bg-slate-600 text-left flex items-center gap-2">
                            <i class="bi bi-x-circle"></i> Cancel Invoice
                        </button>
                    </form>
                    @endif
                    <form action="{{ route('admin.invoices.send', $invoice->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-3 py-2 bg-blue-100 text-blue-700 rounded-lg text-sm hover:bg-blue-200 text-left flex items-center gap-2">
                            <i class="bi bi-send"></i> Send to Client
                        </button>
                    </form>
                    <a href="{{ route('admin.invoices.print', $invoice->id) }}" target="_blank" class="w-full px-3 py-2 bg-slate-100 text-slate-700 rounded-lg text-sm hover:bg-slate-200 text-left flex items-center gap-2">
                        <i class="bi bi-printer"></i> Print / PDF
                    </a>
                    <form action="{{ route('admin.invoices.destroy', $invoice->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete invoice?')" class="w-full px-3 py-2 bg-red-50 text-red-600 rounded-lg text-sm hover:bg-red-100 text-left flex items-center gap-2">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-4">
                <h3 class="text-sm font-semibold text-slate-700 mb-3">Client</h3>
                @if($invoice->client)
                <a href="{{ route('admin.clients.show', $invoice->client->userid) }}" class="font-medium text-slate-800 hover:text-blue-600">{{ $invoice->client->company }}</a>
                <p class="text-sm text-slate-500">{{ $invoice->client->email }}</p>
                @else
                <p class="text-sm text-slate-400">No client</p>
                @endif
            </div>

            <a href="{{ route('admin.invoices.index') }}" class="flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-100 text-slate-600 rounded-lg text-sm hover:bg-slate-200">
                <i class="bi bi-arrow-left"></i> Back to Invoices
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