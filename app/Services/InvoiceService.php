<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Itemable;
use App\Models\Tax;
use App\Helpers\SettingsHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class InvoiceService extends BaseService
{
    protected AuditService $auditService;
    protected InvoiceNotificationService $invoiceNotification;

    public function __construct(AuditService $auditService, InvoiceNotificationService $invoiceNotification)
    {
        $this->auditService = $auditService;
        $this->invoiceNotification = $invoiceNotification;
    }

    public function prepareInvoiceData(array $validated, $request): array
    {
        $validated['subtotal'] = $validated['subtotal'] ?? 0;
        $validated['total_tax'] = $validated['total_tax'] ?? 0;
        $validated['adjustment'] = $validated['adjustment'] ?? 0;
        $validated['discount_percent'] = $validated['discount_percent'] ?? 0;
        $validated['discount_total'] = $validated['discount_total'] ?? 0;
        $validated['include_shipping'] = $validated['include_shipping'] ?? 0;
        $validated['show_shipping_on_invoice'] = $validated['show_shipping_on_invoice'] ?? 0;
        $validated['recurring'] = $validated['recurring'] ?? 0;
        $validated['cancel_overdue_reminders'] = $validated['cancel_overdue_reminders'] ?? 0;

        $data = $validated;
        $data['datecreated'] = now();
        $data['addedfrom'] = Auth::id();
        $data['hash'] = Str::random(32);
        $data['sent'] = 0;
        $data['discount_type'] = $request->discount_type ?? 'percent';
        $showQtyMap = ['qty' => 1, 'hours' => 2, 'items' => 3];
        $data['show_quantity_as'] = $showQtyMap[$request->show_quantity_as] ?? (int)($request->show_quantity_as ?? 1);
        $data['sale_agent'] = $request->sale_agent ?? 0;
        $data['project_id'] = $request->project_id ?? 0;
        $data['subscription_id'] = $request->subscription_id ?? 0;
        $data['billing_country'] = $request->billing_country ?? 0;
        $data['shipping_country'] = $request->shipping_country ?? 0;
        $data['prefix'] = $request->prefix ?? '';
        $data['number_format'] = $request->number_format ?? 0;

        if (!empty($request->duedate)) $data['duedate'] = $request->duedate;

        return $data;
    }

    public function generateNextNumber(): int
    {
        $lastInvoice = Invoice::lockForUpdate()->orderBy('id', 'desc')->first();
        return $lastInvoice ? (int)preg_replace('/[^0-9]/', '', $lastInvoice->number) + 1 : (int)SettingsHelper::get('next_invoice_number', 1);
    }

    public function saveItems(Invoice $invoice, $request): array
    {
        $calculatedSubtotal = 0;
        $calculatedTotalTax = 0;

        if ($request->has('item_description')) {
            $descriptions = $request->item_description;
            $qtys = $request->item_qty ?? [];
            $rates = $request->item_rate ?? [];
            $amounts = $request->item_amount ?? [];
            $taxIds = $request->item_tax_id ?? [];

            foreach ($descriptions as $index => $description) {
                if (!empty($description)) {
                    $taxRate = 0;
                    if (!empty($taxIds[$index])) {
                        $tax = Tax::find($taxIds[$index]);
                        if ($tax) {
                            $taxRate = $tax->taxrate;
                        }
                    }

                    $itemAmount = $amounts[$index] ?? 0;

                    Itemable::create([
                        'rel_id' => $invoice->id,
                        'rel_type' => 'invoice',
                        'description' => $description,
                        'qty' => $qtys[$index] ?? 1,
                        'rate' => $rates[$index] ?? 0,
                        'amount' => $itemAmount,
                        'tax_rate' => $taxRate,
                        'item_order' => $index,
                    ]);

                    $calculatedSubtotal += (float) $itemAmount;
                    if ($taxRate > 0) {
                        $calculatedTotalTax += (float) $itemAmount * $taxRate / 100;
                    }
                }
            }
        }

        return ['subtotal' => $calculatedSubtotal, 'total_tax' => $calculatedTotalTax];
    }

    public function recalculateTotals(Invoice $invoice, float $calculatedSubtotal, float $calculatedTotalTax, float $adjustment, float $discountTotal): void
    {
        $invoice->update([
            'subtotal' => $calculatedSubtotal,
            'total_tax' => $calculatedTotalTax,
            'total' => $calculatedSubtotal + $calculatedTotalTax + $adjustment - $discountTotal,
        ]);
    }

    public function syncItemsFromRequest(Invoice $invoice, $request, array &$newItems): array
    {
        $calculatedSubtotal = 0;
        $calculatedTotalTax = 0;

        if ($request->has('item_description')) {
            $descriptions = $request->item_description;
            $qtys = $request->item_qty ?? [];
            $rates = $request->item_rate ?? [];
            $amounts = $request->item_amount ?? [];
            $taxIds = $request->item_tax_id ?? [];

            foreach ($descriptions as $index => $description) {
                if (!empty($description)) {
                    $taxRate = 0;
                    if (!empty($taxIds[$index])) {
                        $tax = Tax::find($taxIds[$index]);
                        if ($tax) {
                            $taxRate = $tax->taxrate;
                        }
                    }
                    $newItems[] = [
                        'description' => $description,
                        'qty' => $qtys[$index] ?? 1,
                        'rate' => $rates[$index] ?? 0,
                        'amount' => $amounts[$index] ?? 0,
                        'tax_rate' => $taxRate,
                        'item_order' => $index,
                    ];
                }
            }
        }

        $invoice->items()->delete();
        foreach ($newItems as $itemData) {
            Itemable::create(array_merge($itemData, [
                'rel_id' => $invoice->id,
                'rel_type' => 'invoice',
            ]));
            $calculatedSubtotal += (float) ($itemData['amount'] ?? 0);
            if (!empty($itemData['tax_rate'])) {
                $calculatedTotalTax += (float) ($itemData['amount'] ?? 0) * (float) $itemData['tax_rate'] / 100;
            }
        }

        return ['subtotal' => $calculatedSubtotal, 'total_tax' => $calculatedTotalTax];
    }

    public function logActivity(Invoice $invoice, string $description): void
    {
        \App\Models\SalesActivity::create([
            'rel_type' => 'invoice',
            'rel_id' => $invoice->id,
            'description' => $description,
            'staffid' => Auth::user()->staffid ?? 0,
            'full_name' => (Auth::user()->firstname ?? '') . ' ' . (Auth::user()->lastname ?? ''),
            'date' => now(),
        ]);
    }
}
