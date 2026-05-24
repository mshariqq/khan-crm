<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\CustomersGroupController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Client\DashboardController as ClientDashboard;
use App\Http\Controllers\Guest\HomeController;
use App\Http\Controllers\Auth\StaffAuthController;
use App\Http\Controllers\Auth\ClientAuthController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\TaskTimerController;
use App\Http\Controllers\Admin\FileController;
use App\Http\Controllers\Admin\TaskChecklistController;
use App\Http\Controllers\Admin\SearchController;

// --- PUBLIC EMAIL TRACKING ROUTES ---
Route::get('email/track/open/{message_id}', [\App\Http\Controllers\Admin\EmailTrackingController::class, 'trackOpen'])
    ->name('email.tracking.open')
    ->middleware('throttle:60,1');
Route::get('email/track/click/{message_id}', [\App\Http\Controllers\Admin\EmailTrackingController::class, 'trackClick'])
    ->name('email.tracking.click')
    ->middleware('throttle:60,1');

// --- GUEST AREA ---
// Home page and Knowledge Base - accessible to ALL (no middleware)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/knowledge-base', [HomeController::class, 'kb'])->name('kb');
Route::get('/kb/article/{slug}', [HomeController::class, 'kbArticle'])->name('kb.article');
Route::post('/kb/article/{id}/feedback', [HomeController::class, 'kbFeedback'])->name('kb.feedback');

// Public Project Sharing View
Route::get('/shared/project/{token}', [\App\Http\Controllers\Guest\SharedProjectController::class, 'view'])->name('public.projects.view');

Route::middleware('guest')->group(function () {
    // Login routes - only for guest users
    Route::get('/login/staff', [StaffAuthController::class, 'showLogin'])->name('login.staff');
    Route::post('/login/staff', [StaffAuthController::class, 'login'])->middleware('throttle:5,15');
    
    Route::get('/login/client', [ClientAuthController::class, 'showLogin'])->name('login.client');
    Route::post('/login/client', [ClientAuthController::class, 'login'])->middleware('throttle:5,15');

    // Two-Factor Authentication routes
    Route::get('/login/staff/2fa', [\App\Http\Controllers\Auth\TwoFactorChallengeController::class, 'showChallenge'])->name('staff.2fa.challenge');
    Route::post('/login/staff/2fa', [\App\Http\Controllers\Auth\TwoFactorChallengeController::class, 'verify'])->name('staff.2fa.verify');
    Route::post('/login/staff/2fa/resend', [\App\Http\Controllers\Auth\TwoFactorChallengeController::class, 'resend'])->name('staff.2fa.resend');

    // Email Verification routes
    Route::get('/email/verify/staff/{id}/{key}', [\App\Http\Controllers\Auth\EmailVerificationController::class, 'verifyStaff'])->name('staff.email.verify');
    Route::get('/email/verify/client/{id}/{key}', [\App\Http\Controllers\Auth\EmailVerificationController::class, 'verifyContact'])->name('client.email.verify');
});

// --- STAFF AREA ---
Route::middleware(['auth:staff'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/search', [SearchController::class, 'index'])->name('admin.search');
    Route::get('notifications/fetch', [\App\Http\Controllers\Admin\NotificationController::class, 'fetch'])->name('admin.notifications.fetch');
    Route::get('notifications/fetch-grouped', [\App\Http\Controllers\Admin\NotificationController::class, 'fetchGrouped'])->name('admin.notifications.fetch_grouped');
    Route::get('notifications/unread-count', [\App\Http\Controllers\Admin\NotificationController::class, 'unreadCount'])->name('admin.notifications.unread_count');
    Route::post('notifications/{id}/read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('admin.notifications.read');
    Route::post('notifications/{id}/unread', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsUnread'])->name('admin.notifications.unread');
    Route::post('notifications/read-all', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('admin.notifications.read_all');
    Route::get('notifications/preferences', [\App\Http\Controllers\Admin\NotificationPreferenceController::class, 'index'])->name('admin.notifications.preferences.index');
    Route::post('notifications/preferences', [\App\Http\Controllers\Admin\NotificationPreferenceController::class, 'update'])->name('admin.notifications.preferences.update');
    Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('admin.settings.update');

    // Email Templates Routes
    Route::get('settings/email-templates', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'index'])->name('admin.settings.email_templates.index');
    Route::get('settings/email-templates/{id}/edit', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'edit'])->name('admin.settings.email_templates.edit');
    Route::post('settings/email-templates/{id}', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'update'])->name('admin.settings.email_templates.update');
    Route::get('settings/email-templates/preview/{slug}', [\App\Http\Controllers\Admin\EmailTrackingController::class, 'preview'])->name('admin.settings.email_templates.preview');
    Route::post('settings/email-templates/test-send', [\App\Http\Controllers\Admin\EmailTrackingController::class, 'testSend'])->name('admin.settings.email_templates.test_send');

    // Ticket Email Integration Routes
    Route::get('settings/ticket-email-integration', [\App\Http\Controllers\Admin\TicketEmailIntegrationController::class, 'index'])->name('admin.settings.ticket_email_integration.index');
    Route::get('settings/ticket-email-integration/create', [\App\Http\Controllers\Admin\TicketEmailIntegrationController::class, 'create'])->name('admin.settings.ticket_email_integration.create');
    Route::post('settings/ticket-email-integration', [\App\Http\Controllers\Admin\TicketEmailIntegrationController::class, 'store'])->name('admin.settings.ticket_email_integration.store');
    Route::get('settings/ticket-email-integration/{id}/edit', [\App\Http\Controllers\Admin\TicketEmailIntegrationController::class, 'edit'])->name('admin.settings.ticket_email_integration.edit');
    Route::put('settings/ticket-email-integration/{id}', [\App\Http\Controllers\Admin\TicketEmailIntegrationController::class, 'update'])->name('admin.settings.ticket_email_integration.update');
    Route::delete('settings/ticket-email-integration/{id}', [\App\Http\Controllers\Admin\TicketEmailIntegrationController::class, 'destroy'])->name('admin.settings.ticket_email_integration.destroy');

    Route::resource('roles', RoleController::class)->names([
        'index' => 'admin.roles.index',
        'create' => 'admin.roles.create',
        'store' => 'admin.roles.store',
        'edit' => 'admin.roles.edit',
        'update' => 'admin.roles.update',
        'destroy' => 'admin.roles.destroy',
    ]);
    Route::resource('permissions', PermissionController::class)->names([
        'index' => 'admin.permissions.index',
        'create' => 'admin.permissions.create',
        'store' => 'admin.permissions.store',
        'edit' => 'admin.permissions.edit',
        'update' => 'admin.permissions.update',
        'destroy' => 'admin.permissions.destroy',
    ]);

    // System Lookups
    Route::resource('items', \App\Http\Controllers\Admin\ItemController::class)->names([
        'index' => 'admin.items.index',
        'create' => 'admin.items.create',
        'store' => 'admin.items.store',
        'edit' => 'admin.items.edit',
        'update' => 'admin.items.update',
        'show' => 'admin.items.show',
        'destroy' => 'admin.items.destroy',
    ]);

    // Item Pane Routes
    Route::get('pane/items/create', [\App\Http\Controllers\Admin\ItemPaneController::class, 'create'])->name('admin.items.pane.create');
    Route::get('pane/items/{id}/edit', [\App\Http\Controllers\Admin\ItemPaneController::class, 'edit'])->name('admin.items.pane.edit');
    Route::get('pane/items/{id}', [\App\Http\Controllers\Admin\ItemPaneController::class, 'show'])->name('admin.items.pane.show');
    Route::resource('proposals', \App\Http\Controllers\Admin\ProposalController::class)->names([
        'index' => 'admin.proposals.index',
        'create' => 'admin.proposals.create',
        'store' => 'admin.proposals.store',
        'edit' => 'admin.proposals.edit',
        'update' => 'admin.proposals.update',
        'show' => 'admin.proposals.show',
        'destroy' => 'admin.proposals.destroy',
    ]);

    // Proposal sub-routes
    Route::post('proposals/{proposal}/comments', [\App\Http\Controllers\Admin\ProposalController::class, 'storeComment'])->name('admin.proposals.store_comment');
    Route::delete('proposal_comments/{comment}', [\App\Http\Controllers\Admin\ProposalController::class, 'destroyComment'])->name('admin.proposal_comments.destroy');
    Route::post('proposals/{proposal}/notes', [\App\Http\Controllers\Admin\ProposalController::class, 'storeNote'])->name('admin.proposals.store_note');
    Route::delete('notes/{note}', [\App\Http\Controllers\Admin\NoteController::class, 'destroy'])->name('admin.notes.destroy');
    Route::post('proposals/{proposal}/reminders', [\App\Http\Controllers\Admin\ProposalController::class, 'storeReminder'])->name('admin.proposals.store_reminder');
    Route::delete('reminders/{reminder}', [\App\Http\Controllers\Admin\ReminderController::class, 'destroy'])->name('admin.reminders.destroy');
    Route::post('proposals/{proposal}/mark-accepted', [\App\Http\Controllers\Admin\ProposalController::class, 'markAccepted'])->name('admin.proposals.mark_accepted');
    Route::post('proposals/{proposal}/mark-declined', [\App\Http\Controllers\Admin\ProposalController::class, 'markDeclined'])->name('admin.proposals.mark_declined');
    Route::post('proposals/{proposal}/send', [\App\Http\Controllers\Admin\ProposalController::class, 'sendProposal'])->name('admin.proposals.send');
    Route::get('proposals/{proposal}/print', [\App\Http\Controllers\Admin\ProposalController::class, 'print'])->name('admin.proposals.print');

    // Proposal Pane Routes
    Route::get('pane/proposals/create', [\App\Http\Controllers\Admin\ProposalPaneController::class, 'create'])->name('admin.proposals.pane.create');
    Route::get('pane/proposals/{id}/edit', [\App\Http\Controllers\Admin\ProposalPaneController::class, 'edit'])->name('admin.proposals.pane.edit');
    Route::get('pane/proposals/{id}', [\App\Http\Controllers\Admin\ProposalPaneController::class, 'show'])->name('admin.proposals.pane.show');

    Route::resource('subscriptions', \App\Http\Controllers\Admin\SubscriptionController::class)->names([
        'index' => 'admin.subscriptions.index',
        'create' => 'admin.subscriptions.create',
        'store' => 'admin.subscriptions.store',
        'edit' => 'admin.subscriptions.edit',
        'update' => 'admin.subscriptions.update',
        'show' => 'admin.subscriptions.show',
        'destroy' => 'admin.subscriptions.destroy',
    ]);
    Route::resource('email-templates', \App\Http\Controllers\Admin\EmailTemplateController::class)->names([
        'index' => 'admin.emailtemplates.index',
        'create' => 'admin.emailtemplates.create',
        'store' => 'admin.emailtemplates.store',
        'edit' => 'admin.emailtemplates.edit',
        'update' => 'admin.emailtemplates.update',
        'show' => 'admin.emailtemplates.show',
        'destroy' => 'admin.emailtemplates.destroy',
    ]);

    Route::resource('countries', \App\Http\Controllers\Admin\CountryController::class)->names([

        'index' => 'admin.countries.index',
        'create' => 'admin.countries.create',
        'store' => 'admin.countries.store',
        'edit' => 'admin.countries.edit',
        'update' => 'admin.countries.update',
        'destroy' => 'admin.countries.destroy',
    ]);
    Route::resource('currencies', \App\Http\Controllers\Admin\CurrencyController::class)->names([
        'index' => 'admin.currencies.index',
        'create' => 'admin.currencies.create',
        'store' => 'admin.currencies.store',
        'edit' => 'admin.currencies.edit',
        'update' => 'admin.currencies.update',
        'destroy' => 'admin.currencies.destroy',
    ]);
    Route::resource('expense-categories', \App\Http\Controllers\Admin\ExpenseCategoryController::class)->names([
        'index' => 'admin.expense-categories.index',
        'create' => 'admin.expense-categories.create',
        'store' => 'admin.expense-categories.store',
        'edit' => 'admin.expense-categories.edit',
        'update' => 'admin.expense-categories.update',
        'destroy' => 'admin.expense-categories.destroy',
    ]);
    Route::resource('leave-types', \App\Http\Controllers\Admin\LeaveTypeController::class)->names([
        'index' => 'admin.leave-types.index',
        'create' => 'admin.leave-types.create',
        'store' => 'admin.leave-types.store',
        'edit' => 'admin.leave-types.edit',
        'update' => 'admin.leave-types.update',
        'destroy' => 'admin.leave-types.destroy',
    ]);
    Route::resource('services', \App\Http\Controllers\Admin\ServiceController::class)->names([
        'index' => 'admin.services.index',
        'create' => 'admin.services.create',
        'store' => 'admin.services.store',
        'edit' => 'admin.services.edit',
        'update' => 'admin.services.update',
        'destroy' => 'admin.services.destroy',
    ]);
    Route::resource('payment-modes', \App\Http\Controllers\Admin\PaymentModeController::class)->names([
        'index' => 'admin.paymentmodes.index',
        'create' => 'admin.paymentmodes.create',
        'store' => 'admin.paymentmodes.store',
        'edit' => 'admin.paymentmodes.edit',
        'update' => 'admin.paymentmodes.update',
        'destroy' => 'admin.paymentmodes.destroy',
    ]);
    Route::resource('taxes', \App\Http\Controllers\Admin\TaxController::class)->names([
        'index' => 'admin.taxes.index',
        'create' => 'admin.taxes.create',
        'store' => 'admin.taxes.store',
        'edit' => 'admin.taxes.edit',
        'update' => 'admin.taxes.update',
        'destroy' => 'admin.taxes.destroy',
    ]);
    Route::resource('item-groups', \App\Http\Controllers\Admin\ItemsGroupController::class)->names([
        'index' => 'admin.itemsgroups.index',
        'create' => 'admin.itemsgroups.create',
        'store' => 'admin.itemsgroups.store',
        'edit' => 'admin.itemsgroups.edit',
        'update' => 'admin.itemsgroups.update',
        'destroy' => 'admin.itemsgroups.destroy',
    ]);

    // Items Group Pane Routes
    Route::get('pane/items-groups/create', [\App\Http\Controllers\Admin\ItemsGroupPaneController::class, 'create'])->name('admin.itemsgroups.pane.create');
    Route::get('pane/items-groups/{id}/edit', [\App\Http\Controllers\Admin\ItemsGroupPaneController::class, 'edit'])->name('admin.itemsgroups.pane.edit');
    Route::get('pane/items-groups/{id}', [\App\Http\Controllers\Admin\ItemsGroupPaneController::class, 'show'])->name('admin.itemsgroups.pane.show');

    Route::get('/clients/search', [ClientController::class, 'searchClients'])->name('admin.clients.search');
    Route::get('/clients/export', [ClientController::class, 'exportCsv'])->name('admin.clients.export');
    Route::resource('clients/groups', CustomersGroupController::class)->names([
        'index' => 'admin.customer-groups.index',
        'create' => 'admin.customer-groups.create',
        'store' => 'admin.customer-groups.store',
        'edit' => 'admin.customer-groups.edit',
        'update' => 'admin.customer-groups.update',
        'destroy' => 'admin.customer-groups.destroy',
    ]);
    
    Route::resource('clients', ClientController::class)->names([
        'index' => 'admin.clients.index',
        'show' => 'admin.clients.show',
        'create' => 'admin.clients.create',
        'store' => 'admin.clients.store',
        'edit' => 'admin.clients.edit',
        'update' => 'admin.clients.update',
        'destroy' => 'admin.clients.destroy',
    ]);

    Route::prefix('clients/{client}')->group(function () {
        Route::get('/projects', [ClientController::class, 'projects'])->name('admin.clients.projects');
        Route::get('/tickets', [ClientController::class, 'tickets'])->name('admin.clients.tickets');
        Route::get('/invoices', [ClientController::class, 'invoices'])->name('admin.clients.invoices');
        Route::get('/estimates', [ClientController::class, 'estimates'])->name('admin.clients.estimates');
        Route::get('/expenses', [ClientController::class, 'expenses'])->name('admin.clients.expenses');
        Route::get('/tasks', [ClientController::class, 'tasks'])->name('admin.clients.tasks');
        Route::get('/reminders', [ClientController::class, 'reminders'])->name('admin.clients.reminders');
        Route::get('/notes', [ClientController::class, 'notes'])->name('admin.clients.notes');
        Route::get('/contacts', [ClientController::class, 'contacts'])->name('admin.clients.contacts');
    });

    Route::resource('contacts', ContactController::class)->names([
        'index' => 'admin.contacts.index',
        'create' => 'admin.contacts.create',
        'store' => 'admin.contacts.store',
        'edit' => 'admin.contacts.edit',
        'update' => 'admin.contacts.update',
        'destroy' => 'admin.contacts.destroy',
    ]);

    Route::get('pane/contacts/create', [\App\Http\Controllers\Admin\ContactPaneController::class, 'create'])->name('admin.contacts.pane.create');
    Route::get('pane/contacts/{id}/edit', [\App\Http\Controllers\Admin\ContactPaneController::class, 'edit'])->name('admin.contacts.pane.edit');
    Route::get('pane/contacts/{id}', [\App\Http\Controllers\Admin\ContactPaneController::class, 'show'])->name('admin.contacts.pane.show');

    Route::resource('staff', StaffController::class)->names([
        'index' => 'admin.staff.index',
        'create' => 'admin.staff.create',
        'store' => 'admin.staff.store',
        'edit' => 'admin.staff.edit',
        'update' => 'admin.staff.update',
        'destroy' => 'admin.staff.destroy',
    ]);
    Route::post('staff/{id}/send-verification', [StaffController::class, 'sendVerificationEmail'])->name('admin.staff.send_verification');

    Route::resource('departments', DepartmentController::class)->names([
        'index' => 'admin.departments.index',
        'create' => 'admin.departments.create',
        'store' => 'admin.departments.store',
        'edit' => 'admin.departments.edit',
        'update' => 'admin.departments.update',
        'destroy' => 'admin.departments.destroy',
    ]);

    Route::resource('leads', \App\Http\Controllers\Admin\LeadController::class)->names([
        'index' => 'admin.leads.index',
        'create' => 'admin.leads.create',
        'store' => 'admin.leads.store',
        'edit' => 'admin.leads.edit',
        'update' => 'admin.leads.update',
        'show' => 'admin.leads.show',
        'destroy' => 'admin.leads.destroy',
    ]);

    // Lead sub-routes
    Route::post('leads/{lead}/notes', [\App\Http\Controllers\Admin\LeadController::class, 'storeNote'])->name('admin.leads.notes.store');
    Route::post('leads/{lead}/reminders', [\App\Http\Controllers\Admin\LeadController::class, 'storeReminder'])->name('admin.leads.reminders.store');
    Route::post('leads/{lead}/convert', [\App\Http\Controllers\Admin\LeadController::class, 'convertToClient'])->name('admin.leads.convert');
    Route::post('leads/{lead}/mark-lost', [\App\Http\Controllers\Admin\LeadController::class, 'markAsLost'])->name('admin.leads.mark_lost');
    Route::post('leads/{lead}/mark-junk', [\App\Http\Controllers\Admin\LeadController::class, 'markAsJunk'])->name('admin.leads.mark_junk');
    Route::post('leads/{lead}/gdpr', [\App\Http\Controllers\Admin\LeadController::class, 'storeGdpr'])->name('admin.leads.gdpr.store');

    // Lead Pane Routes
    Route::get('pane/leads/{lead}/proposals', [\App\Http\Controllers\Admin\LeadPaneController::class, 'showProposals'])->name('admin.leads.pane.proposals');
    Route::get('pane/leads/{lead}/proposals/create', [\App\Http\Controllers\Admin\LeadPaneController::class, 'createProposal'])->name('admin.leads.pane.proposals.create');

    Route::get('projects/export', [\App\Http\Controllers\Admin\ProjectController::class, 'export'])->name('admin.projects.export');

Route::resource('projects', \App\Http\Controllers\Admin\ProjectController::class)->names([
    'index' => 'admin.projects.index',
    'create' => 'admin.projects.create',
    'store' => 'admin.projects.store',
    'edit' => 'admin.projects.edit',
    'update' => 'admin.projects.update',
    'show' => 'admin.projects.show',
    'destroy' => 'admin.projects.destroy',
]);

// Project dashboard route
Route::get('projects/{project}/dashboard', [\App\Http\Controllers\Admin\ProjectController::class, 'dashboard'])->name('admin.projects.dashboard');

// Project sub-routes for new tabs
Route::get('projects/{project}/notes/create', [\App\Http\Controllers\Admin\ProjectController::class, 'notesCreate'])->name('admin.projects.notes.create');
Route::post('projects/{project}/notes', [\App\Http\Controllers\Admin\ProjectController::class, 'notesStore'])->name('admin.projects.notes.store');
Route::put('projects/{project}/notes/{note}', [\App\Http\Controllers\Admin\ProjectController::class, 'notesUpdate'])->name('admin.projects.notes.update');
Route::get('projects/{project}/discussions', [\App\Http\Controllers\Admin\ProjectController::class, 'discussions'])->name('admin.projects.discussions');
Route::get('projects/{project}/discussions/create', [\App\Http\Controllers\Admin\ProjectController::class, 'discussionsCreate'])->name('admin.projects.discussions.create');
Route::post('projects/{project}/discussions', [\App\Http\Controllers\Admin\ProjectController::class, 'storeDiscussion'])->name('admin.projects.discussions.store');
Route::post('projects/{project}/discussions/{discussion}/comment', [\App\Http\Controllers\Admin\ProjectController::class, 'addDiscussionComment'])->name('admin.projects.discussions.comment');
Route::get('projects/{project}/timesheets', [\App\Http\Controllers\Admin\ProjectController::class, 'timesheets'])->name('admin.projects.timesheets');
Route::get('projects/{project}/gantt', [\App\Http\Controllers\Admin\ProjectController::class, 'gantt'])->name('admin.projects.gantt');
Route::get('projects/{project}/tab/{tab}', [\App\Http\Controllers\Admin\ProjectController::class, 'tabContent'])->name('admin.projects.tab');
Route::post('projects/{project}/send-notification', [\App\Http\Controllers\Admin\ProjectController::class, 'sendNotification'])->name('admin.projects.send_notification');
Route::post('projects/{project}/send', [\App\Http\Controllers\Admin\ProjectController::class, 'sendProject'])->name('admin.projects.send');

Route::post('projects/{project}/status', [\App\Http\Controllers\Admin\ProjectController::class, 'updateStatus'])->name('admin.projects.status');
Route::post('projects/{project}/progress', [\App\Http\Controllers\Admin\ProjectController::class, 'updateProgress'])->name('admin.projects.progress');
Route::post('projects/{project}/team', [\App\Http\Controllers\Admin\ProjectController::class, 'updateTeamMembers'])->name('admin.projects.team');
Route::post('projects/{project}/duplicate', [\App\Http\Controllers\Admin\ProjectController::class, 'duplicate'])->name('admin.projects.duplicate');
Route::post('projects/{project}/archive', [\App\Http\Controllers\Admin\ProjectController::class, 'archive'])->name('admin.projects.archive');
Route::post('projects/{project}/restore', [\App\Http\Controllers\Admin\ProjectController::class, 'restoreProject'])->name('admin.projects.restore');
Route::post('projects/{project}/file', [\App\Http\Controllers\Admin\ProjectController::class, 'uploadFile'])->name('admin.projects.file.upload');
Route::post('projects/{project}/convert-billable', [\App\Http\Controllers\Admin\ProjectController::class, 'convertBillableToInvoice'])->name('admin.projects.convert_billable');
Route::get('projects/{project}/gdpr/export', [\App\Http\Controllers\Admin\ProjectController::class, 'exportGdpr'])->name('admin.projects.gdpr.export');
Route::post('projects/{project}/gdpr/delete', [\App\Http\Controllers\Admin\ProjectController::class, 'deleteGdpr'])->name('admin.projects.gdpr.delete');
Route::delete('projects/{project}/notes/{note}', [\App\Http\Controllers\Admin\ProjectController::class, 'notesDelete'])->name('admin.projects.notes.delete');
Route::put('projects/{project}/discussions/{discussion}', [\App\Http\Controllers\Admin\ProjectController::class, 'updateDiscussion'])->name('admin.projects.discussions.update');
Route::delete('projects/{project}/discussions/{discussion}', [\App\Http\Controllers\Admin\ProjectController::class, 'deleteDiscussion'])->name('admin.projects.discussions.delete');
Route::post('projects/{project}/discussions/{discussion}/pin', [\App\Http\Controllers\Admin\ProjectController::class, 'togglePinDiscussion'])->name('admin.projects.discussions.pin');
Route::delete('projects/{project}/discussions/{discussion}/comment/{comment}', [\App\Http\Controllers\Admin\ProjectController::class, 'deleteDiscussionComment'])->name('admin.projects.discussions.comment.delete');
Route::put('projects/{project}/discussions/{discussion}/comment/{comment}', [\App\Http\Controllers\Admin\ProjectController::class, 'updateDiscussionComment'])->name('admin.projects.discussions.comment.update');

// Discussion Search
Route::get('projects/{project}/discussions/search', [\App\Http\Controllers\Admin\ProjectController::class, 'searchDiscussions'])->name('admin.projects.discussions.search');

// Discussion Categories
Route::get('discussion-categories', [\App\Http\Controllers\Admin\ProjectController::class, 'discussionCategories'])->name('admin.projects.discussion_categories.index');
Route::post('discussion-categories', [\App\Http\Controllers\Admin\ProjectController::class, 'storeDiscussionCategory'])->name('admin.projects.discussion_categories.store');
Route::delete('discussion-categories/{id}', [\App\Http\Controllers\Admin\ProjectController::class, 'deleteDiscussionCategory'])->name('admin.projects.discussion_categories.destroy');

// Discussion Email Integration (stub)
Route::get('projects/{project}/discussions/email-settings', [\App\Http\Controllers\Admin\ProjectController::class, 'discussionEmailSettings'])->name('admin.projects.discussions.email_settings');

Route::post('milestones/{milestone}/complete', [\App\Http\Controllers\Admin\MilestoneController::class, 'complete'])->name('admin.milestones.complete');

// ===== Project Advanced Features =====
Route::get('projects/portfolio', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'portfolio'])->name('admin.projects.portfolio');
Route::get('projects/profitability', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'profitabilityReport'])->name('admin.projects.profitability');
Route::get('projects/calendar', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'calendar'])->name('admin.projects.calendar');

// Batch Operations
Route::post('projects/batch/status', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'batchStatus'])->name('admin.projects.batch.status');
Route::post('projects/batch/delete', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'batchDelete'])->name('admin.projects.batch.delete');
Route::post('projects/batch/assign-team', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'batchAssignTeam'])->name('admin.projects.batch.assign_team');

// Project Templates
Route::get('project-templates', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'templatesIndex'])->name('admin.projects.templates.index');
Route::get('project-templates/create', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'templatesCreate'])->name('admin.projects.templates.create');
Route::post('project-templates', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'templatesStore'])->name('admin.projects.templates.store');
Route::post('project-templates/{template}/apply', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'templatesApply'])->name('admin.projects.templates.apply');

// Project Categories
Route::get('project-categories', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'categoriesIndex'])->name('admin.projects.categories.index');
Route::post('project-categories', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'categoriesStore'])->name('admin.projects.categories.store');
Route::put('project-categories/{id}', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'categoriesUpdate'])->name('admin.projects.categories.update');
Route::delete('project-categories/{id}', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'categoriesDestroy'])->name('admin.projects.categories.destroy');

// Per-Project Advanced Features
Route::prefix('projects/{project}')->group(function () {
    // Phases
    Route::get('phases', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'phasesIndex'])->name('admin.projects.phases.index');
    Route::post('phases', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'phasesStore'])->name('admin.projects.phases.store');
    Route::put('phases/{phase}', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'phasesUpdate'])->name('admin.projects.phases.update');
    Route::delete('phases/{phase}', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'phasesDestroy'])->name('admin.projects.phases.destroy');

    // Risks
    Route::get('risks', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'risksIndex'])->name('admin.projects.risks.index');
    Route::post('risks', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'risksStore'])->name('admin.projects.risks.store');
    Route::put('risks/{risk}', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'risksUpdate'])->name('admin.projects.risks.update');
    Route::delete('risks/{risk}', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'risksDestroy'])->name('admin.projects.risks.destroy');

    // Change Requests
    Route::get('change-requests', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'changeRequestsIndex'])->name('admin.projects.change_requests.index');
    Route::post('change-requests', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'changeRequestsStore'])->name('admin.projects.change_requests.store');
    Route::post('change-requests/{cr}/approve', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'changeRequestsApprove'])->name('admin.projects.change_requests.approve');
    Route::post('change-requests/{cr}/reject', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'changeRequestsReject'])->name('admin.projects.change_requests.reject');

    // Resource Allocations
    Route::get('resources', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'resourcesIndex'])->name('admin.projects.resources.index');
    Route::post('resources', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'resourcesStore'])->name('admin.projects.resources.store');
    Route::delete('resources/{resource}', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'resourcesDestroy'])->name('admin.projects.resources.destroy');

    // Critical Path
    Route::get('critical-path', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'criticalPath'])->name('admin.projects.critical_path');

    // Baselines
    Route::get('baselines', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'baselinesIndex'])->name('admin.projects.baselines.index');
    Route::post('baselines', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'baselinesCapture'])->name('admin.projects.baselines.capture');

    // Closure Checklist
    Route::get('closure-checklist', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'closureChecklistIndex'])->name('admin.projects.closure_checklist.index');
    Route::post('closure-checklist', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'closureChecklistAdd'])->name('admin.projects.closure_checklist.add');
    Route::post('closure-checklist/{item}/toggle', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'closureChecklistToggle'])->name('admin.projects.closure_checklist.toggle');

    // Lessons Learned
    Route::get('lessons-learned', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'lessonsIndex'])->name('admin.projects.lessons.index');
    Route::post('lessons-learned', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'lessonsStore'])->name('admin.projects.lessons.store');

    // Locations
    Route::get('locations', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'locationsIndex'])->name('admin.projects.locations.index');
    Route::post('locations', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'locationsStore'])->name('admin.projects.locations.store');
    Route::delete('locations/{location}', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'locationsDestroy'])->name('admin.projects.locations.destroy');

    // Sharing
    Route::get('shares', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'sharesIndex'])->name('admin.projects.shares.index');
    Route::post('shares', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'sharesStore'])->name('admin.projects.shares.store');
    Route::delete('shares/{share}', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'sharesDestroy'])->name('admin.projects.shares.destroy');

    // Watchers
    Route::post('toggle-watcher', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'toggleWatcher'])->name('admin.projects.toggle_watcher');

    // Recurring
    Route::get('recurring', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'recurringConfig'])->name('admin.projects.recurring.index');
    Route::post('recurring', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'recurringStore'])->name('admin.projects.recurring.store');
    Route::post('recurring/deactivate', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'recurringDeactivate'])->name('admin.projects.recurring.deactivate');

    // Feedback
    Route::get('feedback', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'feedbackIndex'])->name('admin.projects.feedback.index');
    Route::post('feedback', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'feedbackStore'])->name('admin.projects.feedback.store');

    // Search within tabs
    Route::get('tab/{tab}/search', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'searchTab'])->name('admin.projects.tab.search');
});

// Task Dependencies
Route::post('task-dependencies', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'taskDependenciesStore'])->name('admin.task_dependencies.store');
Route::delete('task-dependencies/{id}', [\App\Http\Controllers\Admin\ProjectAdvancedController::class, 'taskDependenciesDestroy'])->name('admin.task_dependencies.destroy');

Route::get('tasks/kanban', [\App\Http\Controllers\Admin\TaskController::class, 'kanban'])->name('admin.tasks.kanban');

    Route::resource('tasks', \App\Http\Controllers\Admin\TaskController::class)->names([
        'index' => 'admin.tasks.index',
        'create' => 'admin.tasks.create',
        'store' => 'admin.tasks.store',
        'edit' => 'admin.tasks.edit',
        'update' => 'admin.tasks.update',
        'show' => 'admin.tasks.show',
        'destroy' => 'admin.tasks.destroy',
    ]);

    // Attendance Routes
    Route::get('attendance', [\App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('admin.attendance.index');
    Route::post('attendance/clock-in', [\App\Http\Controllers\Admin\AttendanceController::class, 'clockIn'])->name('admin.attendance.clock-in');
    Route::post('attendance/clock-out', [\App\Http\Controllers\Admin\AttendanceController::class, 'clockOut'])->name('admin.attendance.clock-out');
    Route::get('attendance/status', [\App\Http\Controllers\Admin\AttendanceController::class, 'getStatus'])->name('admin.attendance.status');
    
    // Leave Management
    Route::get('leaves', [\App\Http\Controllers\Admin\LeaveController::class, 'index'])->name('admin.leaves.index');
    Route::get('leaves/create', [\App\Http\Controllers\Admin\LeaveController::class, 'create'])->name('admin.leaves.create');
    Route::post('leaves', [\App\Http\Controllers\Admin\LeaveController::class, 'store'])->name('admin.leaves.store');
    Route::patch('leaves/{leave}/status', [\App\Http\Controllers\Admin\LeaveController::class, 'updateStatus'])->name('admin.leaves.updateStatus');
    Route::get('leaves/holidays', [\App\Http\Controllers\Admin\LeaveController::class, 'holidays'])->name('admin.leaves.holidays');
    Route::post('leaves/holidays', [\App\Http\Controllers\Admin\LeaveController::class, 'storeHoliday'])->name('admin.leaves.storeHoliday');
    Route::delete('leaves/holidays/{holiday}', [\App\Http\Controllers\Admin\LeaveController::class, 'destroyHoliday'])->name('admin.leaves.destroyHoliday');
    
    // Payroll Management
    Route::get('payroll', [\App\Http\Controllers\Admin\PayrollController::class, 'index'])->name('admin.payroll.index');
    Route::post('payroll/generate', [\App\Http\Controllers\Admin\PayrollController::class, 'generatePayslips'])->name('admin.payroll.generate');
    Route::get('payroll/salary-config/{id}', [\App\Http\Controllers\Admin\PayrollController::class, 'salaryConfig'])->name('admin.payroll.salary-config');
    Route::post('payroll/salary-config/{id}', [\App\Http\Controllers\Admin\PayrollController::class, 'updateSalaryConfig'])->name('admin.payroll.salary-config.update');
    Route::post('payroll/adjustment', [\App\Http\Controllers\Admin\PayrollController::class, 'addAdjustment'])->name('admin.payroll.adjustment');
    Route::post('payroll/mark-paid/{id}', [\App\Http\Controllers\Admin\PayrollController::class, 'markAsPaid'])->name('admin.payroll.mark-paid');
    Route::get('payroll/my-payslips', [\App\Http\Controllers\Admin\PayrollController::class, 'myPayslips'])->name('admin.payroll.my-payslips');
    Route::get('payroll/view/{id}', [\App\Http\Controllers\Admin\PayrollController::class, 'viewPayslip'])->name('admin.payroll.view');
    Route::post('payroll/sync-from-project', [\App\Http\Controllers\Admin\PayrollController::class, 'syncFromProjects'])->name('admin.payroll.sync_from_project');
    
    // Task AJAX Routes
Route::get('tasks/{id}/quick-view', [\App\Http\Controllers\Admin\TaskController::class, 'quickView'])->name('admin.tasks.quick_view');
Route::post('tasks/{id}/status', [\App\Http\Controllers\Admin\TaskController::class, 'updateStatus'])->name('admin.tasks.update_status');
Route::post('tasks/quick-create', [\App\Http\Controllers\Admin\TaskController::class, 'quickCreate'])->name('admin.tasks.quick_create');
Route::post('tasks/{id}/assign-staff', [\App\Http\Controllers\Admin\TaskController::class, 'assignStaff'])->name('admin.tasks.assign_staff');
Route::post('tasks/{id}/update-priority', [\App\Http\Controllers\Admin\TaskController::class, 'updatePriority'])->name('admin.tasks.update_priority');
Route::post('tasks/{id}/toggle-follow', [\App\Http\Controllers\Admin\TaskController::class, 'toggleFollow'])->name('admin.tasks.toggle_follow');
Route::post('tasks/{id}/duplicate', [\App\Http\Controllers\Admin\TaskController::class, 'duplicate'])->name('admin.tasks.duplicate');

    // Task Pane Routes
    Route::get('pane/tasks/create', [\App\Http\Controllers\Admin\TaskPaneController::class, 'create'])->name('admin.tasks.pane.create');
    Route::get('pane/tasks/{id}/edit', [\App\Http\Controllers\Admin\TaskPaneController::class, 'edit'])->name('admin.tasks.pane.edit');
    Route::get('pane/tasks/{id}', [\App\Http\Controllers\Admin\TaskPaneController::class, 'show'])->name('admin.tasks.pane.show');

    // Task Attachment Routes
    Route::post('tasks/{task}/attachments', [\App\Http\Controllers\Admin\TaskController::class, 'storeAttachment'])->name('admin.tasks.attachments.store');
    Route::delete('tasks/attachments/{file}', [\App\Http\Controllers\Admin\TaskController::class, 'destroyAttachment'])->name('admin.tasks.attachments.destroy');

// Task Comments Routes
    Route::post('comments', [\App\Http\Controllers\Admin\CommentController::class, 'store'])->name('admin.comments.store');
    Route::put('comments/{comment}', [\App\Http\Controllers\Admin\CommentController::class, 'update'])->name('admin.comments.update');
    Route::delete('comments/{comment}', [\App\Http\Controllers\Admin\CommentController::class, 'destroy'])->name('admin.comments.destroy');

Route::resource('milestones', \App\Http\Controllers\Admin\MilestoneController::class)->names([
        'index' => 'admin.milestones.index',
        'create' => 'admin.milestones.create',
        'store' => 'admin.milestones.store',
        'edit' => 'admin.milestones.edit',
        'update' => 'admin.milestones.update',
        'show' => 'admin.milestones.show',
        'destroy' => 'admin.milestones.destroy',
]);

// Milestone Pane Routes
Route::get('pane/milestones/create', [\App\Http\Controllers\Admin\MilestonePaneController::class, 'create'])->name('admin.milestones.pane.create');
Route::get('pane/milestones/{id}/edit', [\App\Http\Controllers\Admin\MilestonePaneController::class, 'edit'])->name('admin.milestones.pane.edit');
Route::get('pane/milestones/{id}', [\App\Http\Controllers\Admin\MilestonePaneController::class, 'show'])->name('admin.milestones.pane.show');

// Task Timer Routes
Route::post('timers/start', [\App\Http\Controllers\Admin\TaskTimerController::class, 'start'])->name('admin.timers.start');
Route::post('timers/{timer}/stop', [\App\Http\Controllers\Admin\TaskTimerController::class, 'stop'])->name('admin.timers.stop');
Route::post('timers', [\App\Http\Controllers\Admin\TaskTimerController::class, 'store'])->name('admin.timers.store');
Route::put('timers/{timer}', [\App\Http\Controllers\Admin\TaskTimerController::class, 'update'])->name('admin.timers.update');
Route::delete('timers/{timer}', [\App\Http\Controllers\Admin\TaskTimerController::class, 'destroy'])->name('admin.timers.destroy');
Route::get('timers/task/{taskId}', [\App\Http\Controllers\Admin\TaskTimerController::class, 'getByTask'])->name('admin.timers.task.timers');
Route::post('timers/{timer}/approve', [\App\Http\Controllers\Admin\TaskTimerController::class, 'approve'])->name('admin.timers.approve');
Route::post('timers/{timer}/reject', [\App\Http\Controllers\Admin\TaskTimerController::class, 'reject'])->name('admin.timers.reject');
Route::post('timers/bulk-approve', [\App\Http\Controllers\Admin\TaskTimerController::class, 'bulkApprove'])->name('admin.timers.bulk_approve');
Route::get('projects/{project}/timesheets/export', [\App\Http\Controllers\Admin\ProjectController::class, 'exportTimesheets'])->name('admin.projects.timesheets.export');
Route::get('projects/{project}/timesheets/summary', [\App\Http\Controllers\Admin\ProjectController::class, 'timesheetSummary'])->name('admin.projects.timesheets.summary');

// File Routes
Route::post('files/upload', [\App\Http\Controllers\Admin\FileController::class, 'upload'])->name('admin.files.upload');
Route::delete('files/{file}', [\App\Http\Controllers\Admin\FileController::class, 'destroy'])->name('admin.files.destroy');
Route::get('files', [\App\Http\Controllers\Admin\FileController::class, 'getFiles'])->name('admin.files.get');
Route::put('files/{file}/rename', [\App\Http\Controllers\Admin\FileController::class, 'rename'])->name('admin.files.rename');
Route::get('files/{file}/preview', [\App\Http\Controllers\Admin\FileController::class, 'preview'])->name('admin.files.preview');
Route::post('files/{file}/expiry', [\App\Http\Controllers\Admin\FileController::class, 'setExpiry'])->name('admin.files.expiry');
Route::get('files/{file}/downloads', [\App\Http\Controllers\Admin\FileController::class, 'downloadHistory'])->name('admin.files.downloads');
Route::get('projects/{project}/files/{file}/download', [\App\Http\Controllers\Admin\FileController::class, 'download'])->name('admin.projects.files.download');
Route::post('files/bulk-download', [\App\Http\Controllers\Admin\FileController::class, 'bulkDownload'])->name('admin.files.bulk-download');
Route::post('files/move-to-folder', [\App\Http\Controllers\Admin\FileController::class, 'moveToFolder'])->name('admin.files.move-to-folder');
// File Folder Routes
Route::get('projects/{project}/file-folders', [\App\Http\Controllers\Admin\FileController::class, 'folders'])->name('admin.files.folders');
Route::post('projects/{project}/file-folders', [\App\Http\Controllers\Admin\FileController::class, 'storeFolder'])->name('admin.files.folders.store');
Route::delete('file-folders/{folder}', [\App\Http\Controllers\Admin\FileController::class, 'destroyFolder'])->name('admin.files.folders.destroy');

// Task Checklist Routes
Route::post('task-checklist', [\App\Http\Controllers\Admin\TaskChecklistController::class, 'store'])->name('admin.task-checklist.store');
Route::put('task-checklist/{item}', [\App\Http\Controllers\Admin\TaskChecklistController::class, 'update'])->name('admin.task-checklist.update');
Route::delete('task-checklist/{item}', [\App\Http\Controllers\Admin\TaskChecklistController::class, 'destroy'])->name('admin.task-checklist.destroy');
Route::post('task-checklist/update-order', [\App\Http\Controllers\Admin\TaskChecklistController::class, 'updateOrder'])->name('admin.task-checklist.update-order');

Route::resource('tickets', \App\Http\Controllers\Admin\TicketController::class)->names([
        'index' => 'admin.tickets.index',
        'create' => 'admin.tickets.create',
        'store' => 'admin.tickets.store',
        'edit' => 'admin.tickets.edit',
        'update' => 'admin.tickets.update',
        'show' => 'admin.tickets.show',
        'destroy' => 'admin.tickets.destroy',
    ]);

    Route::post('ticket-replies', [\App\Http\Controllers\Admin\TicketController::class, 'storeReply'])->name('admin.tickets.reply.store');
    Route::post('tickets/{id}/change-status', [\App\Http\Controllers\Admin\TicketController::class, 'changeStatus'])->name('admin.tickets.change_status');
    Route::post('tickets/{id}/change-assigned', [\App\Http\Controllers\Admin\TicketController::class, 'changeAssigned'])->name('admin.tickets.change_assigned');

    // Ticket Pane Routes
    Route::get('pane/tickets/create', [\App\Http\Controllers\Admin\TicketPaneController::class, 'create'])->name('admin.tickets.pane.create');
    Route::get('pane/tickets/{id}/edit', [\App\Http\Controllers\Admin\TicketPaneController::class, 'edit'])->name('admin.tickets.pane.edit');
    Route::get('pane/tickets/{id}', [\App\Http\Controllers\Admin\TicketPaneController::class, 'show'])->name('admin.tickets.pane.show');

    Route::resource('invoices', \App\Http\Controllers\Admin\InvoiceController::class)->names([
        'index' => 'admin.invoices.index',
        'create' => 'admin.invoices.create',
        'store' => 'admin.invoices.store',
        'edit' => 'admin.invoices.edit',
        'update' => 'admin.invoices.update',
        'show' => 'admin.invoices.show',
        'destroy' => 'admin.invoices.destroy',
    ]);

    // Invoice sub-routes
    Route::post('invoices/{invoice}/notes', [\App\Http\Controllers\Admin\InvoiceController::class, 'storeNote'])->name('admin.invoices.notes.store');
    Route::post('invoices/{invoice}/payments', [\App\Http\Controllers\Admin\InvoiceController::class, 'storePayment'])->name('admin.invoices.payments.store');
    Route::get('invoices/{invoice}/payments/{payment}/edit', [\App\Http\Controllers\Admin\InvoiceController::class, 'editPayment'])->name('admin.invoices.payments.edit');
    Route::put('invoices/{invoice}/payments/{payment}', [\App\Http\Controllers\Admin\InvoiceController::class, 'updatePayment'])->name('admin.invoices.payments.update');
    Route::delete('invoices/{invoice}/payments/{payment}', [\App\Http\Controllers\Admin\InvoiceController::class, 'destroyPayment'])->name('admin.invoices.payments.destroy');
    Route::post('invoices/{invoice}/credits', [\App\Http\Controllers\Admin\InvoiceController::class, 'storeCredit'])->name('admin.invoices.credits.store');
    Route::delete('invoices/{invoice}/credits/{credit}', [\App\Http\Controllers\Admin\InvoiceController::class, 'destroyCredit'])->name('admin.invoices.credits.destroy');
    Route::post('invoices/{invoice}/mark-paid', [\App\Http\Controllers\Admin\InvoiceController::class, 'markAsPaid'])->name('admin.invoices.mark_paid');
    Route::post('invoices/{invoice}/mark-cancelled', [\App\Http\Controllers\Admin\InvoiceController::class, 'markAsCancelled'])->name('admin.invoices.mark_cancelled');
    Route::post('invoices/{invoice}/send', [\App\Http\Controllers\Admin\InvoiceController::class, 'sendInvoice'])->name('admin.invoices.send');
    Route::get('invoices/{invoice}/print', [\App\Http\Controllers\Admin\InvoiceController::class, 'print'])->name('admin.invoices.print');
    Route::post('invoices/{invoice}/reminders', [\App\Http\Controllers\Admin\InvoiceController::class, 'storeReminder'])->name('admin.invoices.reminders.store');
    Route::post('invoices/{invoice}/send-notification', [\App\Http\Controllers\Admin\InvoiceController::class, 'sendNotification'])->name('admin.invoices.send_notification');

    Route::resource('estimates', \App\Http\Controllers\Admin\EstimateController::class)->names([
        'index' => 'admin.estimates.index',
        'create' => 'admin.estimates.create',
        'store' => 'admin.estimates.store',
        'edit' => 'admin.estimates.edit',
        'update' => 'admin.estimates.update',
        'show' => 'admin.estimates.show',
        'destroy' => 'admin.estimates.destroy',
    ]);

    Route::get('estimates/{estimate}/print', [\App\Http\Controllers\Admin\EstimateController::class, 'print'])->name('admin.estimates.print');

    Route::get('pane/estimates/create', [\App\Http\Controllers\Admin\EstimatePaneController::class, 'create'])->name('admin.estimates.pane.create');
    Route::get('pane/estimates/{id}/edit', [\App\Http\Controllers\Admin\EstimatePaneController::class, 'edit'])->name('admin.estimates.pane.edit');
    Route::get('pane/estimates/{id}', [\App\Http\Controllers\Admin\EstimatePaneController::class, 'show'])->name('admin.estimates.pane.show');

    // Estimate sub-routes
    Route::post('estimates/{estimate}/notes', [\App\Http\Controllers\Admin\EstimateController::class, 'storeNote'])->name('admin.estimates.store_note');
    Route::post('estimates/{estimate}/reminders', [\App\Http\Controllers\Admin\EstimateController::class, 'storeReminder'])->name('admin.estimates.store_reminder');
    Route::post('estimates/{estimate}/mark-accepted', [\App\Http\Controllers\Admin\EstimateController::class, 'markAccepted'])->name('admin.estimates.mark_accepted');
    Route::post('estimates/{estimate}/mark-declined', [\App\Http\Controllers\Admin\EstimateController::class, 'markDeclined'])->name('admin.estimates.mark_declined');
    Route::post('estimates/{estimate}/convert-to-invoice', [\App\Http\Controllers\Admin\EstimateController::class, 'convertToInvoice'])->name('admin.estimates.convert_to_invoice');

    Route::resource('expenses', \App\Http\Controllers\Admin\ExpenseController::class)->names([
        'index' => 'admin.expenses.index',
        'create' => 'admin.expenses.create',
        'store' => 'admin.expenses.store',
        'edit' => 'admin.expenses.edit',
        'update' => 'admin.expenses.update',
        'show' => 'admin.expenses.show',
        'destroy' => 'admin.expenses.destroy',
    ]);

    Route::get('pane/expenses/create', [\App\Http\Controllers\Admin\ExpensePaneController::class, 'create'])->name('admin.expenses.pane.create');
    Route::get('pane/expenses/{id}/edit', [\App\Http\Controllers\Admin\ExpensePaneController::class, 'edit'])->name('admin.expenses.pane.edit');
    Route::get('pane/expenses/{id}', [\App\Http\Controllers\Admin\ExpensePaneController::class, 'show'])->name('admin.expenses.pane.show');

    Route::resource('credit-notes', \App\Http\Controllers\Admin\CreditNoteController::class)->names([
        'index' => 'admin.creditnotes.index',
        'create' => 'admin.creditnotes.create',
        'store' => 'admin.creditnotes.store',
        'edit' => 'admin.creditnotes.edit',
        'update' => 'admin.creditnotes.update',
        'show' => 'admin.creditnotes.show',
        'destroy' => 'admin.creditnotes.destroy',
    ]);
    Route::post('credit-notes/{id}/apply', [\App\Http\Controllers\Admin\CreditNoteController::class, 'applyToInvoice'])->name('admin.creditnotes.apply');

    Route::resource('knowledge-base', \App\Http\Controllers\Admin\KnowledgeBaseController::class)->names([
        'index' => 'admin.knowledgebase.index',
        'create' => 'admin.knowledgebase.create',
        'store' => 'admin.knowledgebase.store',
        'edit' => 'admin.knowledgebase.edit',
        'update' => 'admin.knowledgebase.update',
        'show' => 'admin.knowledgebase.show',
        'destroy' => 'admin.knowledgebase.destroy',
    ]);

    Route::resource('knowledge-base-groups', \App\Http\Controllers\Admin\KnowledgeBaseGroupController::class)->names([
        'index' => 'admin.knowledge-base-groups.index',
        'create' => 'admin.knowledge-base-groups.create',
        'store' => 'admin.knowledge-base-groups.store',
        'edit' => 'admin.knowledge-base-groups.edit',
        'update' => 'admin.knowledge-base-groups.update',
        'destroy' => 'admin.knowledge-base-groups.destroy',
    ]);

    Route::post('/logout', [StaffAuthController::class, 'logout'])->name('staff.logout');
});
// --- CLIENT AREA ---
Route::middleware(['auth:client'])->prefix('client')->group(function () {
    Route::get('/dashboard', [ClientDashboard::class, 'index'])->name('client.dashboard');
    
    // Projects
    Route::get('/projects', [\App\Http\Controllers\Client\ProjectController::class, 'index'])->name('client.projects.index');
    Route::get('/projects/export', [\App\Http\Controllers\Client\ProjectController::class, 'export'])->name('client.projects.export');
    Route::get('/projects/{id}', [\App\Http\Controllers\Client\ProjectController::class, 'show'])->name('client.projects.show');
    Route::get('/projects/{project}/files', [\App\Http\Controllers\Client\ProjectController::class, 'files'])->name('client.projects.files');
    Route::get('/projects/{project}/files/{file}/download', [\App\Http\Controllers\Client\ProjectController::class, 'downloadFile'])->name('client.projects.files.download');
    Route::get('/projects/{project}/discussions', [\App\Http\Controllers\Client\ProjectController::class, 'discussions'])->name('client.projects.discussions');
    Route::get('/projects/{project}/discussions/{discussion}', [\App\Http\Controllers\Client\ProjectController::class, 'showDiscussion'])->name('client.projects.discussions.show');
    Route::post('/projects/{project}/discussions/{discussion}/comment', [\App\Http\Controllers\Client\ProjectController::class, 'addComment'])->name('client.projects.discussions.comment');
    Route::get('/projects/{project}/notes', [\App\Http\Controllers\Client\ProjectController::class, 'notes'])->name('client.projects.notes');
    Route::get('/projects/{project}/activity', [\App\Http\Controllers\Client\ProjectController::class, 'activity'])->name('client.projects.activity');
    Route::get('/projects/{project}/timesheets', [\App\Http\Controllers\Client\ProjectController::class, 'timesheets'])->name('client.projects.timesheets');
    Route::get('/projects/{project}/gantt', [\App\Http\Controllers\Client\ProjectController::class, 'gantt'])->name('client.projects.gantt');
    Route::get('/projects/{project}/invoices', [\App\Http\Controllers\Client\ProjectController::class, 'invoices'])->name('client.projects.invoices');
    Route::get('/projects/{project}/estimates', [\App\Http\Controllers\Client\ProjectController::class, 'estimates'])->name('client.projects.estimates');
    Route::get('/projects/{project}/milestones', [\App\Http\Controllers\Client\ProjectController::class, 'milestones'])->name('client.projects.milestones');
    Route::post('/projects/{project}/feedback', [\App\Http\Controllers\Client\ProjectController::class, 'submitFeedback'])->name('client.projects.feedback');
    Route::get('/profile/edit', [\App\Http\Controllers\Client\ProfileController::class, 'edit'])->name('client.profile.edit');
    Route::put('/profile/update', [\App\Http\Controllers\Client\ProfileController::class, 'update'])->name('client.profile.update');

    // Invoices
    Route::get('/invoices', [\App\Http\Controllers\Client\InvoiceController::class, 'index'])->name('client.invoices.index');
    Route::get('/invoices/export', [\App\Http\Controllers\Client\InvoiceController::class, 'export'])->name('client.invoices.export');
    Route::get('/invoices/{id}', [\App\Http\Controllers\Client\InvoiceController::class, 'show'])->name('client.invoices.show');

    // Proposals
    Route::get('/proposals', [\App\Http\Controllers\Client\ProposalController::class, 'index'])->name('client.proposals.index');
    Route::get('/proposals/export', [\App\Http\Controllers\Client\ProposalController::class, 'export'])->name('client.proposals.export');
    Route::get('/proposals/{id}', [\App\Http\Controllers\Client\ProposalController::class, 'show'])->name('client.proposals.show');

    // Estimates
    Route::get('/estimates', [\App\Http\Controllers\Client\EstimateController::class, 'index'])->name('client.estimates.index');
    Route::get('/estimates/export', [\App\Http\Controllers\Client\EstimateController::class, 'export'])->name('client.estimates.export');
    Route::get('/estimates/{id}', [\App\Http\Controllers\Client\EstimateController::class, 'show'])->name('client.estimates.show');

    // Tickets
    Route::get('/tickets', [\App\Http\Controllers\Client\TicketController::class, 'index'])->name('client.tickets.index');
    Route::get('/tickets/export', [\App\Http\Controllers\Client\TicketController::class, 'export'])->name('client.tickets.export');
    Route::get('/tickets/create', [\App\Http\Controllers\Client\TicketController::class, 'create'])->name('client.tickets.create');
    Route::post('/tickets', [\App\Http\Controllers\Client\TicketController::class, 'store'])->name('client.tickets.store');
    Route::get('/tickets/{id}', [\App\Http\Controllers\Client\TicketController::class, 'show'])->name('client.tickets.show');
    Route::post('/tickets/{id}/reply', [\App\Http\Controllers\Client\TicketController::class, 'storeReply'])->name('client.tickets.reply.store');

    Route::post('/logout', [ClientAuthController::class, 'logout'])->name('client.logout');
});
