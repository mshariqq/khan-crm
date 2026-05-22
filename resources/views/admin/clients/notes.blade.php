@extends('layouts.admin')

@section('breadcrumb', 'Client Notes')

@section('main')
<div class="p-6 space-y-6">
    @include('partials.admin.clients.profile_header')

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-yellow-50 rounded-xl flex items-center justify-center">
                    <i class="bi bi-sticky text-yellow-600 text-lg"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Notes</h2>
                    <p class="text-xs text-slate-500">{{ $data->total() }} records</p>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Note</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Created By</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Date Added</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($data as $note)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-slate-800 max-w-[400px]">{{ $note->description }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $note->creator->firstname ?? '—' }} {{ $note->creator->lastname ?? '' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ \Carbon\Carbon::parse($note->dateadded)->format('M j, Y H:i') }}</td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.notes.destroy', $note->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this note?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-red-600 transition-colors" title="Delete Note">
                                        <i class="bi bi-trash text-lg"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                <i class="bi bi-sticky text-4xl block mb-2"></i>
                                <p class="text-sm font-medium">No notes found</p>
                                <p class="text-xs mt-1">Add notes to keep track of important information.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($data->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $data->appends(['tab' => 'notes'])->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
