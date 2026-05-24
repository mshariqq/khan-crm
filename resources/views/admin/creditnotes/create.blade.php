@extends('layouts.admin')

@section('breadcrumb', 'Add Credit Note')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Add New Credit Note</h1>
            <p class="text-slate-500 text-sm">Apply a credit to an invoice.</p>
        </div>
        <a href="{{ route('admin.creditnotes.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-300 transition-all flex items-center gap-2">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <form action="{{ route('admin.creditnotes.store') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @csrf
        <div class="p-6 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Credit Details</h3>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Invoice <span class="text-red-500">*</span></label>
                        <select name="invoice_id" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('invoice_id') border-red-500 @enderror" required>
                            <option value="">Select Invoice</option>
                            @foreach($invoices as $invoice)
                                <option value="{{ $invoice->id }}" {{ old('invoice_id') == $invoice->id ? 'selected' : '' }}>{{ $invoice->number ?? 'INV-'.$invoice->id }} ({{ app_format_money($invoice->total, $invoice->currency) }})</option>
                            @endforeach
                        </select>
                        @error('invoice_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Amount <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('amount') border-red-500 @enderror" placeholder="0.00" required>
                        @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Timeline & Staff</h3>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Date <span class="text-red-500">*</span></label>
                        <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('date') border-red-500 @enderror" required>
                        @error('date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Date Applied</label>
                        <input type="date" name="date_applied" value="{{ old('date_applied', date('Y-m-d')) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('date_applied') border-red-500 @enderror">
                        @error('date_applied') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Staff</label>
                        <select name="staff_id" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('staff_id') border-red-500 @enderror">
                            <option value="">Select Staff</option>
                            @foreach($staff as $s)
                                <option value="{{ $s->staffid }}" {{ old('staff_id') == $s->staffid ? 'selected' : '' }}>{{ $s->firstname }} {{ $s->lastname }}</option>
                            @endforeach
                        </select>
                        @error('staff_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
            <a href="{{ route('admin.creditnotes.index') }}" class="px-6 py-2 text-slate-600 font-semibold rounded-lg hover:bg-slate-200 transition-all">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Save Credit Note</button>
        </div>
    </form>
</div>
@endSection