@extends('layouts.admin')

@section('breadcrumb', 'Knowledge Base Groups')

@section('main')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Knowledge Base Groups</h1>
            <p class="text-slate-500 text-sm mt-1">Organize your knowledge base articles into groups</p>
        </div>
        <a href="{{ route('admin.knowledge-base-groups.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 hover:shadow-xl">
            <i class="bi bi-plus-lg"></i>
            Add Group
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm flex items-center gap-2">
            <i class="bi bi-check-circle-fill"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($groups->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($groups as $group)
            <div class="bg-white rounded-xl border border-slate-200 p-5 hover:shadow-lg hover:border-blue-200 transition-all duration-300 group">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold" style="background-color: {{ $group->color ?? '#6366f1' }}">
                            <i class="bi bi-folder-fill"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-800">{{ $group->name }}</h3>
                            <p class="text-xs text-slate-500">Order: {{ $group->group_order ?? 0 }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="{{ route('admin.knowledge-base-groups.edit', $group->groupid) }}" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('admin.knowledge-base-groups.destroy', $group->groupid) }}" method="POST" onsubmit="event.preventDefault(); confirmDelete('Confirm Delete', 'Delete this knowledge base group?', this.action, (this.querySelector('[name=_method]')?.value || 'POST'));">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                
                @if($group->description)
                    <p class="text-sm text-slate-500 mb-3 line-clamp-2">{{ $group->description }}</p>
                @endif
                
                <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                    <div class="flex items-center gap-2">
                        @if($group->active)
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-medium bg-green-50 text-green-700">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Active
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-500">
                                <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span> Inactive
                            </span>
                        @endif
                    </div>
                    <span class="w-3 h-3 rounded-full" style="background-color: {{ $group->color ?? '#6366f1' }};"></span>
                </div>
            </div>
        @endforeach
    </div>
    
    @if($groups->hasPages())
        <div class="mt-6 px-4 py-3 bg-white rounded-xl border border-slate-200">
            {{ $groups->links() }}
        </div>
    @endif
    @else
    <div class="bg-white rounded-xl border border-slate-200 p-12 text-center">
        <div class="w-16 h-16 mx-auto mb-4 bg-slate-100 rounded-full flex items-center justify-center">
            <i class="bi bi-folder text-2xl text-slate-400"></i>
        </div>
        <h3 class="text-lg font-semibold text-slate-800 mb-2">No Knowledge Base Groups</h3>
        <p class="text-slate-500 text-sm mb-6 max-w-sm mx-auto">Create groups to organize your knowledge base articles for better navigation.</p>
        <a href="{{ route('admin.knowledge-base-groups.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-all">
            <i class="bi bi-plus-lg"></i>
            Create First Group
        </a>
    </div>
    @endif
</div>
@endsection