@extends('layouts.admin')

@section('breadcrumb', 'Profitability Report')

@section('main')
<div class="p-6 space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Profitability Report</h1>
            <p class="text-slate-500 text-sm mt-1">Financial overview of all projects</p>
        </div>
        <button onclick="exportCSV()" class="px-4 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition-all flex items-center gap-2 shadow-lg shadow-emerald-500/20">
            <i class="bi bi-download"></i> Export CSV
        </button>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4">
            <p class="text-xs font-medium text-slate-500">Total Revenue</p>
            <p class="text-lg font-bold text-slate-800 mt-1">{{ app_format_money($totals['revenue'] ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4">
            <p class="text-xs font-medium text-slate-500">Total Expenses</p>
            <p class="text-lg font-bold text-slate-800 mt-1">{{ app_format_money($totals['expenses'] ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4">
            <p class="text-xs font-medium text-slate-500">Total Labor Cost</p>
            <p class="text-lg font-bold text-slate-800 mt-1">{{ app_format_money($totals['labor_cost'] ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4">
            <p class="text-xs font-medium text-slate-500">Total Cost</p>
            <p class="text-lg font-bold text-slate-800 mt-1">{{ app_format_money(($totals['expenses'] ?? 0) + ($totals['labor_cost'] ?? 0)) }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4">
            <p class="text-xs font-medium text-slate-500">Net Profit</p>
            @php $netProfit = ($totals['revenue'] ?? 0) - ($totals['expenses'] ?? 0) - ($totals['labor_cost'] ?? 0); @endphp
            <p class="text-lg font-bold mt-1 {{ $netProfit >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ app_format_money($netProfit) }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4">
            <p class="text-xs font-medium text-slate-500">Avg Margin</p>
            <p class="text-lg font-bold mt-1 {{ ($totals['avg_margin'] ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                {{ number_format($totals['avg_margin'] ?? 0, 1) }}%
            </p>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-100">
            <h2 class="text-lg font-bold text-slate-800">Project Profitability</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full" id="profitabilityTable">
                <thead>
                    <tr class="text-xs text-slate-500 border-b border-slate-100">
                        <th class="text-left px-5 py-3 font-semibold">Project</th>
                        <th class="text-left px-5 py-3 font-semibold">Client</th>
                        <th class="text-right px-5 py-3 font-semibold">Revenue</th>
                        <th class="text-right px-5 py-3 font-semibold">Expenses</th>
                        <th class="text-right px-5 py-3 font-semibold">Labor Cost</th>
                        <th class="text-right px-5 py-3 font-semibold">Total Cost</th>
                        <th class="text-right px-5 py-3 font-semibold">Profit</th>
                        <th class="text-right px-5 py-3 font-semibold">Margin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($projects as $project)
                    @php
                        $revenue = $project->total_revenue ?? 0;
                        $expenses = $project->total_expenses ?? 0;
                        $labor = $project->total_labor_cost ?? 0;
                        $totalCost = $expenses + $labor;
                        $profit = $revenue - $totalCost;
                        $margin = $revenue > 0 ? ($profit / $revenue) * 100 : 0;
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-4">
                            <a href="{{ route('admin.projects.show', $project->id) }}" class="text-sm font-semibold text-slate-800 hover:text-blue-600">{{ $project->name }}</a>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $project->client->company ?? '—' }}</td>
                        <td class="px-5 py-4 text-right text-sm font-medium text-slate-800">{{ app_format_money($revenue) }}</td>
                        <td class="px-5 py-4 text-right text-sm text-slate-700">{{ app_format_money($expenses) }}</td>
                        <td class="px-5 py-4 text-right text-sm text-slate-700">{{ app_format_money($labor) }}</td>
                        <td class="px-5 py-4 text-right text-sm font-medium text-slate-800">{{ app_format_money($totalCost) }}</td>
                        <td class="px-5 py-4 text-right text-sm font-bold {{ $profit >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ app_format_money($profit) }}
                        </td>
                        <td class="px-5 py-4 text-right">
                            <span class="text-sm font-bold {{ $margin >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ number_format($margin, 1) }}%
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center text-slate-400">
                            <i class="bi bi-cash-coin text-2xl mb-2 block"></i>
                            <p class="text-sm">No project data found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function exportCSV() {
    const table = document.getElementById('profitabilityTable');
    const rows = table.querySelectorAll('tr');
    let csv = [];
    rows.forEach(row => {
        const cols = row.querySelectorAll('th, td');
        const vals = Array.from(cols).map(c => '"' + c.textContent.trim().replace(/"/g, '""') + '"');
        csv.push(vals.join(','));
    });
    const blob = new Blob([csv.join('\n')], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'profitability_report.csv';
    a.click();
    URL.revokeObjectURL(url);
}
</script>
@endsection
