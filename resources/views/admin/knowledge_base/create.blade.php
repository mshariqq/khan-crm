@extends('layouts.admin')

@section('breadcrumb', 'Add Knowledge Base Article')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Add New Article</h1>
            <p class="text-slate-500 text-sm">Create a new knowledge base entry.</p>
        </div>
        <a href="{{ route('admin.knowledgebase.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-300 transition-all flex items-center gap-2">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <form action="{{ route('admin.knowledgebase.store') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @csrf
        <div class="p-6 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Basic Information</h3>
                    
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Subject <span class="text-red-500">*</span></label>
                        <input type="text" name="subject" value="{{ old('subject') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('subject') border-red-500 @enderror" placeholder="How to reset password" required>
                        @error('subject') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Group</label>
                        <select name="articlegroup" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('articlegroup') border-red-500 @enderror">
                            <option value="">-- No Group --</option>
                            @foreach($groups as $id => $name)
                                <option value="{{ $id }}" {{ old('articlegroup') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('articlegroup') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Order <span class="text-red-500">*</span></label>
                        <input type="number" name="article_order" value="{{ old('article_order', 0) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('article_order') border-red-500 @enderror" min="0" placeholder="0">
                        <p class="text-xs text-slate-500 mt-1">Display order for sorting</p>
                        @error('article_order') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Article Content</h3>
                    
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Description <span class="text-red-500">*</span></label>
                        <textarea name="description" rows="8" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('description') border-red-500 @enderror" placeholder="Article content..." required>{{ old('description') }}</textarea>
                        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center gap-3 py-4">
                        <select name="active" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            <option value="1" {{ old('active', true) ? 'selected' : '' }}>Published</option>
                            <option value="0" {{ old('active') == '0' ? 'selected' : '' }}>Draft</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
            <a href="{{ route('admin.knowledgebase.index') }}" class="px-6 py-2 text-slate-600 font-semibold rounded-lg hover:bg-slate-200 transition-all">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Save Article</button>
        </div>
    </form>
</div>
@endsection