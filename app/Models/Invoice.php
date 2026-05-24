<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    public const TYPES = [
        'sale' => 'Sale Invoice',
        'purchase' => 'Purchase Bill',
        'estimate' => 'Estimate / Quotation',
        'sale_order' => 'Sale Order',
        'purchase_order' => 'Purchase Order',
        'credit_note' => 'Credit Note',
        'debit_note' => 'Debit Note',
        'delivery_challan' => 'Delivery Challan',
    ];

    protected $fillable = [
        'type', 'invoice_number', 'party_id', 'invoice_date', 'due_date',
        'status', 'payment_status', 'payment_mode', 'account_id',
        'subtotal', 'discount_amount', 'discount_type', 'taxable_amount',
        'cgst_amount', 'sgst_amount', 'igst_amount', 'round_off', 'total_amount',
        'paid_amount', 'balance_amount', 'place_of_supply', 'is_inter_state',
        'notes', 'terms', 'converted_from_id', 'user_id',
    ];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'due_date' => 'date',
            'is_inter_state' => 'boolean',
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'taxable_amount' => 'decimal:2',
            'cgst_amount' => 'decimal:2',
            'sgst_amount' => 'decimal:2',
            'igst_amount' => 'decimal:2',
            'round_off' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'balance_amount' => 'decimal:2',
        ];
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function convertedFrom(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'converted_from_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function affectsStock(): bool
    {
        return in_array($this->type, ['sale', 'purchase', 'credit_note', 'debit_note', 'delivery_challan'], true);
    }

    public function stockDirection(): ?string
    {
        return match ($this->type) {
            'sale', 'delivery_challan', 'debit_note' => 'out',
            'purchase', 'credit_note' => 'in',
            default => null,
        };
    }
}
