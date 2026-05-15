<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share settings with all views
        view()->composer('*', function ($view) {
            $settings = \App\Helpers\SettingsHelper::getMany([
                 'companyname',
                 'company_logo',
                 'favicon',
                 'company_email',
                 'companyphone',
                 'companyaddress',
                 'company_vat',
                 'main_domain',
                 'allow_registration',
                 'use_knowledge_base',
                 'primary_color',
                 'secondary_color',
                 'custom_css',
                 'enable_custom_css',
                 'login_background_url',
                 'terms_of_service_url',
                 'privacy_policy_url',
                 'copyright_text',
                 'default_project_tab',
                 'default_working_hours',
                 'payroll_cutoff_day',
                 'overtime_rate_multiplier',
                 'late_grace_period',
                 'attendance_default_status',
                 'attendance_require_ip',
                 'attendance_absent_threshold',
                 'leave_default_status',
                 'leave_allow_backdate_days',
                 'leave_approval_statuses',
                 'leave_quota_default',
                 'payroll_default_status',
                 'payroll_paid_status',
                 'payroll_currency_symbol',
                 'payroll_adjustment_types',
                 'default_staff_role',
                 'tables_pagination_limit',
             ]);
            $view->with('global_settings', $settings);
        });

        // Custom Blade Directive for Permission Checking
        Blade::if('can_do', function ($permission) {
            $user = Auth::guard('staff')->user();
            return $user && $user->can($permission);
        });
    }
}
