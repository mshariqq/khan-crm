@extends('layouts.admin')

@section('breadcrumb', 'Currencies')

@section('main')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Currencies</h1>
            <p class="text-slate-500 text-sm mt-1">Manage currency settings and symbols</p>
        </div>
        <a href="{{ route('admin.currencies.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
            <i class="bi bi-plus-lg"></i>
            Add Currency
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
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Symbol</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Format</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Default</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($currencies as $currency)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3">
                                <span class="font-medium text-slate-800">{{ $currency->name }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-semibold text-slate-700">{{ $currency->symbol }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                <span class="text-xs text-slate-500">Dec: </span>{{ $currency->decimal_separator ?? '.' }}
                                <span class="text-xs text-slate-500 ml-2">Thou: </span>{{ $currency->thousand_separator ?? ',' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($currency->isdefault)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Default</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.currencies.edit', $currency->id) }}" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.currencies.destroy', $currency->id) }}" method="POST" onsubmit="event.preventDefault(); confirmDelete('Confirm Delete', 'Delete this currency?', this.action, (this.querySelector('[name=_method]')?.value || 'POST'));">
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
                            <td colspan="5" class="px-4 py-12 text-center text-slate-500">No currencies found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($currencies->hasPages())
            <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
                {{ $currencies->links() }}
            </div>
        @endif
    </div>
</div>
@endsection