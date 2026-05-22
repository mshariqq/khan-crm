@extends('layouts.admin')

@section('breadcrumb', 'Client Reminders')

@section('main')
<div class="p-6 space-y-6">
    @include('partials.admin.clients.profile_header')

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-pink-50 rounded-xl flex items-center justify-center">
                    <i class="bi bi-bell text-pink-600 text-lg"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Reminders</h2>
                    <p class="text-xs text-slate-500">{{ $data->total() }} records</p>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Assigned To</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Created By</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Notified</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($data as $reminder)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-slate-800 max-w-[200px] truncate">{{ $reminder->description }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ \Carbon\Carbon::parse($reminder->date)->format('M j, Y H:i') }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $reminder->staff->firstname ?? '—' }} {{ $reminder->staff->lastname ?? '' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $reminder->creator->firstname ?? '—' }} {{ $reminder->creator->lastname ?? '' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $reminder->isnotified ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $reminder->isnotified ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.reminders.destroy', $reminder->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this reminder?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-red-600 transition-colors" title="Delete Reminder">
                                        <i class="bi bi-trash text-lg"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <i class="bi bi-bell text-4xl block mb-2"></i>
                                <p class="text-sm font-medium">No reminders found</p>
                                <p class="text-xs mt-1">Set reminders to stay on top of important dates.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($data->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $data->appends(['tab' => 'reminders'])->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
