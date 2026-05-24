<div class="flex items-center justify-between mb-4">
    <h3 class="text-sm font-semibold text-slate-800">Notes ({{ $notes->count() }})</h3>
    <button onclick="openNoteModal()" class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 flex items-center gap-1">
        <i class="bi bi-plus"></i> Add Note
    </button>
</div>

@if($notes->count() > 0)
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($notes as $note)
    <div onclick="viewNote({{ $note->id }}, '{{ $note->title ?? '' }}', `{!! addslashes($note->description) !!}`, '{{ $note->creator->firstname ?? '' }} {{ $note->creator->lastname ?? '' }}', '{{ $note->dateadded ? $note->dateadded->format('M j, Y g:i A') : '' }}')" 
         class="group relative p-4 bg-gradient-to-br from-amber-50 to-yellow-50 rounded-xl border border-amber-100 shadow-sm hover:shadow-md hover:border-amber-200 transition-all cursor-pointer"
         style="background-image: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);">
        
        <div class="absolute top-2 right-2 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
            <button onclick="event.stopPropagation(); editNote({{ $note->id }}, '{{ $note->title ?? '' }}', `{!! addslashes($note->description) !!}`)" class="p-1 text-amber-600 hover:text-amber-800 hover:bg-amber-100 rounded" title="Edit">
                <i class="bi bi-pencil text-xs"></i>
            </button>
            <form action="{{ route('admin.notes.destroy', $note->id) }}" method="POST" class="inline" onsubmit="event.preventDefault(); confirmDelete('Delete Note', 'Delete this note? This cannot be undone.', '{{ route('admin.notes.destroy', $note->id) }}', 'DELETE');">
                @csrf @method('DELETE')
                <button type="submit" class="p-1 text-amber-600 hover:text-red-600 hover:bg-amber-100 rounded" title="Delete">
                    <i class="bi bi-trash text-xs"></i>
                </button>
            </form>
        </div>

        @if($note->title)
        <h4 class="text-sm font-bold text-amber-900 mb-2 pr-16">{{ $note->title }}</h4>
        @endif
        
        <p class="text-xs text-amber-800 leading-relaxed line-clamp-2">{!! $note->description !!}</p>
        
        <div class="flex items-center gap-2 mt-3 pt-3 border-t border-amber-100">
            @if($note->creator && is_object($note->creator))
            <span class="w-5 h-5 rounded-full bg-amber-500 flex items-center justify-center text-white text-[8px] font-bold">
                {{ substr($note->creator->firstname, 0, 1) }}
            </span>
            <span class="text-[10px] text-amber-700">{{ $note->creator->firstname }}</span>
            @endif
            <span class="text-[10px] text-amber-500 ml-auto">{{ $note->dateadded ? $note->dateadded->format('M j') : '' }}</span>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="text-center py-16">
    <div class="w-20 h-20 mx-auto mb-4 bg-amber-50 rounded-full flex items-center justify-center">
        <svg class="w-10 h-10 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
    </div>
    <p class="text-sm font-medium text-slate-500 mb-1">No notes yet</p>
    <p class="text-xs text-slate-400">Capture important information about this project</p>
    <button onclick="openNoteModal()" class="mt-4 px-4 py-2 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">Add Note</button>
</div>
@endif