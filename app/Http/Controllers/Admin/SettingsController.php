<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Option;
use App\Services\FileService;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    protected $fileService;
    protected $auditService;

    public function __construct(FileService $fileService, AuditService $auditService)
    {
        $this->fileService = $fileService;
        $this->auditService = $auditService;
    }

    public function index()
    {
        // Check if the staff user has permission to access settings
        if (!Auth::user()->can('settings_view')) {
            abort(403, 'Unauthorized action.');
        }

        $settings = Option::all()->pluck('value', 'name');

        // Fetch additional lookups for module settings
        $lead_sources = \App\Models\LeadSource::all();
        $lead_statuses = \App\Models\LeadStatus::all();

        return view('admin.settings.index', compact('settings', 'lead_sources', 'lead_statuses'));
    }

    public function update(Request $request)
    {
        // Check if the staff user has permission to edit settings
        if (!Auth::user()->can('settings_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $settings = $request->except(['_token', 'company_logo', 'favicon']);

        // Handle File Uploads using FileService
        if ($request->hasFile('company_logo')) {
            $upload = $this->fileService->upload($request->file('company_logo'), 'logo');
            $settings['company_logo'] = $this->fileService->url($upload['file_name']);
        }

        if ($request->hasFile('favicon')) {
            $upload = $this->fileService->upload($request->file('favicon'), 'favicon');
            $settings['favicon'] = $this->fileService->url($upload['file_name']);
        }

        // Handle checkboxes that are not present in the request
        $checkboxes = [
            'allow_registration',
            'use_knowledge_base',
            'knowledge_base_without_registration',
            'two_factor_enabled',
            'client_2fa_enabled',
            'remove_decimal_on_zero',
            'show_tax_per_item',
            'enable_custom_css',
            'enable_custom_js',
            'proposal_auto_convert_leads_to_client_on_client_accept',
            'show_sale_agent_on_invoices',
            'show_sale_agent_on_estimates',
            'estimate_auto_convert_to_invoice_on_client_accept'
        ];

        foreach ($checkboxes as $checkbox) {
            if (!$request->has($checkbox)) {
                $settings[$checkbox] = '0';
            }
        }

        foreach ($settings as $name => $value) {
            // Use updateOrCreate to ensure settings are saved correctly
            \App\Models\Option::updateOrCreate(
                ['name' => $name],
                ['value' => $value ?? '']
            );
        }

        // Clear settings cache so changes are reflected immediately
        Cache::forget('settings_autoload');

        $this->auditService->settingsUpdated($settings, $request);

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }
}
