@extends('layouts.admin')

@section('breadcrumb', 'Payroll Management')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Monthly Payslips</h1>
            <p class="text-slate-500 text-sm">Generate and manage staff monthly payments.</p>
        </div>
    </div>

    {{-- Robust Filter Section --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
        <form action="{{ route('admin.payroll.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 items-end">
            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-500 uppercase">Month</label>
                <select name="month" class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 outline-none focus:ring-2 focus:ring-blue-500">
                    @for($m=1; $m<=12; $m++)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : ($m == date('m') ? 'selected' : '') }}>
                            {{ date('F', mktime(0,0,0,$m,1)) }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-500 uppercase">Year</label>
                <input type="number" name="year" value="{{ request('year', date('Y')) }}" class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 outline-none focus:ring-2 focus:ring-blue-500">
            </div>

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
                <label class="text-xs font-semibold text-slate-500 uppercase">Status</label>
                <select name="status" class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Statuses</option>
                    <option value="Draft" {{ request('status') == 'Draft' ? 'selected' : '' }}>Draft</option>
                    <option value="Paid" {{ request('status') == 'Paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>

            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-500 uppercase">Min Amount</label>
                <input type="number" step="0.01" name="min_amount" value="{{ request('min_amount') }}" class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 outline-none focus:ring-2 focus:ring-blue-500" placeholder="0.00">
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all">
                    Filter
                </button>
                <a href="{{ route('admin.payroll.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 text-sm font-semibold rounded-lg hover:bg-slate-200 transition-all">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="flex justify-end mb-4">
        <form action="{{ route('admin.payroll.generate') }}" method="POST" class="flex gap-2">
            @csrf
            <input type="hidden" name="month" value="{{ request('month', date('m')) }}">
            <input type="hidden" name="year" value="{{ request('year', date('Y')) }}">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-500/20 flex items-center gap-2">
                <i class="bi bi-gear-wide-connected"></i> Generate All Payslips
            </button>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Staff Member</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Basic</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Additions</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Deductions</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Net Amount</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($payslips as $payslip)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-xs">
                                {{ substr($payslip->staff->firstname, 0, 1) }}
                            </div>
                            <span class="text-sm font-medium text-slate-800">{{ $payslip->staff->fullname }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">{{ $global_settings['payroll_currency_symbol'] ?? '$' }}{{ number_format($payslip->basic, 2) }}</td>
                    <td class="px-6 py-4 text-sm text-emerald-600 font-medium">+{{ $global_settings['payroll_currency_symbol'] ?? '$' }}{{ number_format($payslip->total_additions, 2) }}</td>
                    <td class="px-6 py-4 text-sm text-rose-600 font-medium">-{{ $global_settings['payroll_currency_symbol'] ?? '$' }}{{ number_format($payslip->total_deductions, 2) }}</td>
                    <td class="px-6 py-4 text-sm font-bold text-slate-900">{{ $global_settings['payroll_currency_symbol'] ?? '$' }}{{ number_format($payslip->net_amount, 2) }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider 
                            @php $paidStatus = $global_settings['payroll_paid_status'] ?? 'Paid'; @endphp
                            {{ $payslip->status == $paidStatus ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ $payslip->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('admin.payroll.view', $payslip->id) }}" class="p-2 text-slate-400 hover:text-blue-600 transition-colors" title="View Details">
                            <i class="bi bi-eye"></i>
                        </a>
                        <button onclick="openAdjustmentModal({{ $payslip->id }})" class="p-2 text-slate-400 hover:text-indigo-600 transition-colors" title="Add Adjustment">
                            <i class="bi bi-plus-circle"></i>
                        </button>
                        @php $draftStatus = $global_settings['payroll_default_status'] ?? 'Draft'; @endphp
                        @if($payslip->status == $draftStatus)
                        <form action="{{ route('admin.payroll.mark-paid', $payslip->id) }}" method="POST" class="inline-block">
                            @csrf
                            <button type="submit" class="p-2 text-slate-400 hover:text-emerald-600 transition-colors" title="Mark as Paid">
                                <i class="bi bi-check-circle"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-slate-400">
                            <i class="bi bi-receipt-cutoff text-3xl mb-2"></i>
                            <p class="text-sm">No payslips generated for this period.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $payslips->links() }}
    </div>
</div>

<div id="adjustmentModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4">
        <div class="p-4 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-800">Add Payslip Adjustment</h3>
            <button onclick="closeAdjustmentModal()" class="text-slate-400 hover:text-slate-600"><i class="bi bi-x-lg"></i></button>
        </div>
        <form id="adjustmentForm" class="p-6 space-y-4">
            <input type="hidden" id="adj_payslip_id">
            <div class="space-y-1">
                <label class="text-xs font-medium text-slate-600">Type</label>
                <select id="adj_type" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="Addition">Addition (Bonus, etc.)</option>
                    <option value="Deduction">Deduction (Penalty, etc.)</option>
                </select>
            </div>
            <div class="space-y-1">
                <label class="text-xs font-medium text-slate-600">Label</label>
                <input type="text" id="adj_label" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g. Performance Bonus">
            </div>
            <div class="space-y-1">
                <label class="text-xs font-medium text-slate-600">Amount</label>
                <input type="number" step="0.01" id="adj_amount" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:ring-2 focus:ring-blue-500" placeholder="0.00">
            </div>
            <button type="button" onclick="submitAdjustment()" class="w-full py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all">Apply Adjustment</button>
        </form>
    </div>
</div>

<script>
function openAdjustmentModal(id) {
    document.getElementById('adj_payslip_id').value = id;
    document.getElementById('adjustmentModal').classList.remove('hidden');
}
function closeAdjustmentModal() {
    document.getElementById('adjustmentModal').classList.add('hidden');
}
async function submitAdjustment() {
    const id = document.getElementById('adj_payslip_id').value;
    const type = document.getElementById('adj_type').value;
    const label = document.getElementById('adj_label').value;
    const amount = document.getElementById('adj_amount').value;

    if(!label || !amount) { alert('Please fill all fields'); return; }

    try {
        const res = await fetch('{{ route('admin.payroll.adjustment') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ payslip_id: id, type, label, amount })
        });
        const data = await res.json();
        if(data.success) {
            showToast('Adjustment applied successfully');
            window.location.reload();
        } else {
            showToast(data.message, 'error');
        }
    } catch (e) {
        showToast('Error applying adjustment', 'error');
    }
}
</script>
@endsection
