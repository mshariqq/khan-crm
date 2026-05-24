@extends('layouts.admin')

@section('breadcrumb', 'Edit Project')

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
            <h1 class="text-2xl font-bold text-slate-800">Edit Project</h1>
            <p class="text-slate-500 text-sm">Update the details of {{ $project->name }}.</p>
        </div>
        <a href="{{ route('admin.projects.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-300 transition-all flex items-center gap-2">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <form action="{{ route('admin.projects.update', $project->id) }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @csrf
        @method('PUT')
        <div class="p-6 space-y-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Project Details</h3>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Project Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $project->name) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('name') border-red-500 @enderror" required>
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Client <span class="text-red-500">*</span></label>
                        <select name="clientid" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('clientid') border-red-500 @enderror" required>
                            @foreach($clients as $client)
                                <option value="{{ $client->userid }}" {{ old('clientid', $project->clientid) == $client->userid ? 'selected' : '' }}>{{ $client->company }}</option>
                            @endforeach
                        </select>
                        @error('clientid') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Status <span class="text-red-500">*</span></label>
                        <select name="status" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('status') border-red-500 @enderror" required>
                            <option value="1" {{ old('status', $project->status) == '1' ? 'selected' : '' }}>Active</option>
                            <option value="2" {{ old('status', $project->status) == '2' ? 'selected' : '' }}>Completed</option>
                            <option value="3" {{ old('status', $project->status) == '3' ? 'selected' : '' }}>On Hold</option>
                            <option value="4" {{ old('status', $project->status) == '4' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Priority</label>
                        <select name="priority" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            <option value="low" {{ old('priority', $project->priority) == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority', $project->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority', $project->priority) == 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ old('priority', $project->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Category</label>
                        <select name="category_id" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            <option value="">No Category</option>
                            @foreach(\App\Models\ProjectCategory::all() as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $project->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Billing Type</label>
                        <select name="billing_type" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            <option value="1" {{ old('billing_type', $project->billing_type) == '1' ? 'selected' : '' }}>Fixed Rate</option>
                            <option value="2" {{ old('billing_type', $project->billing_type) == '2' ? 'selected' : '' }}>Project Hours</option>
                            <option value="3" {{ old('billing_type', $project->billing_type) == '3' ? 'selected' : '' }}>Task Hours</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Timeline</h3>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Start Date</label>
                            <input type="date" name="start_date" value="{{ old('start_date', $project->start_date) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            @error('start_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Deadline</label>
                            <input type="date" name="deadline" value="{{ old('deadline', $project->deadline) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            @error('deadline') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2 mt-6">Financials</h3>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Project Cost</label>
                            <input type="number" step="0.01" name="project_cost" value="{{ old('project_cost', $project->project_cost) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="0.00">
                            @error('project_cost') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Rate Per Hour</label>
                            <input type="number" step="0.01" name="project_rate_per_hour" value="{{ old('project_rate_per_hour', $project->project_rate_per_hour) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="0.00">
                            @error('project_rate_per_hour') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Estimated Hours</label>
                        <input type="number" name="estimated_hours" value="{{ old('estimated_hours', $project->estimated_hours) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="0">
                        @error('estimated_hours') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center gap-2 mt-2">
                        <input type="checkbox" name="progress_from_tasks" value="1" {{ old('progress_from_tasks', $project->progress_from_tasks) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <label class="text-sm text-slate-600">Auto-calculate progress from task completion</label>
                    </div>
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-sm font-medium text-slate-600">Description</label>
                <div id="editor" class="mt-1">{!! old('description', $project->description) !!}</div>
                <input type="hidden" name="description" id="description">
                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-3">
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Team Members</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    @foreach($staff as $member)
                        @php $checked = in_array($member->staffid, old('staff_ids', $project->staff->pluck('staffid')->toArray())); @endphp
                        <label class="flex items-center gap-2 p-3 rounded-lg border border-slate-200 hover:bg-slate-50 cursor-pointer transition-all">
                            <input type="checkbox" name="staff_ids[]" value="{{ $member->staffid }}" {{ $checked ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-slate-700">{{ $member->firstname }} {{ $member->lastname }}</span>
                        </label>
                    @endforeach
                </div>
                @error('staff_ids') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
            <a href="{{ route('admin.projects.index') }}" class="px-6 py-2 text-slate-600 font-semibold rounded-lg hover:bg-slate-200 transition-all">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Update Project</button>
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
            placeholder: 'Project scope and objectives...',
            modules: {
                toolbar: {
                    container: [
                        ['bold', 'italic', 'underline'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['image', 'link', 'clean']
                    ],
                    handlers: {
                        image: function() {
                            const input = document.createElement('input');
                            input.setAttribute('type', 'file');
                            input.setAttribute('accept', 'image/*');
                            input.click();
                            input.onchange = function() {
                                const file = input.files[0];
                                if (!file) return;
                                const formData = new FormData();
                                formData.append('image', file);
                                formData.append('_token', '{{ csrf_token() }}');
                                fetch('/admin/projects/upload-editor-image', {
                                    method: 'POST',
                                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                    body: formData
                                })
                                .then(r => r.json())
                                .then(data => {
                                    if (data.url) {
                                        const range = quill.getSelection(true);
                                        quill.insertEmbed(range.index, 'image', data.url);
                                    }
                                })
                                .catch(() => alert('Image upload failed'));
                            };
                        }
                    }
                }
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