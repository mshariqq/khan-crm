@extends('layouts.client')

@section('title', 'Files - ' . $project->name)
@section('breadcrumb', 'Project Files')

@section('main')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('client.projects.show', $project) }}" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-200 rounded-xl text-slate-500 hover:text-blue-600 transition-colors">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Project Files</h1>
                <p class="text-sm text-slate-500">{{ $project->name }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr class="text-[10px] uppercase tracking-wider font-bold text-slate-400">
                        <th class="px-6 py-4">File Name</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Size</th>
                        <th class="px-6 py-4">Uploaded</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($files as $file)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <span class="text-xl text-slate-400">
                                    <i class="bi {{ $file->file_icon ?? 'bi-file-earmark' }}"></i>
                                </span>
                                <span class="text-sm font-bold text-slate-800">{{ $file->original_file_name ?? $file->file_name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs uppercase font-bold text-slate-500">{{ $file->extension }}</span>
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-500">
                            {{ $file->file_size_formatted ?? number_format($file->file_size / 1024, 1) . ' KB' }}
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-500">
                            {{ $file->dateadded ? \Carbon\Carbon::parse($file->dateadded)->format('M d, Y') : '' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('client.projects.files.download', [$project, $file]) }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-600 text-xs font-bold rounded-lg hover:bg-blue-100 transition-colors">
                                <i class="bi bi-download"></i> Download
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="bi bi-folder2-open text-3xl text-slate-300"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800">No files found</h3>
                            <p class="text-slate-500">No project files have been shared with you yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(isset($files) && method_exists($files, 'hasPages') && $files->hasPages())
    <div class="mt-4">
        {{ $files->links() }}
    </div>
    @endif
</div>
@endsection
