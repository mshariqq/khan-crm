<div class="flex items-center justify-between mb-4">
    <h3 class="text-sm font-semibold text-slate-800">Locations ({{ $project->locations->count() }})</h3>
    <button onclick="document.getElementById('inlineLocationForm').classList.toggle('hidden')" class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 flex items-center gap-1">
        <i class="bi bi-plus"></i> Add Location
    </button>
</div>

<form id="inlineLocationForm" action="{{ route('admin.projects.locations.store', $project->id) }}" method="POST" class="hidden mb-4 p-4 bg-slate-50 rounded-lg border border-slate-200 space-y-3">
    @csrf
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Name</label>
            <input type="text" name="name" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. Head Office" required>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Address</label>
            <input type="text" name="address" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Street address">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">City</label>
            <input type="text" name="city" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">State / Province</label>
            <input type="text" name="state" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Country</label>
            <input type="text" name="country" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Latitude / Longitude</label>
            <div class="flex gap-2">
                <input type="text" name="latitude" class="w-1/2 px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Lat">
                <input type="text" name="longitude" class="w-1/2 px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Lng">
            </div>
        </div>
    </div>
    <div class="flex justify-end gap-2">
        <button type="button" onclick="document.getElementById('inlineLocationForm').classList.add('hidden')" class="px-3 py-1.5 text-xs text-slate-600 hover:text-slate-800">Cancel</button>
        <button type="submit" class="px-4 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">Add Location</button>
    </div>
</form>

@if($project->locations->count() > 0)
<div class="space-y-2">
    @foreach($project->locations as $location)
    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg hover:bg-slate-100 transition-colors group">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-rose-100 flex items-center justify-center">
                <i class="bi bi-geo-alt text-rose-600 text-sm"></i>
            </div>
            <div>
                <span class="text-sm font-medium text-slate-700">{{ $location->name }}</span>
                <div class="flex items-center gap-1 text-[10px] text-slate-400 mt-0.5">
                    @if($location->address)<span>{{ $location->address }},</span>@endif
                    @if($location->city)<span>{{ $location->city }},</span>@endif
                    @if($location->state)<span>{{ $location->state }},</span>@endif
                    @if($location->country)<span>{{ $location->country }}</span>@endif
                </div>
                @if($location->latitude && $location->longitude)
                <p class="text-[10px] text-slate-400">{{ $location->latitude }}, {{ $location->longitude }}</p>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if($location->latitude && $location->longitude)
            <a href="https://www.google.com/maps?q={{ $location->latitude }},{{ $location->longitude }}" target="_blank" class="px-2 py-1 text-[10px] bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 flex items-center gap-1">
                <i class="bi bi-map"></i> Maps
            </a>
            @elseif($location->address)
            <a href="https://www.google.com/maps?q={{ urlencode($location->address . ', ' . $location->city) }}" target="_blank" class="px-2 py-1 text-[10px] bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 flex items-center gap-1">
                <i class="bi bi-map"></i> Maps
            </a>
            @endif
            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                <form action="{{ route('admin.projects.locations.destroy', [$project->id, $location->id]) }}" method="POST" class="inline" onsubmit="event.preventDefault(); confirmDelete('Confirm Delete', 'Delete this location?', this.action, (this.querySelector('[name=_method]')?.value || 'POST'));">
                    @csrf @method('DELETE')
                    <button type="submit" class="p-1 text-slate-400 hover:text-red-600 rounded hover:bg-slate-200" title="Delete"><i class="bi bi-trash text-xs"></i></button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="text-center py-12">
    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
        <i class="bi bi-geo-alt text-slate-400 text-xl"></i>
    </div>
    <p class="text-sm text-slate-500">No locations added</p>
    <button onclick="document.getElementById('inlineLocationForm').classList.remove('hidden')" class="mt-3 inline-block px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">Add Location</button>
</div>
@endif
