@extends('layouts.admin')

@section('breadcrumb', 'New Knowledge Base Group')

@section('main')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">New Knowledge Base Group</h1>
            <p class="text-slate-500 text-sm mt-1">Create a new group to organize knowledge base articles</p>
        </div>
        <a href="{{ route('admin.knowledge-base-groups.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 font-medium rounded-lg hover:bg-slate-200 transition-all flex items-center gap-2">
            <i class="bi bi-arrow-left"></i>
            Back to List
        </a>
    </div>

    <form action="{{ route('admin.knowledge-base-groups.store') }}" method="POST" class="max-w-3xl">
        @csrf
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Group Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all @error('name') border-red-500 @enderror" placeholder="Getting Started" required>
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Description</label>
                            <textarea name="description" rows="4" class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all @error('description') border-red-500 @enderror" placeholder="Brief description of this group...">{{ old('description') }}</textarea>
                            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Display Order</label>
                            <input type="number" name="group_order" value="{{ old('group_order', 0) }}" class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all @error('group_order') border-red-500 @enderror" min="0" placeholder="0">
                            <p class="text-xs text-slate-500 mt-1">Higher number = higher position in list</p>
                            @error('group_order') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Group Color</label>
                            <div class="flex items-center gap-4">
                                <input type="color" name="color" value="{{ old('color', '#6366f1') }}" class="w-16 h-16 rounded-xl border border-slate-200 cursor-pointer" title="Pick a color">
                                <div class="flex-1">
                                    <div class="grid grid-cols-6 gap-2">
                                        @foreach(['#6366f1', '#8b5cf6', '#ec4899', '#ef4444', '#f59e0b', '#10b981', '#06b6d4', '#3b82f6'] as $color)
                                            <button type="button" class="w-8 h-8 rounded-lg border-2 transition-all hover:scale-110" 
                                                style="background-color: {{ $color }}"
                                                onclick="document.querySelector('input[name=&quot;color&quot;]').value = '{{ $color }}'">
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="pt-4">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="active" value="1" {{ old('active', true) ? 'checked' : '' }} class="w-5 h-5 text-blue-600 bg-slate-100 border-slate-200 rounded focus:ring-blue-500 focus:ring-offset-2">
                                <div>
                                    <span class="text-sm font-semibold text-slate-700">Active</span>
                                    <p class="text-xs text-slate-500">Show this group in knowledge base</p>
                                </div>
                            </label>
                        </div>
                        
                        <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white" style="background-color: {{ old('color', '#6366f1') }}" id="previewColor">
                                    <i class="bi bi-folder-fill"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500">Preview</p>
                                    <p class="font-medium text-slate-800" id="previewName">{{ old('name') ?: 'Group Name' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                <a href="{{ route('admin.knowledge-base-groups.index') }}" class="text-slate-600 hover:text-slate-800 font-medium">Cancel</a>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">
                    Create Group
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.querySelector('input[name="name"]').addEventListener('input', function(e) {
    document.getElementById('previewName').textContent = e.target.value || 'Group Name';
});
document.querySelector('input[name="color"]').addEventListener('input', function(e) {
    document.getElementById('previewColor').style.backgroundColor = e.target.value;
});
</script>
@endsection