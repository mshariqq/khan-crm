@extends('layouts.admin')

@section('breadcrumb', 'Salary Configuration')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Salary Configuration</h1>
            <p class="text-slate-500 text-sm">Define the base monthly salary and fixed adjustments for this staff member.</p>
        </div>
        <a href="{{ route('admin.payroll.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-300 transition-all flex items-center gap-2">
            <i class="bi bi-arrow-left"></i> Back to Payroll
        </a>
    </div>

    <form action="{{ route('admin.payroll.salary-config.update', $staff->staffid) }}" method="POST" class="max-w-2xl bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @csrf
        @method('POST')
        <div class="p-6 space-y-6">
            <div class="flex items-center gap-3 mb-6 p-4 bg-blue-50 rounded-xl border border-blue-100">
                <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">
                    {{ substr($staff->firstname, 0, 1) }}
                </div>
                <div>
                    <h3 class="font-semibold text-slate-900">{{ $staff->fullname }}</h3>
                    <p class="text-xs text-slate-500">Employee ID: {{ $staff->staffid }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">Base Monthly Salary <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">$</span>
                        <input type="number" step="0.01" name="base_salary" value="{{ old('base_salary', $config->base_salary) }}" class="w-full pl-7 pr-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('base_salary') border-red-500 @enderror" required>
                    </div>
                    @error('base_salary') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">Effective Date <span class="text-red-500">*</span></label>
                    <input type="date" name="effective_date" value="{{ old('effective_date', $config->effective_date ?? now()->format('Y-m-d')) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('effective_date') border-red-500 @enderror" required>
                    @error('effective_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">Fixed Monthly Allowances</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">$</span>
                        <input type="number" step="0.01" name="fixed_allowances" value="{{ old('fixed_allowances', $config->fixed_allowances) }}" class="w-full pl-7 pr-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="0.00">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">Fixed Monthly Deductions</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">$</span>
                        <input type="number" step="0.01" name="fixed_deductions" value="{{ old('fixed_deductions', $config->fixed_deductions) }}" class="w-full pl-7 pr-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="0.00">
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
            <a href="{{ route('admin.payroll.index') }}" class="px-6 py-2 text-slate-600 font-semibold rounded-lg hover:bg-slate-200 transition-all">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Update Config</button>
        </div>
    </form>
</div>
@endsection
