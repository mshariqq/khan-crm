@extends('layouts.admin')

@section('breadcrumb', 'My Payslips')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">My Payslips</h1>
            <p class="text-slate-500 text-sm">View and download your monthly payment records.</p>
        </div>
        <a href="{{ route('admin.payroll.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-300 transition-all flex items-center gap-2">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Period</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Net Amount</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($payslips as $payslip)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 text-sm font-medium text-slate-800">
                        {{ date('F Y', mktime(0,0,0,$payslip->month, 1)) }}
                    </td>
                    <td class="px-6 py-4 text-sm font-bold text-slate-900">
                        {{ $global_settings['payroll_currency_symbol'] ?? '$' }}{{ number_format($payslip->net_amount, 2) }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider 
                            @php $paidStatus = $global_settings['payroll_paid_status'] ?? 'Paid'; @endphp
                            {{ $payslip->status == $paidStatus ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ $payslip->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.payroll.view', $payslip->id) }}" class="px-3 py-1.5 bg-blue-50 text-blue-600 text-xs font-semibold rounded-lg hover:bg-blue-100 transition-all">
                            View Payslip
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-slate-400">
                            <i class="bi bi-receipt-cutoff text-3xl mb-2"></i>
                            <p class="text-sm">No payslips generated yet.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
