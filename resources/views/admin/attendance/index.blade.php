@extends('layouts.admin')

@section('breadcrumb', 'Attendance History')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Attendance History</h1>
            <p class="text-slate-500 text-sm">View and manage staff clock-in/out records.</p>
        </div>
    </div>

    {{-- Robust Filter Section --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
        <form action="{{ route('admin.attendance.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 items-end">
            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-500 uppercase">Staff Member</label>
                <select name="staff_id" class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Staff</option>
                    @foreach(\App\Models\Staff::all() as $staff)
                        <option value="{{ $staff->staffid }}" {{ request('staff_id') == $staff->staffid ? 'selected' : '' }}>{{ $staff->fullname }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-500 uppercase">From Date</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-500 uppercase">To Date</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-500 uppercase">Status</label>
                <select name="status" class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Statuses</option>
                    <option value="Present" {{ request('status') == 'Present' ? 'selected' : '' }}>Present</option>
                    <option value="Absent" {{ request('status') == 'Absent' ? 'selected' : '' }}>Absent</option>
                    <option value="Late" {{ request('status') == 'Late' ? 'selected' : '' }}>Late</option>
                </select>
            </div>

            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-500 uppercase">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 outline-none focus:ring-2 focus:ring-blue-500" placeholder="Name or IP...">
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all">
                    Filter
                </button>
                <a href="{{ route('admin.attendance.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 text-sm font-semibold rounded-lg hover:bg-slate-200 transition-all">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Staff Member</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Clock In</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Clock Out</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">IP Address</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($attendances as $attendance)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-xs">
                                {{ substr($attendance->staff->firstname, 0, 1) }}
                            </div>
                            <span class="text-sm font-medium text-slate-800">{{ $attendance->staff->fullname }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">
                        {{ $attendance->date }}
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">
                        {{ $attendance->clock_in ?? '--:--' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">
                        {{ $attendance->clock_out ?? '--:--' }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider 
                            {{ $attendance->status == 'Present' ? 'bg-emerald-100 text-emerald-700' : ($attendance->status == 'Absent' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                            {{ $attendance->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-xs text-slate-400 font-mono">
                        {{ $attendance->ip_address ?? 'N/A' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-slate-400">
                            <i class="bi bi-calendar-x text-3xl mb-2"></i>
                            <p class="text-sm">No attendance records found.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex justify-center">
        {{ $attendances->links() }}
    </div>
</div>
@endsection
