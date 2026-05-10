@extends('layouts.admin')

@section('breadcrumb', 'Item Details')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Item Details</h1>
            <p class="text-slate-500 text-sm">Viewing detailed information for {{ $item->description }}.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.items.edit', $item->id) }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all flex items-center gap-2">
                <i class="bi bi-pencil-square"></i> Edit Item
            </a>
            <a href="{{ route('admin.items.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-300 transition-all flex items-center gap-2">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2 mb-4">Description</h3>
                <p class="text-slate-600 leading-relaxed">
                    {{ $item->long_description ?? 'No detailed description provided.' }}
                </p>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2 mb-4">Details</h3>
                <div class="space-y-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Short Name</span>
                        <span class="text-slate-800 font-medium">{{ $item->description }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Item Group</span>
                        <span class="text-slate-800 font-medium">{{ $item->group->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Unit</span>
                        <span class="text-slate-800 font-medium">{{ $item->unit ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2 mb-4">Pricing & Taxes</h3>
                <div class="space-y-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Rate</span>
                        <span class="text-slate-800 font-bold text-lg">{{ number_format($item->rate, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Tax 1</span>
                        <span class="text-slate-800 font-medium">{{ $item->tax }}%</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Tax 2</span>
                        <span class="text-slate-800 font-medium">{{ $item->tax2 }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
