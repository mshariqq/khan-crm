<?php

namespace App\Services;

use App\Models\Estimate;
use App\Models\Itemable;
use App\Models\Tax;
use App\Helpers\SettingsHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EstimateService extends BaseService
{
    public function prepareEstimateData(array $validated, $request): array
    {
        $validated['subtotal'] = $validated['subtotal'] ?? 0;
        $validated['total_tax'] = $validated['total_tax'] ?? 0;
        $validated['adjustment'] = $validated['adjustment'] ?? 0;
        $validated['discount_percent'] = $validated['discount_percent'] ?? 0;
        $validated['discount_total'] = $validated['discount_total'] ?? 0;
        $validated['include_shipping'] = $validated['include_shipping'] ?? 0;
        $validated['show_shipping_on_estimate'] = $validated['show_shipping_on_estimate'] ?? 0;

        $data = $validated;
        $data['date'] = $request->date ?? date('Y-m-d');
        $data['datecreated'] = now();
        $data['addedfrom'] = Auth::id();
        $data['hash'] = Str::random(32);
        $data['sent'] = 0;
        $data['show_quantity_as'] = $request->show_quantity_as ?? 1;
        $data['discount_type'] = $request->discount_type ?? 'percent';
        $data['sale_agent'] = $request->sale_agent ?? 0;
        $data['project_id'] = $request->project_id ?? 0;
        $data['billing_country'] = $request->billing_country ?? 0;
        $data['shipping_country'] = $request->shipping_country ?? 0;
        $data['prefix'] = $request->prefix ?? '';
        $data['number_format'] = $request->number_format ?? 0;

        if (!empty($request->reference_no)) $data['reference_no'] = $request->reference_no;
        if (!empty($request->expirydate)) $data['expirydate'] = $request->expirydate;

        return $data;
    }

    public function generateNextNumber(): int
    {
        $lastEstimate = Estimate::lockForUpdate()->orderBy('id', 'desc')->first();
        return $lastEstimate ? (int)preg_replace('/[^0-9]/', '', $lastEstimate->number) + 1 : (int)SettingsHelper::get('next_estimate_number', 1);
    }

    public function saveItems(Estimate $estimate, $request): array
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
                        'rel_id' => $estimate->id,
                        'rel_type' => 'estimate',
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

    public function recalculateTotals(Estimate $estimate, float $calculatedSubtotal, float $calculatedTotalTax, float $adjustment, float $discountTotal): void
    {
        $estimate->update([
            'subtotal' => $calculatedSubtotal,
            'total_tax' => $calculatedTotalTax,
            'total' => $calculatedSubtotal + $calculatedTotalTax + $adjustment - $discountTotal,
        ]);
    }

    public function syncItemsFromRequest(Estimate $estimate, $request): array
    {
        $estimate->items()->delete();
        $calculatedSubtotal = 0;
        $calculatedTotalTax = 0;

        if ($request->has('items')) {
            foreach ($request->items as $index => $item) {
                if (!empty($item['description'])) {
                    $taxRate = 0;
                    if (!empty($item['tax_id'])) {
                        $tax = Tax::find($item['tax_id']);
                        if ($tax) {
                            $taxRate = $tax->taxrate;
                        }
                    }

                    $itemAmount = $item['amount'] ?? (($item['qty'] ?? 1) * ($item['rate'] ?? 0));

                    Itemable::create([
                        'rel_id' => $estimate->id,
                        'rel_type' => 'estimate',
                        'description' => $item['description'],
                        'qty' => $item['qty'] ?? 1,
                        'rate' => $item['rate'] ?? 0,
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
}
