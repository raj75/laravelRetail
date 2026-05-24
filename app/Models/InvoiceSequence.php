<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceSequence extends Model
{
    protected $fillable = ['type', 'prefix', 'next_number'];

    public static function nextNumber(string $type): string
    {
        $defaults = [
            'sale' => 'SAL',
            'purchase' => 'PUR',
            'estimate' => 'EST',
            'sale_order' => 'SO',
            'purchase_order' => 'PO',
            'credit_note' => 'CN',
            'debit_note' => 'DN',
            'delivery_challan' => 'DC',
        ];

        $seq = static::firstOrCreate(
            ['type' => $type],
            ['prefix' => $defaults[$type] ?? 'INV', 'next_number' => 1]
        );

        $number = $seq->prefix.'-'.str_pad((string) $seq->next_number, 5, '0', STR_PAD_LEFT);
        $seq->increment('next_number');

        return $number;
    }
}
