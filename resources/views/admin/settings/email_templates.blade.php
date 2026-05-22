@extends('layouts.admin')

@section('breadcrumb', 'Email Templates')

@section('main')
<div class="p-4 space-y-4">
    <div class="flex items-center justify-between bg-white rounded-xl border border-slate-200 p-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.settings.index') }}" class="p-2 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                <i class="bi bi-envelope"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-800">Email Templates</h1>
                <p class="text-sm text-slate-500">Manage automated system notifications</p>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-2 flex-wrap">
        <a href="{{ route('admin.settings.email_templates.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ !request('type') ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }} transition-all">
            All
        </a>
        @foreach($types as $t)
            <a href="{{ route('admin.settings.email_templates.index', ['type' => $t]) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request('type') == $t ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }} transition-all">
                {{ ucfirst($t) }}
            </a>
        @endforeach
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-slate-400">Template Name</th>
                        <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-slate-400">Type</th>
                        <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-slate-400 text-center">Status</th>
                        <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-slate-400 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($templates->groupBy('type') as $type => $group)
                        <tr class="bg-slate-50/50">
                            <td colspan="4" class="px-6 py-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
                                {{ $type }}
                            </td>
                        </tr>
                        @foreach($group as $template)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-700">{{ $template->name }}</p>
                                <p class="text-xs text-slate-400">{{ $template->subject }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-slate-100 text-slate-500 rounded text-[10px] font-bold uppercase tracking-wider">
                                    {{ $template->type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($template->active)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase rounded-full">
                                        <i class="bi bi-check-circle-fill"></i> Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-slate-100 text-slate-400 text-[10px] font-black uppercase rounded-full">
                                        <i class="bi bi-dash-circle"></i> Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.settings.email_templates.edit', $template->emailtemplateid) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                <i class="bi bi-envelope text-4xl block mb-2"></i>
                                <p class="text-sm font-medium">No templates found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($templates->hasPages())
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
            {{ $templates->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
