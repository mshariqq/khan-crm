<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Role;
use App\Models\Department;
use App\Models\Permission;
use App\Models\StaffPermission;
use App\Services\PermissionService;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    protected $permissionService;
    protected $auditService;

    public function __construct(PermissionService $permissionService, AuditService $auditService)
    {
        $this->permissionService = $permissionService;
        $this->auditService = $auditService;
    }

    public function index()
    {
        if (!Auth::user()->can('staff_view')) {
            abort(403, 'Unauthorized action.');
        }

        $staff = Staff::with('staffRole')->paginate(15);
        return view('admin.staff.index', compact('staff'));
    }

    public function create()
    {
        if (!Auth::user()->can('staff_create')) {
            abort(403, 'Unauthorized action.');
        }

        $roles = Role::all();
        $departments = Department::all();

        return view('admin.staff.create', compact('roles', 'departments'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('staff_create')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'email' => 'required|email|unique:tblstaff,email',
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/',
            'role' => 'required|integer',
            'active' => 'required|integer',
            'departments' => 'nullable|array',
        ]);

        $role = Role::find($request->role);
        $staff = Staff::create([
            'email' => $request->email,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'active' => $request->active,
            'datecreated' => now(),
            'admin' => $role && $role->is_admin ? 1 : 0,
        ]);

        if ($request->has('departments')) {
            $staff->departments()->attach($request->departments);
        }

        $this->auditService->staffCreated($staff->staffid, $staff->toArray(), $request);
        $this->permissionService->clearCache();
        return redirect()->route('admin.staff.index')->with('success', 'Staff member created successfully!');
    }

    public function edit($id)
    {
        if (!Auth::user()->can('staff_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $staff = Staff::with('departments')->findOrFail($id);
        $roles = Role::all();
        $departments = Department::all();
        $allPermissions = Permission::orderBy('name')->get();
        $staffPermissionCaps = StaffPermission::where('staff_id', $staff->staffid)->pluck('capability')->toArray();
        $deniedPermissions = array_values(array_filter($staffPermissionCaps, fn($p) => str_starts_with($p, '!')));
        $deniedPermissionSlugs = array_map(fn($p) => substr($p, 1), $deniedPermissions);
        $rolePermissionSlugs = $staff->staffRole ? $staff->staffRole->getPermissionSlugs() : [];

        return view('admin.staff.edit', compact('staff', 'roles', 'departments', 'allPermissions', 'deniedPermissionSlugs', 'rolePermissionSlugs'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('staff_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'email' => 'required|email|unique:tblstaff,email,' . $id . ',staffid',
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'role' => 'required|integer',
            'active' => 'required|integer',
            'departments' => 'nullable|array',
        ]);

        $staff = Staff::findOrFail($id);

        if ($request->filled('password')) {
            $request->validate([
                'current_password' => 'required|string',
                'password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/',
            ]);

            if (!Hash::check($request->current_password, auth()->user()->password)) {
                return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
            }
        }
        
        $role = Role::find($request->role);
        $updateData = [
            'email' => $request->email,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'role' => $request->role,
            'active' => $request->active,
            'admin' => $role && $role->is_admin ? 1 : 0,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $oldData = $staff->toArray();
        $staff->update($updateData);

        if ($request->has('departments')) {
            $staff->departments()->sync($request->departments);
        }

        $this->auditService->staffUpdated($staff->staffid, $oldData, $staff->fresh()->toArray(), $request);

        if ($request->has('denied_permissions')) {
            StaffPermission::where('staff_id', $staff->staffid)
                ->where('capability', 'like', '!%')
                ->delete();
            foreach ($request->denied_permissions as $slug) {
                StaffPermission::updateOrCreate(
                    ['staff_id' => $staff->staffid, 'capability' => '!' . $slug],
                    ['feature' => explode('_', $slug)[0] ?? $slug]
                );
            }
        } else {
            StaffPermission::where('staff_id', $staff->staffid)
                ->where('capability', 'like', '!%')
                ->delete();
        }

        $this->permissionService->clearCache();
        return redirect()->route('admin.staff.index')->with('success', 'Staff member updated successfully!');
    }

    public function sendVerificationEmail($id)
    {
        if (!Auth::user()->can('staff_edit')) {
            abort(403, 'Unauthorized action.');
        }

        return app(\App\Http\Controllers\Auth\EmailVerificationController::class)->sendStaffVerification(
            new Request(['staff_id' => $id])
        );
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('staff_delete')) {
            abort(403, 'Unauthorized action.');
        }

        $staff = Staff::findOrFail($id);

        if ($staff->staffid === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $staffData = $staff->toArray();
        $staff->delete();
        $this->auditService->staffDeleted($id, $staffData, $request);
        $this->permissionService->clearCache();
        return redirect()->route('admin.staff.index')->with('success', 'Staff member deleted successfully!');
    }
}
