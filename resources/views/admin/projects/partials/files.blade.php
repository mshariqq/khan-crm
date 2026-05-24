<div id="files-app" x-data="fileManager()">
    <style>
    .drop-zone { border: 2px dashed #cbd5e1; transition: all 0.2s; }
    .drop-zone.drag-over { border-color: #3b82f6; background: #eff6ff; }
    .file-card .actions { opacity: 0; }
    .file-card:hover .actions { opacity: 1; }
    .file-card.selected { border-color: #3b82f6; background: #eff6ff; }
    .preview-modal-bg { background: rgba(0,0,0,0.85); }
    </style>

    {{-- Toolbar --}}
    <div class="flex items-center justify-between mb-4 gap-3 flex-wrap">
        <div class="flex items-center gap-2">
            <h3 class="text-sm font-semibold text-slate-800">
                Files <span class="text-slate-400 font-normal">({{ $files->total() }})</span>
            </h3>
            @if($totalSize)
            <span class="text-[10px] text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded-full">
                {{ \App\Models\ProjectFile::make()->setAttribute('file_size', $totalSize)->file_size_formatted ?? number_format($totalSize / 1048576, 1) . ' MB' }} total
            </span>
            @endif
        </div>
        <div class="flex items-center gap-2">
            <select id="fileTypeFilter" onchange="filterFiles()" class="px-2 py-1 bg-slate-50 border border-slate-200 rounded text-xs focus:ring-1 focus:ring-blue-500 outline-none">
                <option value="">All Types</option>
                <option value="image">Images</option>
                <option value="pdf">PDFs</option>
                <option value="video">Videos</option>
                <option value="audio">Audio</option>
                <option value="office">Documents</option>
                <option value="archive">Archives</option>
                <option value="other">Other</option>
            </select>
            <button onclick="toggleSelectMode()" class="px-2 py-1 bg-slate-50 border border-slate-200 rounded text-xs text-slate-500 hover:text-slate-700" title="Select files">
                <i class="bi bi-check2-square"></i>
            </button>
            <button onclick="document.getElementById('folderModal').classList.remove('hidden')" class="px-2 py-1 bg-slate-50 border border-slate-200 rounded text-xs text-slate-500 hover:text-slate-700" title="New Folder">
                <i class="bi bi-folder-plus"></i>
            </button>
            <label class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 flex items-center gap-1 cursor-pointer">
                <i class="bi bi-cloud-upload"></i> Upload
                <input type="file" class="hidden" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.csv,.txt,.zip,.rar,.mp4,.avi,.mov,.mp3,.wav" onchange="uploadFiles(this)">
            </label>
        </div>
    </div>

    {{-- Bulk Actions Bar --}}
    <div id="bulkActions" class="hidden bg-blue-50 border border-blue-200 rounded-lg px-4 py-2 mb-3 flex items-center gap-3">
        <span class="text-xs text-blue-700"><span id="selectedCount">0</span> selected</span>
        <button onclick="bulkDownload()" class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700"><i class="bi bi-download"></i> Download</button>
        <button onclick="bulkMove()" class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded hover:bg-blue-200"><i class="bi bi-folder"></i> Move</button>
        <button onclick="clearSelection()" class="px-2 py-1 text-slate-500 text-xs hover:text-slate-700">Cancel</button>
    </div>

    {{-- Folder Breadcrumb + Grid --}}
    <div class="flex items-center gap-1 mb-3 text-xs text-slate-500 flex-wrap">
        <button onclick="selectFolder(null)" class="hover:text-blue-600 {{ is_null(request('folder_id')) ? 'text-blue-600 font-semibold' : '' }}"><i class="bi bi-house"></i> All</button>
        @foreach($folders as $folder)
        <span class="mx-1">/</span>
        <button onclick="selectFolder({{ $folder->id }})" class="hover:text-blue-600 flex items-center gap-1 {{ request('folder_id') == $folder->id ? 'text-blue-600 font-semibold' : '' }}">
            <i class="bi bi-folder"></i> {{ $folder->name }}
            <span class="text-[10px] text-slate-400">({{ $folder->files_count }})</span>
        </button>
        @endforeach
    </div>

    {{-- Upload Progress --}}
    <div id="uploadProgress" class="hidden mb-3">
        <div class="flex items-center justify-between text-xs text-slate-600 mb-1">
            <span id="uploadStatus">Uploading...</span>
            <span id="uploadPercent">0%</span>
        </div>
        <div class="w-full bg-slate-100 rounded-full h-2">
            <div id="uploadProgressBar" class="bg-blue-600 h-2 rounded-full transition-all" style="width: 0%"></div>
        </div>
    </div>

    {{-- Drop Zone --}}
    <div id="dropZone" class="drop-zone rounded-xl p-8 mb-4 text-center hidden">
        <i class="bi bi-cloud-upload text-3xl text-slate-300 mb-2 block"></i>
        <p class="text-sm text-slate-500">Drop files here to upload</p>
        <p class="text-xs text-slate-400 mt-1">Max 10MB per file</p>
    </div>

    {{-- Files Grid --}}
    @if($files->count() > 0)
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
        @foreach($files as $file)
        @php
        $isImage = $file->is_image;
        $ext = $file->extension;
        $fSize = $file->file_size_formatted;
        $previewType = $file->preview_type;
        $fileUrl = app(\App\Services\FileService::class)->url($file->file_name);
        @endphp
        <div class="file-card p-3 bg-white border border-slate-200 rounded-lg hover:border-slate-300 transition-all group relative"
             data-type="{{ $previewType }}"
             data-ext="{{ $ext }}"
             data-folder="{{ $file->folder_id ?? '' }}"
             data-id="{{ $file->id }}"
             draggable="true"
             ondragstart="dragFile(event, {{ $file->id }})">
            {{-- Selection checkbox --}}
            <div class="absolute top-1 left-1 z-10 hidden select-checkbox">
                <input type="checkbox" value="{{ $file->id }}" onchange="updateSelection()" class="rounded border-slate-300 text-blue-600">
            </div>

            {{-- Thumbnail --}}
            <div class="w-full aspect-square rounded-lg mb-2 overflow-hidden flex items-center justify-center cursor-pointer @if($isImage) bg-slate-100 @else bg-blue-50 @endif relative"
                 onclick="previewFile({{ $file->id }})">
                @if($isImage)
                <img src="{{ $fileUrl }}" class="w-full h-full object-cover" alt="" loading="lazy">
                @else
                <div class="text-center">
                    <i class="bi {{ $file->file_icon }} text-3xl @if(in_array($ext,['pdf']) && $ext !== 'csv') text-red-500 @elseif(in_array($ext,['doc','docx'])) text-blue-500 @elseif(in_array($ext,['xls','xlsx','csv'])) text-green-500 @else text-slate-400 @endif"></i>
                    <p class="text-[10px] text-slate-400 mt-1 uppercase">{{ $ext }}</p>
                </div>
                @endif
                @if($file->virus_scan_status === 'pending')
                <span class="absolute top-1 right-1 text-[8px] bg-amber-100 text-amber-700 px-1 rounded">pending scan</span>
                @endif
                @if($file->virus_scan_status === 'infected')
                <span class="absolute top-1 right-1 text-[8px] bg-red-100 text-red-700 px-1 rounded">⚠ infected</span>
                @endif
                @if($file->isExpired())
                <span class="absolute top-1 right-1 text-[8px] bg-red-100 text-red-700 px-1 rounded">expired</span>
                @endif
                @if($file->document_status)
                <span class="absolute bottom-1 left-1 text-[8px] {{ $file->document_status_color }} px-1 rounded">{{ $file->document_status_label }}</span>
                @endif
            </div>

            {{-- File info --}}
            <div class="flex items-start justify-between gap-1">
                <div class="flex-1 min-w-0" ondblclick="renameFile({{ $file->id }}, '{{ str_replace("'", "\'", $file->original_file_name) }}')">
                    <p class="text-xs text-slate-700 font-medium truncate" title="{{ $file->original_file_name }}">{{ $file->original_file_name }}</p>
                    <div class="flex items-center gap-2 text-[10px] text-slate-400">
                        @if($fSize)<span>{{ $fSize }}</span>@endif
                        <span>{{ $file->dateadded ? $file->dateadded->diffForHumans() : '' }}</span>
                        @if($file->file_version > 1)<span class="bg-slate-100 px-1 rounded">v{{ $file->file_version }}</span>@endif
                    </div>
                </div>
            </div>

            {{-- Action buttons --}}
            <div class="actions absolute top-1 right-1 flex items-center gap-0.5 transition-opacity">
                <a href="{{ $fileUrl }}" target="_blank" class="p-1 text-slate-400 hover:text-blue-600" title="Open">
                    <i class="bi bi-box-arrow-up-right text-[10px]"></i>
                </a>
                <a href="{{ route('admin.projects.files.download', [$project->id, $file->id]) }}" class="p-1 text-slate-400 hover:text-blue-600" title="Download">
                    <i class="bi bi-download text-[10px]"></i>
                </a>
                <button onclick="renameFile({{ $file->id }}, '{{ str_replace("'", "\'", $file->original_file_name) }}')" class="p-1 text-slate-400 hover:text-blue-600" title="Rename">
                    <i class="bi bi-pencil text-[10px]"></i>
                </button>
                <button onclick="showFileInfo({{ $file->id }})" class="p-1 text-slate-400 hover:text-slate-600" title="Info">
                    <i class="bi bi-info-circle text-[10px]"></i>
                </button>
                <button onclick="deleteFile({{ $file->id }})" class="p-1 text-slate-400 hover:text-red-600" title="Delete">
                    <i class="bi bi-trash text-[10px]"></i>
                </button>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4">
        {{ $files->links() }}
    </div>
    @else
    <div class="text-center py-16">
        <div class="w-20 h-20 mx-auto mb-4 bg-slate-50 rounded-full flex items-center justify-center">
            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
        </div>
        <p class="text-sm font-medium text-slate-500 mb-1">No files yet</p>
        <p class="text-xs text-slate-400 mb-4">Drop files here or click Upload to get started</p>
        <label class="inline-block px-4 py-2 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 cursor-pointer">
            <i class="bi bi-cloud-upload"></i> Upload Files
            <input type="file" class="hidden" multiple onchange="uploadFiles(this)">
        </label>
    </div>
    @endif

    {{-- Preview Modal --}}
    <div id="previewModal" class="fixed inset-0 preview-modal-bg hidden z-50 flex items-center justify-center p-4" onclick="if(event.target===this)closePreview()">
        <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-auto relative">
            <button onclick="closePreview()" class="absolute top-3 right-3 p-1 bg-white rounded-full shadow z-10 text-slate-500 hover:text-slate-700">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
            <div id="previewContent" class="p-4"></div>
        </div>
    </div>

    {{-- Rename Modal --}}
    <div id="renameModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-4">
            <h3 class="text-sm font-semibold text-slate-800 mb-3">Rename File</h3>
            <input type="text" id="renameInput" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 mb-3" maxlength="255">
            <input type="hidden" id="renameFileId">
            <div class="flex justify-end gap-2">
                <button onclick="document.getElementById('renameModal').classList.add('hidden')" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">Cancel</button>
                <button onclick="saveRename()" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Save</button>
            </div>
        </div>
    </div>

    {{-- Folder Modal --}}
    <div id="folderModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-4">
            <h3 class="text-sm font-semibold text-slate-800 mb-3">New Folder</h3>
            <input type="text" id="folderNameInput" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 mb-3" placeholder="Folder name..." maxlength="255">
            <div class="flex justify-end gap-2">
                <button onclick="document.getElementById('folderModal').classList.add('hidden')" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">Cancel</button>
                <button onclick="createFolder()" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Create</button>
            </div>
        </div>
    </div>

    {{-- Info/Details Modal --}}
    <div id="infoModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4">
            <div class="flex items-center justify-between p-4 border-b border-slate-200">
                <h3 class="text-sm font-semibold text-slate-800">File Details</h3>
                <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="p-1 text-slate-400 hover:text-slate-600"><i class="bi bi-x-lg"></i></button>
            </div>
            <div id="infoContent" class="p-4 space-y-3 text-sm">
                <div class="animate-pulse flex space-x-4"><div class="flex-1 space-y-2"><div class="h-3 bg-slate-200 rounded"></div><div class="h-3 bg-slate-200 rounded w-3/4"></div></div></div>
            </div>
        </div>
    </div>

    {{-- Move to Folder Modal --}}
    <div id="moveModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-4">
            <h3 class="text-sm font-semibold text-slate-800 mb-3">Move to Folder</h3>
            <select id="moveFolderSelect" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 mb-3">
                <option value="">No Folder (Root)</option>
                @foreach($folders as $folder)
                <option value="{{ $folder->id }}">{{ $folder->name }}</option>
                @endforeach
            </select>
            <div class="flex justify-end gap-2">
                <button onclick="document.getElementById('moveModal').classList.add('hidden')" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">Cancel</button>
                <button onclick="saveMove()" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Move</button>
            </div>
        </div>
    </div>

    {{-- Expiry Modal --}}
    <div id="expiryModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-4">
            <h3 class="text-sm font-semibold text-slate-800 mb-3">Set File Expiry</h3>
            <input type="datetime-local" id="expiryInput" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 mb-3">
            <input type="hidden" id="expiryFileId">
            <div class="flex items-center gap-2 mb-3">
                <button onclick="document.getElementById('expiryFileId').value='';document.getElementById('expiryInput').value='';saveExpiry()" class="text-xs text-red-600 hover:text-red-700">Clear Expiry</button>
            </div>
            <div class="flex justify-end gap-2">
                <button onclick="document.getElementById('expiryModal').classList.add('hidden')" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">Cancel</button>
                <button onclick="saveExpiry()" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Save</button>
            </div>
        </div>
    </div>
</div>

<script>
const FILE_API_BASE = '{{ route('admin.files.get') }}';
let selectMode = false;
let selectedFileIds = new Set();

function uploadFiles(input) {
    const files = input.files;
    if (files.length === 0) return;
    if (Array.from(files).some(f => f.size > 10 * 1024 * 1024)) {
        alert('Each file must be under 10MB');
        return;
    }
    const formData = new FormData();
    formData.append('project_id', {{ $project->id }});
    const folderId = new URLSearchParams(window.location.search).get('folder_id');
    if (folderId) formData.append('folder_id', folderId);
    for (let i = 0; i < files.length; i++) {
        formData.append('files[]', files[i]);
    }
    // Show progress bar
    const progressEl = document.getElementById('uploadProgress');
    const progressBar = document.getElementById('uploadProgressBar');
    const progressPercent = document.getElementById('uploadPercent');
    const uploadStatus = document.getElementById('uploadStatus');
    progressEl.classList.remove('hidden');
    progressBar.style.width = '0%';
    progressPercent.textContent = '0%';
    uploadStatus.textContent = 'Uploading ' + files.length + ' file(s)...';

    const xhr = new XMLHttpRequest();
    xhr.upload.addEventListener('progress', function(e) {
        if (e.lengthComputable) {
            const pct = Math.round((e.loaded / e.total) * 100);
            progressBar.style.width = pct + '%';
            progressPercent.textContent = pct + '%';
        }
    });
    xhr.addEventListener('load', function() {
        if (xhr.status >= 200 && xhr.status < 300) {
            try {
                const data = JSON.parse(xhr.responseText);
                if (data.success) {
                    uploadStatus.textContent = 'Upload complete!';
                    progressBar.className = 'bg-emerald-500 h-2 rounded-full transition-all';
                    progressBar.style.width = '100%';
                    setTimeout(() => { progressEl.classList.add('hidden'); loadTab('files'); }, 800);
                } else {
                    uploadStatus.textContent = data.message || 'Upload failed';
                    progressBar.className = 'bg-red-500 h-2 rounded-full transition-all';
                }
            } catch(e) {
                uploadStatus.textContent = 'Upload failed';
                progressBar.className = 'bg-red-500 h-2 rounded-full transition-all';
            }
        } else {
            uploadStatus.textContent = 'Upload failed';
            progressBar.className = 'bg-red-500 h-2 rounded-full transition-all';
        }
    });
    xhr.addEventListener('error', function() {
        uploadStatus.textContent = 'Upload failed - network error';
        progressBar.className = 'bg-red-500 h-2 rounded-full transition-all';
    });
    xhr.open('POST', '{{ route("admin.files.upload") }}');
    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.send(formData);
    input.value = '';
}

function deleteFile(id) {
    if (!confirm('Delete this file?')) return; // styled via modal for form submissions
    fetch('{{ route('admin.files.get') }}/' + id, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' },
        body: new URLSearchParams({ _method: 'DELETE' })
    })
    .then(r => r.json())
    .then(data => { if (data.success) loadTab('files'); else alert(data.message || 'Delete failed'); })
    .catch(() => alert('Delete failed'));
}

function previewFile(id) {
    const modal = document.getElementById('previewModal');
    const content = document.getElementById('previewContent');
    content.innerHTML = '<div class="flex items-center justify-center p-12"><div class="animate-spin w-8 h-8 border-2 border-blue-600 border-t-transparent rounded-full"></div></div>';
    modal.classList.remove('hidden');
    fetch(FILE_API_BASE + '/' + id + '/preview', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) { content.innerHTML = '<p class="text-red-500 p-4">' + (data.message || 'Preview failed') + '</p>'; return; }
        const f = data.file;
        const p = data.preview;
        let html = '';
        if (p.type === 'image') {
            html = '<img src="' + p.url + '" class="max-w-full max-h-[80vh] mx-auto" alt="' + (f.original_file_name || '') + '">';
        } else if (p.type === 'pdf') {
            html = '<iframe src="' + p.url + '" class="w-full h-[80vh]" frameborder="0"></iframe>';
        } else if (p.type === 'video') {
            html = '<video controls class="w-full max-h-[80vh]" autoplay><source src="' + p.url + '" type="' + (p.mime || 'video/mp4') + '">Your browser does not support video.</video>';
        } else if (p.type === 'audio') {
            html = '<div class="flex items-center justify-center p-12"><audio controls autoplay><source src="' + p.url + '" type="' + (p.mime || 'audio/mpeg') + '"></audio></div>';
        } else if (p.type === 'csv') {
            let table = '<div class="overflow-auto max-h-[70vh]"><table class="w-full text-xs border-collapse"><thead><tr>';
            (p.header || []).forEach(h => { table += '<th class="border border-slate-200 px-2 py-1 bg-slate-50 text-left font-semibold">' + h + '</th>'; });
            table += '</tr></thead><tbody>';
            (p.rows || []).forEach(row => {
                table += '<tr>';
                row.forEach(cell => { table += '<td class="border border-slate-200 px-2 py-1">' + (cell || '') + '</td>'; });
                table += '</tr>';
            });
            table += '</tbody></table></div>';
            html = table;
        } else {
            html = '<div class="text-center py-12"><i class="bi bi-file-earmark text-4xl text-slate-300 mb-3 block"></i><p class="text-slate-500">Preview not available</p><a href="' + data.url + '" target="_blank" class="mt-3 inline-block px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Download</a></div>';
        }
        content.innerHTML = html;
    })
    .catch(() => { content.innerHTML = '<p class="text-red-500 p-4">Preview failed</p>'; });
}

function closePreview() {
    document.getElementById('previewModal').classList.add('hidden');
    document.getElementById('previewContent').innerHTML = '';
}

function renameFile(id, currentName) {
    document.getElementById('renameFileId').value = id;
    document.getElementById('renameInput').value = currentName;
    document.getElementById('renameModal').classList.remove('hidden');
    setTimeout(() => document.getElementById('renameInput').select(), 100);
}

function saveRename() {
    const id = document.getElementById('renameFileId').value;
    const name = document.getElementById('renameInput').value.trim();
    if (!name) return;
    fetch(FILE_API_BASE + '/' + id + '/rename', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ name, _method: 'PUT' })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) { document.getElementById('renameModal').classList.add('hidden'); loadTab('files'); }
        else alert(data.message || 'Rename failed');
    })
    .catch(() => alert('Rename failed'));
}

function createFolder() {
    const name = document.getElementById('folderNameInput').value.trim();
    if (!name) return;
    fetch('/admin/projects/{{ $project->id }}/file-folders', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ name })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) { document.getElementById('folderNameInput').value = ''; document.getElementById('folderModal').classList.add('hidden'); loadTab('files'); }
        else alert(data.message || 'Failed');
    })
    .catch(() => alert('Failed'));
}

function selectFolder(id) {
    const params = new URLSearchParams(window.location.search);
    if (id) params.set('folder_id', id);
    else params.delete('folder_id');
    const url = window.location.pathname + '?' + params.toString();
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => { if (data.html) document.getElementById('dynamic-content').innerHTML = data.html; });
}

function showFileInfo(id) {
    const modal = document.getElementById('infoModal');
    const content = document.getElementById('infoContent');
    modal.classList.remove('hidden');
    fetch(FILE_API_BASE + '/' + id + '/preview', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) { content.innerHTML = '<p class="text-red-500">Failed to load file info</p>'; return; }
        const f = data.file;
        let html = '';
        html += '<div class="flex items-center gap-3 pb-3 border-b border-slate-100"><div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center"><i class="bi ' + (f.file_icon || 'bi-file-earmark') + ' text-blue-600"></i></div><div><p class="font-medium text-slate-800">' + (f.original_file_name || f.file_name) + '</p><p class="text-xs text-slate-400">' + (f.filetype || '') + '</p></div></div>';
        html += '<div class="grid grid-cols-2 gap-2 text-xs"><div><span class="text-slate-400">Size</span><p class="text-slate-700 font-medium">' + (f.file_size_formatted || (f.file_size ? Math.round(f.file_size/1024) + ' KB' : 'N/A')) + '</p></div>';
        html += '<div><span class="text-slate-400">Type</span><p class="text-slate-700 font-medium uppercase">' + (f.extension || '') + '</p></div>';
        html += '<div><span class="text-slate-400">Uploaded</span><p class="text-slate-700 font-medium">' + (f.dateadded ? new Date(f.dateadded).toLocaleDateString() : 'N/A') + '</p></div>';
        html += '<div><span class="text-slate-400">Uploaded by</span><p class="text-slate-700 font-medium">' + (f.staff?.full_name || 'Unknown') + '</p></div>';
        html += '<div><span class="text-slate-400">Version</span><p class="text-slate-700 font-medium">' + (f.file_version || 1) + '</p></div>';
        html += '<div><span class="text-slate-400">Virus Scan</span><p class="text-slate-700 font-medium">' + (f.virus_scan_status || 'pending') + '</p></div>';
        html += '<div class="col-span-2"><span class="text-slate-400">Downloads</span><p class="text-slate-700 font-medium">' + (f.downloads_count || 0) + ' times</p></div>';
        html += '</div>';
        html += '<div class="flex gap-2 pt-3 border-t border-slate-100 mt-3">';
        html += '<a href="/admin/projects/' + f.project_id + '/files/' + f.id + '/download" class="px-3 py-1.5 bg-blue-100 text-blue-700 text-xs rounded-lg hover:bg-blue-200"><i class="bi bi-download"></i> Download</a>';
        html += '<button onclick="document.getElementById(\'infoModal\').classList.add(\'hidden\');setExpiry(' + f.id + ',\'' + (f.expires_at || '') + '\')" class="px-3 py-1.5 bg-slate-100 text-slate-600 text-xs rounded-lg hover:bg-slate-200"><i class="bi bi-clock"></i> Set Expiry</button>';
        html += '</div>';
        // Download history
        if (data.downloads) {
            html += '<div class="pt-3 border-t border-slate-100 mt-3"><h4 class="text-xs font-semibold text-slate-700 mb-2">Download History</h4>';
            html += '<div class="max-h-32 overflow-y-auto space-y-1">';
            (data.downloads || []).forEach(d => {
                html += '<div class="text-[10px] text-slate-500 flex items-center justify-between"><span>' + (d.staff?.full_name || d.contact_id ? 'Client' : 'Unknown') + '</span><span>' + new Date(d.downloaded_at).toLocaleString() + '</span></div>';
            });
            html += '</div></div>';
        }
        content.innerHTML = html;
    })
    .catch(() => { content.innerHTML = '<p class="text-red-500">Failed to load</p>'; });
}

function setExpiry(id, currentExpiry) {
    document.getElementById('expiryFileId').value = id;
    document.getElementById('expiryInput').value = currentExpiry ? currentExpiry.slice(0,16) : '';
    document.getElementById('infoModal').classList.add('hidden');
    document.getElementById('expiryModal').classList.remove('hidden');
}

function saveExpiry() {
    const id = document.getElementById('expiryFileId').value;
    const expiresAt = document.getElementById('expiryInput').value || null;
    fetch(FILE_API_BASE + '/' + id + '/expiry', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ expires_at: expiresAt })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) { document.getElementById('expiryModal').classList.add('hidden'); loadTab('files'); }
        else alert(data.message || 'Failed');
    })
    .catch(() => alert('Failed'));
}

function toggleSelectMode() {
    selectMode = !selectMode;
    document.querySelectorAll('.select-checkbox').forEach(el => el.classList.toggle('hidden', !selectMode));
    document.getElementById('bulkActions').classList.toggle('hidden', !selectMode);
    if (!selectMode) clearSelection();
}

function updateSelection() {
    selectedFileIds = new Set();
    document.querySelectorAll('.select-checkbox input:checked').forEach(cb => selectedFileIds.add(cb.value));
    document.getElementById('selectedCount').textContent = selectedFileIds.size;
    document.querySelectorAll('.file-card').forEach(card => {
        card.classList.toggle('selected', selectedFileIds.has(card.dataset.id));
    });
}

function clearSelection() {
    selectedFileIds.clear();
    document.querySelectorAll('.select-checkbox input').forEach(cb => cb.checked = false);
    document.querySelectorAll('.file-card').forEach(card => card.classList.remove('selected'));
    document.getElementById('selectedCount').textContent = '0';
}

function bulkDownload() {
    if (selectedFileIds.size === 0) return;
    fetch('{{ route("admin.files.bulk-download") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ file_ids: Array.from(selectedFileIds) })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success && data.url) {
            window.open(data.url, '_blank');
            clearSelection();
            toggleSelectMode();
        } else alert(data.message || 'Bulk download failed');
    })
    .catch(() => alert('Bulk download failed'));
}

function bulkMove() {
    if (selectedFileIds.size === 0) return;
    document.getElementById('moveModal').classList.remove('hidden');
}

function saveMove() {
    const folderId = document.getElementById('moveFolderSelect').value || null;
    fetch('{{ route("admin.files.move-to-folder") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ file_ids: Array.from(selectedFileIds), folder_id: folderId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) { document.getElementById('moveModal').classList.add('hidden'); clearSelection(); toggleSelectMode(); loadTab('files'); }
        else alert(data.message || 'Move failed');
    })
    .catch(() => alert('Move failed'));
}

function filterFiles() {
    const type = document.getElementById('fileTypeFilter').value;
    document.querySelectorAll('.file-card').forEach(card => {
        if (!type || card.dataset.type === type || (type === 'other' && !['image','pdf','video','audio','office','archive'].includes(card.dataset.type))) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

// Drag and drop
const dropZone = document.getElementById('dropZone');
let dragCounter = 0;

document.addEventListener('dragover', e => { e.preventDefault(); });
document.addEventListener('drop', e => { e.preventDefault(); });

document.querySelector('#files-app').addEventListener('dragenter', e => {
    e.preventDefault();
    dragCounter++;
    dropZone.classList.remove('hidden');
    dropZone.classList.add('drag-over');
});

document.querySelector('#files-app').addEventListener('dragleave', e => {
    e.preventDefault();
    dragCounter--;
    if (dragCounter === 0) {
        dropZone.classList.add('hidden');
        dropZone.classList.remove('drag-over');
    }
});

document.querySelector('#files-app').addEventListener('drop', e => {
    e.preventDefault();
    dragCounter = 0;
    dropZone.classList.add('hidden');
    dropZone.classList.remove('drag-over');
    const files = e.dataTransfer.files;
    if (files.length === 0) return;
    const input = document.createElement('input');
    input.type = 'file';
    input.multiple = true;
    const dt = new DataTransfer();
    for (let i = 0; i < files.length; i++) dt.items.add(files[i]);
    input.files = dt.files;
    uploadFiles(input);
});

function dragFile(e, id) {
    e.dataTransfer.setData('text/plain', id);
}
</script>
