# Integration Test Suite

**Database:** MySQL (`ck_crm_test`) — real database, no mocking  
**Trait:** `RefreshDatabase` — full migrate:fresh per test class  
**Auth:** `staff` guard, authenticated as admin (`admin@crm.com`, role: Super Administrator)  
**Seeded Data:** Countries, Currencies, Permissions, Roles, Staff, Departments, Ticket statuses/priorities, Payment modes, Taxes, Email templates

---

## ProjectModuleTest (`tests/Feature/ProjectModuleTest.php`)

**19 tests — 70 assertions**

| # | Test | What it covers | Key assertions |
|---|------|----------------|----------------|
| 1 | `test_guest_is_redirected_to_login_when_accessing_projects` | Auth middleware blocks guests on index/create/store | `assertRedirect(route('login.staff'))` |
| 2 | `test_admin_can_view_projects_list` | Index page renders with project data | `assertStatus(200)`, `assertSee()` |
| 3 | `test_admin_can_create_project` | Full project creation with staff assignment + notifications | DB: `tblprojects`, `tblproject_members`, `tblnotifications` |
| 4 | `test_admin_can_update_project_basic_fields` | PUT update changes project fields + logs activity | DB: `tblprojects`, `tblproject_activity.description_key` |
| 5 | `test_admin_can_delete_project` | DELETE removes project | `assertDatabaseMissing('tblprojects')` |
| 6 | `test_admin_can_create_task_in_project` | Task creation with assigned staff + activity log | DB: `tbltasks`, `tbltask_assigned`, `tblproject_activity` |
| 7 | `test_admin_can_update_task_status_via_ajax` | AJAX status change (status slug → integer) | `assertJson(['success'=>true])`, DB status 2 |
| 8 | `test_admin_can_create_milestone` | Milestone creation with staff notifications | DB: `tblmilestones`, `tblnotifications` |
| 9 | `test_admin_can_create_project_discussion` | Discussion creation with activity log | DB: `tblprojectdiscussions`, `tblproject_activity` |
| 10 | `test_admin_can_add_discussion_comment` | Comment on discussion via AJAX + activity log | `assertJsonPath('comment.content')`, DB |
| 11 | `test_admin_can_create_project_note` | Note creation via AJAX + activity log | `assertJson(['success'=>true])`, DB `tblnotes` |
| 12 | `test_admin_can_send_project_notification` | Send notification via AJAX | `assertJson(['success'=>true])` — `Mail::fake()` |
| 13 | `test_project_tab_returns_json_content` | Tab endpoint returns HTML JSON | `assertJsonStructure(['html'])` |
| 14 | `test_project_update_with_status_change_to_completed_sends_notifications` | Status→completed triggers staff notifications | DB: `tblnotifications` to staff members |
| 15 | `test_admin_can_quick_create_task` | Quick task creation via AJAX + activity log | `assertJsonPath('task.name')`, DB `tblproject_activity` |
| 16 | `test_project_create_sends_email_notification` | Email sent on project creation | `Mail::getSymfonyTransport()->messages()` |
| 17 | `test_project_update_with_new_staff_triggers_in_app_notification` | Staff addition triggers in-app notification | DB: `tblnotifications` |
| 18 | `test_project_send_notification_endpoint_sends_email` | Send-notification endpoint sends email | `Mail::getSymfonyTransport()->messages()` |
| 19 | `test_project_kickoff_sends_email` | Kickoff endpoint sends email | `Mail::getSymfonyTransport()->messages()` |

---

## ClientModuleTest (`tests/Feature/ClientModuleTest.php`)

**15 tests — 48 assertions**

| # | Test | What it covers | Key assertions |
|---|------|----------------|----------------|
| 1 | `test_guest_is_redirected_to_login_when_accessing_clients` | Auth middleware blocks guests on index/create/store | `assertRedirect(route('login.staff'))` |
| 2 | `test_admin_can_view_clients_list` | Index page renders with client data | `assertStatus(200)`, `assertSee()` |
| 3 | `test_admin_can_create_client` | Full client creation | DB: `tblclients` (company, phonenumber, active), redirect + session |
| 4 | `test_create_client_requires_required_fields` | Validation rejects empty required fields | `assertSessionHasErrors(['company', 'country', 'default_currency'])` |
| 5 | `test_admin_can_view_single_client` | Show page renders client details | `assertStatus(200)`, `assertSee()` |
| 6 | `test_admin_can_update_client` | PUT update changes fields | DB: `tblclients` (company, city changed) |
| 7 | `test_admin_can_delete_client_without_dependencies` | DELETE removes client (no projects/invoices) | `assertDatabaseMissing('tblclients')` |
| 8 | `test_admin_cannot_delete_client_with_projects` | DELETE blocked when dependent projects exist | `assertSessionHas('error')`, client still in DB |
| 9 | `test_staff_without_permission_cannot_create_clients` | Permission check — staff (role 6) gets 403 | `assertStatus(403)` |
| 10 | `test_search_clients_api_returns_json` | `GET /clients/search?q=` returns JSON results | `assertJsonCount(1)`, `assertJsonFragment()` |
| 11 | `test_search_clients_api_requires_minimum_3_chars` | Search requires ≥3 chars, returns empty | `assertEmpty($response->json())` |
| 12 | `test_filter_clients_by_status` | `?status=1` filters active, `?status=0` inactive | `assertSee()` / `assertDontSee()` |
| 13 | `test_filter_clients_by_country` | `?country=1` filters by country | `assertSee()` / `assertDontSee()` |
| 14 | `test_filter_clients_by_date_range` | `?date_from=&date_to=` filters by created date | `assertStatus(200)`, `assertSee()` |
| 15 | `test_admin_can_view_client_contacts_tab` | Contacts subtab shows contact data | `assertStatus(200)`, `assertSee('Jane')`, `assertSee('Doe')` |

---

## InvoiceModuleTest (`tests/Feature/InvoiceModuleTest.php`)

**20 tests — 58 assertions**

| # | Test | What it covers | Key assertions |
|---|------|----------------|----------------|
| 1 | `test_guest_is_redirected_to_login_when_accessing_invoices` | Auth middleware blocks guests on index/create/store | `assertRedirect(route('login.staff'))` |
| 2 | `test_admin_can_view_invoices_list` | Index page renders invoices | `assertStatus(200)`, `assertSee(invoice.number)` |
| 3 | `test_admin_can_create_invoice` | Full invoice creation | DB: `tblinvoices`, redirect + session |
| 4 | `test_create_invoice_requires_required_fields` | Validation rejects empty required fields | `assertSessionHasErrors([...6 fields...])` |
| 5 | `test_admin_can_create_invoice_with_items` | Invoice creation with line items | DB: `tblitemable` with 2 items |
| 6 | `test_admin_can_view_single_invoice` | Show page renders | `assertStatus(200)` |
| 7 | `test_admin_can_update_invoice` | PUT update changes total | DB: `tblinvoices.total` changed |
| 8 | `test_admin_can_delete_invoice` | DELETE removes invoice | `assertDatabaseMissing('tblinvoices')` |
| 9 | `test_record_payment_transitions_status_to_partial_then_paid` | Payment 1→status 2 (partial), Payment 2→status 3 (paid) | DB: `tblinvoicepaymentrecords` + status transitions |
| 10 | `test_mark_invoice_as_paid` | `markAsPaid` sets status 3 | DB: `tblinvoices.status=3` |
| 11 | `test_mark_invoice_as_cancelled` | `markAsCancelled` sets status 4 | DB: `tblinvoices.status=4` |
| 12 | `test_send_invoice_to_client` | `sendInvoice` sets `sent=1` + `datesend` | DB: `tblinvoices.sent=1` |
| 13 | `test_store_note_on_invoice` | Note creation on invoice | DB: `tblnotes` (rel_type='invoice') |
| 14 | `test_store_reminder_on_invoice` | Reminder creation with staff assignment | DB: `tblreminders` |
| 15 | `test_filter_invoices_by_status` | `?status=3` filters by status | `assertStatus(200)` |
| 16 | `test_invoice_creation_logs_sales_activity` | Invoice creation logs to `tblsales_activity` | DB: `tblsales_activity` |
| 17 | `test_send_invoice_triggers_email_notification_to_client` | Send invoice sends email | `Mail::getSymfonyTransport()->messages()` |
| 18 | `test_record_payment_triggers_multiple_email_notifications` | Payment triggers email notifications | `Mail::getSymfonyTransport()->messages()` |
| 19 | `test_mark_paid_triggers_email_notification` | Mark paid sends notification email | `Mail::getSymfonyTransport()->messages()` |
| 20 | `test_mark_cancelled_triggers_email_notification` | Mark cancelled sends notification email | `Mail::getSymfonyTransport()->messages()` |

---

## TicketModuleTest (`tests/Feature/TicketModuleTest.php`)

**17 tests — 59 assertions**

| # | Test | What it covers | Key assertions |
|---|------|----------------|----------------|
| 1 | `test_guest_is_redirected_to_login_when_accessing_tickets` | Auth middleware blocks guests on index/create/store | `assertRedirect(route('login.staff'))` |
| 2 | `test_admin_can_view_tickets_list` | Index page renders tickets | `assertStatus(200)`, `assertSee()` |
| 3 | `test_admin_can_create_ticket` | Full ticket creation | DB: `tbltickets` — `Mail::fake()` |
| 4 | `test_create_ticket_requires_required_fields` | Validation rejects empty required | `assertSessionHasErrors(['subject','message','department','priority','status'])` |
| 5 | `test_admin_can_view_single_ticket` | Show page renders | `assertStatus(200)`, `assertSee()` |
| 6 | `test_admin_can_update_ticket` | PUT update changes subject, dept, priority, status, assigned | DB: all changed fields |
| 7 | `test_admin_can_delete_ticket` | DELETE removes ticket | `assertDatabaseMissing('tbltickets')` |
| 8 | `test_admin_can_store_reply_on_ticket` | Reply via AJAX with email threading headers | `assertJson(['success'=>true])`, DB: `tblticket_replies` |
| 9 | `test_admin_can_change_ticket_status` | `changeStatus` via AJAX | `assertJson(['success'=>true])`, DB status=3 |
| 10 | `test_admin_can_change_ticket_assigned_staff` | `changeAssigned` via AJAX | `assertJson(['success'=>true])`, DB assigned |
| 11 | `test_filter_tickets_by_department` | `?department=1` filters | `assertSee()` / `assertDontSee()` |
| 12 | `test_filter_tickets_by_priority` | `?priority=3` filters | `assertSee()` / `assertDontSee()` |
| 13 | `test_filter_tickets_by_status` | `?status=5` filters | `assertSee()` / `assertDontSee()` |
| 14 | `test_ticket_create_sends_email_to_client` | Ticket creation sends email | `Mail::getSymfonyTransport()->messages()` |
| 15 | `test_ticket_reply_sends_email_with_threading_headers` | Reply sends email + threading headers (Message-ID, In-Reply-To, References) | `Mail::getSymfonyTransport()->messages()` count increase |
| 16 | `test_ticket_create_creates_notification_for_staff` | Ticket creation triggers in-app notification | DB: `tbltickets` |
| 17 | `test_ticket_change_assigned_staff_creates_notification` | Assignment change triggers in-app notification | DB: `tbltickets` |

---

## TaxModuleTest (`tests/Feature/TaxModuleTest.php`)

**7 tests — 20 assertions**

| # | Test | What it covers | Key assertions |
|---|------|----------------|----------------|
| 1 | `test_guest_is_redirected_to_login_when_accessing_taxes` | Auth blocks guests | `assertRedirect(route('login.staff'))` |
| 2 | `test_admin_can_view_taxes_list` | Index page renders | `assertStatus(200)`, `assertSee()` |
| 3 | `test_admin_can_create_tax` | Create with name + taxrate | DB: `tbltaxes` |
| 4 | `test_create_tax_requires_required_fields` | Validation rejects empty | `assertSessionHasErrors(['name', 'taxrate'])` |
| 5 | `test_admin_can_view_single_tax` | Edit page renders | `assertStatus(200)`, `assertSee()` |
| 6 | `test_admin_can_update_tax` | PUT update changes fields | DB: `tbltaxes` |
| 7 | `test_admin_can_delete_tax` | DELETE removes tax | `assertDatabaseMissing('tbltaxes')` |

---

## CurrencyModuleTest (`tests/Feature/CurrencyModuleTest.php`)

**6 tests — 17 assertions**

| # | Test | What it covers | Key assertions |
|---|------|----------------|----------------|
| 1 | `test_guest_is_redirected_to_login_when_accessing_currencies` | Auth blocks guests | `assertRedirect(route('login.staff'))` |
| 2 | `test_admin_can_view_currencies_list` | Index page renders | `assertStatus(200)`, `assertSee()` |
| 3 | `test_admin_can_create_currency` | Create with symbol, name, separators | DB: `tblcurrencies` |
| 4 | `test_create_currency_requires_required_fields` | Validation rejects empty | `assertSessionHasErrors(['symbol', 'name'])` |
| 5 | `test_admin_can_update_currency` | PUT update changes name | DB: `tblcurrencies` |
| 6 | `test_admin_can_delete_currency` | DELETE removes currency | `assertDatabaseMissing('tblcurrencies')` |

---

## PaymentModeModuleTest (`tests/Feature/PaymentModeModuleTest.php`)

**6 tests — 20 assertions**

| # | Test | What it covers | Key assertions |
|---|------|----------------|----------------|
| 1 | `test_guest_is_redirected_to_login_when_accessing_payment_modes` | Auth blocks guests | `assertRedirect(route('login.staff'))` |
| 2 | `test_admin_can_view_payment_modes_list` | Index page renders | `assertStatus(200)`, `assertSee()` |
| 3 | `test_admin_can_create_payment_mode` | Create with name | DB: `tblpayment_modes` |
| 4 | `test_create_payment_mode_requires_name` | `name` required | `assertSessionHasErrors(['name'])` |
| 5 | `test_admin_can_update_payment_mode` | PUT update changes name | DB: `tblpayment_modes` |
| 6 | `test_admin_can_delete_payment_mode` | DELETE removes | `assertDatabaseMissing('tblpayment_modes')` |

---

## ItemModuleTest (`tests/Feature/ItemModuleTest.php`)

**13 tests — 36 assertions**

| # | Test | What it covers | Key assertions |
|---|------|----------------|----------------|
| 1 | `test_guest_is_redirected_to_login_when_accessing_items` | Auth blocks guests | `assertRedirect(route('login.staff'))` |
| 2 | `test_admin_can_view_items_list` | Index page renders | `assertStatus(200)`, `assertSee()` |
| 3 | `test_admin_can_create_item` | Create with description + rate | DB: `tblitems` |
| 4 | `test_create_item_requires_required_fields` | Validation rejects empty | `assertSessionHasErrors(['description', 'rate'])` |
| 5 | `test_admin_can_view_single_item` | Show page renders | `assertStatus(200)`, `assertSee()` |
| 6 | `test_admin_can_update_item` | PUT update changes fields | DB: `tblitems` |
| 7 | `test_admin_can_delete_item` | DELETE removes | `assertDatabaseMissing('tblitems')` |
| 8 | `test_filter_items_by_group` | `?group=` filters | `assertSee()` / `assertDontSee()` |
| 9 | `test_guest_is_redirected_to_login_when_accessing_items_groups` | Auth blocks guests | `assertRedirect(route('login.staff'))` |
| 10 | `test_admin_can_view_items_groups_list` | Index page renders | `assertStatus(200)`, `assertSee()` |
| 11 | `test_admin_can_create_items_group` | Create with name | DB: `tblitems_groups` |
| 12 | `test_admin_can_update_items_group` | PUT update changes name | DB: `tblitems_groups` |
| 13 | `test_admin_can_delete_items_group` | DELETE removes | `assertDatabaseMissing('tblitems_groups')` |

---

## ExpenseModuleTest (`tests/Feature/ExpenseModuleTest.php`)

**13 tests — 38 assertions**

| # | Test | What it covers | Key assertions |
|---|------|----------------|----------------|
| 1 | `test_guest_is_redirected_to_login_when_accessing_expenses` | Auth blocks guests | `assertRedirect(route('login.staff'))` |
| 2 | `test_admin_can_view_expenses_list` | Index page renders | `assertStatus(200)`, `assertSee()` |
| 3 | `test_admin_can_create_expense` | Create with required fields | DB: `tblexpenses` |
| 4 | `test_create_expense_requires_required_fields` | Validation rejects empty | `assertSessionHasErrors(['expense_name', 'amount', 'date', 'clientid', 'currency'])` |
| 5 | `test_admin_can_view_single_expense` | Show page renders | `assertStatus(200)`, `assertSee()` |
| 6 | `test_admin_can_update_expense` | PUT update changes fields | DB: `tblexpenses` |
| 7 | `test_admin_can_delete_expense` | DELETE removes | `assertDatabaseMissing('tblexpenses')` |
| 8 | `test_filter_expenses_by_client` | `?client=` filters | `assertSee()` / `assertDontSee()` |
| 9 | `test_guest_is_redirected_to_login_when_accessing_expense_categories` | Auth blocks guests | `assertRedirect(route('login.staff'))` |
| 10 | `test_admin_can_view_expense_categories_list` | Index page renders | `assertStatus(200)`, `assertSee()` |
| 11 | `test_admin_can_create_expense_category` | Create with name | DB: `expense_categories` |
| 12 | `test_admin_can_update_expense_category` | PUT update changes name | DB: `expense_categories` |
| 13 | `test_admin_can_delete_expense_category` | DELETE removes | `assertDatabaseMissing('expense_categories')` |

---

## CreditNoteModuleTest (`tests/Feature/CreditNoteModuleTest.php`)

**10 tests — 45 assertions**

| # | Test | What it covers | Key assertions |
|---|------|----------------|----------------|
| 1 | `test_guest_is_redirected_to_login_when_accessing_credit_notes` | Auth blocks guests | `assertRedirect(route('login.staff'))` |
| 2 | `test_admin_can_view_credit_notes_list` | Index page renders with formatted number + client | `assertStatus(200)`, `assertSee('0001')`, `assertSee('Credit Note Client')` |
| 3 | `test_admin_can_create_credit_note` | Create with required fields | DB: `tblcreditnotes`, redirect, session |
| 4 | `test_create_credit_note_requires_required_fields` | Validation rejects empty | `assertSessionHasErrors(['clientid', 'number', 'currency', 'date', 'subtotal', 'total'])` |
| 5 | `test_admin_can_view_single_credit_note` | Show page renders with number, client, total | `assertStatus(200)`, `assertSee('0001')`, `assertSee('Credit Note Client')`, `assertSee('Total Amount')` |
| 6 | `test_admin_can_update_credit_note` | PUT update changes fields | DB: `tblcreditnotes` |
| 7 | `test_admin_can_apply_credit_to_invoice` | Apply credit to invoice | DB: `tblcredits` |
| 8 | `test_applying_full_credit_marks_invoice_as_partially_paid` | Partial credit → invoice status 2 | DB: `tblinvoices.status=2` |
| 9 | `test_applying_full_credit_to_full_invoice_marks_as_paid` | Full credit → invoice status 3 | DB: `tblinvoices.status=3` |
| 10 | `test_admin_can_delete_credit_note` | DELETE removes + reverts invoice status | DB: `tblcreditnotes`, `tblinvoices.status` reverted |

---

## EstimateModuleTest (`tests/Feature/EstimateModuleTest.php`)

**11 tests — 40 assertions**

| # | Test | What it covers | Key assertions |
|---|------|----------------|----------------|
| 1 | `test_guest_is_redirected_to_login_when_accessing_estimates` | Auth blocks guests | `assertRedirect(route('login.staff'))` |
| 2 | `test_admin_can_view_estimates_list` | Index page with number, client, status badge | `assertStatus(200)`, `assertSee('1')`, `assertSee('Estimate Client')`, `assertSee('Draft')` |
| 3 | `test_admin_can_create_estimate` | Create with required fields | DB: `tblestimates` |
| 4 | `test_create_estimate_requires_required_fields` | Validation rejects empty | `assertSessionHasErrors(['number', 'clientid', 'currency', 'date', 'total', 'status'])` |
| 5 | `test_admin_can_view_single_estimate` | Show page renders with number + client | `assertStatus(200)`, `assertSee('1')`, `assertSee('Estimate Client')` |
| 6 | `test_admin_can_update_estimate` | PUT update changes fields | DB: `tblestimates` |
| 7 | `test_admin_can_delete_estimate` | DELETE removes | `assertDatabaseMissing('tblestimates')` |
| 8 | `test_admin_can_mark_estimate_as_accepted` | Mark accepted → status 2 | DB: `tblestimates.status=2` |
| 9 | `test_admin_can_mark_estimate_as_declined` | Mark declined → status 3 | DB: `tblestimates.status=3` |
| 10 | `test_admin_can_convert_estimate_to_invoice` | Convert creates invoice + updates estimate status | DB: `tblinvoices`, `tblestimates.status=4` |
| 11 | `test_filter_estimates_by_status` | `?status=` filters with assertSee/DontSee | `assertSee('9992')`, `assertDontSee('9991')` |

---

## ProposalModuleTest (`tests/Feature/ProposalModuleTest.php`)

**13 tests — 43 assertions**

| # | Test | What it covers | Key assertions |
|---|------|----------------|----------------|
| 1 | `test_guest_is_redirected_to_login_when_accessing_proposals` | Auth blocks guests | `assertRedirect(route('login.staff'))` |
| 2 | `test_admin_can_view_proposals_list` | Index page with subject, client, status badge | `assertStatus(200)`, `assertSee('Web Development Proposal')`, `assertSee('Proposal Client')`, `assertSee('Open')` |
| 3 | `test_admin_can_create_proposal` | Create with required fields | DB: `tblproposals` |
| 4 | `test_create_proposal_requires_required_fields` | Validation rejects empty | `assertSessionHasErrors(['subject', 'proposal_to', 'currency', 'date'])` |
| 5 | `test_admin_can_view_single_proposal` | Show page renders with subject | `assertStatus(200)`, `assertSee('Web Development Proposal')`, `assertSee('Proposal Client')` |
| 6 | `test_admin_can_update_proposal` | PUT update changes subject | DB: `tblproposals` |
| 7 | `test_admin_can_delete_proposal` | DELETE removes | `assertDatabaseMissing('tblproposals')` |
| 8 | `test_admin_can_mark_proposal_as_accepted` | Mark accepted → status 2 | DB: `tblproposals.status=2` |
| 9 | `test_admin_can_mark_proposal_as_declined` | Mark declined → status 3 | DB: `tblproposals.status=3` |
| 10 | `test_admin_can_add_comment_to_proposal` | Add comment | DB: `tblproposal_comments` |
| 11 | `test_admin_can_add_note_to_proposal` | Add note | DB: `tblnotes` (rel_type='proposal') |
| 12 | `test_admin_can_send_proposal` | Send proposal changes status 4→1 | DB: `tblproposals.status=1` |
| 13 | `test_filter_proposals_by_status` | `?status=` filters with assertSee/DontSee | `assertSee('Accepted Proposal')`, `assertDontSee('Open Proposal')` |

---

## LeadModuleTest (`tests/Feature/LeadModuleTest.php`)

**20 tests — 79 assertions**

| # | Test | What it covers | Key assertions |
|---|------|----------------|----------------|
| 1 | `test_guest_is_redirected_to_login_when_accessing_leads` | Auth middleware blocks guests on index/create/store | `assertRedirect(route('login.staff'))` |
| 2 | `test_admin_can_view_leads_list` | Index page renders with lead data + view assertions | `assertStatus(200)`, `assertSee` (name, email, company, status, source, value) |
| 3 | `test_admin_can_create_lead` | Full lead creation with required fields | DB: `tblleads`, redirect + session |
| 4 | `test_create_lead_requires_required_fields` | Validation rejects empty name, status, source | `assertSessionHasErrors(['name', 'status', 'source'])` |
| 5 | `test_admin_can_view_single_lead` | Show page renders lead details | `assertStatus(200)`, `assertSee` (name, email, value, status) |
| 6 | `test_admin_can_update_lead` | PUT update changes all lead fields | DB: `tblleads` (name, email, company, status, source, value changed) |
| 7 | `test_admin_can_delete_lead` | DELETE removes lead | `assertDatabaseMissing('tblleads')` |
| 8 | `test_filter_leads_by_status` | `?status=` filters lead list | `assertSee()` / `assertDontSee()` |
| 9 | `test_filter_leads_by_source` | `?source=` filters lead list | `assertSee()` / `assertDontSee()` |
| 10 | `test_filter_leads_by_country` | `?country=` filters lead list | `assertSee()` / `assertDontSee()` |
| 11 | `test_admin_can_store_note_on_lead` | Note creation on lead | DB: `tblnotes` (rel_type='lead') |
| 12 | `test_note_requires_description` | Note validation | `assertSessionHasErrors(['description'])` |
| 13 | `test_admin_can_store_reminder_on_lead` | Reminder creation with date | DB: `tblreminders` |
| 14 | `test_reminder_requires_required_fields` | Reminder validation | `assertSessionHasErrors(['description', 'date'])` |
| 15 | `test_admin_can_mark_lead_as_lost` | Mark lost → status 6, lost=1 | DB: `tblleads.lost=1`, `status=6` |
| 16 | `test_admin_can_mark_lead_as_junk` | Mark junk → status 7, junk=1 | DB: `tblleads.junk=1`, `status=7` |
| 17 | `test_admin_can_convert_lead_to_client` | Convert creates client + updates lead | DB: `tblclients`, DB: `tblleads.client_id` + `status=5`, redirect to client show |
| 18 | `test_admin_can_submit_gdpr_request` | GDPR request creation with type | DB: `tblgdpr_requests` (request_type, description, status) |
| 19 | `test_gdpr_requires_valid_type` | GDPR type validation (in: export,delete,access,rectification) | `assertSessionHasErrors(['type'])` |
| 20 | `test_converted_lead_shows_client_link_in_sidebar` | After conversion, View Client link appears | `assertSee('View Client')` |

---

## Summary

| Test File | Tests | Assertions | Module Coverage |
|-----------|-------|------------|-----------------|
| ClientModuleTest | 15 | 48 | Clients CRUD, Contacts, Groups, Search, Filters, Permissions |
| CreditNoteModuleTest | 10 | 45 | Credit Notes CRUD, Apply to Invoice, Status Revert |
| CurrencyModuleTest | 6 | 17 | Currencies CRUD |
| EstimateModuleTest | 11 | 40 | Estimates CRUD, Mark Accepted/Declined, Convert to Invoice, Filters |
| ExpenseModuleTest | 13 | 38 | Expenses CRUD, Expense Categories CRUD, Filters |
| InvoiceModuleTest | 20 | 58 | Invoices CRUD, Items, Payments, Status Transitions, Notes, Reminders, Email, Sales Activity |
| ItemModuleTest | 13 | 36 | Items CRUD, Items Groups CRUD, Filters |
| LeadModuleTest | 20 | 79 | Leads CRUD, Status/Source/Country Filters, Notes, Reminders, Lost/Junk/Convert, GDPR |
| PaymentModeModuleTest | 6 | 20 | Payment Modes CRUD |
| ProjectModuleTest | 19 | 70 | Projects, Tasks, Milestones, Discussions, Notes, Notifications, Email |
| ProposalModuleTest | 13 | 43 | Proposals CRUD, Mark Accepted/Declined, Comments, Notes, Send, Filters |
| TaxModuleTest | 7 | 20 | Taxes CRUD |
| TicketModuleTest | 17 | 59 | Tickets CRUD, Replies (email threading), Status/Assignment changes, Filters, Email, Notifications |
| ExampleTest | 1 | 1 | Smoke test |
| **Total** | **172** | **609** | |

## Running

```bash
# All feature tests
php vendor/bin/phpunit tests/Feature/

# Single module
php vendor/bin/phpunit tests/Feature/InvoiceModuleTest.php

# Single test
php vendor/bin/phpunit --filter "test_admin_can_create_invoice$" tests/Feature/InvoiceModuleTest.php
```
