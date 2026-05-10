<div id="attendance-widget" class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm transition-all">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center">
                <i class="bi bi-clock-history text-indigo-600"></i>
            </div>
            <div>
                <h3 class="font-semibold text-slate-900">Daily Attendance</h3>
                <p class="text-xs text-slate-500">Track your work hours</p>
            </div>
        </div>
        <div id="attendance-timer" class="hidden text-right">
            <span class="text-lg font-mono font-bold text-indigo-600" id="live-timer">00:00:00</span>
            <p class="text-[10px] text-slate-400 uppercase">Time Worked</p>
        </div>
    </div>

    <div id="attendance-action-area" class="flex flex-col items-center justify-center py-4">
        <div id="attendance-status-badge" class="mb-4 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-500">
            Not Clocked In
        </div>
        
        <button id="attendance-btn" onclick="handleAttendanceAction()" class="w-full py-3 rounded-xl font-bold text-sm transition-all shadow-md flex items-center justify-center gap-2">
            <i class="bi bi-play-fill"></i>
            <span id="btn-text">Clock In</span>
        </button>
        
        <p id="attendance-timestamp" class="mt-3 text-xs text-slate-400 hidden">
            Clocked in at <span id="clock-in-time" class="font-medium text-slate-600">--:--</span>
        </p>
    </div>

    <script>
    async function updateAttendanceWidget() {
        try {
            const res = await fetch('{{ route('admin.attendance.status') }}');
            const data = await res.json();
            
            const btn = document.getElementById('attendance-btn');
            const btnText = document.getElementById('btn-text');
            const badge = document.getElementById('attendance-status-badge');
            const timestamp = document.getElementById('attendance-timestamp');
            const clockInTime = document.getElementById('clock-in-time');
            const timer = document.getElementById('attendance-timer');

            if (data.clocked_in && !data.clocked_out) {
                // Clocked In state
                btn.className = 'w-full py-3 rounded-xl font-bold text-sm transition-all shadow-md flex items-center justify-center gap-2 bg-rose-500 text-white hover:bg-rose-600';
                btnText.innerText = 'Clock Out';
                badge.className = 'mb-4 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-700';
                badge.innerText = 'On Duty';
                timestamp.classList.remove('hidden');
                clockInTime.innerText = data.clock_in_time;
                timer.classList.remove('hidden');
                startLiveTimer(data.clock_in_time);
            } else if (data.clocked_out) {
                // Clocked Out state
                btn.className = 'w-full py-3 rounded-xl font-bold text-sm transition-all shadow-md flex items-center justify-center gap-2 bg-slate-200 text-slate-500 cursor-not-allowed';
                btn.disabled = true;
                btnText.innerText = 'Shift Ended';
                badge.className = 'mb-4 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-500';
                badge.innerText = 'Clocked Out';
                timestamp.classList.remove('hidden');
                clockInTime.innerText = data.clock_in_time;
                timer.classList.remove('hidden');
                stopLiveTimer();
            } else {
                // Not Clocked In state
                btn.className = 'w-full py-3 rounded-xl font-bold text-sm transition-all shadow-md flex items-center justify-center gap-2 bg-blue-600 text-white hover:bg-blue-700';
                btnText.innerText = 'Clock In';
                badge.className = 'mb-4 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-500';
                badge.innerText = 'Not Clocked In';
                timestamp.classList.add('hidden');
                timer.classList.add('hidden');
                btn.disabled = false;
            }
        } catch (e) {
            console.error('Attendance widget error:', e);
        }
    }

    async function handleAttendanceAction() {
        const btn = document.getElementById('attendance-btn');
        const btnText = document.getElementById('btn-text');
        const currentStatus = await fetch('{{ route('admin.attendance.status') }}').then(r => r.json());
        
        const url = currentStatus.clocked_in ? '{{ route('admin.attendance.clock-out') }}' : '{{ route('admin.attendance.clock-in') }}';
        
        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-wait');

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            
            if (data.success) {
                showToast(data.message);
                updateAttendanceWidget();
            } else {
                showToast(data.message, 'error');
                updateAttendancePointers();
            }
        } catch (e) {
            showToast('An error occurred', 'error');
        } finally {
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-wait');
        }
    }

    let timerInterval;
    function startLiveTimer(startTime) {
        clearInterval(timerInterval);
        timerInterval = setInterval(() => {
            const now = new Date();
            const start = new Date(startTime);
            const diff = Math.abs(now - start);
            
            const hours = Math.floor(diff / (1000 * 60 * 60));
            const mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const secs = Math.floor((diff % (1000 * 60)) / 1000);
            
            document.getElementById('live-timer').innerText = 
                `${String(hours).padStart(2, '0')}:${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        }, 1000);
    }

    function stopLiveTimer() {
        clearInterval(timerInterval);
    }

    document.addEventListener('DOMContentLoaded', updateAttendanceWidget);
    </script>
</div>
