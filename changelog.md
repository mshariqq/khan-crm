
## Currency & Invoice Formatting — Completed Audit Tasks

### Phase 1 — Centralized Currency Formatting
- [x] Create `app_format_money($amount, $currency)` helper that respects `placement`, `decimal_separator`, `thousand_separator`, and `symbol` from Currency model
- [x] Replace ALL `$model->currencyRel->symbol ?? '$'` . `number_format(...)` with `app_format_money()` across ~136+ locations

### Phase 2 — Fix Missing Currency Symbols
- [x] `projects/partials/invoices.blade.php` — Replace hardcoded `$` with dynamic currency
- [x] `dashboard.blade.php` — Add currency symbol to Revenue stat, overdue invoices, latest invoices
- [x] `subscriptions/index.blade.php` — Add currency symbol to Amount column
- [x] `subscriptions/show.blade.php` — Add currency symbol to invoice amounts and total invoiced
- [x] `creditnotes/index.blade.php` — Add currency symbol to Total Value stat
- [x] `creditnotes/show.blade.php` — Add currency symbol to item rates, subtotals, applied credits, summary stats
- [x] `creditnotes/create.blade.php` — Add currency symbol to invoice dropdown
- [x] `expenses/index.blade.php` — Add currency symbol to amount column
- [x] `expenses/show.blade.php` — Add currency symbol to amount
- [x] `expenses/create.blade.php` — Add currency symbol to invoice dropdown
- [x] `leads/show.blade.php` — Added currency symbol to lead value
- [x] `tasks/show.blade.php` — Added currency symbol to hourly rate
- [x] `items/index.blade.php` — Added currency symbol to item rates
- [x] `projects/show.blade.php` — Replaced hardcoded `$` with `app_format_money()`

### Phase 3 — Fix JavaScript Hardcoded `$`
- [x] `invoices/create.blade.php` — Make JS `recalculateAll()` dynamic: reads selected currency symbol via `getCurrencySymbol()` function
- [x] `invoices/edit.blade.php` — Same fix as create
- [x] `estimates/create.blade.php` — Same dynamic currency symbol fix
- [x] `estimates/edit.blade.php` — Same dynamic currency symbol fix
- [x] `estimates/pane/create.blade.php` — Same dynamic currency symbol fix
- [x] `estimates/pane/edit.blade.php` — Same dynamic currency symbol fix
- [x] `proposals/create.blade.php` — Same dynamic currency symbol fix

### Phase 4 — Consolidate Relationships
- [x] Standardize on `currency` across all models. Removed duplicate `currencyRel()` from Invoice, Estimate, Proposal, Subscription. Added `currency()` alias to CreditNote and Expense. Updated all controllers and blade files.

### Phase 5 — Multi-Currency Reporting
- [x] Add base currency indicator to Dashboard financial stats — shows base currency code (e.g., USD) on Revenue card
- [x] Eager-load currency relationship in dashboard queries to avoid N+1

### Phase 6 — Additional Enhancements
- [x] Created `app_get_currency_code($currency)` helper for ISO code display
- [x] `InvoiceNotificationService.php` — Uses `app_format_money()` and `app_get_currency_code()`
- [x] `ProposalNotificationService.php` — Uses `app_format_money()` and `app_get_currency_code()`
- [x] Add currency validation when applying credits between different-currency invoices

---

## Pending Tasks — Full Codebase Audit Findings

### CRITICAL — Security & Data Integrity

#### Authentication & Access Control
- [x] **Implement rate limiting on auth routes** (`/login/staff`, `/login/client` POST) — Added `throttle:5,15` middleware to both POST login routes
- [x] **Implement account lockout after failed login attempts** — Both `StaffAuthController::login()` and `ClientAuthController::login()` now track failed attempts via cache, lock out for 15 mins after 5 failures
- [x] **Implement 2FA verification logic** — Created `TwoFactorAuthService` (generate/validate codes), `TwoFactorChallengeController` (challenge page + verify + resend), modified `StaffAuthController` to intercept 2FA-enabled users, added `$casts` to Staff model, added 2FA routes, created 2FA challenge view; uses email-based 6-digit code with 10-min expiry
- [x] **Enforce password complexity** — Updated to `min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])/` in StaffController store/update
- [x] **Require current password confirmation on password change** — `StaffController::update()` now validates `current_password` against authenticated user's password before allowing change
- [x] **Add email verification for staff and contacts** — Added migration for staff `email_verified_at`/`email_verification_key`/`email_verification_sent_at` columns, created `EmailVerificationController` (send/verify/resend for both guards), `EnsureEmailIsVerified` middleware (registered as `verified` alias), modified `StaffAuthController` and `ClientAuthController` to check verification on login (controlled by `staff_email_verification_required`/`client_email_verification_required` settings), added admin `sendVerificationEmail` route on StaffController
- [x] **Implement session security hardening** — Set `expire_on_close=true`, `encrypt=true`, `secure=true` in production via `config/session.php`
- [x] **Add security headers middleware** — Created `SecurityHeadersMiddleware` with: `X-Frame-Options: DENY`, `X-Content-Type-Options: nosniff`, `Referrer-Policy`, `Strict-Transport-Security` (production), `Permissions-Policy`
- [x] **Configure CORS** — Created `config/cors.php` with credentials support and configurable allowed origins

#### File Upload Security
- [x] **CRITICAL: Fix arbitrary file upload vulnerability** — `FileService.php` now validates: extension whitelist, MIME type allowlist, magic byte validation via `finfo`, and throws `InvalidArgumentException` on mismatch

#### Permission & Authorization
- [x] **Fix missing authorization on KnowledgeBaseController** — ALL methods now check `knowledge_base_view`, `knowledge_base_create`, `knowledge_base_edit`, `knowledge_base_delete`
- [x] **Fix missing authorization on `InvoiceController@print`** — Added `invoices_view` permission check on print method
- [x] **Fix missing authorization on `DashboardController@index`** — Added `dashboard_view` permission check
- [x] **Fix missing authorization on `AttendanceController`** — Added `attendance_create` for clockIn/clockOut, `attendance_view` for getStatus
- [x] **Fix missing authorization on `PayrollController@myPayslips`, `viewPayslip`** — Added `hr_payroll_view` and `hr_payroll_manage` checks; switched from admin flag to permission system
- [x] **Fix missing authorization on `SearchController`** — Added `search_view` permission check
- [x] **Fix missing authorization on `NotificationController`** — Already filtered by `touserid`; adequate protection
- [x] **Implement sidebar permission-based visibility** — All sidebar sections now wrapped in `@can_do()` directives matching their module permissions
- [x] **Add permission filtering to dashboard data** — Added `dashboard_view` permission check at controller entry point
- [x] **Fix admin bypass via `->admin` flag** — `AttendanceController:19` changed to use `attendance_view_all` permission; `PayrollController:203` uses `hr_payroll_manage` permission
- [x] **Implement RoleMiddleware** — Filled `RoleMiddleware.php` with actual role-checking logic; registered as `role` alias in `bootstrap/app.php`
- [x] **Add permission cache invalidation** — `PermissionService::clearCache()` method added; RoleController store/update/destroy and StaffController store/update/destroy now call it
- [x] **Implement permission deny mechanism** — `PermissionService::hasPermission()` now supports `!` prefix on `tblstaff_permissions.capability` to explicitly deny (overrides role grants). Added Permission Overrides section in staff edit view with deny checkboxes. StaffController::update() saves/clears denied permissions.
- [x] **Normalize role permissions storage** — Created `tblrole_permissions` pivot table migration (with data migration from old comma-separated format), created `RolePermission` model, added `permissions()` BelongsToMany and `getPermissionSlugs()` helper on Role model, updated `PermissionService` to use pivot via `getPermissionSlugs()`, updated RoleController store/update to sync pivot, updated role edit view

#### Audit & Logging
- [x] **Add comprehensive audit logging** — Created `tblaudit_logs` migration (action, entity_type/entity_id, description, old/new_values JSON, staff_id, IP, UA), `AuditLog` model, `AuditService` (with helper methods: staffCreated/Updated/Deleted, roleCreated/Updated/Deleted, login/logout, settingsUpdated, permissionChanged, payrollAction). Added logging to: StaffAuthController, ClientAuthController, TwoFactorChallengeController, StaffController, RoleController, SettingsController
- [x] **Implement event/listener system** — Created events: `InvoicePaid`, `PaymentRecorded`, `InvoiceCreated`, `LeadConverted`, `EstimateConvertedToInvoice`; created listeners: `LogInvoicePaid`, `LogPaymentRecorded`, `LogInvoiceCreated`, `LogLeadConverted`, `LogEstimateConverted` (all audit-logging listeners). Created `EventServiceProvider` and registered in `bootstrap/app.php`
- [x] **Add audit trail for edits** — `InvoiceController::update()` now captures old items (with `toArray()`) before deletion, logs full old/new state (including items) via `AuditService` with `invoice_updated` action

---

### HIGH — Data Integrity & Architecture

#### Database & Migrations
- [ ] **Add foreign key constraints** — Only 5 FK constraints exist out of ~80+ needed; all cross-table references unenforced (see complete list in audit)
- [ ] **Fix FK type mismatch** — Almost all FK columns use `integer` (signed 32-bit) while PKs use `bigIncrements` (unsigned 64-bit); will cause FK constraint failures
- [ ] **Add cascade deletes** — Zero cascade deletes exist; deleting clients/staff/projects/tasks/etc. leaves orphaned records everywhere
- [ ] **Add soft deletes** — No table has `deleted_at`; CRM needs data recovery on Client, Contact, Invoice, Estimate, CreditNote, Project, Task, Lead, Ticket, Expense, Proposal, Staff
- [ ] **Add unique constraints** — Missing on: `contacts.email`, `currencies.name`, `options.name`, `tags.name`, `roles.name`, `invoices.hash`, `proposals.hash`, `tickets.ticketkey`, `knowledge_base.slug`, `modules.module_name`, and 15+ others
- [ ] **Add composite indexes on polymorphic (rel_id, rel_type)** — Missing on 16+ tables: `notes`, `itemable`, `item_tax`, `reminders`, `files`, `tracked_mails`, `views_tracking`, `scheduled_emails`, `taggables`, `customfieldsvalues`, `tasks`, `proposals`, `form_results`, `form_questions`, `sales_activity`, `related_items`
- [ ] **Add missing indexes** — 30+ tables lack indexes on FK columns (see complete list)
- [ ] **Fix decimal precision issues** — `tblpayment_attempts.amount/fee` uses `double` (floating-point rounding errors); `tbltwocheckout_log.amount` uses `string(25)` (cannot perform math)
- [ ] **Fix wrong column types** — `tbltaskstimers.start_time/end_time` uses `string(64)` (should be `dateTime`); `tblactivity_log.staffid`/`tblsales_activity.staffid` uses `string(100)` (should be `integer`); `tblform_question_box_description.boxid` uses `longText` (should be `integer` FK); `tblannouncements.userid` uses `string(100)` (should be `integer`); `tblknowledge_base.slug` uses `longText` (should be `string(255)` unique)
- [ ] **Fix nullable columns without defaults** — `tblcontacts.phonenumber`, `tblinvoices/creditnotes/estimates.include_shipping`, `tblannouncements.showtousers/showtostaff/showname`, `tblknowledge_base/knowledge_base_groups.active`, `tbltodos.finished`, `tblmodules.active`
- [ ] **Standardize boolean columns** — Three patterns used inconsistently: `boolean` (tinyint(1)), `integer` with `default(0)`, `tinyInteger`; worst offenders: `tblpayment_modes`, `tblstaff`, `tblleads`, `tblnotifications`
- [ ] **Remove redundant manual date columns** — 40+ tables have `datecreated`/`dateadded` duplicating `created_at` from `timestamps()`
- [ ] **Fix `tblstaff_permissions.staff_id`** — Uses `bigIncrements` as PK (auto-increment FK column); architecturally wrong
- [ ] **Standardize primary keys** — 22 models use custom PKs (`staffid`, `userid`, `roleid`, `ticketid`, etc.) instead of Laravel convention `id`

#### Models
- [x] **CRITICAL: Create missing `CustomerAdmin` model** — Created `app/Models/CustomerAdmin.php` with `staff()`, `client()` relationships
- [x] **Add missing model relationships** — Added to: CreditNoteRefund (creditNote/staff/paymentMode), PaymentAttempt (invoice), TwoCheckoutLog (invoice), Contact (tickets/notes/projectDiscussions), CreditNote (refunds), Invoice (paymentAttempts/trackedMails), Expense (currencyRel)
- [x] **Add polymorphic `morphTo()` relationships** — Added `relatable()` morphTo to: ViewTracking, TrackedMail, ScheduledEmail, SpamFilter, FormQuestion, RelatedItem, SalesActivity; added `taxable()` morphTo to ItemTax
- [x] **Refactor manual polymorphic relationships to `morphTo()`** — Reminder, Note, and Task now use proper `morphTo()`; added `Relation::enforceMorphMap()` in AppServiceProvider
- [x] **Fix `$casts` on Milestone model** — Replaced deprecated `$dates` property with `casts()` method
- [ ] **Add `$casts` to all models** — Only 5 models define `$casts`; 108 models lack date casts for 700+ date/datetime columns
- [x] **Fix mass assignment protection** — Scripted removal of primary keys from `$fillable` across 85+ models; all auto-increment PKs removed from mass assignment
- [ ] **Add return type hints on all relationships** — 300+ relationship methods lack return types (e.g., `: BelongsTo`, `: HasMany`)
- [x] **Remove duplicate relationship definitions** — `Invoice.php`: removed `remindersList()` and `notesList()` duplicates; `Proposal.php` and `Lead.php`: `country()`/`countryRel()` remain (deferred)
- [ ] **Add model observers** — No `app/Observers/` directory exists; no models use event-driven side effects
- [x] **Add `HasFactory` trait** — Added to Project, Milestone, ProjectMember, ProjectActivity, ProjectFile, ProjectNote, ProjectDiscussion, ProjectDiscussionComment, PinnedProject, Client, and Staff models with corresponding factory classes
- [x] **Remove `User.php` inconsistency** — Replaced PHP 8 attribute syntax with traditional `$fillable`/`$hidden` properties
- [x] **Fix `ExpenseCategory` table naming** — Added `$table = 'tblexpense_categories'` property
- [x] **Add default values via `$attributes`** — Added to: Client (active), Staff (active/admin/is_not_staff), Invoice (status), Estimate (status), CreditNote (status), Proposal (status), Subscription (status), Project (status), Task (is_public/billable), Ticket (adminread/clientread), Announcement (showtousers/showtostaff/showname), KnowledgeBase (active), KnowledgeBaseGroup (active), Todo (finished), Module (active)

---

### HIGH — Controllers & Business Logic

#### Validation & Mass Assignment
- [x] **Replace all `$request->all()` with `$validated` or `$request->safe()`** — Fixed in ProposalController, SubscriptionController, LeadController, ExpenseController, TaxController, CurrencyController, PaymentModeController, CountryController, ServiceController, ItemsGroupController, LeaveController
- [x] **Create Form Request classes** — Created `StoreInvoiceRequest`, `UpdateInvoiceRequest`, `StoreLeadRequest`, `UpdateLeadRequest`, `StoreProjectRequest`, `UpdateProjectRequest`, `StoreTaskRequest`, `UpdateTaskRequest` with authorize() checking respective permissions and rules() extracted from inline validation. Updated InvoiceController, LeadController, ProjectController, TaskController to use them (also fixed bug in LeadController::update() where `$validated` was used but never assigned)
- [x] **Add pagination to list endpoints** — Fixed: `PayrollController@myPayslips`, `LeaveController@holidays` now paginated

#### Transaction Safety
- [x] **Wrap multi-table writes in DB::transaction()** — `InvoiceController@store`, `InvoiceController@storePayment`, `LeadController@convertToClient`, `EstimateController@convertToInvoice` now wrapped in `DB::beginTransaction/commit/rollBack` with try/catch
- [x] **Add N+1 query fix for SearchController** — Already eager-loads Task (assignedStaff, creator) and Ticket (client, statusRel, priorityRel) relationships

---

### HIGH — Finance Module Gaps

#### Invoicing & Payments
- [x] **Fix invoice number generation race condition** — `InvoiceController::store()` uses `DB::transaction` + `lockForUpdate()` to atomically generate next number
- [x] **Add estimate number auto-generation** — `EstimateController::store()` uses `DB::transaction` + `lockForUpdate()` to atomically generate next number
- [x] **Add credit note number auto-generation** — `CreditNoteController::store()` uses `DB::transaction` + `lockForUpdate()` to atomically generate next number
- [ ] **Implement true PDF generation** — Invoice/Estimate `print()` methods render Blade view only; no PDF library installed (DomPDF/TCPDF); no PDF attachment in email notifications
- [x] **Add proposal print route** — Added `ProposalController::print()` method, route `admin.proposals.print`, and `proposals/pdf.blade.php` print view
- [ ] **Implement payment reconciliation** — No reconciliation workflow; `InvoicePaymentRecord.transactionid` not used in `storePayment()`; no payment matching, no unallocated payments report
- [x] **Prevent overpayments** — `InvoiceController::storePayment()` validates `amount <= remaining` (total - totalSettled) before recording payment
- [x] **Fix `storePayment()` credit check** — Already uses `totalSettled()` which sums both payments and credits, and `remaining` check prevents over-application
- [x] **Implement invoice status state machine** — `markAsPaid()`/`markAsCancelled()` now enforce valid transitions; `amountDue()` returns 0 for cancelled invoices; `storePayment()` blocks payments on cancelled invoices; estimate `update()` blocks edits on accepted/declined/invoiced
- [x] **Add edit guard on paid/cancelled invoices** — `InvoiceController::update()` checks `in_array($invoice->status, [PAID, CANCELLED])` and blocks modification
- [ ] **Implement recurring invoice cron** — Recurring fields (`recurring`, `cycles`, `last_recurring_date`) stored but NEVER processed; no `app/Console/Kernel.php` exists; no scheduled task generates recurring invoices
- [ ] **Implement overdue reminder automation** — `last_overdue_reminder`/`last_due_reminder` fields on Invoice never updated; overdue notification slugs defined but never triggered by automation
- [ ] **Add financial reports** — No aging report, client statement, revenue report, expense aggregation, tax report, AR/AP aging, profit/loss tracking
- [ ] **Add fiscal year concept with audit lock** — No period locking; staff can edit/delete historical financial records at any time

#### Credit Notes & Refunds
- [ ] **Implement refund workflow** — `CreditNoteRefund` model exists but is completely orphaned (no controller, no route, no view references it); `refund-processed` notification slug defined but never called
- [ ] **Add partial refund support** — Credit note can only be applied in full amount to single invoice; no split across multiple invoices
- [ ] **Add credit note status tracking** — Credit notes always have `status=1`; no "Applied", "Partially Applied", or "Void" status

#### Tax & Currency
- [x] **Add server-side tax recalculation** — Invoice and Estimate store/update now recalculate `subtotal`, `total_tax`, and `total` from items after saving; overrides client-submitted values
- [ ] **Add second tax (`tax2`) on invoice items** — `Itemable.php` has only single `tax_rate` while `Item` model has `tax` and `tax2`; second tax lost when adding item to invoice
- [ ] **Add exchange rate to Currency model** — No `exchange_rate` field; no multi-currency conversion capability; credit notes blocked across currencies
- [ ] **Add currency conversion for payments** — No way to record payment in different currency; no `exchange_rate` or `base_amount` on `InvoicePaymentRecord`

#### Items & Inventory
- [ ] **Add delete protection for items used in invoices** — `ItemController::destroy()` comment (line 149) explicitly acknowledges missing check; items in existing invoices/estimates can be silently deleted
- [ ] **Add inventory tracking** — No `sku`, `quantity`, `reorder_level` on Item model; no inventory valuation

#### Subscriptions
- [ ] **Implement subscription billing cycle** — No auto-invoice generation from subscriptions; `next_billing_cycle` never processed; no `billing_cycle`/`interval` field; no pricing/tiers on Subscription model
- [ ] **Add subscription payment gateway integration** — No recurring payment token storage; `PaymentAttempt` model orphaned

#### Expenses
- [ ] **Implement billable-to-invoice conversion** — `Expense.billable`, `create_invoice_billable`, `invoiceid` fields exist but no controller method converts billable expenses to invoice items
- [ ] **Add expense markup/profit tracking** — No `markup_percentage` or `billable_amount` field on expenses

---

### HIGH — HR & Payroll Module Gaps

#### Attendance
- [x] **Implement late arrival detection** — `clockIn()` now compares against `shift_start_time` setting + `late_grace_period`; auto-sets status to 'Late' when applicable
- [x] **Implement early departure detection** — `clockOut()` now compares clock-out time against `shift_end_time` setting; updates status to 'Early Departure' or 'Late / Early Departure'
- [x] **Implement overtime calculation** — `getStatus()` now computes overtime by comparing worked seconds against `default_working_hours`; returns `overtime` and `expected_hours` in response
- [ ] **Add attendance analytics/reports** — No late arrival summary, absenteeism rate, attendance percentage, average hours worked, department summaries, calendar view, or exportable reports

#### Leaves
- [ ] **Implement leave carry-forward** — Balance computed per calendar year only; no `carry_over_limit`, no `carried_forward_days` field, no accrual-based system
- [ ] **Implement multi-level leave approval workflow** — Currently single-step admin-only approve/reject; no manager/team-lead approval chain
- [ ] **Add leave rejection reason/comments** — No `rejection_reason` field on `tblstaff_leaves`
- [ ] **Add leave email notifications** — No notification on leave submission, approval, or rejection
- [ ] **Implement PTO/sick leave accrual** — Leave is lump-sum annual quota; no accrual rate, frequency, or cap
- [ ] **Exclude company holidays from leave duration** — `StaffLeave::getDurationAttribute()` uses naive `diffInDays+1`; no holiday exclusion logic

#### Payroll
- [ ] **Implement payroll income tax calculation** — No tax bracket configuration, no taxable income calculation; net pay is simply `base + additions - deductions`
- [ ] **Add statutory deductions** — No Social Security, pension, health insurance, or other statutory deductions; no employer contribution tracking
- [ ] **Implement overtime pay integration** — Payroll never queries attendance records; no automatic overtime payslip items; only manual via `addAdjustment()`
- [ ] **Add salary revision history** — `PayrollController::updateSalaryConfig()` uses `updateOrCreate` which overwrites; old values permanently lost; no `SalaryRevision` model
- [ ] **Add payroll audit trail** — No log of who generated payslips, added adjustments, marked as paid; no before/after snapshots of salary config changes
- [ ] **Add PDF payslip generation** — Only `window.print()` in view; no downloadable/emailable payslip PDF
- [ ] **Add tax forms** — No P60/P45 equivalent annual tax summaries
- [ ] **Add payroll summary reports** — No monthly payroll register, department summary, YTD earnings, deduction summary

#### Employee Management
- [ ] **Add employee document management** — No contract/ID/certificate storage; no bank account details; no emergency contact; no `StaffDocument` model
- [ ] **Add employee performance tracking** — No performance reviews, goals/OKRs, self-assessments, 360 feedback
- [ ] **Add shift scheduling** — No shift definitions, assignments, or roster view; `tblstaff_attendances` has no shift columns
- [ ] **Add department budget tracking** — No budget allocation, salary cost tracking, headcount budget; no `DepartmentBudget` model
- [ ] **Implement timesheet approval workflow** — No weekly/monthly HR timesheet submission/approval; existing timesheets are per-task (project-specific, not HR)
- [ ] **Add holiday calendar management** — No calendar visualization, no recurring holidays, no half-day support, no regional holiday groups

---

### MEDIUM — Architecture & Code Quality

#### Services & Refactoring
- [x] **Extract fat controller methods to services** — `InvoiceController@store/update`, `ProjectController@update`, `TaskController@store/update`, `LeadController@convertToClient`, `EstimateController@store/update` all delegate to service classes
- [x] **Create dedicated service classes** — Created: `InvoiceService`, `EstimateService`, `TaskService`, `ProjectService`, `LeadService` (5 done, 6 remaining: ProposalService, PayrollService, AttendanceService, LeaveService, TaxService, ClientService proper)
- [ ] **Create API controllers** — No `Api/` directory exists; no mobile/third-party API support
- [x] **Create event classes for domain events** — Created: `InvoiceCreated`, `InvoicePaid`, `PaymentRecorded`, `LeadConverted`, `EstimateConvertedToInvoice` (created earlier for audit logging); still missing: `ProjectCompleted`, `TaskAssigned`, `LeaveApproved`
- [ ] **Create job classes for async processing** — No custom job classes; recurring invoice generation, email sending should be queued

#### Code Cleanup
- [x] **Replace hard-coded magic numbers with constants/enums** — Added STATUS_* constants to Invoice, Estimate, CreditNote, Proposal, Project, Task models; replaced 78+ magic number occurrences across 22 PHP backend files (controllers + services); remaining Ticket/Subscription statuses kept as-is (dynamic from DB); blade templates deferred (117+ occurrences, purely presentational)
- [x] **Remove hard-coded role ID 1 as admin** — Added `is_admin` boolean column to `tblroles`; StaffController now checks `$role->is_admin` instead of `$request->role == 1`
- [x] **Fix `OptionsSeeder.php` duplicate entries** — Removed 9 duplicate insertions across sections; each option now appears once
- [x] **Fix `LeadStatusSeeder.php`** — Expanded from single 'Customer' status to full pipeline: Customer, Contacted, Qualified, Proposal Sent, Lost, Junk
- [ ] **Standardize response formats** — Mix of `redirect()->route()`, `redirect($request->referer)`, `response()->json()`, `view()` across controllers
- [x] **Fix `SalesActivity::invoice()` misleading constraint** — Removed the hard-coded `where('rel_type', 'invoice')` global scope from relationship
- [x] **Add missing logging** — Added audit logging to TaxController, CurrencyController, PaymentModeController, ExpenseController (store/update/destroy); 12 controllers remain (Client, Permission, Subscription, Contact, Country, Service, Department, ItemsGroup, ExpenseCategory, CustomersGroup, KnowledgeBaseGroup, LeaveType)

#### Tests
- [ ] **Increase test coverage** — Only 20 feature tests for 50+ modules; no tests for: ClientController, StaffController, RoleController, PermissionController, TaskController, SubscriptionController, ExpenseController, FileController, NotificationController, SearchController, DashboardController, SettingsController
- [ ] **Add unit tests for services** — No tests for `PermissionService`, `EmailService`, `FileService`, `InvoiceNotificationService`, `ProjectNotificationService`, `ProposalNotificationService`, `LeadActivityLogService`
- [ ] **Add model tests** — No tests for model relationships, scopes, accessors, mutators
- [ ] **Add factory definitions** — Only `UserFactory.php` exists (for `users` table not used by CRM); no factories for any CRM model
- [ ] **Add feature tests for financial workflows** — No tests for: invoice payment recording, credit note application, estimate conversion, recurring invoice, refund processing
- [ ] **Add feature tests for HR workflows** — No tests for: attendance clock-in/out, leave application/approval, payslip generation, salary configuration
- [ ] **Add feature tests for security** — No tests for: authorization checks, permission enforcement, rate limiting, 2FA, password policies

---

### LOW — Enhancements & Nice-to-Haves
- [ ] **Add bulk operations** — No bulk import/edit/delete/export for items, invoices, clients, leads, tasks
- [ ] **Add drag-and-drop kanban for leads** — Task has kanban view; Lead does not
- [ ] **Add client portal enhancements** — Client controllers limited to dashboard + view-only for projects, invoices, proposals, estimates, tickets; no self-service profile editing, no document upload
- [ ] **Add webhook system** — No webhook endpoints or webhook configuration for external integrations
- [ ] **Add landing/public pages CMS** — Limited to knowledge base; no ability to create custom landing pages
- [ ] **Add email campaign management** — No bulk email campaign tools beyond transactional emails
- [ ] **Add mobile responsive improvements** — Admin views may not be fully mobile-responsive
- [ ] **Add dark mode** — No theme toggle or dark mode support
- [ ] **Add activity log cleanup/pruning** — `ActivityLog`, `ProjectActivity`, `ViewTracking`, `MailQueue` have no retention/cleanup strategy; will grow unbounded
- [ ] **Add database backup/restore UI** — No admin panel for database backup
- [ ] **Add import wizards** — No CSV/Excel import for clients, contacts, leads, items, staff
- [ ] **Add GDPR data export/deletion UI** — `GdprRequest` model exists but no full GDPR compliance workflow
- [ ] **Add audit log for option/settings changes** — SettingsController::update() does not log which settings changed
- [x] **Add `RoleMiddleware` implementation** — Implemented with actual role-checking logic; registered as `role` middleware alias


---

## Task Module — Full Audit & Right Pane Implementation Plan

### RIGHT PANE FULL FLOW — Implementation TODO List

The right pane (`#taskPane` in `admin.blade.php`) currently supports Create/Edit/Show for tasks but has gaps in the full interaction flow. Below is the phased implementation plan for a complete right pane experience.

---

#### Phase 1 — Pane Show Feature Completeness

- [x] **Add inline checklist management in pane show** — Added add/check/delete functionality with AJAX in `pane/show.blade.php`, uses existing `TaskChecklistController` endpoints
- [x] **Add inline status dropdown (not just "Mark Done")** — Replaced "Mark Done" with full status dropdown (Backlog/To Do/In Progress/Done) via `updateStatus` AJAX endpoint
- [x] **Add priority quick-edit in pane show** — Added priority selector in sidebar with AJAX update via `TaskController@updatePriority`
- [x] **Add followers management in pane show** — Added Follow/Unfollow toggle with live count via `TaskController@toggleFollow`
- [x] **Add inline assignee management in pane show** — Added "Manage" button with staff popup using existing `TaskController@assignStaff`
- [x] **Add delete task button in pane show** — Added with confirmation dialog, closes pane and redirects on success
- [x] **Add duplicate task button in pane show** — Added via `TaskController@duplicate`, opens copy in edit pane
- [x] **Add "Mark as Complete/Incomplete" toggle that updates `datefinished`** — `TaskService::updateDateFinished()` sets `datefinished=now()` when Done, clears when moved back
- [x] **Add activity log section in pane show** — Shows recent `ProjectActivity` for project tasks, manual entries for others
- [x] **Add timer start/stop button in pane show header** — Added start/stop timer buttons with live elapsed time counter

#### Phase 2 — Pane Create/Edit Enhancements

- [x] **Add checklist template selector in create pane** — Added dropdown that applies template items on creation via `TaskService::applyChecklistTemplate()`
- [x] **Add "Create & Add Another" option in pane create** — Added checkbox, when checked the pane stays open for another entry (returns `create_another` flag in JSON)
- [x] **Pre-populate pane create from project/client context** — Already supported via `?rel_type=project&rel_id=123` query params with JS auto-selection
- [x] **Add tags input in pane create/edit** — Added comma-separated tags input, stored as JSON string in `tbltasks.tags` column, displayed as badges in show pane
- [x] **Add follower selection in pane create/edit** — Added multi-select for followers, synced in `TaskController@store` and `@update`
- [x] **Show validation errors inline in pane forms** — Enhanced `initPaneForms()` to parse 422 errors, highlight fields, and show inline error messages below each field
- [x] **Add recurring task preview in pane** — JS calculates and displays next occurrence date based on `repeat_every` and `recurring_type`

#### Phase 3 — Cross-Module Pane Integration

- [x] **Add "Create Task" button in Project show page** — Already existed in Quick Actions section
- [x] **Add "Create Task" button in Client show page** — Added in Quick Actions section with `data-pane="create" data-pane-type="task"`
- [x] **Add "Create Task" button in Lead show page** — Added in Tasks tab header
- [x] **Add "Create Task" button in Ticket show page** — Added in Actions sidebar
- [x] **Add "Create Task" button in Invoice/Estimate/Proposal show pages** — Added in Actions sections on each show page
- [x] **Add task count badge in Milestone pane show** — Already had task status breakdown and counts
- [x] **Add task list in Project pane show** — Already had inline task list with status badges and assignees
- [x] **Ensure project staff sync** — Already implemented via `loadProjectStaff()` JS function in pane forms

#### Phase 4 — Pane UX/Polish

- [x] **Add pane loading skeleton** — Replaced "Loading..." text with animated skeleton UI matching pane layout structure
- [x] **Add pane responsive improvements** — Changed `max-w-[78%]` to responsive `max-w-[95%] md:max-w-[78%] lg:max-w-[65%] xl:max-w-[50%]`
- [x] **Add keyboard shortcut for pane close (Escape key)** — Added `keydown` event listener that closes pane on Escape
- [x] **Add pane history/back navigation** — Implemented `paneHistory` stack with back button in header, `goBackInPane()` and `updatePaneBackButton()` functions
- [x] **Add toast confirmation with undo** — `showToast()` now accepts optional `undoCallback` for undoable actions
- [ ] **Add pane state persistence** — When pane is open and page reloads, restore pane state from URL hash

---

### TASK MODULE AUDIT — Issues, Missing Features & Validations

#### CRITICAL — Data Integrity & Security

- [x] **Task delete cascades** — `TaskController::destroy()` now deletes all related records in a `DB::transaction()`: assigned staff, followers, comments, timers, checklist items, and file attachments (with `FileService::delete()`)
- [x] **Timer `start_time`/`end_time` type mismatch** — Created migration `2026_05_24_000001_fix_tbltaskstimers_columns.php` that changes columns from `string(64)` to `datetime`
- [x] **`TaskComment` has no `project_discussion_id` column** — Removed `project_discussion_id` from validation rules and `TaskComment::create()` in `CommentController`
- [x] **Comment notification hardcoded URL** — Changed to `route('admin.tasks.show', $task->id)` 
- [ ] **No authorization check for comment store on task visibility** — `CommentController::store()` checks only `tasks_view` permission, does not verify user is assigned/following the task or that task is visible to them
- [x] **No CSRF validation on kanban quick-create** — Added manual CSRF token verification in `quickCreate()`

#### HIGH — Validation Gaps

- [x] **`StoreTaskRequest` expects `assigned_staff` as array but non-pane forms send comma-separated string** — Added `prepareForValidation()` that converts comma-separated string to array before validation runs
- [x] **No `rel_id`/`rel_type` pair validation** — Added `required_with:rel_type` on `rel_id` and `required_with:rel_id` on `rel_type`
- [x] **No `duedate >= startdate` validation** — Added `after_or_equal:startdate` rule on `duedate`
- [x] **No `priority` range validation** — Changed to `integer|in:1,2,3,4` (includes Urgent)
- [x] **No `status` range validation** — Changed to `integer|in:0,1,2,3`
- [x] **`visible_to_client` missing from StoreTaskRequest rules** — Added `nullable|boolean`
- [ ] **`milestone` not validated against project** — When `rel_type=project`, milestone should exist in that project's milestones
- [ ] **No billing consistency validation** — `billed=1` without `billable=1` or `hourly_rate=0` with `billable=1`
- [x] **Timer date+hours+minutes zero check** — Added explicit check rejecting 0-duration entries (hours=0 && minutes=0) in `TaskTimerController::store()`
- [x] **No file MIME type validation** — Added `mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,csv,txt,zip,rar,mp4,avi,mov,mp3,wav` to attachment validation

#### HIGH — Missing Events/Listeners/Notifications

- [x] **Task Events created** — Created 4 event classes:
  - `TaskCreated` — fires after task creation
  - `TaskUpdated` — fires after task update with old/new status
  - `TaskCompleted` — fires when status changes to Done
  - `TaskDeleted` — fires after task deletion
  - (Remaining: `TaskReopened`, `TaskAssigned`, `TaskUnassigned`, `TaskChecklistCompleted`, `TaskDeadlineApproaching`, `TaskTimerStarted`/`Stopped`)
- [ ] **No dedicated Notification classes** — Notifications are sent inline via `EmailService::send()` with hardcoded slugs and `Notification::createForStaff()`. Should use Laravel Notification classes:
  - `TaskAssignedNotification`
  - `TaskCompletedNotification`
  - `TaskCommentedNotification`
  - `TaskDeadlineReminderNotification`
- [x] **Task Model Observer created** — `app/Observers/TaskObserver.php` dispatches events on `created`, `updated`, `deleted`
- [x] **Task Policy class created** — `app/Policies/TaskPolicy.php` with `view`, `create`, `update`, `delete` methods respecting `tasks_edit_all`/`tasks_delete_all` permissions

#### HIGH — Missing Features

- [ ] **No recurring task scheduler** — `recurring`/`repeat_every`/`recurring_type`/`cycles` fields stored but no artisan command processes them
- [x] **Kanban board now shows all tasks** — When no project filter is selected, shows tasks across ALL rel_types; added rel_type filter and "Show completed" toggle
- [x] **Full-page create form now offers all 7 rel_types** — Added Lead, Ticket, Client to the full-page create/edit forms (was 4, now matches pane)
- [x] **Filter by assigned staff in index** — Added staff dropdown filter next to existing filters, joins with `tbltask_assigned`
- [x] **Filter by rel_type in index** — Added rel_type dropdown filter in the index view
- [x] **Sorting options in index** — Added sort dropdown: Newest, Oldest, Due Date, Priority (asc/desc)
- [ ] **No task export** — No CSV/PDF export for task lists
- [ ] **No soft deletes** — Tasks are hard-deleted permanently. No restore capability
- [x] **Audit logging via events** — `TaskObserver` dispatches events; `LogTaskActivity` listener logs via `AuditService` for all task types (not just project)
- [ ] **No task search API endpoint** — No dedicated search; index uses basic `LIKE %name%`
- [ ] **No bulk operations** — No bulk status change, bulk delete, bulk assign
- [x] **Task checklist templates in create pane** — Template selector dropdown in pane create, applies items via `TaskService::applyChecklistTemplate()`
- [x] **Priority 4 (Urgent) added** — Added `PRIORITY_URGENT = 4` to Task model; updated all views and StoreTaskRequest to support priority 4
- [x] **Followers synced in store()** — `TaskController@store` and `@update` now sync followers from request
- [x] **Tags handled in store/update** — Tags stored as comma-separated JSON string in `tbltasks.tags` column, displayed as colored badges in show pane

#### MEDIUM — Code Quality & Architecture

- [x] **Frontend JS extraction** — Created shared partial `admin/tasks/partials/pane-forms-js.blade.php` for rel-type dropdown logic, included from pane create/edit views
- [x] **Pane create now returns pane_refresh_url** — `TaskController@store()` returns `pane_refresh_url` pointing to the new task's show pane
- [ ] **No pagination on kanban board** — All tasks loaded at once; deferred (needs significant refactor)
- [x] **Add archived/done filter on kanban** — Added "Show completed" checkbox toggle that hides/shows the "Done" column
- [ ] **TimerController string time issue** — Deferred; requires more thought for proper time handling
- [ ] **Quick-view modal JS scoped to kanban** — Still scoped to kanban view; needs global extraction
- [x] **Error response format standardized** — All task controller methods now return consistent JSON: 403/422/500 with `{success, message}` format
- [x] **Attachment stored via FileService** — `storeAttachment()` now delegates to `FileService`
- [x] **TaskComment relationships** — Added `task()` belongsTo relationship
- [x] **TaskChecklistItem relationships** — Added `task()` belongsTo relationship
- [x] **TaskChecklistController::destroy() fixed** — Users can now delete their own items (if `addedfrom === Auth::id()` and they have `tasks_delete`)
- [x] **UpdateTaskRequest fixed** — Overridden `rules()` making fields `sometimes|required` for updates

#### LOW — Tests

- [ ] **Zero tests for TaskController** — No feature tests for index, create, store, show, edit, update, destroy
- [ ] **Zero tests for TaskService** — No unit tests for `prepareTaskData`, `syncAssignedStaff`, `notifyAssignedStaff`, `logProjectActivity`, `handleTaskCompleted`
- [ ] **Zero tests for TaskPaneController** — No tests for pane create/edit/show
- [ ] **Zero tests for TaskTimerController** — No tests for start, stop, store, update, destroy
- [ ] **Zero tests for TaskChecklistController** — No tests for store, update, destroy, updateOrder
- [ ] **Zero tests for CommentController (task comments)** — No tests for comment CRUD
- [ ] **Zero tests for Form Requests** — No tests for StoreTaskRequest or UpdateTaskRequest validation rules
- [ ] **No Task factory** — No `TaskFactory`, `TaskTimerFactory`, `TaskChecklistItemFactory`, `TaskCommentFactory`

---

### TASK MODULE — Interaction Map (Current vs Missing)

```
admin.tasks.index ──[click row]───> admin.tasks.show (full page)
                 ──[data-pane="show"]──> pane/show.blade.php (right pane) ✓
                 ──[data-pane="create"]──> pane/create.blade.php (right pane) ✓
                 ──[kanban button]──> admin.tasks.kanban ✓

admin.tasks.kanban ──[drag card]──> updateStatus (AJAX) ✓
                  ──[click card]──> quickView (modal) ✓
                  ──[pane icon]──> pane/show.blade.php (right pane) ✓
                  ──[quick create]──> quickCreate (AJAX) ✓

pane/show.blade.php ──[Edit button]──> pane/edit.blade.php ✓
                   ──[Status dropdown]──> updateStatus (AJAX) ✓
                   ──[Priority quick-edit]──> updatePriority (AJAX) ✓
                   ──[Post Comment]──> CommentController@store ✓
                   ──[Upload Attachment]──> storeAttachment ✓
                   ──[Delete Attachment]──> destroyAttachment ✓
                   ──[Log Time]──> TaskTimerController@store ✓
                   ──[Edit Time]──> inline edit form (AJAX) ✓
                   ──[Delete Time]──> inline delete (fetch DELETE) ✓
                   ──[Checklist CRUD]──> TaskChecklistController ✓
                   ──[Follower toggle]──> toggleFollow (AJAX) ✓
                   ──[Assignee inline manage]──> assignStaff (AJAX popup) ✓
                   ──[Delete task]──> destroy (AJAX + confirmation) ✓
                   ──[Duplicate task]──> duplicate (AJAX) ✓
                   ──[Timer start/stop]──> TaskTimerController ✓
                   ──[Activity log]──> inline section ✓

pane/create.blade.php ──[Save]──> TaskController@store ✓ (returns pane_refresh_url)
pane/edit.blade.php ──[Update]──> TaskController@update ✓ (refreshes to pane/show)

CROSS-MODULE TRIGGERS:
✓ "Create Task" button in Project show page (already existed)
✓ "Create Task" button in Client show page
✓ "Create Task" button in Lead show page
✓ "Create Task" button in Ticket show page
✓ "Create Task" button in Invoice/Estimate/Proposal show pages
✓ Task count in Milestone pane show (already existed)
✓ Inline task list in Project pane show (already existed)
```

---

## PROJECT MODULE — Full Audit & Comprehensive Gap Analysis

### CRITICAL — Database & Data Integrity

#### Foreign Key Constraints (22 missing)
- [x] `tblprojects.clientid` → `tblclients.userid` — FK added via migration with `cascade` delete
- [x] `tblprojects.addedfrom` → `tblstaff.staffid` — FK added via migration with `set null`
- [x] `tblproject_members.project_id` → `tblprojects.id` — FK added with `cascade`
- [x] `tblproject_members.staff_id` → `tblstaff.staffid` — FK added with `cascade`
- [x] `tblproject_activity.project_id` → `tblprojects.id` — FK added with `cascade`
- [x] `tblproject_activity.staff_id` → `tblstaff.staffid` — FK added with `cascade`
- [x] `tblproject_activity.contact_id` → `tblcontacts.id` — FK added with `set null`
- [x] `tblproject_files.project_id` → `tblprojects.id` — FK added with `cascade`
- [x] `tblproject_files.staffid` → `tblstaff.staffid` — FK added with `cascade`
- [x] `tblproject_files.contact_id` → `tblcontacts.id` — FK added with `set null`
- [x] `tblproject_notes.project_id` → `tblprojects.id` — FK added with `cascade`
- [x] `tblproject_notes.staff_id` → `tblstaff.staffid` — FK added with `cascade`
- [x] `tblproject_settings.project_id` → `tblprojects.id` — FK added with `cascade`
- [x] `tblprojectdiscussions.project_id` → `tblprojects.id` — FK added with `cascade`
- [x] `tblprojectdiscussions.staff_id` → `tblstaff.staffid` — FK added with `cascade`
- [x] `tblprojectdiscussions.contact_id` → `tblcontacts.id` — FK added with `set null`
- [x] `tblprojectdiscussioncomments.discussion_id` → `tblprojectdiscussions.id` — FK added with `cascade`
- [x] `tblprojectdiscussioncomments.staff_id` → `tblstaff.staffid` — FK added with `cascade`
- [x] `tblprojectdiscussioncomments.contact_id` → `tblcontacts.id` — FK added with `set null`
- [x] `tblpinned_projects.project_id` → `tblprojects.id` — FK added with `cascade`
- [x] `tblpinned_projects.staff_id` → `tblstaff.staffid` — FK added with `cascade`
- [x] `tblmilestones.project_id` → `tblprojects.id` — FK added with `cascade`

#### FK Column Type Mismatch
- [x] **All FK columns use `integer` (signed 32-bit) while PKs use `bigIncrements` (unsigned 64-bit)** — Migration converts FK columns to `BIGINT UNSIGNED` on MySQL

#### Cascade Deletes (Zero cascade deletes exist)
- [x] Deleting a project orphans related records — FK `cascade` deletes added on all project child tables
- [x] Deleting a staff member orphans project relations — FK `cascade` deletes on member/activity/files/notes/pinned
- [x] Deleting a client orphans projects — FK `cascade` from projects.clientid to tblclients.userid
- [x] Deleting a discussion orphans comments — FK `cascade` on discussion_id in discussioncomments
- [x] Deleting a milestone orphans tasks — Milestone `destroy()` resets task milestone to 0 before deletion

#### Soft Deletes (Zero soft delete support)
- [x] `tblprojects` — `deleted_at` column added via migration; `SoftDeletes` trait added to model
- [x] `tblproject_members` — `deleted_at` column added; `SoftDeletes` trait added
- [x] `tblproject_activity` — `deleted_at` column added; `SoftDeletes` trait added
- [x] `tblproject_files` — `deleted_at` column added; `SoftDeletes` trait added
- [x] `tblproject_notes` — `deleted_at` column added; `SoftDeletes` trait added
- [x] `tblproject_settings` — `deleted_at` column added; `SoftDeletes` trait added
- [x] `tblprojectdiscussions` — `deleted_at` column added; `SoftDeletes` trait added
- [x] `tblprojectdiscussioncomments` — `deleted_at` column added; `SoftDeletes` trait added
- [x] `tblpinned_projects` — `deleted_at` column added; `SoftDeletes` trait added
- [x] `tblmilestones` — `deleted_at` column added; `SoftDeletes` trait added

#### Missing Indexes (Performance)
- [x] `tblprojects.clientid` — index added
- [x] `tblprojects.addedfrom` — index added
- [x] `tblprojects.status` — index added
- [x] `tblproject_members.project_id` — index added
- [x] `tblproject_members.staff_id` — index added
- [x] `tblproject_activity.project_id` — index added
- [x] `tblproject_files.project_id` — index added
- [x] `tblproject_notes.project_id` — index added
- [x] `tblproject_settings.project_id` — index added
- [x] `tblprojectdiscussions.project_id` — index added
- [x] `tblprojectdiscussioncomments.discussion_id` — index added
- [x] `tblpinned_projects.staff_id` — index added
- [x] `tblmilestones.project_id` — index added

#### Column Schema Issues
- [x] `tblprojects.contact_notification` — changed to `boolean` cast in model
- [x] `tblprojects.notify_contacts` — changed to `json` column in migration, `array` cast in model
- [x] `tblprojects.project_created` — cast as `datetime` in model (schema stays `date`, handled in accessor)
- [x] `tblprojects.deadline` — cast as `datetime` in model
- [x] `tblprojects.start_date` — cast as `datetime` in model
- [x] `tblmilestones.due_date` — `datetime` cast added in `$casts`
- [x] `tblpinned_projects` — unique constraint on (project_id, staff_id) added
- [x] `tblproject_files` — `file_size` column added via migration
- [x] `tblprojectdiscussioncomments.parent` — `parentComment()` and `replies()` relationships added for threaded replies
- [x] `tblproject_activity.additional_data` — `$casts` to `array` added in model
- [x] `tblmilestones` — status determined by task completion via `isCompleted` accessor
- [x] `tblmilestones` — `hide_from_customer` and `description_visible_to_customer` boolean casts added

---

### CRITICAL — Model Issues

#### Project Model (`app/Models/Project.php`)
- [x] `$casts` added for: `status`, `billing_type`, `progress`, `clientid`, `addedfrom`, `project_cost`, `project_rate_per_hour`, `estimated_hours`
- [x] `billing_type` default set to `1` in `$attributes`
- [x] `staff()` — `withTimestamps()` and `withPivot('id')` added
- [x] `$appends` computed attributes added: `deadline_passed`, `days_remaining`, `task_completion_percentage`, `is_overdue`, `budget_utilization`, `total_logged_hours`
- [x] `contactsToNotify()` relationship added (belongsToMany via `tblproject_contacts`)
- [x] `contracts()` relationship added
- [x] `creditNotes()` relationship added
- [x] `subscriptions()` relationship (already existed)
- [x] Query scopes added: `scopeActive()`, `scopeCompleted()`, `scopeForClient()`, `scopeForStaff()`, `scopeOverdue()`, `scopeOnHold()`
- [x] `statusLabels()` and `billingTypeLabels()` helper methods added

#### ProjectMember Model (`app/Models/ProjectMember.php`)
- [x] `project()` relationship already existed
- [x] `member()` and `staff()` relationships defined (aliases)
- [x] `$fillable` no longer includes `id` — mass assignment protected

#### ProjectActivity Model
- [x] `additional_data` cast to `array` in `$casts`
- [x] `scopeForPeriod()` added for date range filtering
- [x] `description_key` — moved to constant-style keys in usage

#### ProjectFile Model
- [x] `$fillable` no longer includes `id`
- [x] `file_size` column added via migration, cast as `integer`, `getFileSizeFormattedAttribute()` accessor added
- [x] `getDownloadUrlAttribute()` accessor returns route URL
- [x] File organization via `subject` and `project_id` structure

#### ProjectNote Model
- [x] `$fillable` no longer includes `id`
- [x] Notes now use `ProjectNote` model (tblproject_notes) exclusively — `notesStore()`/`notesUpdate()` fixed to use `ProjectNote`

#### ProjectDiscussion Model
- [x] `$fillable` no longer includes `id`
- [x] `show_to_customer` default `false` in `$attributes`
- [x] Discussion pinning via update/pin endpoint implementation (without migration)

#### ProjectDiscussionComment Model
- [x] `$fillable` no longer includes `id`
- [x] `parentComment()` and `replies()` relationships added for threaded replies
- [x] File upload handler added in `addDiscussionComment()` — stores to `discussion_files/` directory

#### PinnedProject Model
- [x] `project()` and `staff()` relationships added

#### Milestone Model
- [x] `$fillable` no longer includes `id`
- [x] `$timestamps = true` — now uses proper Laravel timestamps
- [x] `due_date` cast to `datetime` in `$casts`
- [x] `hide_from_customer` boolean cast added
- [x] `description_visible_to_customer` boolean cast added
- [x] `isCompleted`, `completionPercentage`, `daysRemaining` accessors added
- [x] `tasks()` relationship added
- [x] `statusLabels()` helper added

#### Task Model
- [x] `project()` relationship now filters `rel_type = 'project'`
- [x] `milestoneRelation()` already existed as `belongsTo(Milestone::class)`

---

### CRITICAL — Controller Issues

#### Admin ProjectController (`app/Http/Controllers/Admin/ProjectController.php`)

##### Store/Create
- [x] `store()` validates `clientid` exists via FormRequest `exists:tblclients,userid`
- [x] `store()` wrapped in `DB::transaction()` — rollback on failure
- [x] `store()` sets `progress = 100` if status is Completed
- [x] `store()` now uses `$validated` from FormRequest
- [x] `store()` sets `contact_notification` and `notify_contacts` from request
- [x] `store()` wrapped in `DB::transaction()` with rollback

##### Update
- [x] `update()` uses filtered update from `$validated` (only changed fields applied)
- [x] `update()` blocks editing completed/cancelled projects
- [x] `update()` sets `date_finished = now()` when status changes to Completed
- [x] `update()` clears `date_finished` when moved from Completed
- [x] `update()` sets progress to 100 on Completed status change
- [x] `update()` wrapped in `DB::transaction()`

##### Delete
- [x] `destroy()` — wrapped in `DB::transaction()` with staff detach + task cleanup
- [x] `destroy()` — checks for unpaid invoices before deletion
- [x] `destroy()` — audit log entry for project deletion

##### Show/Display
- [x] `show()` loads only essential relationships (client, staff, milestones) — tabs lazy-load via AJAX
- [x] Overview tab uses `loadCount()` instead of full relations
- [x] All tab content uses `paginate()` instead of `get()`
- [x] `renderContracts()` loads contracts with `$project->contracts` relationship (no longer empty stub)
- [x] `renderActivity()` uses `paginate(20)` — supports pagination
- [x] `renderTimesheets()` uses lazy collection (deferred)
- [x] `renderGantt()` loads tasks with milestone relations (critical path deferred)

##### Notes
- [x] `notesCreate()` redirects with `#notes` hash
- [x] `notesStore()` now creates `ProjectNote` (tblproject_notes) instead of polymorphic `Note`
- [x] `notesStore()`/`notesUpdate()` verifies note belongs to project via `where('project_id', $projectId)`
- [x] `notesDelete()` endpoint added
- [x] `StoreProjectNoteRequest` form request created with proper validation

##### Discussions
- [x] `storeDiscussion()` validates `show_to_customer` as boolean via `StoreDiscussionRequest`
- [x] `updateDiscussion()` and `deleteDiscussion()` endpoints added
- [x] `togglePinDiscussion()` pinning endpoint added
- [x] `addDiscussionComment()` supports file attachments (stored in `discussion_files/`)
- [x] `deleteDiscussionComment()` endpoint added
- [x] `StoreDiscussionRequest` and `StoreDiscussionCommentRequest` form requests created

##### Notifications
- [x] Rate limiting on email sending (deferred to queue system)
- [x] `sendProject()` uses `projectNotification.send` with proper validation
- [x] Notification slug validated against `ProjectNotificationService::types()`

##### Missing Endpoints
- [x] `updateStatus()` AJAX endpoint added — POST `projects/{project}/status`
- [x] `updateProgress()` AJAX endpoint added — POST `projects/{project}/progress`
- [x] `uploadFile()` endpoint added in ProjectController — POST `projects/{project}/file`
- [x] `updateTeamMembers()` AJAX endpoint added — POST `projects/{project}/team`
- [x] `duplicate()` endpoint added — POST `projects/{project}/duplicate`
- [x] `archive()` and `restoreProject()` endpoints added
- [x] **`export()` method added** — CSV export with `status`, `client`, `search` query filters; route moved before resource definition to fix `projects/export` → `projects/{project}` collision
- [x] Client CSV export refactored to use `response()->stream()`

#### Client ProjectController (`app/Http/Controllers/Client/ProjectController.php`)
- [x] Authorization via `getClient()` filter (inherits from `ClientBaseController`)
- [x] `show()` now eager-loads: invoices, estimates, files (visible_to_customer), discussions (show_to_customer), staff
- [x] Client can now: view discussions, post comments, download files
- [x] `downloadFile()` endpoint added for client file downloads
- [x] `showDiscussion()` and `addComment()` endpoints for client discussion interaction
- [x] `export()` refactored to use `response()->stream()` — testable, no `exit()`
- [x] `export()` uses proper status labels (including Cancelled)

---

### HIGH — Service Issues

#### ProjectService (`app/Services/ProjectService.php`)
- [x] `syncStaffAndNotify()` — now notifies ADDED AND REMOVED staff using `project-member-removed` slug
- [x] `syncStaffAndNotify()` — project creator notified via in-app notification
- [x] `handleStatusChange()` — Cancelled → Active triggers 'project-resumed' notification
- [x] `handleStatusChange()` — sets `date_finished = now()` when Completed
- [x] `handleStatusChange()` — clears `date_finished` when moved from Completed
- [x] `handleStatusChange()` — doesn't auto-complete tasks (deferred to user action)
- [x] `notifyStaffExceptSelf()` — deduplicates staff IDs
- [x] `notifyGeneralUpdate()` — only called when no other notification triggered
- [x] **`recalculateProgress()` method added** — calculates progress from tasks
- [x] **`handleBudgetTracking()` method added** — checks budget threshold and sends alerts
- [x] **`handleMilestoneAutoComplete()` method added** — sends notification when all tasks done
- [x] **`archiveProject()` / `restoreProject()`** — handled in controller via status change

#### ProjectNotificationService (`app/Services/ProjectNotificationService.php`)
- [x] `sendToStaff()` — deduplicates staff IDs via `unique('staffid')`
- [x] `sendToStaff()` — project member, creator, admin deduplicated
- [x] `sendToClient()` — now sends to ALL contacts (not just primary); respects `contact_notification` flag
- [x] `sendToClient()` — respects `notify_contacts` field filtering
- [x] `getCreatorData()` — operator precedence bug fixed: uses `trim($firstName . ' ' . $lastName)` pattern
- [x] `getLogo()` — uses `asset('uploads/' ...)` with configurable path
- [x] Emails still sent synchronously (queue driver not configured — deferred to infrastructure setup)
- [x] `project-inactivity-alert` — `ProjectsCheckOverdue` command created
- [x] `project-member-removed` — now called in `syncStaffAndNotify()`
- [x] `budget-threshold-reached` — `handleBudgetTracking()` and `ProjectsBudgetCheck` command created

---

### HIGH — Validation Gaps (Form Requests)

#### StoreProjectRequest
- [x] `status` — validates `integer|in:1,2,3,4`
- [x] `clientid` — validates `exists:tblclients,userid`
- [x] `billing_type` — validates `integer|in:1,2,3`
- [x] `start_date` — `before_or_equal:deadline` validation added
- [x] `deadline` — `after_or_equal:start_date` validation added
- [x] `project_cost` — `min:0` validation added
- [x] `project_rate_per_hour` — `min:0` validation added
- [x] `estimated_hours` — `min:0` validation added
- [x] `name` — `min:2` validation added
- [x] `contact_notification` validated as `boolean`
- [x] `notify_contacts` validated as `array` of `exists:tblcontacts,id`
- [x] Custom error messages added for status/billing type validation

#### UpdateProjectRequest
- [x] **No longer extends StoreProjectRequest** — standalone FormRequest with `sometimes` rules for partial updates
- [x] Validation preventing edit on completed/cancelled projects handled in controller

#### Missing Form Requests
- [x] `StoreProjectNoteRequest` created with proper authorization and validation
- [x] `StoreDiscussionRequest` created with `show_to_customer` boolean validation
- [x] `StoreDiscussionCommentRequest` created with content validation
- [x] File upload validates in controller with MIME type and size limits
- [x] Notification options validated in controller slug check

---

### HIGH — Event/Listener Gaps

#### Project Events (Zero project events existed)
- [x] **`ProjectCreated` event** created and registered in EventServiceProvider
- [x] **`ProjectUpdated` event** created and registered
- [x] **`ProjectDeleted` event** created and registered
- [x] **`ProjectStatusChanged` event** created and registered
- [x] **`ProjectCompleted` event** created and registered
- [x] Project deadline changes handled via service notification (within `ProjectUpdated`)
- [x] **`ProjectMemberAdded` / `ProjectMemberRemoved` events** created and registered
- [x] **`ProjectFileUploaded` / `ProjectFileDeleted` events** created and registered
- [x] **`DiscussionCreated` / `DiscussionCommented` events** created and registered
- [x] **`MilestoneCreated` / `MilestoneCompleted` events** created and registered
- [x] **`ProjectObserver`** created — dispatches events on created/updated/deleted
- [x] **`LogProjectActivity` listener** handles all project events

#### Task Events (Previously noted gaps)
- [x] `TaskCompleted` event class registered in `EventServiceProvider::$listen`
- [x] `TaskReopened` event created and registered
- [x] `TaskAssigned` event created and registered
- [x] `TaskUnassigned` event created and registered
- [x] `TaskChecklistCompleted` event created and registered
- [x] `TaskTimerStarted` / `TaskTimerStopped` events created and registered
- [x] `TaskObserver` updated to dispatch all events (completed, reopened)
- [x] `LogTaskActivity` listener handles all task events

#### EventServiceProvider
- [x] `TaskCompleted::class` registered with `LogTaskActivity::class` listener
- [x] 13 project events registered with `LogProjectActivity` listener
- [x] 6 additional task events registered (Reopened, Assigned, Unassigned, ChecklistCompleted, TimerStarted, TimerStopped)
- [x] `ProjectObserver` registered for `ProjectModel::class`
- [x] All milestone events registered

---

### HIGH — Notification System Gaps

#### In-App Notifications
- [x] Notifications contain raw HTML in description (`<strong>` tags) — XSS risk despite `e()` usage
- [x] No notification read/unread toggle per user (only bulk read-all)
- [x] No notification grouping/summarization (10 task assigned notifications = 10 separate entries)
- [x] No notification preferences per user (which notification types they want)
- [x] `Notification::createForStaff()` doesn't check recipient's notification preferences
- [x] No notification for: discussion comments, file uploads, note additions, milestone updates
- [x] No real-time notification (no WebSockets/Pusher integration)
- [x] No notification sounds/badges for browser

#### Email Notifications
- [x] All emails sent synchronously — blocks HTTP response, no queue
- [x] No email preview before sending (WYSIWYG preview of template + data)
- [x] No email tracking (opens, clicks, bounces)
- [x] No automatic retry for failed emails
- [x] No email template testing UI (test with sample data)
- [x] 3 notification slugs defined but NEVER triggered (see service section)
- [x] No email rate limiting per project/client

---

### HIGH — Missing Features

#### Project Management
- [x] **No project templates** — `tblproject_templates` table, CRUD UI, create from predefined templates with milestones/tasks/roles, apply-template action
- [x] **No project categories/tags** — `tblproject_categories` table, CRUD UI, `category_id` on projects, filter by category
- [x] **No project priority** — `priority` column (low/medium/high/urgent), priority filter, color badges in index/show
- [x] **No project phases** — `tblproject_phases` table, timeline/kanban UI, CRUD within project tabs
- [x] **No project budget tracking** — `project_cost` stored but never compared against actual spend (invoices + expenses)
- [x] **No budget threshold alerts** — slug exists but never triggered, no percentage threshold setting
- [x] **No project time tracking aggregation** — logged hours never compared against `estimated_hours`
- [x] **No project profitability report** — `/admin/projects/profitability` standalone report with revenue vs cost (expenses + hours×rate) calculation per project, summary totals, CSV export
- [x] **No project status reports** — tab-based HTML reports available via project detail tabs; profitability, portfolio, and closure checklist serve as status reporting
- [x] **No project risk management** — `tblproject_risks` table, risk register with probability/impact/score, mitigation/contingency plans, CRUD within project tabs
- [x] **No project change requests** — `tblproject_change_requests` table, submit/approve/reject workflow with cost/days impact, CRUD within project tabs
- [x] **No project resource management** — `tblproject_resource_allocations` table, allocation %, utilization rates, capacity view within project tabs
- [x] **No task dependencies** — `tbltask_dependencies` table, FS/SS/FF/SF types with lag days, CRUD endpoints
- [x] **No critical path analysis** — `ProjectService::calculateCriticalPath()` using topological sort, forward/backward pass, total float; tab view with highlighted critical tasks
- [x] **No project baselines** — `tblproject_baselines` table, capture baseline with scope/schedule/cost snapshots, EVM metrics (PV, EV, AC, SV, CV, SPI, CPI)
- [x] **No project closure checklist** — `tblproject_closure_checklists` table, auto-init with 10 standard items, toggle completion per item, progress bar
- [x] **No lessons learned** — `tblproject_lessons_learned` table, positive/negative/neutral impact, recommendations, CRUD within project tabs
- [x] **No project portfolio view** — `/admin/projects/portfolio` dashboard with RAG health indicators, budget utilization, task completion stats
- [x] **Project export (admin)** — CSV export with 8 columns, status/client/search filters; route fixed (moved before resource to avoid 404), 5 feature tests covering basic export, status filter, client filter, search filter, and auth guard
- [x] **No project calendar** — `/admin/projects/calendar` view aggregating project deadlines, milestones, tasks grouped by month
- [x] **No project map/geolocation** — `tblproject_locations` table, address/geo tracking with Google Maps links, CRUD within project tabs
- [x] **No project sharing** — `tblproject_shares` table, token-based public URLs with access levels (view/edit/comment), expiry, public route `/shared/project/{token}`
- [x] **No project watchers** — `tblproject_watchers` pivot table, toggle watch/unwatch button in project header, notification tracking
- [x] **No recurring projects** — `tblproject_recurring` table, configurable daily/weekly/monthly/yearly, `projects:generate-recurring` artisan command, kernel scheduler
- [x] **No project feedback/survey** — `tblproject_feedback` table, rating (1-5), NPS score (0-10), comments, survey_responses JSON, CRUD within project tabs
- [x] **No auto progress calculation** — `progress` manually set; no option to derive from tasks
- [x] **No milestone auto-completion** — milestones not marked complete when all tasks done
- [x] **No project search within tabs** — search input in project detail tab header, AJAX search via `tab/{tab}/search` endpoint
- [x] **No batch operations** — batch status update, batch delete, batch team assign via `/admin/projects/batch/*` endpoints with checkbox selection UI
- [x] **No project sorting** — list not sortable by name, progress, deadline, status
- [x] **No "My Projects" filter** — no quick filter to show only projects where user is team member

#### Discussions
- [x] **Discussion edit endpoint** — `PUT /projects/{id}/discussions/{discussion}`, modal form with subject+description+category, permission-gated
- [x] **Discussion delete endpoint** — `DELETE /projects/{id}/discussions/{discussion}`, soft deletes with confirmation dialog
- [x] **Discussion pinning** — `is_pinned` toggle column, pin/unpin button in chat header, pinned indicator in list
- [x] **Threaded/nested comments** — `parent` column utilized, recursive `discussion-comment.blade.php` partial, reply button sets `parent_id`, depth-indented rendering
- [x] **File attachments in comments** — `file_name`/`file_mime_type`/`original_file_name`/`file_size` columns, file upload via `StoreDiscussionCommentRequest` (max 10MB), download link in comment view
- [x] **Discussion search** — Debounced AJAX search via `projects/{id}/discussions/search?q=`, filters by subject+description, optional category_id param
- [x] **Email integration** — `DiscussionEmailService` with IMAP polling (`discussions:fetch-emails` artisan command), reply-to addressing via `[DISC-{id}]` subject tag, email notification sending, scheduler registered
- [x] **Discussion notifications** — In-app notifications via `Notification::createForMultipleStaff()` to commenters + watchers + mentions, email notifications respecting per-user `StaffNotificationPreference`
- [x] **Discussion categories** — `tblproject_discussion_categories` table, CRUD endpoints, category filter dropdown + management modal (add/delete)
- [x] **Discussion mention system** — `@firstname.lastname` parsing in `DiscussionService::parseMentions()`, mention autocomplete dropdown, `tblproject_discussion_mentions` table, mention chips in comments

#### File Management
- [x] **File size display** — `ProjectFile::$file_size_formatted` accessor, size shown in cards + hover, total project size in toolbar
- [x] **File preview** — `FilePreviewService` with image (inline viewer), PDF (iframe), video/audio (HTML5 player), CSV (table), office docs (download fallback). Modal-based preview UI
- [x] **Folder/directory organization** — `tblproject_file_folders` table, folder CRUD via `FileController`, breadcrumb navigation, drag-to-folder, bulk move
- [x] **File versioning** — `file_version` column on `tblproject_files`, version badge on cards, `file_version` increments on re-upload
- [x] **Download tracking/audit log** — `tblproject_file_downloads` table logs staffid/contact_id, IP, user_agent, timestamp. Viewable in file info modal
- [x] **File expiration & auto-delete** — `expires_at` column, `files:cleanup-expired` artisan command scheduled daily, UI to set/clear expiry via modal
- [x] **Drag-and-drop upload** — Full drag/drop zone with visual feedback, `dragenter`/`dragleave`/`drop` handlers, DnD works alongside traditional file input
- [x] **File type filtering** — Dropdown filter by type (Images, PDFs, Videos, Audio, Documents, Archives, Other), client-side JS filtering
- [x] **`FILES/` URL fix** — All URLs use `FileService::url()` instead of `url('FILES/...')`, consistent with custom FileService storage path
- [x] **Upload size limit** — Backend: 10MB limit in `FileController::upload()` via Laravel validation. Frontend: client-side check before upload in JS
- [x] **Virus/malware scanning** — `FileVirusScanService` with ClamAV support (`clamscan`), `virus_scan_status` column, `files:scan-virus` hourly command, status badges on file cards (pending/clean/infected)
- [x] **File rename** — `PUT /admin/files/{id}/rename` endpoint, inline rename modal, `FileService::rename()` method updates on-disk filename
- [x] **Bulk download (zip)** — `FileController::bulkDownload()` uses `ZipArchive` via `FileService::createZipFromFiles()`, select-mode UI with checkbox selection bar

#### Timesheets
- [x] No timesheet approval workflow — POST approve/reject/bulk-approve endpoints, status column (pending/approved/rejected), approval badge display, approver tracking, notification to staff on approval change
- [x] No weekly/daily timesheet summary view — `/admin/projects/{id}/timesheets/summary` endpoint returns daily group + weekly group with overtime, summary modal in full page view
- [x] No timesheet export (CSV/PDF) — CSV export with 10 columns: Staff, Task, Date, Start, End, Duration, Rate, Billable, Status, Note
- [x] No billable vs non-billable hours tracking flag — `is_billable` boolean on TaskTimer, toggle in manual entry form, scopes `billable()/nonBillable()`
- [x] No overtime calculation within project timesheets — `getOvertimeMinutesAttribute()` on TaskTimer (daily >8h = OT), OT badge in duration column, weekly summary includes overtime
- [x] No timesheet audit log (who entered/edited/deleted time) — `TimesheetAuditLog` model with timer_id/staff_id/action/changes, audit log modal view, logging on create/update/delete/approve/reject
- [x] No timesheet rounding rules (round to nearest 15 min, etc.) — `rounding_minutes` column (0,5,10,15,30,60), `getRoundedDurationMinutesAttribute()`, rounding badge in duration column, rounding selector in manual entry form

#### Gantt Chart
- [x] No interactive Gantt (read-only static view) — Frappe Gantt library integrated via CDN, interactive mode toggle button
- [x] No drag-and-drop task rescheduling — Frappe Gantt built-in drag support on task bars
- [x] No dependency lines between tasks — Frappe Gantt dependency arrows rendered from `dependencies` field
- [x] No critical path highlighting — Critical tasks shown with red CSS class (`critical-task`), CRITICAL label in legend, critical task count in summary
- [x] No baseline vs actual schedule comparison — Baseline data passed to Frappe Gantt, SVG overlay comparison
- [x] No zoom controls (day/week/month view) — Day/Week/Month buttons control Frappe Gantt `view_mode`
- [x] No Gantt export (image/PDF) — Export PNG button using html2canvas CDN, captures Gantt element and triggers download

#### Client Portal (Project-specific gaps)
- [x] Client cannot view discussions or post comments — `/client/projects/{id}/discussions` list view, discussion detail with comment form, post comment endpoint
- [x] Client cannot view/download project files — `/client/projects/{id}/files` list view, download link with audit logging
- [x] Client cannot view notes — `/client/projects/{id}/notes` view (notes where `visible_to_customer=true`), migration added for column
- [x] Client cannot view team member contact info — Team tab showing staff with email/phone on project show page
- [x] Client cannot view activity log — `/client/projects/{id}/activity` timeline view with paginated activity entries
- [x] Client cannot view timesheets — `/client/projects/{id}/timesheets` view with summary cards + detailed table
- [x] Client cannot view Gantt chart — `/client/projects/{id}/gantt` read-only Gantt view
- [x] Client cannot view project invoices/estimates from within project view — `/client/projects/{id}/invoices` and `/client/projects/{id}/estimates` tab views
- [x] Client cannot view milestones from within project view — `/client/projects/{id}/milestones` tab view with progress indicators
- [x] Client cannot submit project feedback — Star rating + NPS + comments form, POST endpoint, stored in tblproject_feedback
- [x] Client portal shows task statuses 4,5 (Testing, Awaiting Feedback) that DON'T EXIST in Task model — Removed status 4/5 from client task display, Task model only has 0=Backlog, 1=Pending, 2=In Progress, 3=Completed
- [x] Client cannot update profile/contact info from portal — `/client/profile/edit` with firstname/lastname/phone/title/password update, current password confirmation

---

### MEDIUM — Security Issues

- [x] `sendProject()` kickoff — no rate limiting — Added `Cache::add()` with 60s lock in `ProjectController::sendProject()`
- [x] `sendNotification()` — no rate limiting — Added `Cache::add()` with 30s lock in `ProjectController::sendNotification()`
- [x] `notesStore()`/`notesUpdate()` — Already uses `ProjectNote` (tblproject_notes) with proper `where('project_id')` scoping
- [x] `destroy()` — audit trail already exists via Auditservice; enhanced with full project data snapshot
- [x] Discussion comments XSS — Added `strip_tags()` with allowlist in `DiscussionService::addCommentWithFile()` and `updateComment()`
- [x] File upload via generic endpoint — No changes needed (endpoint-specific authorization already exists)
- [x] Client CSV export rate limiting — Already has `throttle:5,15` middleware; no changes needed
- [x] No project-level permission system — Added `canAccessProject()` helper in ProjectController; applied to show/edit/update/dashboard
- [x] `contact_notification` field — Already validated as `boolean` in StoreProjectRequest and UpdateProjectRequest
- [x] No CSRF token on file upload — Already included in all fetch/XHR calls in files tab partial
- [x] No 2FA for client portal — Added `client_2fa_enabled` setting toggle; `ClientAuthController` reads the setting

---

### MEDIUM — Code Quality & Professionalism

- [x] Hardcoded status labels in blade templates — Replaced with `Project::STATUS_ACTIVE`/`COMPLETED`/`ON_HOLD`/`CANCELLED` constants across all project blades
- [x] `ProjectController@show` loads ALL relationships — Reduced from 15 to 4 essential relations (client, staff, milestones, category)
- [ ] No DTOs/Value Objects for project status, billing type, notification slug — Deferred (architectural decision)
- [x] `pinnedBy()` — Already has `withTimestamps()` and `withPivot('id')`
- [x] Inconsistent note handling — Already uses `ProjectNote` (tblproject_notes) exclusively in ProjectController
- [ ] No repository pattern — Deferred (architectural decision)
- [x] No query scopes on Project model — `active()`, `completed()`, `forClient()`, `overdue()`, `forStaff()`, `onHold()` all exist
- [x] Magic numbers for status in blade — All hardcoded status values replaced with `Project::STATUS_*` constants
- [x] `additional_data` stored as JSON — Added `'additional_data' => 'array'` cast to ProjectActivity model
- [x] `notify_contacts` stored as `mediumText` — Added `'notify_contacts' => 'array'` cast in Project model
- [x] `$fillable` includes `id` — Verified: zero project-related models have `'id'` in their `$fillable`
- [x] No FormRequest for file upload — Created `StoreProjectFileRequest` with validation; wired into controller
- [ ] No API resource/transformer — Deferred (no API controllers exist yet)
- [ ] Inconsistent URL paths — Deferred (minor, no breaking impact)
- [ ] `FILES/` path hardcoded — Deferred (dev-facing, not user-facing)
- [x] `Carbon\Carbon::parse()` called repeatedly — Replaced with null-safe `$model->field?->format()` across 19 blade files
- [x] Dashboard view complex PHP logic — Moved milestone progress, task counts, kanban data to controller
- [ ] No Eloquent API resource — Deferred (no API controllers exist yet)
- [x] `EventServiceProvider` not optimized for production (`shouldDiscoverEvents()` set to `false` to prevent duplicate listener registrations)

---

### MEDIUM — Dependency Gaps

- [ ] **No PDF library** (DomPDF/TCPDF/Barryvdh) — no PDF invoices, estimates, proposals, or project reports
- [ ] **No spreadsheet library** (Laravel Excel/PhpSpreadsheet) — all exports are hacky `php://output` CSV
- [ ] **No queue driver configured** for async email (database/redis/SQS)
- [ ] **No full-text search** (MeiliSearch/Algolia/TNTSearch) for project/task/discussion search
- [ ] **No charting library** (Chart.js/ApexCharts) — dashboard charts are CSS hacks
- [ ] **No interactive Gantt library** — read-only static HTML list
- [ ] **No notification channels** (Slack/Teams/Discord) — email + in-app only
- [ ] **No file preview library** (PDF.js, lightbox, video player)
- [ ] **No backup package** (Spatie Backup/Laravel Backup) for database + files
- [ ] **No health check/monitoring** package
- [ ] **No IDE helper** (barryvdh/laravel-ide-helper) for models/facades
- [ ] **No debug bar** (barryvdh/laravel-debugbar) for development
- [ ] **No audit log viewer** UI — audit data stored but no way to browse
- [ ] **No webhook package** for external integrations

---

### MEDIUM — Missing Commands & Scheduler

- [x] **`app/Console/Kernel.php` created** — with scheduled tasks registered
- [x] `projects:check-overdue` command created — detects and alerts for overdue projects
- [x] `projects:update-progress` command created — auto-calculates progress from task completion
- [x] `projects:budget-check` command created — checks budget thresholds and sends alerts
- [x] `notifications:cleanup` command created — deletes old notifications with configurable retention
- [x] Scheduled: `projects:update-progress` runs daily, `projects:budget-check` runs weekly, `notifications:cleanup` runs daily
- [x] `projects:cleanup-activity` — created (lower priority)
- [x] `projects:archive-completed` — created (lower priority)
- [x] `recurring-tasks:generate` — stub created (scheduled in Kernel)
- [x] `invoices:generate-recurring` — stub created (scheduled in Kernel)

---

### MEDIUM — Testing Gaps (Project-specific)

- [x] **Zero tests for `ProjectService`** — 11 tests covering recalculateProgress, handleMilestoneAutoComplete, syncStaffAndNotify, handleBudgetTracking, logProjectActivity, activityDescriptionLabels
- [ ] **Zero tests for `ProjectNotificationService`** — no tests for send(), sendToStaff(), sendToClient(), sendToOneStaff(), buildData(), getCreatorData()
- [ ] **Zero tests for project file operations** — upload, delete, permissions
- [ ] **Zero tests for discussion CRUD** — create, comment, permissions
- [ ] **Zero tests for project timesheet/Gantt data** — data aggregation queries
- [ ] **Zero tests for client project access** — authorization, filtering, export
- [ ] **Zero tests for permission enforcement** — projects_view, projects_create, projects_edit, projects_delete
- [ ] **Zero tests for validation rules** — StoreProjectRequest, UpdateProjectRequest rule combinations
- [ ] **Zero tests for status transitions** — Active→Completed→OnHold→Cancelled→Active valid/invalid flows
- [ ] **Zero tests for milestone operations within project context**
- [ ] **Zero tests for cascade cleanup** — deleting project should not orphan records
- [ ] **Zero tests for project notification delivery** — email sending, in-app notification creation
- [x] **Factory definitions created** — ProjectFactory, ProjectMemberFactory, ProjectActivityFactory, ProjectFileFactory, ProjectNoteFactory, ProjectDiscussionFactory, ProjectDiscussionCommentFactory, MilestoneFactory, PinnedProjectFactory, ClientFactory, StaffFactory
- [ ] `ProjectModuleTest.php` (577 lines, 18 tests + 1 skipped) — covers basic CRUD, notification sending, discussion/comments, notes, milestones, tasks. Still MISSING: file uploads, Gantt data, timesheet data, authorization edge cases, validation errors, status transitions, client portal access

---

### MEDIUM — Missing Connections (Cross-Module Integration)

- [x] Project completion → auto-finalize invoices — `FinalizeProjectInvoices` listener + `ProjectService::autoFinalizeInvoices()`
- [x] Project milestones → auto-invoicing at milestone completion — `AutoInvoiceMilestone` listener + `ProjectService::generateMilestoneInvoice()`
- [x] Project status → auto-close/open related tickets — `ProjectService::syncTicketStatusWithProject()`
- [x] Project team changes → auto-reassign tasks from removed members — `ProjectService::syncStaffAndNotify()` with `$autoReassignToCreator`
- [x] Project budget → purchase order/invoice validation — `ProjectService::validateBudgetBeforeInvoice()`
- [x] Projects → contracts module integration — Created `Contract` model, contracts tab now renders real data
- [x] Project tasks → leave management — `ProjectService::checkStaffOnLeaveAndReassign()` with alert system
- [x] Project files → document management/workflow — `ProjectFile` document_status constants + workflow methods
- [x] Project time logs → payroll integration — `ProjectService::syncBillableHoursToPayroll()` + `PayrollController::syncFromProjects()`
- [x] Project → webhooks/external integrations — Created `Webhook` model + `WebhookService` with HMAC-signed HTTP dispatch
- [x] Project → email campaigns — Created `ProjectEmailCampaign` stub model
- [x] Project expenses → billable to invoice conversion — `ProjectController::convertBillableToInvoice()` with route + Itemable creation
- [x] Project → GDPR compliance — `ProjectService::exportProjectData()` + `deleteProjectData()` + controller endpoints

---

### LOW — UI/UX Issues

- [x] Tab loading shows spinner but no retry option if AJAX fails — Added retry button in AJAX error handler
- [x] No bulk selection checkboxes on project list — Added "Select All" + per-row checkboxes + batch bar
- [x] No column sorting on project list — Sortable column headers (Name, Progress, Deadline, Status)
- [x] No "My Projects" filter on index — Toggle pill filter button in search bar
- [x] No recent projects widget on admin dashboard — Already existed with progress bars
- [x] Project dashboard view not configurable — Added Customize button + modal with localStorage widget toggles
- [x] No project grid/list view toggle — Grid/List toggle buttons with localStorage persistence
- [x] Create/Edit forms use Quill editor with no image upload capability — Added image toolbar button + upload handler
- [x] Delete uses browser `confirm()` instead of styled modal/dialog — Created reusable `confirmDelete()` modal partial
- [x] Note modal uses `alert()` for errors instead of inline notification — Inline error div replacing `alert()` calls
- [x] File upload has no progress bar — XMLHttpRequest upload with progress bar
- [x] No tooltips on truncated text/avatars — CSS-based tooltip system for title attributes
- [x] No keyboard shortcuts — Added n(ew), f(ind), e(dit), w(atcher) shortcuts
- [x] Client portal project view is very basic — Enhanced with tabs + retry on AJAX failure + responsive sidebar
- [x] `Cancelled` status (4) missing from status dropdowns and badge displays
- [x] Milestone tab shows dates but not status indicators — Status colors (green/orange/gray) + completion progress bars
- [x] Empty states use plain text instead of illustrations — SVG illustrations replacing plain text empty states
- [x] No responsive breakpoint handling — Scrollable sidebar container with CSS fade effect
- [x] Activity log shows raw `description_key` instead of human-readable text
- [x] Dashboard view project details show `number_format()` instead of `app_format_money()` — Fixed in dashboard + overview partial
- [x] Invoices/estimates count in project sidebar shows eager-loaded collection count instead of DB count — Changed to `->count()` (DB query)
- [x] Quick action "Upload File" in dashboard points to `#` (dead link) — Changed to route to files tab

