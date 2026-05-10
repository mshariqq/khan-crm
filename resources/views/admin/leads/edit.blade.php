@extends('layouts.admin')

@section('breadcrumb', 'Edit Lead')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
<style>
    .ql-editor { font-size: 14px; padding: 15px; min-height: 200px; }
    .ql-toolbar { border: 1px solid #e2e8f0 !important; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem; background: #f8fafc; }
    .ql-container { border: 1px solid #e2e8f0 !important; border-top: none !important; border-bottom-left-radius: 0.5rem; border-bottom-right-radius: 0.5rem; }
</style>
@endpush

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Edit Lead</h1>
            <p class="text-slate-500 text-sm">Update the details of {{ $lead->name }}.</p>
        </div>
        <a href="{{ route('admin.leads.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-300 transition-all flex items-center gap-2">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <form action="{{ route('admin.leads.update', $lead->id) }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @csrf
        @method('PUT')
        <div class="p-6 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Contact Information</h3>
                    
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $lead->name) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('name') border-red-500 @enderror" required>
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Email Address</label>
                        <input type="email" name="email" value="{{ old('email', $lead->email) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="john@example.com">
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Phone Number</label>
                        <input type="text" name="phonenumber" value="{{ old('phonenumber', $lead->phonenumber) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="+1 234 567 890">
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Company Name</label>
                        <input type="text" name="company" value="{{ old('company', $lead->company) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="Acme Corp">
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Lead Classification</h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Status <span class="text-red-500">*</span></label>
                            <select name="status" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('status') border-red-500 @enderror" required>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}" {{ old('status', $lead->status) == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                                @endforeach
                            </select>
                            @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Source <span class="text-red-500">*</span></label>
                            <select name="source" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('source') border-red-500 @enderror" required>
                                @foreach($sources as $source)
                                    <option value="{{ $source->id }}" {{ old('source', $lead->source) == $source->id ? 'selected' : '' }}>{{ $source->name }}</option>
                                @endforeach
                            </select>
                            @error('source') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Assigned Staff</label>
                            <select name="assigned" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                                <option value="">Unassigned</option>
                                @foreach($staff as $s)
                                    <option value="{{ $s->staffid }}" {{ old('assigned', $lead->assigned) == $s->staffid ? 'selected' : '' }}>{{ $s->firstname }} {{ $s->lastname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Lead Value</label>
                            <input type="number" step="0.01" name="lead_value" value="{{ old('lead_value', $lead->lead_value) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="0.00">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Convert to Client</label>
                        <select name="client_id" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            <option value="">No Client Associated</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->userid }}" {{ old('client_id', $lead->client_id) == $client->userid ? 'selected' : '' }}>{{ $client->company }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-sm font-medium text-slate-600">Description / Notes</label>
                <div id="editor" class="mt-1">{!! old('description', $lead->description) !!}</div>
                <input type="hidden" name="description" id="description">
            </div>
        </div>

        <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
            <a href="{{ route('admin.leads.index') }}" class="px-6 py-2 text-slate-600 font-semibold rounded-lg hover:bg-slate-200 transition-all">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Update Lead</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Enter lead details...',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link', 'clean']
                ]
            }
        });

        const form = document.querySelector('form');
        const descriptionInput = document.querySelector('#description');

        form.onsubmit = function() {
            descriptionInput.value = quill.root.innerHTML;
        };
    });
</script>
@endpush
