
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
- [ ] **Add `HasFactory` trait** — Only `User.php` uses `HasFactory`; no factories exist for any CRM model
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

- [ ] **Add inline checklist management in pane show** — Currently checklist items are display-only in `pane/show.blade.php`. Need: add checklist item form, check/uncheck via AJAX, delete button per item, assignee on checklist items
- [ ] **Add inline status dropdown (not just "Mark Done")** — Replace single "Mark Done" button with a status dropdown (Backlog/To Do/In Progress/Done) so users can transition through all statuses from the pane
- [ ] **Add priority quick-edit in pane show** — Allow changing priority from the show pane without opening edit form
- [ ] **Add followers management in pane show** — Currently followers are display-only. Need: add follower toggle (follow/unfollow), show follower count
- [ ] **Add inline assignee management in pane show** — Allow adding/removing assigned staff directly from show pane (not just via edit form)
- [ ] **Add delete task button in pane show** — With confirmation dialog, redirects to task index on success
- [ ] **Add duplicate task button in pane show** — Creates a copy of the task, opens in pane edit
- [ ] **Add "Mark as Complete/Incomplete" toggle that updates `datefinished`** — When marking done, set `datefinished = now()`; when moving back, clear `datefinished`
- [ ] **Add activity log section in pane show** — Show recent changes (status changes, assignment changes, checklist completions) inline in the pane
- [ ] **Add timer start/stop button in pane show header** — Quick start/stop timer without scrolling to timesheet section

#### Phase 2 — Pane Create/Edit Enhancements

- [ ] **Add checklist template selector in create pane** — `tbltasks_checklist_templates` table exists but has no UI. Add dropdown to apply a template on creation
- [ ] **Add "Create & Add Another" option in pane create** — After saving, keep pane open for another entry
- [ ] **Pre-populate pane create from project/client context** — When `openTaskPane` is called with `?rel_type=project&rel_id=123`, pre-select the related entity and filter milestones/assignees accordingly
- [ ] **Add tags input in pane create/edit** — Tags field exists in StoreTaskRequest but has no UI in the forms and no processing in the controller
- [ ] **Add follower selection in pane create/edit** — Allow adding followers during task creation/editing (currently only assigned staff, not followers)
- [ ] **Show validation errors inline in pane forms** — Currently pane form error handling is minimal (`showToast(data.message || 'An error occurred', 'error')`). Parse Laravel validation error response and show inline field errors
- [ ] **Add recurring task preview in pane** — When setting recurring, show a preview of next occurrence date

#### Phase 3 — Cross-Module Pane Integration

- [ ] **Add "Create Task" button in Project show page** — Opens task pane with `rel_type=project` and `rel_id=project->id` pre-selected
- [ ] **Add "Create Task" button in Client show page** — Opens task pane with `rel_type=client` and `rel_id=client->userid` pre-selected
- [ ] **Add "Create Task" button in Lead show page** — Opens task pane with `rel_type=lead` and `rel_id=lead->id` pre-selected
- [ ] **Add "Create Task" button in Ticket show page** — Opens task pane with `rel_type=ticket` and `rel_id=ticket->ticketid` pre-selected
- [ ] **Add "Create Task" button in Invoice/Estimate/Proposal show pages** — Same pattern
- [ ] **Add task count badge in Milestone pane show** — Show number of tasks grouped by status for the milestone
- [ ] **Add task list in Project pane show** — Inline task list for the project with quick status toggle
- [ ] **Ensure project staff sync** — When a task is related to a project, the project's staff should auto-sync as candidate assignees; currently the pane form filters but doesn't auto-select

#### Phase 4 — Pane UX/Polish

- [ ] **Add pane loading skeleton** — Replace "Loading..." text with a proper skeleton UI
- [ ] **Add pane responsive improvements** — `max-w-[78%]` is too wide on tablets; add breakpoints
- [ ] **Add keyboard shortcut for pane close (Escape key)** — Currently only close via X button or overlay click
- [ ] **Add pane history/back navigation** — Allow navigating back to previous pane view (e.g., from edit back to show)
- [ ] **Add toast confirmation with undo** — For delete/destructive actions, show toast with undo option
- [ ] **Add pane state persistence** — When pane is open and page reloads, restore pane state from URL hash

---

### TASK MODULE AUDIT — Issues, Missing Features & Validations

#### CRITICAL — Data Integrity & Security

- [ ] **Task delete does NOT cascade** — `TaskController::destroy()` only calls `$task->delete()`. Orphaned records: `tbltask_assigned`, `tbltask_followers`, `tbltask_comments`, `tbltaskstimers`, `tbltask_checklist_items`, `tblfiles` (attachments). Will cause FK constraint failures if enforced, or infinite orphaned data otherwise
- [ ] **Timer `start_time`/`end_time` stored as `string(64)`** — Migration `2026_05_06_000031_create_tbltaskstimers_table.php` declares `string(64)`, but `TaskTimer` model casts as `datetime` and `TaskTimerController` passes `now()` (Carbon object). Type mismatch — silent failure or data corruption
- [ ] **`TaskComment` migration has no `project_discussion_id` column** — `CommentController::store()` validates `project_discussion_id` but no such column exists in `tbltask_comments` migration. This field is silently dropped on create
- [ ] **Comment notification uses hardcoded URL instead of named route** — `CommentController:63` uses `config('app.url') . '/admin/tasks/' . $task->id` instead of `route('admin.tasks.show', $task->id)`
- [ ] **No authorization check for comment store on task visibility** — `CommentController::store()` checks only `tasks_view` permission, does not verify user is assigned/following the task or that task is visible to them
- [ ] **No CSRF validation on kanban quick-create** — `quickCreate()` in `TaskController` uses `$request->validate()` but doesn't validate CSRF token via `@csrf` or header check; relies on `meta[name="csrf-token"]` client-side only

#### HIGH — Validation Gaps

- [ ] **`StoreTaskRequest` expects `assigned_staff` as array but non-pane forms send comma-separated string** — The FormRequest validation runs BEFORE the `explode(',', ...)` in the controller, so validation will fail when the full-page form (non-pane) submits
- [ ] **No `rel_id`/`rel_type` pair validation** — `rel_id` is `nullable|integer`, `rel_type` is `nullable|string|max:30`. No rule ensuring both are set together, or that the combination exists in the related table
- [ ] **No `duedate >= startdate` validation** — Only `nullable|date`. Users can set due date before start date
- [ ] **No `priority` range validation** — `required|integer` — should be `integer|in:1,2,3` (or 4 for urgent)
- [ ] **No `status` range validation** — `required|integer` — should be `integer|in:0,1,2,3`
- [ ] **`visible_to_client` missing from StoreTaskRequest rules** — Field is submitted in forms but not validated
- [ ] **`milestone` not validated against project** — When `rel_type=project`, milestone should exist in that project's milestones
- [ ] **No billing consistency validation** — `billed=1` can be set without `billable=1`; `invoice_id` can reference non-existent invoice; `hourly_rate` can be 0 while `billable=1`
- [ ] **Timer date+hours+minutes don't validate against each other** — `hours=0&minutes=0` creates a 0-duration timer (should be rejected)
- [ ] **No file MIME type validation in attachment upload** — `storeAttachment()` validates `max:25600` (KB) but no MIME validation; contradicts `FileService` which validates strictly

#### HIGH — Missing Events/Listeners/Notifications

- [ ] **No Task Events exist** — Unlike `InvoicePaid`, `LeadConverted`, etc., tasks have zero event classes. Should create:
  - `TaskCreated` — fires after task creation
  - `TaskUpdated` — fires after task update
  - `TaskCompleted` — fires when status changes to Done (STATUS_COMPLETED)
  - `TaskReopened` — fires when moved from Done back to another status
  - `TaskDeleted` — fires after task deletion
  - `TaskAssigned` — fires when staff is assigned to task
  - `TaskUnassigned` — fires when staff is removed
  - `TaskChecklistCompleted` — fires when all checklist items are done
  - `TaskDeadlineApproaching` — fires when due date is within threshold
  - `TaskTimerStarted` / `TaskTimerStopped` — for time tracking events
- [ ] **No dedicated Notification classes** — Notifications are sent inline via `EmailService::send()` with hardcoded slugs and `Notification::createForStaff()`. Should use Laravel Notification classes:
  - `TaskAssignedNotification`
  - `TaskCompletedNotification`
  - `TaskCommentedNotification`
  - `TaskDeadlineReminderNotification`
- [ ] **No Task Model Observer** — No `app/Observers/TaskObserver.php` to hook into `created`, `updated`, `deleted` events for centralized side-effects
- [ ] **No Task Policy class** — Permission checks use inline `Auth::user()->can()` scattered across controllers. Should have `app/Policies/TaskPolicy.php` with `view()`, `create()`, `update()`, `delete()` methods

#### HIGH — Missing Features

- [ ] **No recurring task scheduler** — `recurring`/`repeat_every`/`recurring_type`/`cycles` fields exist in DB and form, but no artisan command or scheduler creates recurring task instances. **DEAD FEATURE**
- [ ] **Kanban board only works for project tasks** — `TaskController::kanban()` filters `->where('rel_type', 'project')`. Tasks linked to invoices, clients, leads, tickets are invisible on kanban
- [ ] **Full-page create form only offers 4 rel_types** — Project, Invoice, Estimate, Proposal. Pane create offers 7 (adds Lead, Ticket, Client). **INCONSISTENCY**
- [ ] **No filtering by assigned staff in index** — Index filters only by priority, status, and name search. No `?assigned_to=staffid` filter
- [ ] **No filtering by rel_type in index** — No way to show only project tasks, client tasks, etc.
- [ ] **No sorting options in index** — Hard-coded to `orderBy('milestone')->orderBy('id', 'desc')`. No user-configurable sorting
- [ ] **No task export** — No CSV/PDF export for task lists
- [ ] **No soft deletes** — Tasks are hard-deleted permanently. No restore capability
- [ ] **No audit logging for non-project tasks** — `TaskService::logProjectActivity()` only logs when `rel_type === 'project'`. Tasks linked to invoices, clients, leads, tickets have zero audit trail
- [ ] **No task search API endpoint** — No dedicated search; index uses basic `LIKE %name%` with no full-text search
- [ ] **No bulk operations** — No bulk status change, bulk delete, bulk assign. Only single-task operations
- [ ] **No task checklist templates UI** — `tbltasks_checklist_templates` table exists and is migrated but has no controller, routes, or views
- [ ] **Priority 4 (Urgent) referenced in client view but not in model** — `admin.clients.tasks` blade maps priority `4 => Urgent` with `bg-red-100 text-red-700` class, but `Task` model constants and `tbltasks.priority` column only support values 1-3
- [ ] **Followers accepted in StoreTaskRequest but never synced in store()** — `followers` array is validated in FormRequest but `TaskController::store()` never calls `$task->followers()->sync()`
- [ ] **Tags accepted in StoreTaskRequest but never handled** — `tags` array validated but no `tbltags` or `tbltaggables` processing in controller or service

#### MEDIUM — Code Quality & Architecture

- [ ] **Frontend JS duplication between full-page and pane forms** — Create/edit JS logic is duplicated in `create.blade.php`, `edit.blade.php`, `pane/create.blade.php`, `pane/edit.blade.php`. Should extract to shared partial or JS module
- [ ] **Pane create not using data-pane-refresh for redirect** — `data-pane-submit="1"` is set but no `data-pane-refresh` attribute, so after save the pane just closes. Should refresh relevant parent view
- [ ] **No pagination on kanban board** — All tasks loaded at once. Will cause performance issues for large projects
- [ ] **No archived/done filter on kanban** — Completed tasks remain on the board forever with no way to hide them
- [ ] **TimerController uses strings in `start_time`/`end_time` for manual entries** — `TaskTimerController:108` parses `$request->date . ' 09:00:00'` as a fixed 9 AM time. Hours/minutes are used for duration calculation from 9 AM, not as actual start time
- [ ] **Quick-view modal JS is scoped to kanban view** — `openTaskModal()`/`closeTaskModal()` functions defined in `kanban.blade.php`, not in global layout. After navigating away and back, they still work, but poor architectural separation
- [ ] **Inconsistent error response format** — Some endpoints return `['success' => false, 'message' => '...']` with 403, some with 500, some with 400. The front-end `initPaneForms()` only checks `data.success` — does not differentiate by HTTP status
- [ ] **`TaskController` stores attachment with `File::create()` directly** — Should use `FileService` or delegated service method instead of inline creation
- [ ] **`TaskComment` model has no relationships defined beyond `addedFrom`** — No `task()`, `staff()`, or `file()` relationships
- [ ] **`TaskChecklistItem` model has no `task()` relationship** — Missing BelongsTo relationship back to Task
- [ ] **`TaskChecklistController::destroy()` has dead permission check** — Checks `tasks_delete` then `tasks_delete_all`, but `delete_all` check returns 403 regardless of ownership. Only users with `tasks_delete_all` can delete; no user can delete their own item
- [ ] **`UpdateTaskRequest` extends `StoreTaskRequest` without modification** — Validates `required` fields even for updates where they might already be set. Should override rules to make non-changed fields optional

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
                   ──[Mark Done]──> updateStatus (AJAX) ✓
                   ──[Post Comment]──> CommentController@store ✓
                   ──[Upload Attachment]──> storeAttachment ✓
                   ──[Delete Attachment]──> destroyAttachment ✓
                   ──[Log Time]──> TaskTimerController@store ✓
                   ──[Edit Time]──> inline edit form (AJAX) ✓
                   ──[Delete Time]──> inline delete (fetch DELETE) ✓
                   ✗ [Checklist CRUD] ──> MISSING
                   ✗ [Status dropdown] ──> MISSING
                   ✗ [Priority quick-edit] ──> MISSING
                   ✗ [Follower toggle] ──> MISSING
                   ✗ [Assignee inline manage] ──> MISSING
                   ✗ [Delete task] ──> MISSING
                   ✗ [Duplicate task] ──> MISSING
                   ✗ [Timer start/stop header] ──> MISSING
                   ✗ [Activity log] ──> MISSING

pane/create.blade.php ──[Save]──> TaskController@store ✓ (closes pane, no refresh)
pane/edit.blade.php ──[Update]──> TaskController@update ✓ (refreshes to pane/show)

MISSING CROSS-MODULE TRIGGERS:
✗ No "Create Task" button in Project show page
✗ No "Create Task" button in Client show page
✗ No "Create Task" button in Lead show page
✗ No "Create Task" button in Ticket show page
✗ No "Create Task" button in Invoice/Estimate/Proposal show pages
✗ No task count in Milestone pane show
✗ No inline task list in Project pane show
```
