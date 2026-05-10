@extends('layouts.admin')

@section('breadcrumb', 'Taxes')

@section('main')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Taxes</h1>
            <p class="text-slate-500 text-sm mt-1">Manage tax rates for invoices and expenses</p>
        </div>
        <a href="{{ route('admin.taxes.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
            <i class="bi bi-plus-lg"></i>
            Add Tax
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tax Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tax Rate</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($taxes as $tax)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3">
                                <span class="font-medium text-slate-800">{{ $tax->name }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-semibold bg-slate-100 text-slate-700">{{ number_format($tax->taxrate, 2) }}%</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.taxes.edit', $tax->id) }}" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.taxes.destroy', $tax->id) }}" method="POST" onsubmit="return confirm('Delete this tax?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-12 text-center text-slate-500">No taxes found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($taxes->hasPages())
            <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
                {{ $taxes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection