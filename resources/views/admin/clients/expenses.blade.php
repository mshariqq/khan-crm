@extends('layouts.admin')

@section('breadcrumb', 'Client Expenses')

@section('main')
<div class="p-6 space-y-6">
    @include('partials.admin.clients.profile_header')
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <div class="space-y-4">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Date</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Description</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Amount</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($data as $expense)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $expense->date }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-slate-800">{{ $expense->description }}</td>
                            <td class="px-4 py-3 text-sm font-bold text-slate-800">{{ $expense->total }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="/admin/expenses/{{ $expense->id }}" class="text-blue-600 hover:underline text-sm">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-slate-400 text-sm">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="bi bi-cash-stack text-3xl"></i>
                                    <p>No expenses found yet</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                </table>
                <div class="mt-4">
                    {{ $data->appends(['tab' => 'expenses'])->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
