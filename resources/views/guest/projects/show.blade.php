<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $project->name }} - Shared Project</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-slate-50 min-h-screen">
    <div class="max-w-4xl mx-auto p-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800">{{ $project->name }}</h1>
                        <p class="text-sm text-slate-500">{{ $project->client->company ?? 'No Client' }}</p>
                    </div>
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                        {{ $share->access_level }} access
                    </span>
                </div>
            </div>
            <div class="p-6 space-y-6">
                @if($project->description)
                <div class="prose prose-sm max-w-none">
                    {!! $project->description !!}
                </div>
                @endif

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl border border-blue-200">
                        <p class="text-2xl font-bold text-blue-700">{{ $project->tasks->count() }}</p>
                        <p class="text-xs text-blue-600">Tasks</p>
                    </div>
                    <div class="p-4 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl border border-emerald-200">
                        <p class="text-2xl font-bold text-emerald-700">{{ $project->tasks->where('status', 3)->count() }}</p>
                        <p class="text-xs text-emerald-600">Completed</p>
                    </div>
                    <div class="p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl border border-purple-200">
                        <p class="text-2xl font-bold text-purple-700">{{ $project->progress }}%</p>
                        <p class="text-xs text-purple-600">Progress</p>
                    </div>
                    <div class="p-4 bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl border border-amber-200">
                        <p class="text-2xl font-bold text-amber-700">{{ $project->milestones->count() }}</p>
                        <p class="text-xs text-amber-600">Milestones</p>
                    </div>
                </div>

                @if($project->milestones->count() > 0)
                <div>
                    <h3 class="text-lg font-semibold text-slate-800 mb-3">Milestones</h3>
                    <div class="space-y-2">
                        @foreach($project->milestones as $milestone)
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg border border-slate-200">
                            <div class="flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full" style="background: {{ $milestone->color ?? '#3b82f6' }}"></span>
                                <span class="text-sm text-slate-700">{{ $milestone->name }}</span>
                            </div>
                            @if($milestone->due_date)
                            <span class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($milestone->due_date)->format('M j, Y') }}</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($share->access_level === 'edit')
                <div class="p-4 bg-amber-50 rounded-xl border border-amber-200">
                    <p class="text-sm text-amber-700">
                        <i class="bi bi-pencil"></i> You have edit access to this project.
                    </p>
                </div>
                @endif
            </div>
            <div class="p-4 bg-slate-50 border-t border-slate-200 text-center">
                <p class="text-xs text-slate-400">Powered by KhanCrm</p>
            </div>
        </div>
    </div>
</body>
</html>
