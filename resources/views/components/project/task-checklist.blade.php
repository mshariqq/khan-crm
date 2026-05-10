@props(['taskId', 'items' => [], 'canManage' => false])

<div class="space-y-3">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-slate-800">Task Checklist</h3>
        @if($canManage)
            <button 
                id="add-checklist-item-btn" 
                class="px-4 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all flex items-center gap-2"
            >
                <i class="bi bi-plus-lg"></i> Add Item
            </button>
        @endif
    </div>

    <div id="checklist-items" class="space-y-2">
        @forelse($items->sortBy('checklist_order') as $item)
            <div class="flex items-center gap-3 p-4 bg-slate-50 rounded-xl border border-slate-200 hover:border-slate-300 transition-all">
                @if($canManage)
                    <input 
                        type="checkbox" 
                        class="task-checkitem w-5 h-5 text-blue-600 rounded border-slate-300 focus:ring-blue-500"
                        data-item-id="{{ $item->id }}"
                        {{ $item->finished == 1 ? 'checked' : '' }}
                    >
                @else
                    <input 
                        type="checkbox" 
                        class="w-5 h-5 text-blue-600 rounded border-slate-300"
                        {{ $item->finished == 1 ? 'checked' : '' }}
                        disabled
                    >
                @endif
                <div class="flex-1 min-w-0">
                    <p class="text-sm {{ $item->finished == 1 ? 'line-through text-slate-500' : 'text-slate-800' }}">
                        {{ $item->description }}
                    </p>
                </div>
                @if($canManage)
                    <button 
                        class="delete-checkitem-btn text-red-600 hover:text-red-700 opacity-0 group-hover:opacity-100 transition-all"
                        data-item-id="{{ $item->id }}"
                    >
                        <i class="bi bi-x"></i>
                    </button>
                @endif
            </div>
        @empty
            <div class="text-center py-8 text-slate-500">
                <i class="bi bi-list-ul text-3xl opacity-50 mb-2"></i>
                <p class="text-sm">No checklist items yet</p>
                @if($canManage)
                    <button class="mt-3 text-blue-600 hover:text-blue-700 text-sm font-medium" id="add-first-item-btn">
                        <i class="bi bi-plus-lg"></i> Add First Item
                    </button>
                @endif
            </div>
        @endforelse
    </div>

    @if($canManage)
        <div id="add-item-form" class="hidden">
            <div class="flex items-center gap-2">
                <input 
                    type="text" 
                    id="checklist-item-input" 
                    placeholder="Enter checklist item..." 
                    class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-sm"
                >
                <button 
                    id="save-checklist-item-btn" 
                    class="px-4 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-xl hover:bg-green-700 transition-all"
                >
                    <i class="bi bi-check"></i>
                </button>
                <button 
                    id="cancel-add-item-btn" 
                    class="px-4 py-2.5 bg-slate-100 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-200 transition-all"
                >
                    <i class="bi bi-x"></i>
                </button>
            </div>
        </div>

        @push('scripts')
        <script>
            $(document).ready(function() {
                const $addBtn = $('#add-checklist-item-btn');
                const $addFirstBtn = $('#add-first-item-btn');
                const $form = $('#add-item-form');
                const $input = $('#checklist-item-input');
                const $saveBtn = $('#save-checklist-item-btn');
                const $cancelBtn = $('#cancel-add-item-btn');
                const $itemsContainer = $('#checklist-items');

                function showForm() {
                    $form.removeClass('hidden');
                    $addBtn.addClass('hidden');
                    $input.focus();
                }

                function hideForm() {
                    $form.addClass('hidden');
                    $addBtn.removeClass('hidden');
                    $input.val('');
                }

                $addBtn.on('click', showForm);
                $addFirstBtn.on('click', function(e) {
                    e.preventDefault();
                    showForm();
                });

                $cancelBtn.on('click', hideForm);

                $saveBtn.on('click', function() {
                    const description = $input.val().trim();
                    if (!description) {
                        alert('Please enter a description');
                        return;
                    }

                    $.ajax({
                        url: '{{ route("admin.task-checklist.store") }}',
                        method: 'POST',
                        data: {
                            task_id: {{ $taskId }},
                            description: description,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                location.reload();
                            } else {
                                alert('Failed to add item: ' + response.message);
                            }
                        },
                        error: function(xhr) {
                            alert('Failed to add item');
                            console.error('Error:', xhr);
                        }
                    });
                });

                $input.on('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        $saveBtn.click();
                    }
                });

                // Handle checkbox changes
                $('.task-checkitem').on('change', function() {
                    const itemId = $(this).data('item-id');
                    const checked = $(this).prop('checked');

                    $.ajax({
                        url: '{{ route("admin.task-checklist.update", "__ITEM_ID__") }}'.replace('__ITEM_ID__', itemId),
                        method: 'PUT',
                        data: {
                            finished: checked ? 1 : 0,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                location.reload();
                            } else {
                                alert('Failed to update item: ' + response.message);
                            }
                        },
                        error: function(xhr) {
                            alert('Failed to update item');
                            console.error('Error:', xhr);
                        }
                    });
                });

                // Handle item deletion
                $('.delete-checkitem-btn').on('click', function() {
                    const itemId = $(this).data('item-id');

                    if (!confirm('Are you sure you want to delete this item?')) {
                        return;
                    }

                    $.ajax({
                        url: '{{ route("admin.task-checklist.destroy", "__ITEM_ID__") }}'.replace('__ITEM_ID__', itemId),
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                location.reload();
                            } else {
                                alert('Failed to delete item: ' + response.message);
                            }
                        },
                        error: function(xhr) {
                            alert('Failed to delete item');
                            console.error('Error:', xhr);
                        }
                    });
                });
            });
        </script>
        @endpush
    @endif
</div>