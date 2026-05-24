@extends('layouts.client')

@section('title', 'Invoices - ' . $project->name)
@section('breadcrumb', 'Project Invoices')

@section('main')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('client.projects.show', $project) }}" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-200 rounded-xl text-slate-500 hover:text-blue-600 transition-colors">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Project Invoices</h1>
                <p class="text-sm text-slate-500">{{ $project->name }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr class="text-[10px] uppercase tracking-wider font-bold text-slate-400">
                        <th class="px-6 py-4">Invoice #</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($invoices as $invoice)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <a href="{{ route('client.invoices.show', $invoice->id) }}" class="text-sm font-bold text-blue-600 hover:text-blue-800">
                                {{ $invoice->prefix ?? '' }}{{ $invoice->number }}
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold text-slate-800">{{ app_format_money($invoice->total, $invoice->currency) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusMap = [
                                    1 => ['label' => 'Unpaid', 'class' => 'bg-rose-50 text-rose-600'],
                                    2 => ['label' => 'Partially Paid', 'class' => 'bg-blue-50 text-blue-600'],
                                    3 => ['label' => 'Paid', 'class' => 'bg-emerald-50 text-emerald-600'],
                                    4 => ['label' => 'Cancelled', 'class' => 'bg-slate-100 text-slate-600'],
                                ];
                                $s = $statusMap[$invoice->status] ?? ['label' => 'Unknown', 'class' => 'bg-slate-100 text-slate-600'];
                            @endphp
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-lg {{ $s['class'] }}">
                                {{ $s['label'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-500">
                            {{ $invoice->date ? \Carbon\Carbon::parse($invoice->date)->format('M d, Y') : '' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('client.invoices.show', $invoice->id) }}" class="inline-flex items-center gap-2 text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="bi bi-receipt text-3xl text-slate-300"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800">No invoices found</h3>
                            <p class="text-slate-500">No invoices have been created for this project yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
