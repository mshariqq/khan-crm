<div id="attendance-widget" class="bg-white rounded-xl border border-slate-200 shadow-sm transition-all h-full">
    <div class="p-4 border-b border-slate-100">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0">
                    <i class="bi bi-clock-history text-indigo-600"></i>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-slate-900">Daily Attendance</h3>
                    <p class="text-[10px] text-slate-500">Track your work hours</p>
                </div>
            </div>
            <div id="attendance-timer" class="hidden text-right shrink-0">
                <span class="text-base font-mono font-bold text-indigo-600" id="live-timer">00:00:00</span>
                <p id="timer-label" class="text-[9px] text-slate-400 uppercase leading-tight">Worked</p>
            </div>
        </div>
    </div>

    <div id="attendance-action-area" class="p-4 flex flex-col items-center justify-center">
        <div id="attendance-status-badge" class="mb-3 px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-slate-100 text-slate-500">
            Not Clocked In
        </div>
        
        <button id="attendance-btn" onclick="handleAttendanceAction()" class="w-full py-2.5 rounded-xl font-bold text-sm transition-all shadow flex items-center justify-center gap-2">
            <i class="bi bi-play-fill"></i>
            <span id="btn-text">Clock In</span>
        </button>
        
        <p id="attendance-timestamp" class="mt-2.5 text-[11px] text-slate-400 hidden">
            Clocked in at <span id="clock-in-time" class="font-medium text-slate-600">--:--</span>
            <span id="clock-in-date" class="hidden"> <span class="text-slate-300">|</span> <span id="clock-in-date-text" class="font-medium text-slate-500"></span></span>
        </p>
    </div>

    <script>
    function formatClockTime(datetimeStr) {
        const d = new Date(datetimeStr);
        if (isNaN(d.getTime())) return datetimeStr;
        const months = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
        const day = String(d.getDate()).padStart(2, '0');
        const month = months[d.getMonth()];
        const year = d.getFullYear();
        const hh = String(d.getHours()).padStart(2, '0');
        const mm = String(d.getMinutes()).padStart(2, '0');
        return { time: `${hh}:${mm}`, date: `${month} ${day} ${year}` };
    }

    function formatDuration(seconds) {
        const h = Math.floor(seconds / 3600);
        const m = Math.floor((seconds % 3600) / 60);
        const s = seconds % 60;
        return `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
    }

    function calcDuration(datetimeStr1, datetimeStr2) {
        const d1 = new Date(datetimeStr1);
        const d2 = new Date(datetimeStr2);
        if (isNaN(d1.getTime()) || isNaN(d2.getTime())) return null;
        return Math.floor(Math.abs(d2 - d1) / 1000);
    }

    async function updateAttendanceWidget() {
        try {
            const res = await fetch('{{ route('admin.attendance.status') }}');
            const data = await res.json();

            const btn = document.getElementById('attendance-btn');
            const btnText = document.getElementById('btn-text');
            const badge = document.getElementById('attendance-status-badge');
            const timestamp = document.getElementById('attendance-timestamp');
            const clockInTime = document.getElementById('clock-in-time');
            const clockInDate = document.getElementById('clock-in-date');
            const clockInDateText = document.getElementById('clock-in-date-text');
            const timer = document.getElementById('attendance-timer');
            const timerLabel = document.getElementById('timer-label');

            if (data.clocked_in && !data.clocked_out) {
                // Clocked In state
                btn.className = 'w-full py-3 rounded-xl font-bold text-sm transition-all shadow-md flex items-center justify-center gap-2 bg-rose-500 text-white hover:bg-rose-600';
                btnText.innerText = 'Clock Out';
                badge.className = 'mb-3 px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-700';
                badge.innerText = 'On Duty';
                timestamp.classList.remove('hidden');
                clockInDate.classList.remove('hidden');
                const fmt = formatClockTime(data.clock_in_time);
                clockInTime.innerText = fmt.time;
                clockInDateText.innerText = fmt.date;
                timer.classList.remove('hidden');
                timerLabel.innerText = 'Worked';
                startLiveTimer(data.clock_in_time);
            } else if (data.clocked_out) {
                // Clocked Out state
                btn.className = 'w-full py-3 rounded-xl font-bold text-sm transition-all shadow-md flex items-center justify-center gap-2 bg-slate-200 text-slate-500 cursor-not-allowed';
                btn.disabled = true;
                btnText.innerText = 'Shift Ended';
                badge.className = 'mb-3 px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-slate-100 text-slate-500';
                badge.innerText = 'Clocked Out';
                timestamp.classList.remove('hidden');
                clockInDate.classList.remove('hidden');
                const fmt = formatClockTime(data.clock_in_time);
                clockInTime.innerText = fmt.time;
                clockInDateText.innerText = fmt.date;
                timer.classList.remove('hidden');
                timerLabel.innerText = 'Total';
                stopLiveTimer();
                if (data.worked_duration) {
                    document.getElementById('live-timer').innerText = data.worked_duration;
                } else {
                    const secs = calcDuration(data.clock_in_time, data.clock_out_time);
                    if (secs !== null) document.getElementById('live-timer').innerText = formatDuration(secs);
                }
            } else {
                // Not Clocked In state
                btn.className = 'w-full py-3 rounded-xl font-bold text-sm transition-all shadow-md flex items-center justify-center gap-2 bg-blue-600 text-white hover:bg-blue-700';
                btnText.innerText = 'Clock In';
                badge.className = 'mb-3 px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-slate-100 text-slate-500';
                badge.innerText = 'Not Clocked In';
                timestamp.classList.add('hidden');
                clockInDate.classList.add('hidden');
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
        const [fullDate, timePart] = String(startTime).split(' ');
        const [h, m, s] = (timePart || fullDate).split(':').map(Number);
        tick();
        timerInterval = setInterval(tick, 1000);
        function tick() {
            const now = new Date();
            const start = new Date(now.getFullYear(), now.getMonth(), now.getDate(), h || 0, m || 0, s || 0);
            const diff = Math.abs(now - start);
            const hours = Math.floor(diff / (1000 * 60 * 60));
            const mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const secs = Math.floor((diff % (1000 * 60)) / 1000);
            document.getElementById('live-timer').innerText =
                `${String(hours).padStart(2, '0')}:${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        }
    }

    function stopLiveTimer() {
        clearInterval(timerInterval);
    }

    document.addEventListener('DOMContentLoaded', updateAttendanceWidget);
    </script>
</div>
