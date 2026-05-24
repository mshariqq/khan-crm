<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectFile;
use App\Models\ProjectFileFolder;
use App\Models\ProjectFileDownload;
use App\Models\Project;
use App\Models\ProjectActivity;
use App\Services\FileService;
use App\Services\FilePreviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
{
    public function __construct(
        protected FileService $fileService,
        protected FilePreviewService $previewService
    ) {}

    public function upload(Request $request)
    {
        if (!Auth::user()->can('files_create') && !Auth::user()->can('projects_edit')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'files' => 'required|array',
            'files.*' => 'required|file|max:10240',
            'project_id' => 'required|integer|exists:tblprojects,id',
            'folder_id' => 'nullable|integer|exists:tblproject_file_folders,id',
        ]);

        $project = Project::findOrFail($request->project_id);
        if (!Auth::user()->can('projects_edit') && !$project->staff->contains('staffid', Auth::id())) {
            return response()->json(['success' => false, 'message' => 'You are not a member of this project.'], 403);
        }

        try {
            $uploadedFiles = [];

            foreach ($request->file('files') as $file) {
                $uploaded = $this->fileService->upload($file, 'project');

                $fileRecord = ProjectFile::create([
                    'project_id' => $request->project_id,
                    'folder_id' => $request->folder_id,
                    'staffid' => Auth::id(),
                    'file_name' => $uploaded['file_name'],
                    'original_file_name' => $uploaded['original_file_name'],
                    'filetype' => $uploaded['filetype'],
                    'file_size' => $uploaded['file_size'],
                    'dateadded' => now(),
                ]);

                ProjectActivity::create([
                    'project_id' => $request->project_id,
                    'staff_id' => Auth::id(),
                    'description_key' => 'project_activity_file_uploaded',
                    'additional_data' => json_encode(['file_name' => $uploaded['original_file_name']]),
                    'dateadded' => now(),
                ]);

                $uploadedFiles[] = $fileRecord;
            }

            return response()->json([
                'success' => true,
                'message' => count($uploadedFiles) . ' file(s) uploaded successfully',
                'files' => $uploadedFiles
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('files_delete')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $file = ProjectFile::findOrFail($id);

            if ($file->staffid !== Auth::id() && !Auth::user()->can('files_delete_all')) {
                return response()->json(['success' => false, 'message' => 'Cannot delete file you did not upload'], 403);
            }

            $this->fileService->delete($file->file_name);
            $file->delete();

            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getFiles(Request $request)
    {
        if (!Auth::user()->can('files_view')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'project_id' => 'required|integer|exists:tblprojects,id',
        ]);

        try {
            $files = ProjectFile::with('staff', 'folder')
                ->where('project_id', $request->project_id)
                ->orderBy('dateadded', 'desc')
                ->get();

            foreach ($files as $file) {
                $file->fileurl = $this->fileService->url($file->file_name);
            }

            return response()->json([
                'success' => true,
                'files' => $files
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function rename(Request $request, $id)
    {
        if (!Auth::user()->can('files_create')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate(['name' => 'required|string|max:255']);

        $file = ProjectFile::findOrFail($id);
        $file->update(['original_file_name' => $request->name]);

        return response()->json(['success' => true]);
    }

    public function preview($id)
    {
        if (!Auth::user()->can('files_view')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $file = ProjectFile::with('staff')->findOrFail($id);
        $preview = $this->previewService->getPreviewData($file);
        $fileUrl = $this->fileService->url($file->file_name);

        return response()->json([
            'success' => true,
            'file' => $file,
            'preview' => $preview,
            'url' => $fileUrl,
        ]);
    }

    public function download($projectId, $fileId)
    {
        $file = ProjectFile::where('project_id', $projectId)->findOrFail($fileId);

        $path = $this->fileService->getFilePath($file->file_name);
        if (!$path) {
            return response()->json(['success' => false, 'message' => 'File not found on disk'], 404);
        }

        ProjectFileDownload::create([
            'file_id' => $file->id,
            'staffid' => Auth::id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'downloaded_at' => now(),
        ]);

        $file->update(['last_activity' => now()]);

        return response()->download($path, $file->original_file_name ?? $file->file_name);
    }

    public function bulkDownload(Request $request)
    {
        if (!Auth::user()->can('files_view')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'file_ids' => 'required|array',
            'file_ids.*' => 'integer|exists:tblproject_files,id',
        ]);

        $files = ProjectFile::whereIn('id', $request->file_ids)->get();

        foreach ($files as $file) {
            ProjectFileDownload::create([
                'file_id' => $file->id,
                'staffid' => Auth::id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'downloaded_at' => now(),
            ]);
        }

        $zipName = 'files_' . time() . '.zip';
        $this->fileService->createZipFromFiles($files->all(), $zipName);

        return response()->json([
            'success' => true,
            'url' => $this->fileService->url($zipName),
        ]);
    }

    public function setExpiry(Request $request, $id)
    {
        if (!Auth::user()->can('files_create')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'expires_at' => 'nullable|date|after:now',
        ]);

        $file = ProjectFile::findOrFail($id);
        $file->update(['expires_at' => $request->expires_at]);

        return response()->json(['success' => true]);
    }

    // ===== Folder CRUD =====

    public function folders($projectId)
    {
        $folders = ProjectFileFolder::where('project_id', $projectId)
            ->withCount('files')
            ->orderBy('name')
            ->get();
        return response()->json(['success' => true, 'folders' => $folders]);
    }

    public function storeFolder(Request $request, $projectId)
    {
        if (!Auth::user()->can('files_create')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|integer|exists:tblproject_file_folders,id',
        ]);

        $folder = ProjectFileFolder::create([
            'project_id' => $projectId,
            'name' => $request->name,
            'parent_id' => $request->parent_id,
        ]);

        return response()->json(['success' => true, 'folder' => $folder]);
    }

    public function destroyFolder($id)
    {
        if (!Auth::user()->can('files_delete')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $folder = ProjectFileFolder::findOrFail($id);
        ProjectFile::where('folder_id', $id)->update(['folder_id' => null]);
        $folder->delete();

        return response()->json(['success' => true]);
    }

    public function moveToFolder(Request $request)
    {
        if (!Auth::user()->can('files_create')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'file_ids' => 'required|array',
            'file_ids.*' => 'integer|exists:tblproject_files,id',
            'folder_id' => 'nullable|integer|exists:tblproject_file_folders,id',
        ]);

        ProjectFile::whereIn('id', $request->file_ids)->update(['folder_id' => $request->folder_id]);

        return response()->json(['success' => true]);
    }

    public function downloadHistory($id)
    {
        $file = ProjectFile::findOrFail($id);
        $downloads = $file->downloads()->with('staff', 'contact')
            ->orderBy('downloaded_at', 'desc')
            ->paginate(20);

        return response()->json(['success' => true, 'downloads' => $downloads]);
    }
}
