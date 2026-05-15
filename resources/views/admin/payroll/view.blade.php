@extends('layouts.admin')

@section('breadcrumb', 'Payslip Detail')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.payroll.index') }}" class="p-2 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition-all">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Payslip: {{ date('F Y', mktime(0,0,0,$payslip->month, 1)) }}</h1>
                <p class="text-slate-500 text-sm">Detailed breakdown of monthly payment for {{ $payslip->staff->fullname }}.</p>
            </div>
        </div>
        <div class="flex gap-3">
            <button onclick="window.print()" class="px-4 py-2 bg-slate-100 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-200 transition-all flex items-center gap-2">
                <i class="bi bi-printer"></i> Print
            </button>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden max-w-4xl mx-auto">
        <div class="p-8 bg-slate-50 border-b border-slate-200 grid grid-cols-2 gap-8">
            <div>
                <h2 class="text-lg font-bold text-slate-900 mb-4">Company Details</h2>
                <div class="space-y-1 text-sm text-slate-600">
                    <p class="font-semibold text-slate-800">{{ $global_settings['companyname'] ?? 'Company' }}</p>
                    <p>{{ $global_settings['companyaddress'] ?? '' }}</p>
                    <p>{{ $global_settings['company_email'] ?? '' }}</p>
                </div>
            </div>
            <div class="text-right">
                <h2 class="text-lg font-bold text-slate-900 mb-4">Employee Details</h2>
                <div class="space-y-1 text-sm text-slate-600">
                    <p class="font-semibold text-slate-800">{{ $payslip->staff->fullname }}</p>
                    <p>Staff ID: {{ $payslip->staff->staffid }}</p>
                    <p>Payment Date: {{ $payslip->payment_date ? \Carbon\Carbon::parse($payslip->payment_date)->format('M j, Y') : 'TBD' }}</p>
                </div>
            </div>
        </div>

        <div class="p-8 space-y-8">
            <div class="grid grid-cols-2 gap-12">
                {{-- Earnings --}}
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider border-b pb-2">Earnings</h3>
                    <div class="space-y-2">
                        @foreach($payslip->items->where('type', 'Addition') as $item)
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">{{ $item->label }}</span>
                            <span class="font-medium text-slate-900">{{ $global_settings['payroll_currency_symbol'] ?? '$' }}{{ number_format($item->amount, 2) }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Deductions --}}
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider border-b pb-2">Deductions</h3>
                    <div class="space-y-2">
                        @foreach($payslip->items->where('type', 'Deduction') as $item)
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">{{ $item->label }}</span>
                            <span class="font-medium text-rose-600">-{{ $global_settings['payroll_currency_symbol'] ?? '$' }}{{ number_format($item->amount, 2) }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-200 pt-6 flex justify-end">
                <div class="w-64 space-y-2">
                    <div class="flex justify-between text-sm text-slate-500">
                        <span>Gross Amount:</span>
                        <span>{{ $global_settings['payroll_currency_symbol'] ?? '$' }}{{ number_format($payslip->basic + $payslip->total_additions, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-slate-500">
                        <span>Total Deductions:</span>
                        <span class="text-rose-600">-{{ $global_settings['payroll_currency_symbol'] ?? '$' }}{{ number_format($payslip->total_deductions, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold text-slate-900 border-t border-slate-200 pt-2 mt-2">
                        <span>Net Salary:</span>
                        <span class="text-blue-600">{{ $global_settings['payroll_currency_symbol'] ?? '$' }}{{ number_format($payslip->net_amount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 bg-slate-50 text-center">
            @php $paidStatus = $global_settings['payroll_paid_status'] ?? 'Paid'; @endphp
            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider 
                {{ $payslip->status == $paidStatus ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                {{ $payslip->status }}
            </span>
        </div>
    </div>
</div>
@endsection
