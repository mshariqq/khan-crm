@extends('layouts.admin')

@section('breadcrumb', 'Edit Payment')

@section('main')
<div class="p-4 max-w-2xl mx-auto">
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-emerald-500 flex items-center justify-center text-white">
                <i class="bi bi-cash-coin"></i>
            </div>
            <div>
                <h1 class="text-lg font-bold text-slate-800">Edit Payment</h1>
                <p class="text-sm text-slate-500">Invoice #{{ $invoice->prefix ?? '' }}{{ $invoice->number }}</p>
            </div>
        </div>

        <form action="{{ route('admin.invoices.payments.update', [$invoice->id, $payment->id]) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Amount</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500 text-sm font-medium">{{ $invoice->currency->symbol ?? '$' }}</span>
                        <input type="number" step="0.01" name="amount" value="{{ old('amount', $payment->amount) }}" class="w-full pl-8 pr-3 py-2 rounded-lg border border-slate-300 text-sm font-bold focus:ring-2 focus:ring-emerald-500 outline-none" required>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Payment Mode</label>
                    <select name="paymentmode" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 outline-none" required>
                        <option value="">Select...</option>
                        @foreach($paymentModes as $mode)
                            <option value="{{ $mode->id }}" {{ old('paymentmode', $payment->paymentmode) == $mode->id ? 'selected' : '' }}>{{ $mode->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Date</label>
                    <input type="date" name="date" value="{{ old('date', $payment->date instanceof \Carbon\Carbon ? $payment->date->format('Y-m-d') : date('Y-m-d', strtotime($payment->date))) }}" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 outline-none" required>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Transaction ID</label>
                    <input type="text" name="transactionid" value="{{ old('transactionid', $payment->transactionid) }}" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 outline-none" placeholder="Optional">
                </div>
            </div>
            <div class="mb-6">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Note</label>
                <input type="text" name="note" value="{{ old('note', $payment->note) }}" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 outline-none" placeholder="Note (optional)...">
            </div>
            <div class="flex items-center justify-between">
                <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
                <button type="submit" class="px-6 py-2 bg-emerald-600 text-white font-bold rounded-lg text-sm hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-200 flex items-center gap-1.5">
                    <i class="bi bi-check2-circle"></i> Update Payment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
