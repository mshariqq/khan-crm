<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\EmailTemplate;
use App\Models\Staff;
use App\Helpers\SettingsHelper;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class InvoiceNotificationService
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public static function types(): array
    {
        return [
            'invoice-send-to-client' => [
                'name' => 'Send Invoice to Customer',
                'recipients' => 'client',
                'description' => 'Triggered when invoice is manually sent to the client',
            ],
            'invoice-payment-recorded' => [
                'name' => 'Payment Recorded (Client Receipt)',
                'recipients' => 'client',
                'description' => 'Triggered when a payment is recorded against the invoice',
            ],
            'invoice-paid' => [
                'name' => 'Invoice Fully Paid / Receipt',
                'recipients' => 'client',
                'description' => 'Triggered when invoice status changes to Paid',
            ],
            'invoice-cancelled' => [
                'name' => 'Invoice Cancelled',
                'recipients' => 'client',
                'description' => 'Triggered when invoice is marked as cancelled',
            ],
            'invoice-overdue' => [
                'name' => 'Invoice Overdue Notice',
                'recipients' => 'client',
                'description' => 'Triggered when invoice becomes overdue',
            ],
            'invoice-due-today' => [
                'name' => 'Invoice Due Today',
                'recipients' => 'client',
                'description' => 'Triggered on the due date of the invoice',
            ],
            'invoice-reminder' => [
                'name' => 'Invoice Reminder (Before Due)',
                'recipients' => 'client',
                'description' => 'Sent as a reminder before the due date',
            ],
            'invoice-updated' => [
                'name' => 'Invoice Updated / Revised',
                'recipients' => 'client',
                'description' => 'Triggered when the invoice is updated after being sent',
            ],
            'payment-link-sent' => [
                'name' => 'Payment Link Sent',
                'recipients' => 'client',
                'description' => 'Triggered when a payment link is sent to the client',
            ],
            'credit-note-issued' => [
                'name' => 'Credit Note Issued',
                'recipients' => 'client',
                'description' => 'Triggered when a credit note is applied against the invoice',
            ],
            'refund-processed' => [
                'name' => 'Refund Processed',
                'recipients' => 'client',
                'description' => 'Triggered when a refund is processed for the invoice',
            ],
            'invoice-payment-received-staff' => [
                'name' => 'Payment Received (Staff Alert)',
                'recipients' => 'staff',
                'description' => 'Internal alert to staff when a payment is received',
            ],
            'invoice-overdue-staff' => [
                'name' => 'Invoice Overdue (Staff Alert)',
                'recipients' => 'staff',
                'description' => 'Internal escalation when invoice is overdue',
            ],
            'high-value-invoice-created' => [
                'name' => 'High Value Invoice Created (Staff Alert)',
                'recipients' => 'staff',
                'description' => 'Alert when an invoice exceeds the configured threshold',
            ],
        ];
    }

    public function send(string $slug, int $invoiceId, array $options = []): array
    {
        $types = static::types();
        if (!isset($types[$slug])) {
            return ['success' => false, 'message' => "Unknown notification slug: {$slug}"];
        }

        $invoice = Invoice::with([
            'client.primaryContact', 'currency', 'creator', 'saleAgent', 'payments', 'items'
        ])->find($invoiceId);

        if (!$invoice) {
            return ['success' => false, 'message' => "Invoice #{$invoiceId} not found"];
        }

        $template = EmailTemplate::where('slug', $slug)->where('active', 1)->first();
        if (!$template) {
            return ['success' => false, 'message' => "Email template '{$slug}' is inactive or not found"];
        }

        $data = $this->buildData($invoice, $options);
        $parsed = $template->parse($data);

        $globalSubject = str_replace(
            ['{companyname}', '{crm_url}', '{email_signature}'],
            [
                SettingsHelper::get('companyname', ''),
                config('app.url'),
                SettingsHelper::get('email_signature', ''),
            ],
            $parsed['subject']
        );
        $globalMessage = str_replace(
            ['{companyname}', '{crm_url}', '{email_signature}'],
            [
                SettingsHelper::get('companyname', ''),
                config('app.url'),
                SettingsHelper::get('email_signature', ''),
            ],
            $parsed['message']
        );

        $recipientType = $types[$slug]['recipients'];
        $recipients = $options['recipients'] ?? $recipientType;

        $sent = [];
        $errors = [];

        if ($recipients === 'client' || $recipients === 'both') {
            $clientResult = $this->sendToClient($invoice, $template, $globalSubject, $globalMessage, $data, $options);
            if ($clientResult['success']) {
                $sent[] = $clientResult;
            } else {
                $errors[] = $clientResult;
            }
        }

        if ($recipients === 'staff' || $recipients === 'both') {
            $staffResult = $this->sendToStaff($invoice, $template, $globalSubject, $globalMessage, $data, $options);
            if ($staffResult['success']) {
                $sent[] = $staffResult;
            } else {
                $errors[] = $staffResult;
            }
        }

        return [
            'success' => empty($errors),
            'sent' => $sent,
            'errors' => $errors,
            'message' => empty($errors)
                ? 'Notification sent successfully'
                : 'Some notifications failed',
        ];
    }

    public function sendToClient(
        Invoice $invoice,
        EmailTemplate $template,
        string $subject,
        string $message,
        array $data,
        array $options = []
    ): array {
        $contact = $invoice->client->primaryContact;
        if (!$contact || !$contact->email) {
            return ['success' => false, 'message' => 'No primary contact email found', 'recipient' => 'client'];
        }

        try {
            $viewData = [
                'subject' => $subject,
                'body' => $message,
                'logo' => $this->getLogo(),
                'company_name' => SettingsHelper::get('companyname', ''),
                'company_address' => SettingsHelper::get('companyaddress', ''),
            ];

            Mail::send('emails.base', $viewData, function ($mail) use ($contact, $template, $subject, $invoice) {
                $mail->to($contact->email, $contact->firstname . ' ' . $contact->lastname);
                $mail->subject($subject);
                $mail->from(
                    $template->fromemail ?: SettingsHelper::get('smtp_email', config('mail.from.address')),
                    $template->fromname ? str_replace('{companyname}', SettingsHelper::get('companyname', ''), $template->fromname) : SettingsHelper::get('companyname', config('mail.from.name'))
                );
                if (!empty($options['reply_to'])) {
                    $mail->replyTo($options['reply_to']);
                }
            });

            $this->logActivity($invoice->id, 'Email sent to <b>' . $contact->email . '</b> — ' . $template->name);

            return ['success' => true, 'recipient' => 'client', 'email' => $contact->email];
        } catch (\Exception $e) {
            Log::error("Invoice notification failed for {$contact->email}: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage(), 'recipient' => 'client', 'email' => $contact->email];
        }
    }

    public function sendToStaff(
        Invoice $invoice,
        EmailTemplate $template,
        string $subject,
        string $message,
        array $data,
        array $options = []
    ): array {
        $staffMembers = [];

        if ($invoice->saleAgent && $invoice->saleAgent->email) {
            $staffMembers[] = $invoice->saleAgent;
        }
        if ($invoice->creator && $invoice->creator->email) {
            $staffMembers[] = $invoice->creator;
        }

        $admins = Staff::where('admin', 1)->get();
        foreach ($admins as $admin) {
            if (!collect($staffMembers)->contains('staffid', $admin->staffid)) {
                $staffMembers[] = $admin;
            }
        }

        if (empty($staffMembers)) {
            return ['success' => false, 'message' => 'No staff recipients found', 'recipient' => 'staff'];
        }

        $sent = [];
        foreach ($staffMembers as $staff) {
            try {
                $viewData = [
                    'subject' => $subject,
                    'body' => $message,
                    'logo' => $this->getLogo(),
                    'company_name' => SettingsHelper::get('companyname', ''),
                    'company_address' => SettingsHelper::get('companyaddress', ''),
                ];

                Mail::send('emails.base', $viewData, function ($mail) use ($staff, $template, $subject) {
                    $mail->to($staff->email, $staff->firstname . ' ' . $staff->lastname);
                    $mail->subject($subject);
                    $mail->from(
                        $template->fromemail ?: SettingsHelper::get('smtp_email', config('mail.from.address')),
                        $template->fromname ? str_replace('{companyname}', SettingsHelper::get('companyname', ''), $template->fromname) : SettingsHelper::get('companyname', config('mail.from.name'))
                    );
                });

                $sent[] = $staff->email;
            } catch (\Exception $e) {
                Log::error("Invoice staff notification failed for {$staff->email}: " . $e->getMessage());
            }
        }

        if (!empty($sent)) {
            $this->logActivity($invoice->id, 'Staff notification sent to ' . implode(', ', $sent) . ' — ' . $template->name);
        }

        return ['success' => !empty($sent), 'recipient' => 'staff', 'emails' => $sent];
    }

    protected function buildData(Invoice $invoice, array $options = []): array
    {
        $client = $invoice->client;
        $contact = $client ? $client->primaryContact : null;
        $currencySymbol = $invoice->currency->symbol ?? '$';
        $invoiceNumber = ($invoice->prefix ?? '') . $invoice->number;
        $amountDue = $invoice->total - $invoice->payments()->sum('amount');
        $amountPaid = $invoice->payments()->sum('amount');

        $lineItemsHtml = '';
        foreach ($invoice->items as $item) {
            $lineItemsHtml .= '<tr>';
            $lineItemsHtml .= '<td style="padding:8px;border-bottom:1px solid #e2e8f0;">' . e($item->description) . '</td>';
            $lineItemsHtml .= '<td style="padding:8px;border-bottom:1px solid #e2e8f0;text-align:center;">' . (int)$item->qty . '</td>';
            $lineItemsHtml .= '<td style="padding:8px;border-bottom:1px solid #e2e8f0;text-align:right;">' . $currencySymbol . number_format($item->rate, 2) . '</td>';
            $lineItemsHtml .= '<td style="padding:8px;border-bottom:1px solid #e2e8f0;text-align:right;">' . $currencySymbol . number_format($item->amount, 2) . '</td>';
            $lineItemsHtml .= '</tr>';
        }

        return array_merge([
            'contact_firstname' => $contact->firstname ?? $client->company ?? '',
            'contact_lastname' => $contact->lastname ?? '',
            'client_name' => $client->company ?? '',
            'client_company' => $client->company ?? '',
            'invoice_number' => $invoiceNumber,
            'invoice_id' => (string)$invoice->id,
            'invoice_date' => $invoice->date ? \Carbon\Carbon::parse($invoice->date)->format('d M, Y') : '',
            'due_date' => $invoice->duedate ? \Carbon\Carbon::parse($invoice->duedate)->format('d M, Y') : '',
            'amount_due' => $currencySymbol . number_format(max($amountDue, 0), 2),
            'amount_paid' => $currencySymbol . number_format($amountPaid, 2),
            'balance_due' => $currencySymbol . number_format(max($amountDue, 0), 2),
            'invoice_total' => $currencySymbol . number_format($invoice->total, 2),
            'subtotal' => $currencySymbol . number_format($invoice->subtotal, 2),
            'total_tax' => $currencySymbol . number_format($invoice->total_tax, 2),
            'currency' => $currencySymbol,
            'currency_code' => $invoice->currency->name ?? 'USD',
            'payment_link' => config('app.url') . '/invoices/' . $invoice->id . '/' . $invoice->hash,
            'invoice_link' => config('app.url') . '/invoices/' . $invoice->id . '/' . $invoice->hash,
            'staff_link' => route('admin.invoices.show', $invoice->id),
            'line_items' => $lineItemsHtml,
            'company_name' => SettingsHelper::get('companyname', ''),
            'company_logo' => $this->getLogo(),
            'company_address' => SettingsHelper::get('companyaddress', ''),
            'rep_name' => $invoice->saleAgent ? trim($invoice->saleAgent->firstname . ' ' . $invoice->saleAgent->lastname) : '',
            'gstin' => $client->vat ?? '',
            'notes' => $invoice->clientnote ?? '',
            'terms' => $invoice->terms ?? '',
            'invoice_status' => $this->statusLabel($invoice->status),
            'payment_date' => $options['payment_date'] ?? '',
            'payment_total' => $options['payment_total'] ?? '',
            'payment_method' => $options['payment_method'] ?? '',
            'credit_note_number' => $options['credit_note_number'] ?? '',
            'refund_amount' => $options['refund_amount'] ?? '',
        ], $this->getPaymentData($invoice));
    }

    protected function getPaymentData(Invoice $invoice): array
    {
        $lastPayment = $invoice->payments()->latest()->first();
        $currencySymbol = $invoice->currency->symbol ?? '$';
        return [
            'last_payment_amount' => $lastPayment ? $currencySymbol . number_format($lastPayment->amount, 2) : '',
            'last_payment_date' => $lastPayment ? \Carbon\Carbon::parse($lastPayment->date)->format('d M, Y') : '',
            'last_payment_method' => $lastPayment ? ($lastPayment->paymentMode->name ?? '') : '',
        ];
    }

    protected function statusLabel(int $status): string
    {
        return match ($status) {
            1 => 'Unpaid',
            2 => 'Partially Paid',
            3 => 'Paid',
            4 => 'Cancelled',
            default => 'Unknown',
        };
    }

    protected function getLogo(): string
    {
        $logo = SettingsHelper::get('company_logo', '');
        if ($logo && file_exists(public_path('uploads/' . $logo))) {
            return asset('uploads/' . $logo);
        }
        return '';
    }

    protected function logActivity(int $invoiceId, string $description): void
    {
        try {
            \App\Models\SalesActivity::create([
                'rel_type' => 'invoice',
                'rel_id' => $invoiceId,
                'description' => $description,
                'staffid' => auth()->id() ?? 0,
                'full_name' => auth()->user() ? (auth()->user()->firstname ?? '') . ' ' . (auth()->user()->lastname ?? '') : 'System',
                'date' => now(),
            ]);
        } catch (\Exception $e) {
            Log::warning("Failed to log invoice activity: " . $e->getMessage());
        }
    }
}
