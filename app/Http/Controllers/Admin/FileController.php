<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectFile;
use App\Models\Project;
use App\Models\ProjectActivity;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
{
    public function __construct(
        protected FileService $fileService
    ) {}

    public function upload(Request $request)
    {
        if (!Auth::user()->can('files_create')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'files' => 'required|array',
            'files.*' => 'required|file|max:10240',
            'project_id' => 'required|integer|exists:tblprojects,id',
        ]);

        try {
            $uploadedFiles = [];

            foreach ($request->file('files') as $file) {
                $uploaded = $this->fileService->upload($file, 'project');

                $fileRecord = ProjectFile::create([
                    'project_id' => $request->project_id,
                    'staffid' => Auth::id(),
                    'file_name' => $uploaded['file_name'],
                    'original_file_name' => $uploaded['original_file_name'],
                    'filetype' => $uploaded['filetype'],
                    'dateadded' => now(),
                ]);

                // Log activity for file upload
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
            $files = ProjectFile::with('staff')
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
}
