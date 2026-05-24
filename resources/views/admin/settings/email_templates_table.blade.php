<div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-slate-400">Template Name</th>
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-slate-400">Subject</th>
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-slate-400 text-center">Status</th>
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-slate-400 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($templates as $template)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <p class="font-bold text-slate-700">{{ $template->name }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-xs text-slate-400">{{ $template->subject }}</p>
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
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                            <i class="bi bi-envelope text-4xl block mb-2"></i>
                            <p class="text-sm font-medium">No templates found for this type</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
