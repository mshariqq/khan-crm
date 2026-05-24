<div class="flex items-start justify-between mb-6">
    <div class="flex items-start gap-4">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-blue-200 flex-shrink-0">
            {{ strtoupper(substr($contact->firstname, 0, 1)) }}{{ strtoupper(substr($contact->lastname, 0, 1)) }}
        </div>
        <div>
            <div class="flex items-center gap-3 mb-1">
                <h1 class="text-xl font-bold text-slate-800">{{ $contact->firstname }} {{ $contact->lastname }}</h1>
                @if($contact->is_primary)
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-100 text-blue-700">Primary</span>
                @endif
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $contact->active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                    {{ $contact->active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <div class="flex items-center gap-3 text-xs text-slate-400">
                <span>#{{ $contact->id }}</span>
                @if($contact->email)
                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                <span>{{ $contact->email }}</span>
                @endif
                @if($contact->client)
                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                <span>{{ $contact->client->company }}</span>
                @endif
            </div>
        </div>
    </div>
    <div class="flex items-center gap-2 flex-shrink-0">
        <button onclick="parent.openContactPane('{{ route('admin.contacts.pane.edit', $contact->id) }}')" class="px-3 py-2 bg-white border border-slate-200 text-slate-600 text-xs font-bold rounded-xl hover:bg-slate-50 transition-all flex items-center gap-1.5">
            <i class="bi bi-pencil"></i> Edit
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    <div class="lg:col-span-2 space-y-5">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                <i class="bi bi-person-lines-fill text-slate-400"></i>
                <h3 class="text-sm font-semibold text-slate-800">Contact Information</h3>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Full Name</span>
                        <p class="mt-1 text-sm font-medium text-slate-700">{{ $contact->firstname }} {{ $contact->lastname }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Job Title</span>
                        <p class="mt-1 text-sm font-medium text-slate-700">{{ $contact->title ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Email</span>
                        <p class="mt-1 text-sm font-medium text-slate-700">{{ $contact->email }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Phone</span>
                        <p class="mt-1 text-sm font-medium text-slate-700">{{ $contact->phonenumber ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                <i class="bi bi-building text-slate-400"></i>
                <h3 class="text-sm font-semibold text-slate-800">Client Company</h3>
            </div>
            <div class="p-5">
                @if($contact->client)
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500">
                        <i class="bi bi-building text-lg"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-800">{{ $contact->client->company }}</p>
                        <p class="text-xs text-slate-500">{{ $contact->client->email ?? '' }}</p>
                    </div>
                </div>
                @else
                <p class="text-sm text-slate-400">No client company linked</p>
                @endif
            </div>
        </div>
    </div>

    <div class="space-y-4">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                <i class="bi bi-info-circle text-slate-400"></i>
                <h3 class="text-sm font-semibold text-slate-800">Details</h3>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status</span>
                    <div class="mt-1.5">
                        <span class="px-3 py-1.5 rounded-lg text-xs font-bold {{ $contact->active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                            {{ $contact->active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Primary Contact</span>
                    <div class="mt-1.5 flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full {{ $contact->is_primary ? 'bg-blue-500' : 'bg-slate-300' }}"></div>
                        <span class="text-sm font-medium text-slate-700">{{ $contact->is_primary ? 'Yes' : 'No' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
