@extends('layouts.admin')

@section('breadcrumb', 'Email Template Details')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ $template->name }}</h1>
            <p class="text-slate-500 text-sm">Viewing detailed configuration for email template {{ $template->slug }}.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.emailtemplates.edit', $template->emailtemplateid) }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all flex items-center gap-2">
                <i class="bi bi-pencil-square"></i> Edit Template
            </a>
            <a href="{{ route('admin.emailtemplates.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-300 transition-all flex items-center gap-2">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2 mb-4">Email Content</h3>
                <div class="space-y-6">
                    <div>
                        <label class="text-xs font-semibold text-slate-500 uppercase">Subject</label>
                        <p class="text-slate-800 font-medium text-lg">{{ $template->subject }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-500 uppercase">Message Body</label>
                        <div class="mt-2 p-4 bg-slate-50 border border-slate-200 rounded-lg text-slate-600 leading-relaxed whitespace-pre-wrap">
                            {{ $template->message }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2 mb-4">Configuration</h3>
                <div class="space-y-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Slug</span>
                        <span class="text-slate-800 font-mono font-medium">{{ $template->slug }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Type</span>
                        <span class="px-2 py-1 bg-slate-100 text-slate-700 rounded-full text-xs font-medium">{{ ucfirst($template->type) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Language</span>
                        <span class="text-slate-800 font-medium">{{ $template->language ?? 'en' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Status</span>
                        <span class="px-2 py-1 {{ $template->active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} rounded-full text-xs font-medium">
                            {{ $template->active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="pt-4 border-t border-slate-100 space-y-3">
                        <div class="flex justify-between">
                            <span class="text-slate-500">From Name</span>
                            <span class="text-slate-800 font-medium">{{ $template->fromname ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">From Email</span>
                            <span class="text-slate-800 font-medium">{{ $template->fromemail ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
