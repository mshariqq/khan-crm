@extends('layouts.admin')

@section('breadcrumb', 'Add Payment Mode')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Add New Payment Mode</h1>
            <p class="text-slate-500 text-sm">Define a new payment method accepted by your company.</p>
        </div>
        <a href="{{ route('admin.paymentmodes.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-300 transition-all flex items-center gap-2">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <form action="{{ route('admin.paymentmodes.store') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @csrf
        <div class="p-6 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">General Information</h3>
                    
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Payment Mode Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('name') border-red-500 @enderror" placeholder="Credit Card" required>
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Description</label>
                        <textarea name="description" rows="3" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="Enter payment mode description..."></textarea>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Configuration</h3>
                    
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg border border-slate-200">
                            <label class="text-sm font-medium text-slate-600">Show on PDF</label>
                            <input type="checkbox" name="show_on_pdf" value="1" {{ old('show_on_pdf') ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                        </div>
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg border border-slate-200">
                            <label class="text-sm font-medium text-slate-600">Invoices Only</label>
                            <input type="checkbox" name="invoices_only" value="1" {{ old('invoices_only') ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                        </div>
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg border border-slate-200">
                            <label class="text-sm font-medium text-slate-600">Expenses Only</label>
                            <input type="checkbox" name="expenses_only" value="1" {{ old('expenses_only') ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                        </div>
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg border border-slate-200">
                            <label class="text-sm font-medium text-slate-600">Selected by Default</label>
                            <input type="checkbox" name="selected_by_default" value="1" {{ old('selected_by_default') ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                        </div>
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg border border-slate-200">
                            <label class="text-sm font-medium text-slate-600">Active</label>
                            <input type="checkbox" name="active" value="1" {{ old('active', '1') ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
            <a href="{{ route('admin.paymentmodes.index') }}" class="px-6 py-2 text-slate-600 font-semibold rounded-lg hover:bg-slate-200 transition-all">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Save Payment Mode</button>
        </div>
    </form>
</div>
@endsection
