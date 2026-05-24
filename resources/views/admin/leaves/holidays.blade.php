@extends('layouts.admin')

@section('breadcrumb', 'Company Holidays')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Company Holidays</h1>
            <p class="text-slate-500 text-sm">Manage official company holidays and closures.</p>
        </div>
        <a href="{{ route('admin.leaves.storeHoliday') }}" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all flex items-center gap-2 shadow-lg shadow-blue-500/20">
            <i class="bi bi-plus-lg"></i> Add Holiday
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Holiday Name</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Description</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($holidays as $holiday)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <span class="text-sm font-medium text-slate-800">{{ $holiday->name }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">
                        {{ $holiday->date }}
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-500">
                        {{ $holiday->description }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <form action="{{ route('admin.leaves.destroyHoliday', $holiday->id) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-slate-400 hover:text-red-600 transition-colors" onclick="event.preventDefault(); confirmDelete('Confirm Delete', 'Delete this holiday?', this.form.action, (this.form.querySelector('[name=_method]')?.value || 'POST'));">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-slate-400">
                            <i class="bi bi-calendar-event-x text-3xl mb-2"></i>
                            <p class="text-sm">No holidays defined yet.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
