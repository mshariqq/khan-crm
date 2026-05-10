@extends('layouts.admin')

@section('breadcrumb', 'Leave Management')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Leave Requests</h1>
            <p class="text-slate-500 text-sm">Track and manage employee time-off requests.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.leaves.holidays') }}" class="px-4 py-2 bg-slate-100 text-slate-700 font-semibold rounded-lg hover:bg-slate-200 transition-all flex items-center gap-2">
                <i class="bi bi-calendar-event"></i> Holidays
            </a>
            <a href="{{ route('admin.leaves.create') }}" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all flex items-center gap-2 shadow-lg shadow-blue-500/20">
                <i class="bi bi-plus-lg"></i> Apply for Leave
            </a>
        </div>
    </div>

    {{-- Robust Filter Section --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
        <form action="{{ route('admin.leaves.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 items-end">
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
                <label class="text-xs font-semibold text-slate-500 uppercase">Leave Type</label>
                <select name="type_id" class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Types</option>
                    @foreach(\App\Models\LeaveType::all() as $type)
                        <option value="{{ $type->id }}" {{ request('type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
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
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                    <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all">
                    Filter
                </button>
                <a href="{{ route('admin.leaves.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 text-sm font-semibold rounded-lg hover:bg-slate-200 transition-all">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Employee</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Dates</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Days</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Reason</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($leaves as $leave)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-xs">
                                {{ substr($leave->staff->firstname, 0, 1) }}
                            </div>
                            <span class="text-sm font-medium text-slate-800">{{ $leave->staff->fullname }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">
                        {{ $leave->type->name }}
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">
                        {{ $leave->start_date }} <i class="bi bi-arrow-right mx-1"></i> {{ $leave->end_date }}
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">
                        {{ $leave->duration }} days
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-500 italic">
                        {{ Str::limit($leave->reason, 30) }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider 
                            {{ $leave->status == 'Approved' ? 'bg-emerald-100 text-emerald-700' : ($leave->status == 'Rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                            {{ $leave->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        @if($leave->status == 'Pending' && Auth::user()->admin)
                        <div class="flex justify-end gap-2">
                            <button onclick="updateLeaveStatus({{ $leave->id }}, 'Approved')" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Approve">
                                <i class="bi bi-check-circle-fill"></i>
                            </button>
                            <button onclick="updateLeaveStatus({{ $leave->id }}, 'Rejected')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Reject">
                                <i class="bi bi-x-circle-fill"></i>
                            </button>
                        </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-slate-400">
                            <i class="bi bi-calendar-event-x text-3xl mb-2"></i>
                            <p class="text-sm">No leave requests found.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex justify-center">
        {{ $leaves->links() }}
    </div>
</div>

<script>
async function updateLeaveStatus(leaveId, status) {
    if (!confirm(`Are you sure you want to mark this leave as ${status}?`)) return;

    try {
        const res = await fetch(`/admin/leaves/${leaveId}/status`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ status: status })
        });
        const data = await res.json();
        if (data.success) {
            showToast(data.message);
            window.location.reload();
        } else {
            showToast(data.message, 'error');
        }
    } catch (e) {
        showToast('An error occurred', 'error');
    }
}
</script>
@endsection
