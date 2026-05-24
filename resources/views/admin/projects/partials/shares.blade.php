<div class="flex items-center justify-between mb-4">
    <h3 class="text-sm font-semibold text-slate-800">Share Links ({{ $project->shares->count() }})</h3>
    <button onclick="document.getElementById('inlineShareForm').classList.toggle('hidden')" class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 flex items-center gap-1">
        <i class="bi bi-plus"></i> Generate Link
    </button>
</div>

<form id="inlineShareForm" action="{{ route('admin.projects.shares.store', $project->id) }}" method="POST" class="hidden mb-4 p-4 bg-slate-50 rounded-lg border border-slate-200 space-y-3">
    @csrf
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Email</label>
            <input type="email" name="email" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="recipient@example.com">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Access Level</label>
            <select name="access_level" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="view">View Only</option>
                <option value="comment">Comment</option>
                <option value="edit">Edit</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Expiry</label>
            <input type="date" name="expires_at" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
    </div>
    <div class="flex justify-end gap-2">
        <button type="button" onclick="document.getElementById('inlineShareForm').classList.add('hidden')" class="px-3 py-1.5 text-xs text-slate-600 hover:text-slate-800">Cancel</button>
        <button type="submit" class="px-4 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">Generate Link</button>
    </div>
</form>

@if($project->shares->count() > 0)
<div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-slate-200">
                <th class="text-left py-2 px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Email</th>
                <th class="text-center py-2 px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Access Level</th>
                <th class="text-left py-2 px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Token</th>
                <th class="text-center py-2 px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Expiry</th>
                <th class="text-center py-2 px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Access Count</th>
                <th class="text-right py-2 px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach($project->shares as $share)
            @php $isExpired = $share->expires_at?->isPast() ?? false; @endphp
            <tr class="hover:bg-slate-50 transition-colors group {{ $isExpired ? 'opacity-60' : '' }}">
                <td class="py-2.5 px-3">
                    <span class="text-xs font-medium text-slate-700">{{ $share->email ?? 'Shared Link' }}</span>
                </td>
                <td class="py-2.5 px-3 text-center">
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-medium
                        @if($share->access_level == 'edit') bg-blue-100 text-blue-700
                        @elseif($share->access_level == 'comment') bg-amber-100 text-amber-700
                        @else bg-slate-100 text-slate-600 @endif">
                        {{ ucfirst($share->access_level ?? 'view') }}
                    </span>
                </td>
                <td class="py-2.5 px-3">
                    <code class="text-[10px] bg-slate-100 px-2 py-1 rounded text-slate-600 font-mono truncate max-w-[120px] inline-block">{{ $share->token ?? substr(md5($share->id), 0, 12) }}</code>
                </td>
                <td class="py-2.5 px-3 text-center">
                    @if($share->expires_at)
                    <span class="text-xs {{ $isExpired ? 'text-red-500' : 'text-slate-600' }}">
                        {{ $share->expires_at->format('M j, Y') }}
                        @if($isExpired)
                        <span class="text-[10px] block text-red-400">Expired</span>
                        @endif
                    </span>
                    @else
                    <span class="text-xs text-slate-400">Never</span>
                    @endif
                </td>
                <td class="py-2.5 px-3 text-center text-xs text-slate-600">{{ $share->access_count ?? 0 }}</td>
                <td class="py-2.5 px-3 text-right">
                    <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button onclick="copyShareLink('{{ $share->token ?? '' }}')" class="p-1 text-slate-400 hover:text-blue-600 rounded hover:bg-slate-200" title="Copy Link"><i class="bi bi-link-45deg text-xs"></i></button>
                        <form action="{{ route('admin.projects.shares.revoke', [$project->id, $share->id]) }}" method="POST" class="inline" onsubmit="event.preventDefault(); confirmDelete('Confirm Delete', 'Revoke this share link?', this.action, (this.querySelector('[name=_method]')?.value || 'POST'));">
                            @csrf
                            <button type="submit" class="p-1 text-slate-400 hover:text-red-600 rounded hover:bg-slate-200" title="Revoke"><i class="bi bi-slash-circle text-xs"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
function copyShareLink(token) {
    const url = '{{ url("share") }}/' + token;
    navigator.clipboard.writeText(url).then(() => {
        alert('Link copied to clipboard!');
    }).catch(() => {
        prompt('Copy this link:', url);
    });
}
</script>
@else
<div class="text-center py-12">
    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
        <i class="bi bi-share text-slate-400 text-xl"></i>
    </div>
    <p class="text-sm text-slate-500">No share links yet</p>
    <button onclick="document.getElementById('inlineShareForm').classList.remove('hidden')" class="mt-3 inline-block px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">Generate Link</button>
</div>
@endif
