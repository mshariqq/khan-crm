@extends('layouts.admin')

@section('breadcrumb', 'Apply for Leave')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Request Time Off</h1>
            <p class="text-slate-500 text-sm">Submit a leave request for approval.</p>
        </div>
        <a href="{{ route('admin.leaves.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-300 transition-all flex items-center gap-2">
            <i class="bi bi-arrow-left"></i> Back to History
        </a>
    </div>

    <form action="{{ route('admin.leaves.store') }}" method="POST" class="max-w-2xl bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @csrf
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">Leave Type <span class="text-red-500">*</span></label>
                    <select name="leave_type_id" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('leave_type_id') border-red-500 @enderror" required>
                        <option value="">Select Type</option>
                        @foreach($types as $type)
                            @php $bal = $balances[$type->id] ?? ['used' => 0, 'remaining' => 0]; @endphp
                            <option value="{{ $type->id }}" {{ old('leave_type_id') == $type->id ? 'selected' : '' }} data-remaining="{{ $bal['remaining'] }}">
                                {{ $type->name }} ({{ $bal['remaining'] }}/{{ $type->default_quota }} days left)
                            </option>
                        @endforeach
                    </select>
                    <p id="balance-hint" class="text-xs text-slate-400 mt-1"></p>
                    @error('leave_type_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">Start Date <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('start_date') border-red-500 @enderror" required>
                    @error('start_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">End Date <span class="text-red-500">*</span></label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('end_date') border-red-500 @enderror" required>
                    @error('end_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">Reason</label>
                    <input type="text" name="reason" value="{{ old('reason') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="Brief reason for leave">
                    @error('reason') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-sm font-medium text-slate-600">Detailed Description (Optional)</label>
                <textarea name="detailed_reason" rows="3" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="Provide more details if needed..."></textarea>
            </div>
        </div>

        <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
            <a href="{{ route('admin.leaves.index') }}" class="px-6 py-2 text-slate-600 font-semibold rounded-lg hover:bg-slate-200 transition-all">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Submit Request</button>
        </div>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeSelect = document.querySelector('select[name="leave_type_id"]');
    const startInput = document.querySelector('input[name="start_date"]');
    const endInput = document.querySelector('input[name="end_date"]');
    const hint = document.getElementById('balance-hint');

    function updateHint() {
        const opt = typeSelect.options[typeSelect.selectedIndex];
        if (!opt.value) { hint.textContent = ''; return; }
        const remaining = parseInt(opt.dataset.remaining) || 0;
        if (remaining <= 0) {
            hint.innerHTML = '<span class="text-red-500 font-medium">No remaining days for this leave type.</span>';
        } else {
            hint.innerHTML = `<span class="text-emerald-600 font-medium">${remaining} day(s)</span> remaining this year.`;
        }
    }

    typeSelect.addEventListener('change', updateHint);
    updateHint();
});
</script>
@endsection
