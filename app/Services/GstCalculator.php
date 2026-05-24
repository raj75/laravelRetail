<?php

namespace App\Services;

class GstCalculator
{
    public static function calculateLine(
        float $qty,
        float $rate,
        float $discount,
        float $gstRate,
        bool $isInterState
    ): array {
        $gross = ($qty * $rate) - $discount;
        $taxable = $gross;
        $cgst = $sgst = $igst = 0;

        if ($gstRate > 0) {
            if ($isInterState) {
                $igst = round($taxable * $gstRate / 100, 2);
            } else {
                $half = $gstRate / 2;
                $cgst = round($taxable * $half / 100, 2);
                $sgst = round($taxable * $half / 100, 2);
            }
        }

        $amount = round($taxable + $cgst + $sgst + $igst, 2);

        return compact('taxable', 'cgst', 'sgst', 'igst', 'amount');
    }

    public static function summarizeTotals(array $lines, float $invoiceDiscount = 0): array
    {
        $subtotal = collect($lines)->sum(fn ($l) => ($l['quantity'] ?? 0) * ($l['rate'] ?? 0));
        $taxable = collect($lines)->sum('taxable_amount');
        $cgst = collect($lines)->sum('cgst');
        $sgst = collect($lines)->sum('sgst');
        $igst = collect($lines)->sum('igst');
        $total = collect($lines)->sum('amount') - $invoiceDiscount;
        $rounded = round($total);
        $roundOff = round($rounded - $total, 2);

        return [
            'subtotal' => round($subtotal, 2),
            'taxable_amount' => round($taxable, 2),
            'cgst_amount' => round($cgst, 2),
            'sgst_amount' => round($sgst, 2),
            'igst_amount' => round($igst, 2),
            'total_amount' => $rounded,
            'round_off' => $roundOff,
        ];
    }
}
