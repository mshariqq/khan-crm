<?php

namespace App\Http\Controllers\Client;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends ClientBaseController
{
    public function index(Request $request)
    {
        $client = $this->getClient();
        $query = $client->projects();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $projects = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        
        return view('client.projects.index', compact('projects'));
    }

    public function show($id)
    {
        $client = $this->getClient();
        $project = $client->projects()->with(['tasks', 'milestones', 'files', 'discussions'])->findOrFail($id);
        
        return view('client.projects.show', compact('project'));
    }

    public function export(Request $request)
    {
        $client = $this->getClient();
        $query = $client->projects();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $projects = $query->get();

        $filename = "projects_export_" . now()->format('YmdHis') . ".csv";
        $handle = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        fputcsv($handle, ['Project Name', 'Status', 'Progress', 'Start Date', 'Deadline', 'Created Date']);

        foreach ($projects as $project) {
            $statusLabel = 'Unknown';
            if($project->status == \App\Models\Project::STATUS_ACTIVE) $statusLabel = 'In Progress';
            elseif($project->status == \App\Models\Project::STATUS_COMPLETED) $statusLabel = 'Completed';
            elseif($project->status == \App\Models\Project::STATUS_ON_HOLD) $statusLabel = 'On Hold';

            fputcsv($handle, [
                $project->name,
                $statusLabel,
                $project->progress . '%',
                $project->start_date,
                $project->deadline,
                $project->created_at,
            ]);
        }

        fclose($handle);
        exit;
    }
}
