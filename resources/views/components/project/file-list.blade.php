@props(['files' => [], 'uploadUrl' => '', 'deleteUrl' => '', 'title' => 'Files'])

<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-slate-800">{{ $title }}</h3>
        @if($uploadUrl)
            <label class="px-4 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all cursor-pointer flex items-center gap-2">
                <i class="bi bi-upload"></i> Upload File
                <input type="file" class="hidden" id="file-input" accept="*/*" multiple>
            </label>
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        @forelse($files as $file)
            <div class="group relative p-4 bg-slate-50 rounded-xl border border-slate-200 hover:border-slate-300 transition-all">
                @if($deleteUrl)
                    <button 
                        type="button" 
                        class="delete-file-btn absolute top-2 right-2 w-6 h-6 bg-white rounded-full border border-red-200 text-red-600 opacity-0 group-hover:opacity-100 transition-all hover:bg-red-50"
                        data-file-id="{{ $file->id }}"
                    >
                        <i class="bi bi-x text-sm"></i>
                    </button>
                @endif

                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-white border border-slate-200 flex items-center justify-center">
                        @php
                            $ext = pathinfo($file->file_name ?? '', PATHINFO_EXTENSION);
                            $icon = match(strtolower($ext)) {
                                'pdf' => 'bi-file-pdf text-red-500',
                                'doc', 'docx' => 'bi-file-word text-blue-600',
                                'xls', 'xlsx' => 'bi-file-excel text-green-600',
                                'ppt', 'pptx' => 'bi-file-powerpoint text-orange-500',
                                'jpg', 'jpeg', 'png', 'gif' => 'bi-file-image text-purple-500',
                                'zip', 'rar', '7z' => 'bi-file-zip text-amber-600',
                                default => 'bi-file-earmark text-slate-500'
                            };
                        @endphp
                        <i class="bi {{ $icon }} text-lg"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-slate-800 truncate" title="{{ $file->file_name ?? 'File' }}">
                            {{ $file->file_name ?? 'File' }}
                        </p>
                        <div class="flex items-center justify-between mt-1">
                            <span class="text-xs text-slate-500">
                                {{ isset($file->file_size) ? number_format($file->file_size / 1024, 1) . ' KB' : 'Unknown size' }}
                            </span>
                            <span class="text-xs text-slate-500">
                                {{ isset($file->created_at) ? $file->created_at->diffForHumans() : '' }}
                            </span>
                        </div>
                        <div class="mt-2">
                            <a 
                                href="{{ $file->fileurl ?? '#' }}" 
                                target="_blank" 
                                class="text-xs text-blue-600 hover:text-blue-700 font-medium"
                            >
                                <i class="bi bi-download"></i> Download
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-8 text-slate-500">
                <i class="bi bi-folder2-open text-3xl opacity-50 mb-2"></i>
                <p class="text-sm">No files uploaded yet</p>
            </div>
        @endforelse
    </div>

    <div id="upload-progress" class="hidden">
        <div class="bg-slate-100 rounded-xl p-4">
            <p class="text-sm font-medium text-slate-700 mb-2">Uploading...</p>
            <div class="w-full bg-slate-200 rounded-full h-2">
                <div id="upload-progress-bar" class="bg-blue-600 h-2 rounded-full transition-all" style="width: 0%"></div>
            </div>
        </div>
    </div>
</div>

@if($uploadUrl || $deleteUrl)
    @push('scripts')
    <script>
        $(document).ready(function() {
            @if($uploadUrl)
                $('#file-input').on('change', function(e) {
                    const files = e.target.files;
                    if (files.length === 0) return;

                    const formData = new FormData();
                    for (let i = 0; i < files.length; i++) {
                        formData.append('files[]', files[i]);
                    }
                    formData.append('_token', '{{ csrf_token() }}');

                    $('#upload-progress').removeClass('hidden');
                    $('#upload-progress-bar').css('width', '0%');

                    $.ajax({
                        url: '{{ $uploadUrl }}',
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        xhr: function() {
                            const xhr = new window.XMLHttpRequest();
                            xhr.upload.addEventListener('progress', function(e) {
                                if (e.lengthComputable) {
                                    const percentComplete = (e.loaded / e.total) * 100;
                                    $('#upload-progress-bar').css('width', percentComplete + '%');
                                }
                            });
                            return xhr;
                        },
                        success: function(response) {
                            $('#upload-progress').addClass('hidden');
                            $('#file-input').val('');
                            if (response.success) {
                                location.reload();
                            } else {
                                alert('Upload failed: ' + response.message);
                            }
                        },
                        error: function(xhr) {
                            $('#upload-progress').addClass('hidden');
                            alert('Upload failed');
                            console.error('Upload error:', xhr);
                        }
                    });
                });
            @endif

            @if($deleteUrl)
                $('.delete-file-btn').on('click', function(e) {
                    e.stopPropagation();
                    const fileId = $(this).data('file-id');
                    
                    if (!confirm('Are you sure you want to delete this file?')) {
                        return;
                    }

                    $.ajax({
                        url: '{{ $deleteUrl }}'.replace('__FILE_ID__', fileId),
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                location.reload();
                            } else {
                                alert('Failed to delete file: ' + response.message);
                            }
                        },
                        error: function(xhr) {
                            alert('Failed to delete file');
                            console.error('Delete error:', xhr);
                        }
                    });
                });
            @endif
        });
    </script>
    @endpush
@endif