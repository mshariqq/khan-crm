<?php

namespace App\Http\Controllers\Client;

use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\ProjectFileDownload;
use App\Models\ProjectDiscussion;
use App\Models\ProjectDiscussionComment;
use App\Models\ProjectNote;
use App\Models\ProjectActivity;
use App\Models\ProjectFeedback;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

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

        $project = $client->projects()
            ->with([
                'tasks.assignedStaff',
                'milestones',
                'files' => fn($q) => $q->where('visible_to_customer', 1),
                'discussions' => fn($q) => $q->where('show_to_customer', 1),
                'discussions.staff',
                'discussions.comments.staff',
                'invoices',
                'estimates',
                'staff',
                'activities' => fn($q) => $q->latest()->limit(20),
            ])
            ->findOrFail($id);

        $tasks = $project->tasks;

        return view('client.projects.show', compact('project', 'tasks'));
    }

    public function downloadFile($projectId, $fileId)
    {
        $client = $this->getClient();
        $project = $client->projects()->findOrFail($projectId);

        $file = ProjectFile::where('project_id', $project->id)
            ->where('visible_to_customer', 1)
            ->findOrFail($fileId);

        $fileService = app(FileService::class);
        $path = $fileService->getFilePath($file->file_name);

        if (!$path) {
            $legacyPath = storage_path('app/public/' . $file->file_name);
            if (file_exists($legacyPath)) {
                $path = $legacyPath;
            } else {
                abort(404, 'File not found');
            }
        }

        ProjectFileDownload::create([
            'file_id' => $file->id,
            'contact_id' => auth('client')->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'downloaded_at' => now(),
        ]);

        return response()->download($path, $file->original_file_name ?? basename($file->file_name));
    }

    public function files($projectId)
    {
        $client = $this->getClient();
        $project = $client->projects()->findOrFail($projectId);
        $files = ProjectFile::where('project_id', $project->id)
            ->where('visible_to_customer', 1)
            ->orderBy('dateadded', 'desc')
            ->paginate(20);
        return view('client.projects.files', compact('project', 'files'));
    }

    public function discussions($projectId)
    {
        $client = $this->getClient();
        $project = $client->projects()->findOrFail($projectId);
        $discussions = ProjectDiscussion::where('project_id', $project->id)
            ->where('show_to_customer', 1)
            ->with(['staff', 'comments'])
            ->orderBy('last_activity', 'desc')
            ->paginate(15);
        return view('client.projects.discussions', compact('project', 'discussions'));
    }

    public function showDiscussion($projectId, $discussionId)
    {
        $client = $this->getClient();
        $project = $client->projects()->findOrFail($projectId);

        $discussion = ProjectDiscussion::where('project_id', $project->id)
            ->where('show_to_customer', 1)
            ->with(['comments.staff', 'comments.contact', 'staff'])
            ->findOrFail($discussionId);

        return view('client.projects.discussion-show', compact('project', 'discussion'));
    }

    public function addComment(Request $request, $projectId, $discussionId)
    {
        $client = $this->getClient();
        $contact = $client->primaryContact;

        if (!$contact) {
            return response()->json(['success' => false, 'message' => 'No primary contact found'], 403);
        }

        $project = $client->projects()->findOrFail($projectId);
        $discussion = ProjectDiscussion::where('project_id', $project->id)
            ->where('show_to_customer', 1)
            ->findOrFail($discussionId);

        $request->validate(['content' => 'required|string']);

        $comment = ProjectDiscussionComment::create([
            'discussion_id' => $discussion->id,
            'discussion_type' => 'discussion',
            'content' => $request->content,
            'contact_id' => $contact->id,
            'fullname' => $contact->firstname . ' ' . $contact->lastname,
            'created' => now(),
        ]);

        $discussion->update(['last_activity' => now()]);

        return response()->json(['success' => true, 'comment' => $comment]);
    }

    public function notes($projectId)
    {
        $client = $this->getClient();
        $project = $client->projects()->findOrFail($projectId);
        $notes = ProjectNote::where('project_id', $project->id)
            ->where('visible_to_customer', true)
            ->with('staff')
            ->orderBy('dateadded', 'desc')
            ->get();
        return view('client.projects.notes', compact('project', 'notes'));
    }

    public function activity($projectId)
    {
        $client = $this->getClient();
        $project = $client->projects()->findOrFail($projectId);
        $activities = ProjectActivity::where('project_id', $project->id)
            ->with('staff')
            ->orderBy('dateadded', 'desc')
            ->paginate(30);
        return view('client.projects.activity', compact('project', 'activities'));
    }

    public function timesheets($projectId)
    {
        $client = $this->getClient();
        $project = $client->projects()->with('tasks.timers.staff', 'tasks.timers.task')->findOrFail($projectId);
        $allTimers = $project->tasks->flatMap(function ($task) {
            return $task->timers->map(function ($timer) use ($task) {
                $timer->task_name = $task->name;
                return $timer;
            });
        })->sortByDesc('start_time');
        return view('client.projects.timesheets', compact('project', 'allTimers'));
    }

    public function gantt($projectId)
    {
        $client = $this->getClient();
        $project = $client->projects()->with('tasks', 'milestones')->findOrFail($projectId);
        $tasks = $project->tasks()->with('milestoneRelation')->orderBy('milestone')->orderBy('startdate')->get();
        $milestones = $project->milestones()->orderBy('due_date')->get();
        return view('client.projects.gantt', compact('project', 'tasks', 'milestones'));
    }

    public function invoices($projectId)
    {
        $client = $this->getClient();
        $project = $client->projects()->findOrFail($projectId);
        $invoices = $project->invoices()->orderBy('date', 'desc')->paginate(10);
        return view('client.projects.invoices', compact('project', 'invoices'));
    }

    public function estimates($projectId)
    {
        $client = $this->getClient();
        $project = $client->projects()->findOrFail($projectId);
        $estimates = $project->estimates()->orderBy('date', 'desc')->paginate(10);
        return view('client.projects.estimates', compact('project', 'estimates'));
    }

    public function milestones($projectId)
    {
        $client = $this->getClient();
        $project = $client->projects()->findOrFail($projectId);
        $milestones = $project->milestones()->orderBy('due_date')->get();
        return view('client.projects.milestones', compact('project', 'milestones'));
    }

    public function submitFeedback(Request $request, $projectId)
    {
        $client = $this->getClient();
        $contact = $this->getContact();
        $project = $client->projects()->findOrFail($projectId);

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'nps_score' => 'nullable|integer|min:0|max:10',
            'comments' => 'nullable|string|max:5000',
        ]);

        $feedback = ProjectFeedback::create([
            'project_id' => $project->id,
            'contact_id' => $contact->id,
            'rating' => $request->rating,
            'nps_score' => $request->nps_score,
            'comments' => $request->comments,
            'submitted_at' => now(),
        ]);

        return response()->json(['success' => true, 'feedback' => $feedback]);
    }

    public function export(Request $request)
    {
        $rateKey = 'client-project-export-' . Auth::guard('client')->id();
        $attempts = (int) Cache::get($rateKey, 0);
        if ($attempts >= 3) {
            return redirect()->back()->with('error', 'Maximum of 3 exports per hour reached. Please try again later.');
        }
        Cache::put($rateKey, $attempts + 1, 3600);

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

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="projects_export_' . now()->format('YmdHis') . '.csv"',
        ];

        $callback = function () use ($projects) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Project Name', 'Status', 'Progress', 'Start Date', 'Deadline', 'Created Date']);

            foreach ($projects as $project) {
                $statusLabel = match ($project->status) {
                    Project::STATUS_ACTIVE => 'In Progress',
                    Project::STATUS_COMPLETED => 'Completed',
                    Project::STATUS_ON_HOLD => 'On Hold',
                    Project::STATUS_CANCELLED => 'Cancelled',
                    default => 'Unknown',
                };

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
        };

        return response()->stream($callback, 200, $headers);
    }
}
