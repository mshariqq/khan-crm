@extends('layouts.admin')

@section('breadcrumb', 'Project Templates')

@section('main')
<div class="p-6 space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Project Templates</h1>
            <p class="text-slate-500 text-sm mt-1">Pre-defined project structures</p>
        </div>
        <a href="{{ route('admin.projects.templates.create') }}" class="px-4 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all flex items-center gap-2 shadow-lg shadow-blue-500/20">
            <i class="bi bi-plus-lg"></i> Create Template
        </a>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-xs text-slate-500 border-b border-slate-100">
                        <th class="text-left px-5 py-3 font-semibold">Name</th>
                        <th class="text-left px-5 py-3 font-semibold">Category</th>
                        <th class="text-right px-5 py-3 font-semibold">Est. Budget</th>
                        <th class="text-center px-5 py-3 font-semibold">Visibility</th>
                        <th class="text-right px-5 py-3 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($templates as $template)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                                    <i class="bi bi-file-earmark-zip text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">{{ $template->name }}</p>
                                    @if($template->description)
                                    <p class="text-xs text-slate-500 mt-0.5">{{ Str::limit($template->description, 60) }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $template->category ?? '—' }}</td>
                        <td class="px-5 py-4 text-right text-sm font-medium text-slate-800">{{ app_format_money($template->estimated_budget ?? 0) }}</td>
                        <td class="px-5 py-4 text-center">
                            @if($template->is_public)
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-700">Public</span>
                            @else
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-600">Private</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <button onclick="applyTemplate({{ $template->id }})" class="px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 transition-all mr-2">
                                Apply to Project
                            </button>
                            <a href="{{ route('admin.projects.templates.edit', $template->id) }}" class="p-1.5 text-slate-400 hover:text-amber-600 transition-colors" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.projects.templates.destroy', $template->id) }}" method="POST" class="inline" onsubmit="event.preventDefault(); confirmDelete('Confirm Delete', 'Delete this template?', this.action, (this.querySelector('[name=_method]')?.value || 'POST'));">
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
                            <i class="bi bi-file-earmark-zip text-2xl mb-2 block"></i>
                            <p class="text-sm">No templates yet.</p>
                            <a href="{{ route('admin.projects.templates.create') }}" class="text-blue-600 text-sm font-medium hover:text-blue-700">Create your first template</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function applyTemplate(id) {
    if (confirm('Apply this template to a new project?')) {
        window.location.href = '{{ route('admin.projects.create') }}?template_id=' + id;
    }
}
</script>
@endsection
