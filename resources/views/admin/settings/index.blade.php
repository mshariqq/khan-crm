@extends('layouts.admin')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">System Settings</h1>
            <p class="text-slate-500 text-sm">Manage your CRM global configuration and preferences.</p>
        </div>
        <div class="flex items-center gap-3">
            @if(session('success'))
                <div class="px-4 py-2 bg-green-100 text-green-700 rounded-lg text-sm font-medium animate-fade-in">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <!-- Tabs Header -->
        <div class="flex border-b border-slate-200 bg-slate-50/50 px-4 overflow-x-auto">
            <button onclick="switchTab('general')" id="tab-general" class="tab-btn active px-6 py-4 text-sm font-semibold transition-all border-b-2 border-blue-500 text-blue-600 whitespace-nowrap">
                <i class="bi bi-info-circle mr-2"></i> General
            </button>
            <button onclick="switchTab('branding')" id="tab-branding" class="tab-btn px-6 py-4 text-sm font-semibold text-slate-500 hover:text-slate-700 transition-all border-b-2 border-transparent hover:border-slate-300 whitespace-nowrap">
                <i class="bi bi-palette mr-2"></i> Branding
            </button>
            <button onclick="switchTab('email')" id="tab-email" class="tab-btn px-6 py-4 text-sm font-semibold text-slate-500 hover:text-slate-700 transition-all border-b-2 border-transparent hover:border-slate-300 whitespace-nowrap">
                <i class="bi bi-envelope mr-2"></i> Email
            </button>
            <button onclick="switchTab('financials')" id="tab-financials" class="tab-btn px-6 py-4 text-sm font-semibold text-slate-500 hover:text-slate-700 transition-all border-b-2 border-transparent hover:border-slate-300 whitespace-nowrap">
                <i class="bi bi-cash-stack mr-2"></i> Financials
            </button>
            <button onclick="switchTab('system')" id="tab-system" class="tab-btn px-6 py-4 text-sm font-semibold text-slate-500 hover:text-slate-700 transition-all border-b-2 border-transparent hover:border-slate-300 whitespace-nowrap">
                <i class="bi bi-gear mr-2"></i> System
            </button>
            <button onclick="switchTab('leads')" id="tab-leads" class="tab-btn px-6 py-4 text-sm font-semibold text-slate-500 hover:text-slate-700 transition-all border-b-2 border-transparent hover:border-slate-300 whitespace-nowrap">
                <i class="bi bi-megaphone mr-2"></i> Leads
            </button>
            <button onclick="switchTab('projects')" id="tab-projects" class="tab-btn px-6 py-4 text-sm font-semibold text-slate-500 hover:text-slate-700 transition-all border-b-2 border-transparent hover:border-slate-300 whitespace-nowrap">
                <i class="bi bi-kanban mr-2"></i> Projects
            </button>
            <button onclick="switchTab('frontend')" id="tab-frontend" class="tab-btn px-6 py-4 text-sm font-semibold text-slate-500 hover:text-slate-700 transition-all border-b-2 border-transparent hover:border-slate-300 whitespace-nowrap">
                <i class="bi bi-tv mr-2"></i> Front End
            </button>
            <button onclick="switchTab('legal')" id="tab-legal" class="tab-btn px-6 py-4 text-sm font-semibold text-slate-500 hover:text-slate-700 transition-all border-b-2 border-transparent hover:border-slate-300 whitespace-nowrap">
                <i class="bi bi-shield-check mr-2"></i> Legal
            </button>
            <button onclick="switchTab('hr')" id="tab-hr" class="tab-btn px-6 py-4 text-sm font-semibold text-slate-500 hover:text-slate-700 transition-all border-b-2 border-transparent hover:border-slate-300 whitespace-nowrap">
                <i class="bi bi-people mr-2"></i> HR
            </button>
            <a href="{{ route('admin.settings.ticket_email_integration.index') }}" class="px-6 py-4 text-sm font-semibold text-slate-500 hover:text-slate-700 transition-all border-b-2 border-transparent hover:border-slate-300 whitespace-nowrap flex items-center">
                <i class="bi bi-envelope-at mr-2"></i> Ticket Email Integration
            </a>
            <a href="{{ route('admin.settings.email_templates.index') }}" class="px-6 py-4 text-sm font-semibold text-slate-500 hover:text-slate-700 transition-all border-b-2 border-transparent hover:border-slate-300 whitespace-nowrap flex items-center">
                <i class="bi bi-envelope-paper mr-2"></i> Email Templates
            </a>
        </div>

        <!-- Tabs Content -->
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            
            <!-- General Tab -->
            <div id="content-general" class="tab-content space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700">Company Name</label>
                        <input type="text" name="companyname" value="{{ $settings['companyname'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700">Company Email</label>
                        <input type="email" name="company_email" value="{{ $settings['company_email'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700">Company Phone</label>
                        <input type="text" name="companyphone" value="{{ $settings['companyphone'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700">VAT / Tax Number</label>
                        <input type="text" name="company_vat" value="{{ $settings['company_vat'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    </div>
                    <div class="space-y-2 md:col-span-2">
                        <label class="text-sm font-medium text-slate-700">Company Address</label>
                        <textarea name="companyaddress" rows="2" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">{{ $settings['companyaddress'] ?? '' }}</textarea>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700">Default Language</label>
                        <input type="text" name="active_language" value="{{ $settings['active_language'] ?? 'english' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700">Default Timezone</label>
                        <input type="text" name="default_timezone" value="{{ $settings['default_timezone'] ?? 'UTC' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    </div>
                </div>
            </div>

            <!-- Branding Tab -->
            <div id="content-branding" class="tab-content hidden space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <label class="text-sm font-medium text-slate-700 block">Company Logo</label>
                        <div class="flex items-center gap-4 p-4 border border-dashed border-slate-300 rounded-xl bg-slate-50">
                            @if(!empty($settings['company_logo']))
                                <img src="{{ $settings['company_logo'] }}" class="h-12 w-auto rounded border bg-white shadow-sm" id="logo-preview">
                            @endif
                            <div class="flex-1">
                                <input type="file" name="company_logo" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all">
                                <p class="text-[10px] text-slate-400 mt-1">Recommended: PNG, 250x60px</p>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <label class="text-sm font-medium text-slate-700 block">Favicon</label>
                        <div class="flex items-center gap-4 p-4 border border-dashed border-slate-300 rounded-xl bg-slate-50">
                            @if(!empty($settings['favicon']))
                                <img src="{{ $settings['favicon'] }}" class="h-8 w-8 rounded border bg-white shadow-sm" id="favicon-preview">
                            @endif
                            <div class="flex-1">
                                <input type="file" name="favicon" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all">
                                <p class="text-[10px] text-slate-400 mt-1">Recommended: ICO or PNG, 32x32px</p>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700">Primary Brand Color</label>
                        <div class="flex gap-2">
                            <input type="color" name="primary_color" value="{{ $settings['primary_color'] ?? '#2563eb' }}" class="h-10 w-20 rounded-lg border border-slate-300 p-1">
                            <input type="text" value="{{ $settings['primary_color'] ?? '#2563eb' }}" class="flex-1 px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 outline-none" readonly>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700">Secondary Brand Color</label>
                        <div class="flex gap-2">
                            <input type="color" name="secondary_color" value="{{ $settings['secondary_color'] ?? '#4f46e5' }}" class="h-10 w-20 rounded-lg border border-slate-300 p-1">
                            <input type="text" value="{{ $settings['secondary_color'] ?? '#4f46e5' }}" class="flex-1 px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 outline-none" readonly>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700">Login Page Background URL</label>
                        <input type="text" name="login_background_url" value="{{ $settings['login_background_url'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    </div>
                    <div class="flex items-center gap-3 py-4">
                        <input type="checkbox" name="enable_custom_css" value="1" {{ ($settings['enable_custom_css'] ?? '') == '1' ? 'checked' : '' }} id="enable_css" class="w-5 h-5 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                        <label for="enable_css" class="text-sm font-medium text-slate-700">Enable Custom CSS</label>
                    </div>
                    <div class="space-y-2 md:col-span-2">
                        <label class="text-sm font-medium text-slate-700">Custom CSS</label>
                        <textarea name="custom_css" rows="6" class="w-full px-4 py-2 rounded-lg border border-slate-300 font-mono text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="/* Add your custom CSS here */">{{ $settings['custom_css'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Email Tab -->
            <div id="content-email" class="tab-content hidden space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700">SMTP Host</label>
                        <input type="text" name="smtp_host" value="{{ $settings['smtp_host'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700">SMTP Port</label>
                        <input type="text" name="smtp_port" value="{{ $settings['smtp_port'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700">SMTP User</label>
                        <input type="text" name="smtp_email" value="{{ $settings['smtp_email'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700">SMTP Password</label>
                        <input type="password" name="smtp_password" value="{{ $settings['smtp_password'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700">Encryption</label>
                        <select name="smtp_encrypt" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                            <option value="tls" {{ ($settings['smtp_encrypt'] ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ ($settings['smtp_encrypt'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                            <option value="none" {{ ($settings['smtp_encrypt'] ?? '') == 'none' ? 'selected' : '' }}>None</option>
                        </select>
                    </div>
                    <div class="space-y-2 md:col-span-2">
                        <label class="text-sm font-medium text-slate-700">Email Signature</label>
                        <textarea name="email_signature" rows="4" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">{{ $settings['email_signature'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Financials Tab -->
            <div id="content-financials" class="tab-content hidden space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700">Invoice Prefix</label>
                        <input type="text" name="invoice_prefix" value="{{ $settings['invoice_prefix'] ?? 'INV-' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700">Estimate Prefix</label>
                        <input type="text" name="estimate_prefix" value="{{ $settings['estimate_prefix'] ?? 'EST-' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700">Invoice Due After (Days)</label>
                        <input type="number" name="invoice_due_after" value="{{ $settings['invoice_due_after'] ?? '30' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700">PDF Logo Width (px)</label>
                        <input type="number" name="pdf_logo_width" value="{{ $settings['pdf_logo_width'] ?? '150' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700">Decimal Separator</label>
                        <input type="text" name="decimal_separator" value="{{ $settings['decimal_separator'] ?? '.' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700">Thousand Separator</label>
                        <input type="text" name="thousand_separator" value="{{ $settings['thousand_separator'] ?? ',' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    </div>
                </div>
            </div>

            <!-- System Tab -->
            <div id="content-system" class="tab-content hidden space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700">Pagination Limit</label>
                        <input type="number" name="tables_pagination_limit" value="{{ $settings['tables_pagination_limit'] ?? '25' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700">Main Domain</label>
                        <input type="text" name="main_domain" value="{{ $settings['main_domain'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    </div>
                    <div class="flex items-center gap-3 py-4">
                        <input type="checkbox" name="allow_registration" value="1" {{ ($settings['allow_registration'] ?? '') == '1' ? 'checked' : '' }} id="allow_reg" class="w-5 h-5 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                        <label for="allow_reg" class="text-sm font-medium text-slate-700">Allow Client Registration</label>
                    </div>
                    <div class="flex items-center gap-3 py-4">
                        <input type="checkbox" name="use_knowledge_base" value="1" {{ ($settings['use_knowledge_base'] ?? '') == '1' ? 'checked' : '' }} id="use_kb" class="w-5 h-5 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                        <label for="use_kb" class="text-sm font-medium text-slate-700">Enable Knowledge Base</label>
                    </div>
                    <div class="flex items-center gap-3 py-4">
                        <input type="checkbox" name="knowledge_base_without_registration" value="1" {{ ($settings['knowledge_base_without_registration'] ?? '') == '1' ? 'checked' : '' }} id="kb_no_reg" class="w-5 h-5 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                        <label for="kb_no_reg" class="text-sm font-medium text-slate-700">KB Access without Registration</label>
                    </div>
                    <div class="space-y-2 md:col-span-2">
                        <h3 class="text-lg font-bold text-slate-800 mt-4 border-b pb-2">Financial Settings</h3>
                    </div>
                    <div class="flex items-center gap-3 py-4">
                        <input type="checkbox" name="show_sale_agent_on_invoices" value="1" {{ ($settings['show_sale_agent_on_invoices'] ?? '') == '1' ? 'checked' : '' }} id="show_sale_agent_inv" class="w-5 h-5 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                        <label for="show_sale_agent_inv" class="text-sm font-medium text-slate-700">Show Sale Agent on Invoices</label>
                    </div>
                    <div class="flex items-center gap-3 py-4">
                        <input type="checkbox" name="show_sale_agent_on_estimates" value="1" {{ ($settings['show_sale_agent_on_estimates'] ?? '') == '1' ? 'checked' : '' }} id="show_sale_agent_est" class="w-5 h-5 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                        <label for="show_sale_agent_est" class="text-sm font-medium text-slate-700">Show Sale Agent on Estimates</label>
                    </div>
                    <div class="flex items-center gap-3 py-4">
                        <input type="checkbox" name="estimate_auto_convert_to_invoice_on_client_accept" value="1" {{ ($settings['estimate_auto_convert_to_invoice_on_client_accept'] ?? '') == '1' ? 'checked' : '' }} id="est_auto_conv" class="w-5 h-5 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                        <label for="est_auto_conv" class="text-sm font-medium text-slate-700">Auto convert estimate to invoice on client accept</label>
                    </div>
                    <div class="space-y-2 md:col-span-2">
                        <label class="text-sm font-medium text-slate-700">Default Invoice Terms & Conditions</label>
                        <textarea name="invoice_terms" rows="3" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 outline-none transition-all">{{ $settings['invoice_terms'] ?? '' }}</textarea>
                    </div>
                    <div class="space-y-2 md:col-span-2">
                        <label class="text-sm font-medium text-slate-700">Default Client Note on Invoices</label>
                        <textarea name="invoice_client_note" rows="2" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 outline-none transition-all">{{ $settings['invoice_client_note'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Leads Tab -->
            <div id="content-leads" class="tab-content hidden space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700">Default Lead Source</label>
                        <select name="leads_default_source" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="">Select Source</option>
                            @foreach($lead_sources as $source)
                                <option value="{{ $source->id }}" {{ ($settings['leads_default_source'] ?? '') == $source->id ? 'selected' : '' }}>{{ $source->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700">Default Lead Status</label>
                        <select name="leads_default_status" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="">Select Status</option>
                            @foreach($lead_statuses as $status)
                                <option value="{{ $status->id }}" {{ ($settings['leads_default_status'] ?? '') == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center gap-3 py-4">
                        <input type="checkbox" name="proposal_auto_convert_leads_to_client_on_client_accept" value="1" {{ ($settings['proposal_auto_convert_leads_to_client_on_client_accept'] ?? '') == '1' ? 'checked' : '' }} id="lead_auto_conv" class="w-5 h-5 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                        <label for="lead_auto_conv" class="text-sm font-medium text-slate-700">Auto convert lead to client on proposal accept</label>
                    </div>
                </div>
            </div>

            <!-- Projects Tab -->
            <div id="content-projects" class="tab-content hidden space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700">Default Project Tab</label>
                        <select name="default_project_tab" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="overview" {{ ($settings['default_project_tab'] ?? 'overview') == 'overview' ? 'selected' : '' }}>Overview</option>
                            <option value="tasks" {{ ($settings['default_project_tab'] ?? '') == 'tasks' ? 'selected' : '' }}>Tasks</option>
                            <option value="timesheets" {{ ($settings['default_project_tab'] ?? '') == 'timesheets' ? 'selected' : '' }}>Timesheets</option>
                            <option value="milestones" {{ ($settings['default_project_tab'] ?? '') == 'milestones' ? 'selected' : '' }}>Milestones</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Front End Tab -->
            <div id="content-frontend" class="tab-content hidden space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700">Copyright Footer Text</label>
                        <input type="text" name="copyright_text" value="{{ $settings['copyright_text'] ?? 'All rights reserved' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                        <p class="text-xs text-slate-400">Displayed in the footer across all layouts (Admin, Client Portal, Guest).</p>
                    </div>
                </div>
            </div>

            <!-- Legal Tab -->
            <div id="content-legal" class="tab-content hidden space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700">Terms of Service URL</label>
                        <input type="text" name="terms_of_service_url" value="{{ $settings['terms_of_service_url'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700">Privacy Policy URL</label>
                        <input type="text" name="privacy_policy_url" value="{{ $settings['privacy_policy_url'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700">Cookie Policy URL</label>
                        <input type="text" name="cookie_policy_url" value="{{ $settings['cookie_policy_url'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    </div>
                    <div class="space-y-2 md:col-span-2">
                        <h3 class="text-lg font-bold text-slate-800 mt-4 border-b pb-2">Security Settings</h3>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700">Session Timeout (minutes)</label>
                        <input type="number" name="session_timeout" value="{{ $settings['session_timeout'] ?? '120' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700">Min Password Length</label>
                        <input type="number" name="password_min_length" value="{{ $settings['password_min_length'] ?? '8' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    </div>
                    <div class="flex items-center gap-3 py-4">
                        <input type="checkbox" name="two_factor_enabled" value="1" {{ ($settings['two_factor_enabled'] ?? '') == '1' ? 'checked' : '' }} id="two_factor" class="w-5 h-5 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                        <label for="two_factor" class="text-sm font-medium text-slate-700">Enable Two-Factor Authentication</label>
                    </div>
                    <div class="flex items-center gap-3 py-4">
                        <input type="checkbox" name="client_2fa_enabled" value="1" {{ ($settings['client_2fa_enabled'] ?? '') == '1' ? 'checked' : '' }} id="client_2fa" class="w-5 h-5 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                        <label for="client_2fa" class="text-sm font-medium text-slate-700">Enable Client Two-Factor Authentication</label>
                    </div>
                </div>
            </div>

            {{-- HR Tab --}}
            <div id="content-hr" class="tab-content hidden space-y-8">

                {{-- Section 1: General HR --}}
                <div>
                    <h3 class="text-lg font-bold text-slate-800 border-b border-slate-200 pb-2 mb-4 flex items-center gap-2">
                        <i class="bi bi-gear text-slate-400"></i> General HR Settings
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-700">Default Working Hours/Day</label>
                            <input type="number" step="0.5" name="default_working_hours" value="{{ $settings['default_working_hours'] ?? '8' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                            <p class="text-xs text-slate-400">Used for payroll day-rate and attendance calculations.</p>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-700">Overtime Rate Multiplier</label>
                            <input type="number" step="0.1" min="1" name="overtime_rate_multiplier" value="{{ $settings['overtime_rate_multiplier'] ?? '1.5' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                            <p class="text-xs text-slate-400">e.g. 1.5x = time-and-a-half, 2.0x = double-time.</p>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-700">Default Staff Role</label>
                            <select name="default_staff_role" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                                <option value="">No default</option>
                                @foreach(\App\Models\Role::all() as $role)
                                    <option value="{{ $role->roleid }}" {{ ($settings['default_staff_role'] ?? '') == $role->roleid ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-slate-400">Auto-assigned to new staff members.</p>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-700">Tables Pagination Limit</label>
                            <input type="number" min="5" max="200" name="tables_pagination_limit" value="{{ $settings['tables_pagination_limit'] ?? '25' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                            <p class="text-xs text-slate-400">Number of records per page across all HRM tables.</p>
                        </div>
                    </div>
                </div>

                {{-- Section 2: Attendance --}}
                <div>
                    <h3 class="text-lg font-bold text-slate-800 border-b border-slate-200 pb-2 mb-4 flex items-center gap-2">
                        <i class="bi bi-clock-history text-indigo-400"></i> Attendance Settings
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-700">Default Clock-In Status</label>
                            <input type="text" name="attendance_default_status" value="{{ $settings['attendance_default_status'] ?? 'Present' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                            <p class="text-xs text-slate-400">Status label assigned when staff clocks in.</p>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-700">Shift Start Time</label>
                            <input type="time" name="shift_start_time" value="{{ $settings['shift_start_time'] ?? '09:00' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                            <p class="text-xs text-slate-400">Default start time for late arrival detection.</p>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-700">Shift End Time</label>
                            <input type="time" name="shift_end_time" value="{{ $settings['shift_end_time'] ?? '18:00' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                            <p class="text-xs text-slate-400">Default end time for early departure detection.</p>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-700">Late Clock-In Grace Period (minutes)</label>
                            <input type="number" min="0" name="late_grace_period" value="{{ $settings['late_grace_period'] ?? '15' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                            <p class="text-xs text-slate-400">Clock-ins within this window after shift start are not marked late.</p>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-700">Auto Absent Threshold (hours)</label>
                            <input type="number" min="0" step="0.5" name="attendance_absent_threshold" value="{{ $settings['attendance_absent_threshold'] ?? '24' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                            <p class="text-xs text-slate-400">If no clock-out within this many hours from clock-in, mark as absent. 0 = disabled.</p>
                        </div>
                        <div class="flex items-center gap-3 py-4">
                            <input type="checkbox" name="attendance_require_ip" value="1" {{ ($settings['attendance_require_ip'] ?? '') == '1' ? 'checked' : '' }} id="att_require_ip" class="w-5 h-5 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                            <label for="att_require_ip" class="text-sm font-medium text-slate-700">Record IP Address on Clock-In/Out</label>
                        </div>
                    </div>
                </div>

                {{-- Section 3: Leave --}}
                <div>
                    <h3 class="text-lg font-bold text-slate-800 border-b border-slate-200 pb-2 mb-4 flex items-center gap-2">
                        <i class="bi bi-calendar-event text-amber-400"></i> Leave Settings
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-700">Default Leave Status</label>
                            <input type="text" name="leave_default_status" value="{{ $settings['leave_default_status'] ?? 'Pending' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                            <p class="text-xs text-slate-400">Default status when a leave request is submitted.</p>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-700">Allow Backdated Leaves (days)</label>
                            <input type="number" min="0" name="leave_allow_backdate_days" value="{{ $settings['leave_allow_backdate_days'] ?? '0' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                            <p class="text-xs text-slate-400">How many days in the past a leave can be applied. 0 = only future leaves.</p>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-700">Leave Approval Statuses</label>
                            <input type="text" name="leave_approval_statuses" value="{{ $settings['leave_approval_statuses'] ?? 'Pending,Approved,Rejected' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                            <p class="text-xs text-slate-400">Comma-separated list of valid statuses. Order matters: first = default.</p>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-700">Default Leave Quota for New Types</label>
                            <input type="number" min="0" name="leave_quota_default" value="{{ $settings['leave_quota_default'] ?? '10' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                            <p class="text-xs text-slate-400">Default number of days per year for new leave types.</p>
                        </div>
                    </div>
                </div>

                {{-- Section 4: Payroll --}}
                <div>
                    <h3 class="text-lg font-bold text-slate-800 border-b border-slate-200 pb-2 mb-4 flex items-center gap-2">
                        <i class="bi bi-cash-stack text-emerald-400"></i> Payroll Settings
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-700">Payroll Cutoff Day</label>
                            <select name="payroll_cutoff_day" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                                @foreach(range(1, 28) as $day)
                                    @php $suffix = match($day) { 1 => 'st', 2 => 'nd', 3 => 'rd', default => 'th' }; @endphp
                                    <option value="{{ $day }}" {{ ($settings['payroll_cutoff_day'] ?? '1') == $day ? 'selected' : '' }}>{{ $day }}{{ $suffix }} of month</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-slate-400">Payslips are generated up to this day each month.</p>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-700">Default Payslip Status</label>
                            <input type="text" name="payroll_default_status" value="{{ $settings['payroll_default_status'] ?? 'Draft' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                            <p class="text-xs text-slate-400">Status label when a payslip is first generated.</p>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-700">Paid Status Label</label>
                            <input type="text" name="payroll_paid_status" value="{{ $settings['payroll_paid_status'] ?? 'Paid' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                            <p class="text-xs text-slate-400">Status label when a payslip is marked as paid.</p>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-700">Currency Symbol (Payroll)</label>
                            <input type="text" name="payroll_currency_symbol" value="{{ $settings['payroll_currency_symbol'] ?? '$' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" maxlength="10">
                            <p class="text-xs text-slate-400">Currency symbol displayed on payslips (e.g., $, €, £).</p>
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <label class="text-sm font-medium text-slate-700">Adjustment Allowed Types</label>
                            <input type="text" name="payroll_adjustment_types" value="{{ $settings['payroll_adjustment_types'] ?? 'Addition,Deduction' }}" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                            <p class="text-xs text-slate-400">Comma-separated list of allowed payslip adjustment types.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-200 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 flex items-center gap-2">
                    <i class="bi bi-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .tab-btn.active {
        color: #2563eb; /* blue-600 */
        border-bottom-color: #2563eb;
    }
</style>

<script>
    function switchTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active', 'text-blue-600', 'border-blue-500');
            btn.classList.add('text-slate-500', 'border-transparent');
        });
        document.getElementById('content-' + tabId).classList.remove('hidden');
        const activeBtn = document.getElementById('tab-' + tabId);
        activeBtn.classList.add('active', 'text-blue-600', 'border-blue-500');
        activeBtn.classList.remove('text-slate-500', 'border-transparent');
    }
</script>
@endsection
