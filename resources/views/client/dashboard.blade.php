@extends('layouts.client')

@section('title', 'Dashboard')
@section('breadcrumb', 'Welcome Back')

@section('main')
<div class="space-y-8">
    <!-- Welcome Header -->
    <div class="relative overflow-hidden bg-gradient-to-br from-blue-600 to-indigo-700 rounded-3xl p-8 text-white shadow-xl shadow-blue-500/20">
        <div class="relative z-10">
            <h1 class="text-3xl font-black mb-2">Hello, {{ $user->firstname }}!</h1>
            <p class="text-blue-100 font-medium">Welcome back to your portal. Here's what's happening with your projects.</p>
        </div>
        <!-- Decorative elements -->
        <div class="absolute top-0 right-0 -mt-12 -mr-12 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -mb-12 -ml-12 w-48 h-48 bg-blue-400/20 rounded-full blur-2xl"></div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 text-xl">
                <i class="bi bi-kanban"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Active Projects</p>
                <p class="text-2xl font-black text-slate-800">{{ count($data['active_projects']) }}</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600 text-xl">
                <i class="bi bi-receipt"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Unpaid Invoices</p>
                <p class="text-2xl font-black text-slate-800">{{ count($data['unpaid_invoices']) }}</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 text-xl">
                <i class="bi bi-ticket-perforated"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Support Tickets</p>
                <p class="text-2xl font-black text-slate-800">{{ $client->tickets()->whereIn('status', [1, 2])->count() }}</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 text-xl">
                <i class="bi bi-file-earmark-check"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Proposals</p>
                <p class="text-2xl font-black text-slate-800">{{ $client->proposals()->where('status', 1)->count() }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Projects -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-lg font-black text-slate-800">Active Projects</h3>
                <a href="{{ route('client.projects.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors uppercase tracking-wider">View All</a>
            </div>
            <div class="p-6 space-y-6">
                @forelse($data['active_projects']->take(5) as $project)
                <div class="group flex items-center justify-between p-4 rounded-2xl bg-slate-50 hover:bg-white border border-transparent hover:border-slate-200 transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 group-hover:text-blue-600 group-hover:border-blue-100 transition-all">
                            <i class="bi bi-folder2-open"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800">{{ $project->name }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <div class="w-24 bg-slate-200 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $project->progress }}%"></div>
                                </div>
                                <span class="text-[10px] font-bold text-slate-400">{{ $project->progress }}%</span>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('client.projects.show', $project->id) }}" class="p-2 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
                @empty
                <div class="py-12 text-center">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="bi bi-folder-x text-3xl text-slate-300"></i>
                    </div>
                    <p class="text-slate-500 font-medium">No active projects found.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Invoices -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-lg font-black text-slate-800">Unpaid Invoices</h3>
                <a href="{{ route('client.invoices.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors uppercase tracking-wider">View All</a>
            </div>
            <div class="p-6 space-y-6">
                @forelse($data['unpaid_invoices']->take(5) as $invoice)
                <div class="group flex items-center justify-between p-4 rounded-2xl bg-slate-50 hover:bg-white border border-transparent hover:border-slate-200 transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-rose-500 group-hover:bg-rose-50 group-hover:border-rose-100 transition-all">
                            <i class="bi bi-receipt"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800">#{{ $invoice->prefix }}{{ $invoice->number }}</p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">Due: {{ \Carbon\Carbon::parse($invoice->duedate)->format('M d, Y') }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-black text-slate-800">{{ $invoice->currencyRel->symbol ?? '$' }}{{ number_format($invoice->total, 2) }}</p>
                        <a href="{{ route('client.invoices.show', $invoice->id) }}" class="text-[10px] font-bold text-blue-600 hover:underline uppercase">View Details</a>
                    </div>
                </div>
                @empty
                <div class="py-12 text-center">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="bi bi-check2-circle text-3xl text-emerald-300"></i>
                    </div>
                    <p class="text-slate-500 font-medium">All caught up! No unpaid invoices.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
