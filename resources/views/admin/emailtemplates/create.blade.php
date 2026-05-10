@extends('layouts.admin')

@section('breadcrumb', 'Add Email Template')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Add New Email Template</h1>
            <p class="text-slate-500 text-sm">Define a new template for automated system emails.</p>
        </div>
        <a href="{{ route('admin.emailtemplates.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-300 transition-all flex items-center gap-2">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <form action="{{ route('admin.emailtemplates.store') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @csrf
        <div class="p-6 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Basic Details</h3>
                    
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Template Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('name') border-red-500 @enderror" placeholder="Welcome Email" required>
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Slug <span class="text-red-500">*</span></label>
                        <input type="text" name="slug" value="{{ old('slug') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('slug') border-red-500 @enderror" placeholder="welcome-email" required>
                        @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Type <span class="text-red-500">*</span></label>
                            <select name="type" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('type') border-red-500 @enderror" required>
                                <option value="system" {{ old('type') == 'system' ? 'selected' : '' }}>System</option>
                                <option value="marketing" {{ old('type') == 'marketing' ? 'selected' : '' }}>Marketing</option>
                                <option value="transactional" {{ old('type') == 'transactional' ? 'selected' : '' }}>Transactional</option>
                            </select>
                            @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Language</label>
                            <input type="text" name="language" value="{{ old('language', 'en') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="en">
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Sender Details</h3>
                    
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">From Name</label>
                        <input type="text" name="fromname" value="{{ old('fromname') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="CRM Support">
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">From Email</label>
                        <input type="email" name="fromemail" value="{{ old('fromemail') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="support@yourcompany.com">
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Status</label>
                        <select name="active" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            <option value="1" {{ old('active') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('active') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Email Content</h3>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">Subject <span class="text-red-500">*</span></label>
                    <input type="text" name="subject" value="{{ old('subject') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('subject') border-red-500 @enderror" placeholder="Welcome to our service!" required>
                    @error('subject') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">Message Body (HTML supported) <span class="text-red-500">*</span></label>
                    <textarea name="message" rows="10" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('message') border-red-500 @enderror" placeholder="Hello {customer_name}, welcome to..." required>{{ old('message') }}</textarea>
                    @error('message') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
            <a href="{{ route('admin.emailtemplates.index') }}" class="px-6 py-2 text-slate-600 font-semibold rounded-lg hover:bg-slate-200 transition-all">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Save Template</button>
        </div>
    </form>
</div>
@endsection
