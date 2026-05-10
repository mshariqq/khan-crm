<div class="flex items-center justify-between mb-4">
    <h3 class="text-sm font-semibold text-slate-800">Files ({{ $files->count() }})</h3>
    <label class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 flex items-center gap-1 cursor-pointer">
        <i class="bi bi-plus"></i> Upload
        <input type="file" class="hidden" multiple onchange="uploadFiles(this)">
    </label>
</div>

@php
    $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'];
@endphp

@if($files->count() > 0)
<div class="grid grid-cols-2 md:grid-cols-3 gap-3">
    @foreach($files as $file)
    @php $ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION)); $isImage = in_array($ext, $imageExts); @endphp
    <div class="p-3 bg-white border border-slate-200 rounded-lg hover:border-slate-300 transition-colors group relative">
        <div class="flex items-start justify-between">
            <div class="w-10 h-10 rounded-lg mb-2 flex-shrink-0 overflow-hidden @if(!$isImage) bg-blue-100 flex items-center justify-center @endif">
                @if($isImage)
                <a href="{{ url('FILES/' . $file->file_name) }}" target="_blank">
                    <img src="{{ url('FILES/' . $file->file_name) }}" class="w-10 h-10 object-cover rounded-lg" alt="">
                </a>
                @else
                <i class="bi bi-file-earmark text-blue-600 text-lg"></i>
                @endif
            </div>
            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                <a href="{{ url('FILES/' . $file->file_name) }}" target="_blank" class="p-1 text-slate-400 hover:text-blue-600 rounded hover:bg-slate-200" title="Download">
                    <i class="bi bi-download text-xs"></i>
                </a>
                <button onclick="deleteFile({{ $file->id }})" class="p-1 text-slate-400 hover:text-red-600 rounded hover:bg-slate-200" title="Delete">
                    <i class="bi bi-trash text-xs"></i>
                </button>
            </div>
        </div>
        <p class="text-xs text-slate-700 font-medium truncate">{{ $file->original_file_name ?? $file->file_name }}</p>
        <p class="text-[10px] text-slate-400">{{ $file->dateadded ? \Carbon\Carbon::parse($file->dateadded)->format('M j, Y') : '' }}</p>
    </div>
    @endforeach
</div>
@else
<div class="text-center py-12">
    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
        <i class="bi bi-folder text-slate-400 text-xl"></i>
    </div>
    <p class="text-sm text-slate-500">No files yet</p>
    <label class="mt-3 inline-block px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 cursor-pointer">
        <i class="bi bi-plus"></i> Upload
        <input type="file" class="hidden" multiple onchange="uploadFiles(this)">
    </label>
</div>
@endif

<script>
function uploadFiles(input) {
    const files = input.files;
    if (files.length === 0) return;

    const formData = new FormData();
    formData.append('project_id', {{ $project->id }});
    for (let i = 0; i < files.length; i++) {
        formData.append('files[]', files[i]);
    }

    fetch('{{ route("admin.files.upload") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadTab('files');
        } else {
            alert(data.message || 'Upload failed');
        }
    })
    .catch(error => {
        alert('Upload failed: ' + error.message);
    });
}

function deleteFile(id) {
    if (!confirm('Delete this file?')) return;

    fetch('/admin/files/' + id, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: new URLSearchParams({ _method: 'DELETE' })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            loadTab('files');
        } else {
            alert(data.message || 'Delete failed');
        }
    })
    .catch(() => alert('Delete failed'));
}
</script>
