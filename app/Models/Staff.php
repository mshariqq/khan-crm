<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Staff extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $table = 'tblstaff';
    protected $primaryKey = 'staffid';
    public $timestamps = true;
    protected $fillable = [
        'staffid', 'email', 'firstname', 'lastname', 'facebook', 'linkedin', 'phonenumber', 'skype', 
        'password', 'datecreated', 'profile_image', 'last_ip', 'last_login', 'last_activity', 
        'last_password_change', 'new_pass_key', 'new_pass_key_requested', 'admin', 'role', 'active', 
        'default_language', 'direction', 'media_path_slug', 'is_not_staff', 'hourly_rate', 
        'two_factor_auth_enabled', 'two_factor_auth_code', 'two_factor_auth_code_requested', 
        'email_signature', 'google_auth_secret'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'datecreated' => 'datetime',
        'last_login' => 'datetime',
        'last_activity' => 'datetime',
        'last_password_change' => 'datetime',
        'new_pass_key_requested' => 'datetime',
        'two_factor_auth_code_requested' => 'datetime',
        'active' => 'boolean',
        'is_not_staff' => 'boolean',
        'two_factor_auth_enabled' => 'boolean'
    ];

    protected $attributes = [
        'active' => true,
        'admin' => false,
        'is_not_staff' => false
    ];
    protected $hidden = ['password'];

    public function staffRole()
    {
        return $this->belongsTo(Role::class, 'role', 'roleid');
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'tblstaff_departments', 'staffid', 'departmentid');
    }

    public function permissions()
    {
        return $this->hasMany(StaffPermission::class, 'staff_id', 'staffid');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'tblproject_members', 'staff_id', 'project_id');
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'tbltask_assigned', 'staffid', 'taskid');
    }

    public function getFullNameAttribute()
    {
        return trim($this->firstname . ' ' . $this->lastname);
    }

    public function can($abilities, $arguments = []): bool
    {
        $permission = is_array($abilities) ? $abilities[0] : $abilities;
        return app(\App\Services\PermissionService::class)->hasPermission($this, $permission);
    }

    public function canViewProject(Project $project): bool
    {
        if ($this->can('projects_view')) {
            return true;
        }
        return $project->staff->contains('staffid', $this->staffid);
    }

    public function hasRole(int $roleId): bool
    {
        return app(\App\Services\PermissionService::class)->hasRole($this, $roleId);
    }

    public function createdInvoices()
    {
        return $this->hasMany(Invoice::class, 'addedfrom', 'staffid');
    }

    public function managedInvoices()
    {
        return $this->hasMany(Invoice::class, 'sale_agent', 'staffid');
    }

    public function createdEstimates()
    {
        return $this->hasMany(Estimate::class, 'addedfrom', 'staffid');
    }

    public function managedEstimates()
    {
        return $this->hasMany(Estimate::class, 'sale_agent', 'staffid');
    }

    public function createdProposals()
    {
        return $this->hasMany(Proposal::class, 'addedfrom', 'staffid');
    }

    public function assignedProposals()
    {
        return $this->hasMany(Proposal::class, 'assigned', 'staffid');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'staffid', 'staffid');
    }

    public function leadActivityLogs()
    {
        return $this->hasMany(LeadActivityLog::class, 'staffid', 'staffid');
    }

    public function projectActivities()
    {
        return $this->hasMany(ProjectActivity::class, 'staff_id', 'staffid');
    }

    public function attendance()
    {
        return $this->hasMany(StaffAttendance::class, 'staffid', 'staffid');
    }

    public function leaves()
    {
        return $this->hasMany(StaffLeave::class, 'staffid', 'staffid');
    }

    public function salaryConfig()
    {
        return $this->hasOne(StaffSalaryConfig::class, 'staffid', 'staffid');
    }

    public function payslips()
    {
        return $this->hasMany(StaffPayslip::class, 'staffid', 'staffid');
    }

    public function timers()
    {
        return $this->hasMany(TaskTimer::class, 'staff_id', 'staffid');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'touserid', 'staffid');
    }

    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class, 'touserid', 'staffid')->where('isread', 0);
    }

    public function notificationPreferences()
    {
        return $this->hasMany(StaffNotificationPreference::class, 'staffid', 'staffid');
    }
}
