@extends('layouts.admin')

@section('breadcrumb', 'Task Kanban')

@push('styles')
<style>
.kanban-board {
    min-height: calc(100vh - 200px);
}
.kanban-column {
    min-height: 400px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}
.kanban-column.droppable {
    background-color: rgba(59, 130, 246, 0.1);
    border-color: #3b82f6;
    transform: scale(1.02);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
}
.kanban-task-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    border-left: 4px solid transparent;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}
.kanban-task-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    background: white;
}
.kanban-task-card.priority-high { 
    border-left-color: #ef4444; 
    background: rgba(239, 68, 68, 0.05);
}
.kanban-task-card.priority-medium { 
    border-left-color: #f59e0b; 
    background: rgba(245, 158, 11, 0.05);
}
.kanban-task-card.priority-low { 
    border-left-color: #10b981; 
    background: rgba(16, 185, 129, 0.05);
}
.kanban-task-card.dragging {
    opacity: 0.7;
    transform: rotate(2deg) scale(0.98);
    cursor: grabbing;
}
.column-header {
    position: sticky;
    top: 0;
    z-index: 10;
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}
.add-task-btn {
    opacity: 0;
    transition: all 0.2s;
}
.kanban-column:hover .add-task-btn {
    opacity: 1;
}
.progress-ring {
    transition: stroke-dasharray 0.35s;
    transform: rotate(-90deg);
    transform-origin: 50% 50%;
}
.timer-badge {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}
.task-modal {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.modal-backdrop {
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}
</style>
@endpush

@section('main')
<div class="p-6 space-y-6 h-full flex flex-col">
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 flex-shrink-0">
        <div class="flex items-center gap-3">
            <a href="{{ url()->previous() }}" class="p-2.5 bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-200 transition-all">
                <i class="bi bi-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Task Board</h1>
                <p class="text-slate-500 text-sm">Drag and drop tasks to update status.</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.tasks.index') }}" class="px-4 py-2.5 bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-all flex items-center gap-2">
                <i class="bi bi-list"></i> List View
            </a>
            <a href="{{ route('admin.tasks.create') }}" class="px-4 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 flex items-center gap-2">
                <i class="bi bi-plus-lg"></i> Add Task
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-medium flex items-center gap-2 animate-fade-in flex-shrink-0">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Kanban Board --}}
    <div class="kanban-board flex-1 -mx-6 px-6 overflow-x-auto pb-4">
        <div class="flex gap-4 min-w-max h-full">
            @php
                $columns = [
                    'backlog' => ['label' => 'Backlog', 'color' => 'bg-slate-50', 'border' => 'border-slate-200', 'badge' => 'bg-slate-200 text-slate-700', 'dot' => 'bg-slate-400'],
                    'pending' => ['label' => 'To Do', 'color' => 'bg-slate-50', 'border' => 'border-slate-200', 'badge' => 'bg-slate-200 text-slate-700', 'dot' => 'bg-slate-400'],
                    'in_progress' => ['label' => 'In Progress', 'color' => 'bg-blue-50', 'border' => 'border-blue-200', 'badge' => 'bg-blue-200 text-blue-700', 'dot' => 'bg-blue-500'],
                    'completed' => ['label' => 'Done', 'color' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'badge' => 'bg-emerald-200 text-emerald-700', 'dot' => 'bg-emerald-500'],
                ];
            @endphp

@foreach($columns as $columnKey => $column)
    <div class="kanban-column {{ $column['color'] }} rounded-2xl border {{ $column['border'] }} w-80 flex-shrink-0 flex flex-col" data-status="{{ $columnKey }}">
    {{-- Column Header --}}
    <div class="column-header p-4 pb-3 flex-shrink-0 border-b border-transparent">
        <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <span class="w-3 h-3 rounded-full {{ $column['dot'] }} ring-2 ring-white"></span>
            <h3 class="font-bold text-sm text-slate-800">{{ $column['label'] }}</h3>
            <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $column['badge'] }} task-count-badge ring-1 ring-white/50">
            {{ count($tasks[$columnKey] ?? []) }}
            </span>
        </div>
        <button onclick="quickAddTask('{{ $columnKey }}')" class="add-task-btn w-7 h-7 flex items-center justify-center text-slate-400 hover:text-blue-600 hover:bg-white rounded-lg transition-all border border-transparent hover:border-slate-200" title="Quick add task">
            <i class="bi bi-plus-lg text-sm"></i>
        </button>
        </div>
        <div class="w-full bg-white/60 rounded-full h-2 shadow-inner">
        @php
        $totalTasks = 0;
        foreach ($columns as $key => $c) {
            $totalTasks += count($tasks[$key] ?? []);
        }
        $columnCount = count($tasks[$columnKey] ?? []);
        $colPercent = $totalTasks > 0 ? ($columnCount / $totalTasks) * 100 : 0;
        @endphp
        <div class="h-2 rounded-full transition-all duration-700 {{ $columnKey == 'completed' ? 'bg-emerald-500' : ($columnKey == 'in_progress' ? 'bg-blue-500' : 'bg-slate-400') }}" style="width: {{ $colPercent }}%"></div>
        </div>
    </div>

    {{-- Quick Add Container --}}
    <div class="quick-add-container px-4 pt-3"></div>

    {{-- Task Cards Container --}}
    <div class="task-cards-container flex-1 p-4 space-y-3 overflow-y-auto">
        @forelse($tasks[$columnKey] ?? [] as $task)
        <div class="kanban-task-card rounded-xl p-4 border shadow-sm priority-{{ $task->priority }}" 
             data-task-id="{{ $task->id }}" 
             onclick="openTaskModal({{ $task->id }})"
             draggable="true">
        
        {{-- Card Header: Title & Priority --}}
        <div class="flex items-start justify-between gap-3 mb-3">
            <h4 class="text-sm font-bold text-slate-900 leading-tight flex-1 line-clamp-2">{{ $task->name }}</h4>
            @if($task->priority == 'high' || $task->priority == 3)
            <span class="w-2.5 h-2.5 bg-red-500 rounded-full flex-shrink-0 mt-1 ring-2 ring-red-100" title="High Priority"></span>
            @elseif($task->priority == 'medium' || $task->priority == 2)
            <span class="w-2.5 h-2.5 bg-amber-500 rounded-full flex-shrink-0 mt-1 ring-2 ring-amber-100" title="Medium Priority"></span>
            @else
            <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full flex-shrink-0 mt-1 ring-2 ring-emerald-100" title="Low Priority"></span>
            @endif
        </div>

        {{-- Project Info --}}
        @if($task->project)
        <div class="flex items-center gap-2 mb-3 text-xs">
            <i class="bi bi-folder text-slate-400"></i>
            <span class="text-slate-600 font-medium truncate">{{ $task->project->name }}</span>
        </div>
        @endif

        {{-- Due Date & Time Tracking --}}
        <div class="flex items-center justify-between mb-3">
            @if($task->duedate)
            <div class="flex items-center gap-1.5">
                <i class="bi bi-calendar-event text-slate-400 text-xs"></i>
                <span class="text-xs font-medium {{ \Carbon\Carbon::parse($task->duedate)->isPast() && $task->status != 3 ? 'text-red-600' : 'text-slate-600' }}">
                {{ \Carbon\Carbon::parse($task->duedate)->format('M j') }}
                </span>
            </div>
            @endif
            {{-- Time Tracking Indicator --}}
            @if($task->timer_active)
            <div class="flex items-center gap-1 timer-badge">
                <i class="bi bi-stopwatch text-blue-600 text-xs"></i>
                <span class="text-xs font-bold text-blue-600">{{ $task->total_hours_today }}h</span>
            </div>
            @endif
        </div>

        {{-- Card Stats: Comments, Files, Progress --}}
        <div class="flex items-center gap-4 mb-3">
            @if($task->comments_count > 0)
            <div class="flex items-center gap-1">
                <i class="bi bi-chat-dots text-slate-400 text-xs"></i>
                <span class="text-xs font-medium text-slate-600">{{ $task->comments_count }}</span>
            </div>
            @endif
            
            @if($task->attachments_count > 0)
            <div class="flex items-center gap-1">
                <i class="bi bi-paperclip text-slate-400 text-xs"></i>
                <span class="text-xs font-medium text-slate-600">{{ $task->attachments_count }}</span>
            </div>
            @endif
            
            @if($task->progress > 0)
            <div class="flex items-center gap-2 flex-1">
                <div class="w-full bg-slate-200 rounded-full h-1.5">
                    <div class="bg-blue-600 h-1.5 rounded-full transition-all duration-500" style="width: {{ $task->progress }}%"></div>
                </div>
                <span class="text-xs font-bold text-slate-600">{{ $task->progress }}%</span>
            </div>
            @endif
        </div>

        {{-- Footer: Assignees --}}
        <div class="flex items-center justify-between pt-3 border-t border-slate-100/50">
            <div class="flex -space-x-2">
            @foreach($task->assignedStaff->take(3) as $assignee)
                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 border-2 border-white flex items-center justify-center text-[10px] font-bold text-white shadow-sm" 
                     title="{{ $assignee->firstname }} {{ $assignee->lastname }}">
                {{ substr($assignee->firstname, 0, 1) }}
                </div>
            @endforeach
            @if($task->assignedStaff->count() > 3)
                <div class="w-7 h-7 rounded-full bg-slate-100 border-2 border-white flex items-center justify-center text-[10px] font-bold text-slate-500 shadow-sm">
                +{{ $task->assignedStaff->count() - 3 }}
                </div>
            @endif
            </div>
            
            @if($task->assignedStaff->count() == 0)
            <button class="text-xs text-blue-600 hover:text-blue-700 font-semibold" onclick="event.stopPropagation(); assignTask({{ $task->id }})">
                Assign
            </button>
            @endif
        </div>
        </div>
        @empty
        <div class="text-center py-8">
        <div class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="bi bi-inboxes text-slate-400"></i>
        </div>
        <p class="text-slate-400 text-sm font-medium">No tasks yet</p>
        <p class="text-slate-400 text-xs mt-1">Add your first task to get started</p>
        </div>
        @endforelse
    </div>
    </div>
@endforeach
        </div>
    </div>
</div>

{{-- Task Quick View Modal --}}
<div id="taskModal" class="fixed inset-0 z-50 hidden">
    <div class="modal-backdrop absolute inset-0 bg-black/50" onclick="closeTaskModal()"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="task-modal bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden">
            <div id="taskModalContent">
                {{-- Modal content will be loaded here --}}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Define API routes for AJAX functionality
const TASK_ROUTES = {
    updateStatus: '{{ route('admin.tasks.update_status', ['id' => '__ID__']) }}',
    quickView: '{{ route('admin.tasks.quick_view', ['id' => '__ID__']) }}',
    quickCreate: '{{ route('admin.tasks.quick_create') }}'
};

// Enhanced drag-and-drop functionality
let draggedTask = null;

function initializeDragAndDrop() {
    const taskCards = document.querySelectorAll('.kanban-task-card[data-task-id]');
    const columns = document.querySelectorAll('.kanban-column');

    taskCards.forEach(card => {
        card.addEventListener('dragstart', handleDragStart);
        card.addEventListener('dragend', handleDragEnd);
        card.draggable = true;
    });

    columns.forEach(column => {
        column.addEventListener('dragover', handleDragOver);
        column.addEventListener('drop', handleDrop);
        column.addEventListener('dragleave', handleDragLeave);
    });
}

function handleDragStart(e) {
    draggedTask = this;
    this.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/html', this.innerHTML);
}

function handleDragEnd(e) {
    this.classList.remove('dragging');
    document.querySelectorAll('.kanban-column').forEach(col => {
        col.classList.remove('droppable');
    });
}

function handleDragOver(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
    this.classList.add('droppable');
}

function handleDragLeave(e) {
    if (!this.contains(e.relatedTarget)) {
        this.classList.remove('droppable');
    }
}

function handleDrop(e) {
    e.preventDefault();
    const column = this;
    column.classList.remove('droppable');

    if (draggedTask) {
        const taskId = draggedTask.dataset.taskId;
        const newStatus = column.dataset.status;
        
        // Animate the drop
        const card = draggedTask.cloneNode(true);
        card.style.opacity = '0';
        column.querySelector('.task-cards-container').appendChild(card);
        
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transition = 'opacity 0.3s ease';
        }, 10);

        // Update task status via AJAX
        updateTaskStatus(taskId, newStatus);
        
        // Remove original card
        draggedTask.remove();
    }
}

// AJAX status update
function updateTaskStatus(taskId, newStatus) {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    const url = TASK_ROUTES.updateStatus.replace('__ID__', taskId);

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success notification
            showNotification('Task status updated successfully', 'success');
            updateColumnCounts();
        } else {
            showNotification('Failed to update task status', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error updating task status', 'error');
    });
}

// Task Modal Functions
function openTaskModal(taskId) {
    const modal = document.getElementById('taskModal');
    const content = document.getElementById('taskModalContent');
    
    // Show loading state
    content.innerHTML = `
        <div class="p-8 text-center">
            <div class="w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <p class="text-slate-600">Loading task details...</p>
        </div>
    `;
    
    modal.classList.remove('hidden');
    
    // Fetch task details
    const url = TASK_ROUTES.quickView.replace('__ID__', taskId);
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                content.innerHTML = data.html;
            } else {
                content.innerHTML = `
                    <div class="p-8 text-center">
                        <p class="text-red-600">Failed to load task details</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            content.innerHTML = `
                <div class="p-8 text-center">
                    <p class="text-red-600">Error loading task</p>
                </div>
            `;
        });
}

function closeTaskModal() {
    const modal = document.getElementById('taskModal');
    modal.classList.add('hidden');
}

// Quick Add Task
function quickAddTask(columnStatus) {
    const column = document.querySelector(`[data-status="${columnStatus}"]`);
    const quickAddContainer = column.querySelector('.quick-add-container');
    
    quickAddContainer.innerHTML = `
        <div class="p-3 bg-white/80 rounded-xl border border-slate-200 shadow-sm">
            <input type="text" 
                   class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none mb-2" 
                   placeholder="Task title..."
                   id="quick-task-title-${columnStatus}">
            <div class="flex justify-between items-center">
                <select class="text-xs px-2 py-1 border border-slate-200 rounded focus:ring-2 focus:ring-blue-500 outline-none" id="quick-task-priority-${columnStatus}">
                    <option value="1">Low</option>
                    <option value="2" selected>Medium</option>
                    <option value="3">High</option>
                </select>
                <div class="flex gap-1">
                    <button onclick="cancelQuickAdd('${columnStatus}')" 
                            class="px-2 py-1 text-xs text-slate-500 hover:text-slate-700">Cancel</button>
                    <button onclick="saveQuickTask('${columnStatus}')" 
                            class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">Add</button>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById(`quick-task-title-${columnStatus}`).focus();
}

function cancelQuickAdd(columnStatus) {
    const column = document.querySelector(`[data-status="${columnStatus}"]`);
    const quickAddContainer = column.querySelector('.quick-add-container');
    quickAddContainer.innerHTML = '';
}

function saveQuickTask(columnStatus) {
    const title = document.getElementById(`quick-task-title-${columnStatus}`).value;
    const priority = document.getElementById(`quick-task-priority-${columnStatus}`).value;
    
    if (!title.trim()) return;
    
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    
    fetch(TASK_ROUTES.quickCreate, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            name: title,
            priority: priority,
            status: columnStatus === 'backlog' ? 0 : (columnStatus === 'pending' ? 1 : (columnStatus === 'in_progress' ? 2 : 3)),
            duedate: null
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Task created successfully', 'success');
            location.reload(); // Reload to show new task
        } else {
            showNotification('Failed to create task', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error creating task', 'error');
    });
}

// Notification system
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-xl text-white text-sm font-medium shadow-xl transition-all duration-300 transform translate-x-full`;
    
    if (type === 'success') {
        notification.classList.add('bg-emerald-500');
    } else if (type === 'error') {
        notification.classList.add('bg-red-500');
    } else {
        notification.classList.add('bg-blue-500');
    }
    
    notification.textContent = message;
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 10);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Update column task counts
function updateColumnCounts() {
    document.querySelectorAll('.kanban-column').forEach(column => {
        const status = column.dataset.status;
        const count = column.querySelectorAll('.kanban-task-card').length;
        const badge = column.querySelector('.task-count-badge');
        if (badge) {
            badge.textContent = count;
        }
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeDragAndDrop();
    
    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('taskModal');
            if (!modal.classList.contains('hidden')) {
                closeTaskModal();
            }
        }
    });
});
</script>
@endpush
</div>
@endsection
