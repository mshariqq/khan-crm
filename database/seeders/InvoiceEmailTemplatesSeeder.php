<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvoiceEmailTemplatesSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'type' => 'invoice',
                'slug' => 'invoice-send-to-client',
                'language' => 'english',
                'name' => 'Send Invoice to Customer',
                'subject' => 'Invoice {invoice_number} from {companyname}',
                'message' => '<p>Dear {contact_firstname} {contact_lastname},</p>
<p>Thank you for your business. Please find your invoice details below:</p>
<table cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr><td style="background:#f8fafc;font-weight:bold;">Invoice Number</td><td>{invoice_number}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Invoice Date</td><td>{invoice_date}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Due Date</td><td>{due_date}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Total Amount</td><td>{invoice_total}</td></tr>
</table>
<p>You can view and pay your invoice online by clicking the button below:</p>
<p style="text-align:center;margin:24px 0;">
<a href="{invoice_link}" style="display:inline-block;padding:12px 32px;background:#2563eb;color:#ffffff;text-decoration:none;border-radius:8px;font-weight:bold;">View Invoice & Pay Online</a>
</p>
<p>{notes}</p>
<p>Best regards,<br>{companyname}</p>',
                'fromname' => '{companyname}',
                'fromemail' => '',
                'plaintext' => 0,
                'active' => 1,
                'order' => 0,
            ],
            [
                'type' => 'invoice',
                'slug' => 'invoice-payment-recorded',
                'language' => 'english',
                'name' => 'Payment Recorded (Client Receipt)',
                'subject' => 'Payment Received — Invoice {invoice_number}',
                'message' => '<p>Dear {contact_firstname} {contact_lastname},</p>
<p>We have received a payment against Invoice <strong>{invoice_number}</strong>.</p>
<table cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr><td style="background:#f8fafc;font-weight:bold;">Invoice</td><td>{invoice_number}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Payment Amount</td><td>{payment_total}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Payment Date</td><td>{payment_date}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Amount Paid</td><td>{amount_paid}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Balance Due</td><td>{balance_due}</td></tr>
</table>
<p>Thank you for your payment.</p>
<p>Best regards,<br>{companyname}</p>',
                'fromname' => '{companyname}',
                'fromemail' => '',
                'plaintext' => 0,
                'active' => 1,
                'order' => 0,
            ],
            [
                'type' => 'invoice',
                'slug' => 'invoice-paid',
                'language' => 'english',
                'name' => 'Invoice Fully Paid / Receipt',
                'subject' => 'Invoice {invoice_number} — Paid in Full',
                'message' => '<p>Dear {contact_firstname} {contact_lastname},</p>
<p>Invoice <strong>{invoice_number}</strong> has been paid in full. Thank you!</p>
<table cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr><td style="background:#f8fafc;font-weight:bold;">Invoice Number</td><td>{invoice_number}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Total Amount</td><td>{invoice_total}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Amount Paid</td><td>{amount_paid}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Balance Due</td><td>{balance_due}</td></tr>
</table>
<p>You can download your receipt at any time using the link below:</p>
<p style="text-align:center;margin:24px 0;">
<a href="{invoice_link}" style="display:inline-block;padding:12px 32px;background:#2563eb;color:#ffffff;text-decoration:none;border-radius:8px;font-weight:bold;">View Receipt</a>
</p>
<p>Best regards,<br>{companyname}</p>',
                'fromname' => '{companyname}',
                'fromemail' => '',
                'plaintext' => 0,
                'active' => 1,
                'order' => 0,
            ],
            [
                'type' => 'invoice',
                'slug' => 'invoice-cancelled',
                'language' => 'english',
                'name' => 'Invoice Cancelled',
                'subject' => 'Invoice {invoice_number} has been cancelled',
                'message' => '<p>Dear {contact_firstname} {contact_lastname},</p>
<p>Invoice <strong>{invoice_number}</strong> dated {invoice_date} for <strong>{invoice_total}</strong> has been cancelled.</p>
<p>If you have any questions regarding this cancellation, please contact us.</p>
<p>Best regards,<br>{companyname}</p>',
                'fromname' => '{companyname}',
                'fromemail' => '',
                'plaintext' => 0,
                'active' => 1,
                'order' => 0,
            ],
            [
                'type' => 'invoice',
                'slug' => 'invoice-overdue',
                'language' => 'english',
                'name' => 'Invoice Overdue Notice',
                'subject' => 'Overdue Notice — Invoice {invoice_number}',
                'message' => '<p>Dear {contact_firstname} {contact_lastname},</p>
<p>This is a reminder that Invoice <strong>{invoice_number}</strong> for <strong>{invoice_total}</strong> is now overdue.</p>
<table cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr><td style="background:#f8fafc;font-weight:bold;">Invoice</td><td>{invoice_number}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Due Date</td><td>{due_date}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Amount Due</td><td>{amount_due}</td></tr>
</table>
<p>Please arrange payment at your earliest convenience to avoid any further escalation.</p>
<p style="text-align:center;margin:24px 0;">
<a href="{invoice_link}" style="display:inline-block;padding:12px 32px;background:#2563eb;color:#ffffff;text-decoration:none;border-radius:8px;font-weight:bold;">Pay Now</a>
</p>
<p>Best regards,<br>{companyname}</p>',
                'fromname' => '{companyname}',
                'fromemail' => '',
                'plaintext' => 0,
                'active' => 1,
                'order' => 0,
            ],
            [
                'type' => 'invoice',
                'slug' => 'invoice-due-today',
                'language' => 'english',
                'name' => 'Invoice Due Today',
                'subject' => 'Invoice {invoice_number} is due today',
                'message' => '<p>Dear {contact_firstname} {contact_lastname},</p>
<p>This is a friendly reminder that Invoice <strong>{invoice_number}</strong> for <strong>{invoice_total}</strong> is due today.</p>
<table cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr><td style="background:#f8fafc;font-weight:bold;">Invoice</td><td>{invoice_number}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Due Date</td><td>{due_date}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Amount Due</td><td>{amount_due}</td></tr>
</table>
<p style="text-align:center;margin:24px 0;">
<a href="{invoice_link}" style="display:inline-block;padding:12px 32px;background:#2563eb;color:#ffffff;text-decoration:none;border-radius:8px;font-weight:bold;">Pay Now</a>
</p>
<p>Best regards,<br>{companyname}</p>',
                'fromname' => '{companyname}',
                'fromemail' => '',
                'plaintext' => 0,
                'active' => 1,
                'order' => 0,
            ],
            [
                'type' => 'invoice',
                'slug' => 'invoice-reminder',
                'language' => 'english',
                'name' => 'Invoice Reminder (Before Due)',
                'subject' => 'Reminder — Invoice {invoice_number} due soon',
                'message' => '<p>Dear {contact_firstname} {contact_lastname},</p>
<p>This is a reminder that Invoice <strong>{invoice_number}</strong> for <strong>{invoice_total}</strong> is due on <strong>{due_date}</strong>.</p>
<table cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr><td style="background:#f8fafc;font-weight:bold;">Invoice</td><td>{invoice_number}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Due Date</td><td>{due_date}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Amount Due</td><td>{amount_due}</td></tr>
</table>
<p style="text-align:center;margin:24px 0;">
<a href="{invoice_link}" style="display:inline-block;padding:12px 32px;background:#2563eb;color:#ffffff;text-decoration:none;border-radius:8px;font-weight:bold;">Pay Now</a>
</p>
<p>Best regards,<br>{companyname}</p>',
                'fromname' => '{companyname}',
                'fromemail' => '',
                'plaintext' => 0,
                'active' => 1,
                'order' => 0,
            ],
            [
                'type' => 'invoice',
                'slug' => 'invoice-updated',
                'language' => 'english',
                'name' => 'Invoice Updated / Revised',
                'subject' => 'Updated Invoice {invoice_number} from {companyname}',
                'message' => '<p>Dear {contact_firstname} {contact_lastname},</p>
<p>Invoice <strong>{invoice_number}</strong> has been updated. Please find the revised details below:</p>
<table cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr><td style="background:#f8fafc;font-weight:bold;">Invoice Number</td><td>{invoice_number}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Invoice Date</td><td>{invoice_date}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Due Date</td><td>{due_date}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Total Amount</td><td>{invoice_total}</td></tr>
</table>
<p style="text-align:center;margin:24px 0;">
<a href="{invoice_link}" style="display:inline-block;padding:12px 32px;background:#2563eb;color:#ffffff;text-decoration:none;border-radius:8px;font-weight:bold;">View Updated Invoice</a>
</p>
<p>Best regards,<br>{companyname}</p>',
                'fromname' => '{companyname}',
                'fromemail' => '',
                'plaintext' => 0,
                'active' => 1,
                'order' => 0,
            ],
            [
                'type' => 'invoice',
                'slug' => 'payment-link-sent',
                'language' => 'english',
                'name' => 'Payment Link Sent',
                'subject' => 'Payment Link for Invoice {invoice_number}',
                'message' => '<p>Dear {contact_firstname} {contact_lastname},</p>
<p>You can pay Invoice <strong>{invoice_number}</strong> for <strong>{invoice_total}</strong> online using the secure payment link below:</p>
<p style="text-align:center;margin:24px 0;">
<a href="{payment_link}" style="display:inline-block;padding:12px 32px;background:#2563eb;color:#ffffff;text-decoration:none;border-radius:8px;font-weight:bold;">Pay Invoice Online</a>
</p>
<p>This link will remain active until the invoice is paid.</p>
<p>Best regards,<br>{companyname}</p>',
                'fromname' => '{companyname}',
                'fromemail' => '',
                'plaintext' => 0,
                'active' => 1,
                'order' => 0,
            ],
            [
                'type' => 'invoice',
                'slug' => 'credit-note-issued',
                'language' => 'english',
                'name' => 'Credit Note Issued',
                'subject' => 'Credit Note Issued for Invoice {invoice_number}',
                'message' => '<p>Dear {contact_firstname} {contact_lastname},</p>
<p>A credit note has been issued against Invoice <strong>{invoice_number}</strong>.</p>
<table cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr><td style="background:#f8fafc;font-weight:bold;">Credit Note</td><td>{credit_note_number}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Invoice</td><td>{invoice_number}</td></tr>
</table>
<p>If you have any questions, please contact us.</p>
<p>Best regards,<br>{companyname}</p>',
                'fromname' => '{companyname}',
                'fromemail' => '',
                'plaintext' => 0,
                'active' => 1,
                'order' => 0,
            ],
            [
                'type' => 'invoice',
                'slug' => 'refund-processed',
                'language' => 'english',
                'name' => 'Refund Processed',
                'subject' => 'Refund Processed for Invoice {invoice_number}',
                'message' => '<p>Dear {contact_firstname} {contact_lastname},</p>
<p>A refund of <strong>{refund_amount}</strong> has been processed against Invoice <strong>{invoice_number}</strong>.</p>
<p>The refunded amount should appear in your account within 5-7 business days depending on your payment provider.</p>
<p>If you have any questions, please contact us.</p>
<p>Best regards,<br>{companyname}</p>',
                'fromname' => '{companyname}',
                'fromemail' => '',
                'plaintext' => 0,
                'active' => 1,
                'order' => 0,
            ],
            [
                'type' => 'invoice',
                'slug' => 'invoice-payment-received-staff',
                'language' => 'english',
                'name' => 'Payment Received (Staff Alert)',
                'subject' => 'Payment Received — Invoice {invoice_number} — {amount_paid}',
                'message' => '<p>Hi {rep_name},</p>
<p>A payment has been received for Invoice <strong>{invoice_number}</strong>.</p>
<table cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr><td style="background:#f8fafc;font-weight:bold;">Client</td><td>{client_name}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Invoice</td><td>{invoice_number}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Payment Amount</td><td>{payment_total}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Payment Date</td><td>{payment_date}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Balance Due</td><td>{balance_due}</td></tr>
</table>
<p style="text-align:center;margin:24px 0;">
<a href="{staff_link}" style="display:inline-block;padding:12px 32px;background:#2563eb;color:#ffffff;text-decoration:none;border-radius:8px;font-weight:bold;">View in CRM</a>
</p>
<p>Best regards,<br>{companyname}</p>',
                'fromname' => '{companyname}',
                'fromemail' => '',
                'plaintext' => 0,
                'active' => 1,
                'order' => 0,
            ],
            [
                'type' => 'invoice',
                'slug' => 'invoice-overdue-staff',
                'language' => 'english',
                'name' => 'Invoice Overdue (Staff Alert)',
                'subject' => 'Overdue Alert — Invoice {invoice_number} — {client_name}',
                'message' => '<p>Hi {rep_name},</p>
<p>Invoice <strong>{invoice_number}</strong> for <strong>{client_name}</strong> is overdue.</p>
<table cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr><td style="background:#f8fafc;font-weight:bold;">Client</td><td>{client_name}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Invoice</td><td>{invoice_number}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Due Date</td><td>{due_date}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Amount Due</td><td>{amount_due}</td></tr>
</table>
<p style="text-align:center;margin:24px 0;">
<a href="{staff_link}" style="display:inline-block;padding:12px 32px;background:#2563eb;color:#ffffff;text-decoration:none;border-radius:8px;font-weight:bold;">View in CRM</a>
</p>
<p>Best regards,<br>{companyname}</p>',
                'fromname' => '{companyname}',
                'fromemail' => '',
                'plaintext' => 0,
                'active' => 1,
                'order' => 0,
            ],
            [
                'type' => 'invoice',
                'slug' => 'high-value-invoice-created',
                'language' => 'english',
                'name' => 'High Value Invoice Created (Staff Alert)',
                'subject' => 'High-Value Invoice — {invoice_number} — {invoice_total}',
                'message' => '<p>Hi Team,</p>
<p>A high-value invoice has been created.</p>
<table cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr><td style="background:#f8fafc;font-weight:bold;">Client</td><td>{client_name}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Invoice</td><td>{invoice_number}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Total Amount</td><td>{invoice_total}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Due Date</td><td>{due_date}</td></tr>
<tr><td style="background:#f8fafc;font-weight:bold;">Sales Rep</td><td>{rep_name}</td></tr>
</table>
<p style="text-align:center;margin:24px 0;">
<a href="{staff_link}" style="display:inline-block;padding:12px 32px;background:#2563eb;color:#ffffff;text-decoration:none;border-radius:8px;font-weight:bold;">View in CRM</a>
</p>
<p>Best regards,<br>{companyname}</p>',
                'fromname' => '{companyname}',
                'fromemail' => '',
                'plaintext' => 0,
                'active' => 1,
                'order' => 0,
            ],
        ];

        foreach ($templates as $template) {
            $existing = DB::table('tblemailtemplates')->where('slug', $template['slug'])->first();
            if (!$existing) {
                DB::table('tblemailtemplates')->insert($template);
            }
        }
    }
}
