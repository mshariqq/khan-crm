@extends('layouts.admin')

@section('breadcrumb', 'Credit Note Details')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Credit Note #{{ $credit->id }}</h1>
            <p class="text-slate-500 text-sm">Viewing credit note details.</p>
        </div>
        <div class="flex items-center gap-3">
            @can('credit_notes_edit')
            <a href="{{ route('admin.creditnotes.edit', $credit->id) }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all flex items-center gap-2">
                <i class="bi bi-pencil-square"></i> Edit
            </a>
            @endcan
            <a href="{{ route('admin.creditnotes.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-300 transition-all flex items-center gap-2">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2 mb-4">Credit Information</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-slate-500">Amount</dt>
                        <dd class="text-slate-800 font-semibold text-lg">{{ number_format($credit->amount, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Invoice</dt>
                        <dd class="text-slate-800 font-medium">{{ $credit->invoice->number ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Date</dt>
                        <dd class="text-slate-800">{{ $credit->date ? \Carbon\Carbon::parse($credit->date)->format('M j, Y') : 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Date Applied</dt>
                        <dd class="text-slate-800">{{ $credit->date_applied ? \Carbon\Carbon::parse($credit->date_applied)->format('M j, Y') : 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Staff</dt>
                        <dd class="text-slate-800">{{ $credit->staff->firstname ?? 'N/A' }} {{ $credit->staff->lastname ?? '' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Created At</dt>
                        <dd class="text-slate-800">{{ $credit->created_at ? \Carbon\Carbon::parse($credit->created_at)->format('M j, Y H:i') : 'N/A' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2 mb-4">Summary</h3>
                <div class="space-y-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Credit ID</span>
                        <span class="text-slate-800 font-medium">#{{ $credit->id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Invoice</span>
                        <span class="text-slate-800 font-medium">{{ $credit->invoice->number ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Amount</span>
                        <span class="text-slate-800 font-semibold">{{ number_format($credit->amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Applied By</span>
                        <span class="text-slate-800 font-medium">{{ $credit->staff->firstname ?? 'N/A' }} {{ $credit->staff->lastname ?? '' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Date Applied</span>
                        <span class="text-slate-800 font-medium">{{ $credit->date_applied ? \Carbon\Carbon::parse($credit->date_applied)->format('M j, Y') : 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endSection