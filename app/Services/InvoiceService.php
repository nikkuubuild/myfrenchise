<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Order;

class InvoiceService
{
    /**
     * Generate statutory GST Tax Invoice for a paid order.
     * Enforces Indian GST rules:
     * - Intra-state (Haryana -> Haryana): CGST 9% + SGST 9%
     * - Inter-state (Haryana -> Other State): IGST 18%
     */
    public function generateInvoice(Order $order): Invoice
    {
        // Idempotency: return existing invoice if already generated
        if ($order->invoice) {
            return $order->invoice;
        }

        $sellerState = Invoice::SELLER_STATE;
        $customerState = strtoupper(trim($order->billing_state));
        $isInterstate = ($customerState !== $sellerState);

        $subtotal = $order->subtotal_paise;
        $taxAmount = $order->tax_amount_paise;
        $total = $order->total_amount_paise;

        if ($isInterstate) {
            $cgst = 0;
            $sgst = 0;
            $igst = $taxAmount;
        } else {
            $cgst = (int) round($taxAmount / 2);
            $sgst = $taxAmount - $cgst;
            $igst = 0;
        }

        // Determine Indian financial year (e.g. 2026-2027)
        $month = (int) date('n');
        $year = (int) date('Y');
        $startYear = ($month >= 4) ? $year : $year - 1;
        $financialYear = $startYear.'-'.($startYear + 1);

        return Invoice::create([
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'order_id' => $order->id,
            'invoice_date' => now()->toDateString(),
            'financial_year' => $financialYear,
            'subtotal_paise' => $subtotal,
            'cgst_paise' => $cgst,
            'sgst_paise' => $sgst,
            'igst_paise' => $igst,
            'total_paise' => $total,
            'customer_state' => $customerState,
            'seller_state' => $sellerState,
            'is_interstate' => $isInterstate,
            'sac_code' => $order->servicePackage?->sac_code ?? '998311',
        ]);
    }
}
