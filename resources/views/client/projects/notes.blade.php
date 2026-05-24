@extends('layouts.client')

@section('title', 'Notes - ' . $project->name)
@section('breadcrumb', 'Project Notes')

@section('main')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('client.projects.show', $project) }}" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-200 rounded-xl text-slate-500 hover:text-blue-600 transition-colors">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Project Notes</h1>
                <p class="text-sm text-slate-500">{{ $project->name }}</p>
            </div>
        </div>
    </div>

    @if($notes->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($notes as $note)
        <div class="group relative p-5 bg-gradient-to-br from-amber-50 to-yellow-50 rounded-xl border border-amber-100 shadow-sm hover:shadow-md hover:border-amber-200 transition-all">
            @if($note->title)
            <h4 class="text-sm font-bold text-amber-900 mb-2 pr-8">{{ $note->title }}</h4>
            @endif

            <p class="text-xs text-amber-800 leading-relaxed line-clamp-3">{!! Str::limit(strip_tags($note->description), 150) !!}</p>

            <div class="flex items-center gap-2 mt-4 pt-3 border-t border-amber-100">
                @if($note->creator)
                <span class="w-6 h-6 rounded-full bg-amber-500 flex items-center justify-center text-white text-[8px] font-bold">
                    {{ substr($note->creator->firstname ?? 'U', 0, 1) }}
                </span>
                <span class="text-[10px] font-semibold text-amber-700">{{ $note->creator->firstname ?? '' }} {{ $note->creator->lastname ?? '' }}</span>
                @endif
                <span class="text-[10px] text-amber-500 ml-auto">{{ $note->dateadded ? \Carbon\Carbon::parse($note->dateadded)->format('M j, Y') : '' }}</span>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="flex flex-col items-center justify-center py-20 bg-white rounded-2xl border border-dashed border-slate-300">
        <div class="w-16 h-16 bg-amber-50 rounded-full flex items-center justify-center mb-4">
            <i class="bi bi-journal-text text-3xl text-amber-300"></i>
        </div>
        <h3 class="text-lg font-bold text-slate-800">No notes yet</h3>
        <p class="text-slate-500">No project notes have been shared with you yet.</p>
    </div>
    @endif
</div>
@endsection
