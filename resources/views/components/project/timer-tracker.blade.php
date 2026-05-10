@props(['taskId', 'timers' => [], 'currentTimer' => null])

<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-slate-800">Time Tracking</h3>
        <div class="flex items-center gap-2">
            @if($currentTimer)
                <button 
                    id="stop-timer-btn" 
                    class="px-4 py-2.5 bg-red-600 text-white text-sm font-semibold rounded-xl hover:bg-red-700 transition-all flex items-center gap-2"
                >
                    <i class="bi bi-stop-circle"></i> Stop Timer
                </button>
            @else
                <button 
                    id="start-timer-btn" 
                    class="px-4 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-xl hover:bg-green-700 transition-all flex items-center gap-2"
                >
                    <i class="bi bi-play-circle"></i> Start Timer
                </button>
            @endif
        </div>
    </div>

    @if($currentTimer)
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-900">Timer Running</p>
                    <p class="text-lg font-bold text-blue-600" id="current-timer-display">
                        {{ $currentTimer->start_time->diffForHumans(null, true) }}
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-blue-700">Started at</p>
                    <p class="text-sm font-medium">{{ $currentTimer->start_time->format('M d, Y h:i A') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="space-y-2">
        @forelse($timers->sortByDesc('created_at') as $timer)
            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-200 hover:border-slate-300 transition-all">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                        {{ substr($timer->staff->full_name ?? 'U', 0, 1) }}
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-800">
                            {{ $timer->staff->full_name ?? 'Unknown User' }}
                        </p>
                        <p class="text-xs text-slate-500">
                            {{ $timer->start_time->format('M d, Y') }} • 
                            {{ $timer->start_time->format('h:i A') }} - 
                            {{ $timer->end_time ? $timer->end_time->format('h:i A') : 'Running' }}
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold text-slate-800">
                        @php
                            $duration = $timer->end_time 
                                ? $timer->start_time->diffInMinutes($timer->end_time)
                                : now()->diffInMinutes($timer->start_time);
                            $hours = floor($duration / 60);
                            $minutes = $duration % 60;
                        @endphp
                        @if($hours > 0)
                            {{ $hours }}h {{ $minutes }}m
                        @else
                            {{ $minutes }}m
                        @endif
                    </p>
                    @if($timer->is_billable)
                        <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                            Billable
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-8 text-slate-500">
                <i class="bi bi-clock-history text-3xl opacity-50 mb-2"></i>
                <p class="text-sm">No time tracked yet</p>
            </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        let timerInterval = null;
        
        // Update current timer display in real-time
        @if($currentTimer)
            function updateTimerDisplay() {
                const startTime = new Date('{{ $currentTimer->start_time }}');
                const now = new Date();
                const diffMs = now - startTime;
                
                const hours = Math.floor(diffMs / (1000 * 60 * 60));
                const minutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diffMs % (1000 * 60)) / 1000);
                
                let display = '';
                if (hours > 0) display += hours + 'h ';
                display += minutes + 'm ' + seconds + 's';
                
                $('#current-timer-display').text(display);
            }
            
            timerInterval = setInterval(updateTimerDisplay, 1000);
        @endif

        $('#start-timer-btn').on('click', function() {
            $.ajax({
                url: '{{ route("admin.timers.start") }}',
                method: 'POST',
                data: {
                    task_id: {{ $taskId }},
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Failed to start timer: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('Failed to start timer');
                    console.error('Timer error:', xhr);
                }
            });
        });

        $('#stop-timer-btn').on('click', function() {
            if (!confirm('Are you sure you want to stop the timer?')) {
                return;
            }

            $.ajax({
                url: '{{ route("admin.timers.stop", $currentTimer->id ?? 0) }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Failed to stop timer: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('Failed to stop timer');
                    console.error('Timer error:', xhr);
                }
            });
        });

        // Cleanup interval on page unload
        $(window).on('beforeunload', function() {
            if (timerInterval) {
                clearInterval(timerInterval);
            }
        });
    });
</script>
@endpush