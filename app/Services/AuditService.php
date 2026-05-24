<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditService
{
    public function log(
        string $action,
        ?string $description = null,
        ?string $entityType = null,
        ?int $entityId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $staffId = null,
        ?Request $request = null
    ): AuditLog {
        $staffId ??= Auth::guard('staff')->id();
        $ip = $request?->ip() ?? request()->ip();
        $ua = $request?->userAgent() ?? request()->userAgent();

        return AuditLog::create([
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'description' => $description ?? $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'staff_id' => $staffId,
            'ip_address' => $ip,
            'user_agent' => $ua,
        ]);
    }

    public function staffCreated(int $staffId, array $data, ?Request $request = null): void
    {
        $this->log('staff_created', 'Staff member created', 'Staff', $staffId, null, $data, null, $request);
    }

    public function staffUpdated(int $staffId, array $old, array $new, ?Request $request = null): void
    {
        $diffs = array_keys(array_diff_assoc(
            array_intersect_key($new, $old),
            array_intersect_key($old, $new)
        ));
        if (!empty($diffs)) {
            $this->log('staff_updated', 'Staff member updated: ' . implode(', ', $diffs), 'Staff', $staffId, $old, $new, null, $request);
        }
    }

    public function staffDeleted(int $staffId, array $data, ?Request $request = null): void
    {
        $this->log('staff_deleted', 'Staff member deleted', 'Staff', $staffId, $data, null, null, $request);
    }

    public function roleCreated(int $roleId, array $data, ?Request $request = null): void
    {
        $this->log('role_created', 'Role created: ' . ($data['name'] ?? ''), 'Role', $roleId, null, $data, null, $request);
    }

    public function roleUpdated(int $roleId, array $old, array $new, ?Request $request = null): void
    {
        $this->log('role_updated', 'Role updated: ' . ($new['name'] ?? ''), 'Role', $roleId, $old, $new, null, $request);
    }

    public function roleDeleted(int $roleId, array $data, ?Request $request = null): void
    {
        $this->log('role_deleted', 'Role deleted: ' . ($data['name'] ?? ''), 'Role', $roleId, $data, null, null, $request);
    }

    public function login(string $guard, int $userId, ?Request $request = null): void
    {
        $this->log("login_{$guard}", "{$guard} login", ucfirst($guard), $userId, null, null, $guard === 'staff' ? $userId : null, $request);
    }

    public function logout(string $guard, int $userId, ?Request $request = null): void
    {
        $this->log("logout_{$guard}", "{$guard} logout", ucfirst($guard), $userId, null, null, $guard === 'staff' ? $userId : null, $request);
    }

    public function settingsUpdated(array $changes, ?Request $request = null): void
    {
        $this->log('settings_updated', 'Settings updated: ' . implode(', ', array_keys($changes)), 'Settings', null, null, $changes, null, $request);
    }

    public function permissionChanged(int $staffId, string $permission, string $type, ?Request $request = null): void
    {
        $this->log("permission_{$type}", "Permission {$type}: {$permission}", 'Staff', $staffId, null, [$permission => $type], null, $request);
    }

    public function payrollAction(string $action, ?int $payslipId = null, ?array $data = null, ?Request $request = null): void
    {
        $this->log("payroll_{$action}", "Payroll {$action}", 'Payslip', $payslipId, null, $data, null, $request);
    }

    public function projectCreated(int $projectId, array $data, ?Request $request = null): void
    {
        $this->log('project_created', 'Project created', 'Project', $projectId, null, $data, null, $request);
    }

    public function projectUpdated(int $projectId, array $old, array $new, ?Request $request = null): void
    {
        $diffs = array_keys(array_diff_assoc(
            array_intersect_key($new, $old),
            array_intersect_key($old, $new)
        ));
        if (!empty($diffs)) {
            $this->log('project_updated', 'Project updated: ' . implode(', ', $diffs), 'Project', $projectId, $old, $new, null, $request);
        }
    }

    public function projectDeleted(int $projectId, array $data, ?Request $request = null): void
    {
        $this->log('project_deleted', 'Project deleted', 'Project', $projectId, $data, null, null, $request);
    }

    public function projectStatusChanged(int $projectId, int $oldStatus, int $newStatus, ?Request $request = null): void
    {
        $this->log('project_status_changed', "Project status changed from {$oldStatus} to {$newStatus}", 'Project', $projectId, ['status' => $oldStatus], ['status' => $newStatus], null, $request);
    }
}
