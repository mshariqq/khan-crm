@extends('layouts.admin')

@section('breadcrumb', 'Project Categories')

@section('main')
<div class="p-6 space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Project Categories</h1>
            <p class="text-slate-500 text-sm mt-1">Organize projects into categories</p>
        </div>
    </div>

    {{-- Inline Create Form --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
        <h3 class="text-sm font-bold text-slate-800 mb-3">Add New Category</h3>
        <form action="{{ route('admin.project-categories.store') }}" method="POST" class="flex flex-wrap items-end gap-3">
            @csrf
            <div class="space-y-1">
                <label class="text-xs font-medium text-slate-500">Name</label>
                <input type="text" name="name" value="{{ old('name') }}" class="px-3 py-1.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm w-48" placeholder="Category name" required>
            </div>
            <div class="space-y-1">
                <label class="text-xs font-medium text-slate-500">Color</label>
                <input type="color" name="color" value="{{ old('color', '#3b82f6') }}" class="w-10 h-9 rounded-lg border border-slate-200 cursor-pointer p-0.5">
            </div>
            <div class="space-y-1 flex-1">
                <label class="text-xs font-medium text-slate-500">Description</label>
                <input type="text" name="description" value="{{ old('description') }}" class="px-3 py-1.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm w-full" placeholder="Optional description">
            </div>
            <button type="submit" class="px-4 py-1.5 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all">Add Category</button>
        </form>
    </div>

    {{-- Categories Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-xs text-slate-500 border-b border-slate-100">
                        <th class="text-left px-5 py-3 font-semibold">Name</th>
                        <th class="text-left px-5 py-3 font-semibold">Color</th>
                        <th class="text-left px-5 py-3 font-semibold">Description</th>
                        <th class="text-center px-5 py-3 font-semibold">Projects</th>
                        <th class="text-right px-5 py-3 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($categories as $category)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-4 text-sm font-semibold text-slate-800">{{ $category->name }}</td>
                        <td class="px-5 py-4">
                            <span class="inline-block w-6 h-6 rounded-full border border-slate-200" style="background-color: {{ $category->color ?? '#3b82f6' }}"></span>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $category->description ?? '—' }}</td>
                        <td class="px-5 py-4 text-center text-sm font-medium text-slate-700">{{ $category->projects_count ?? $category->projects->count() ?? 0 }}</td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('admin.project-categories.edit', $category->id) }}" class="p-1.5 text-slate-400 hover:text-amber-600 transition-colors" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.project-categories.destroy', $category->id) }}" method="POST" class="inline" onsubmit="event.preventDefault(); confirmDelete('Confirm Delete', 'Delete this category? Projects in this category will be uncategorized.', this.action, (this.querySelector('[name=_method]')?.value || 'POST'));">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 transition-colors" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-slate-400">
                            <i class="bi bi-tags text-2xl mb-2 block"></i>
                            <p class="text-sm">No categories yet. Create your first category above.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
