@extends('layouts.admin')

@section('breadcrumb', 'Countries')

@section('main')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Countries</h1>
            <p class="text-slate-500 text-sm mt-1">Manage countries for your CRM</p>
        </div>
        <a href="{{ route('admin.countries.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
            <i class="bi bi-plus-lg"></i>
            Add Country
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
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Country</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">ISO2</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">ISO3</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Calling Code</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($countries as $country)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="font-medium text-slate-800">{{ $country->long_name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700">{{ $country->iso2 }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $country->iso3 ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $country->calling_code ?? '-' }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.countries.edit', $country->country_id) }}" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.countries.destroy', $country->country_id) }}" method="POST" onsubmit="return confirm('Delete this country?');">
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
                            <td colspan="5" class="px-4 py-12 text-center text-slate-500">No countries found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($countries->hasPages())
            <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
                {{ $countries->links() }}
            </div>
        @endif
    </div>
</div>
@endsection