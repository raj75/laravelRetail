<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    protected $fillable = [
        'name', 'sku', 'barcode', 'category_id', 'unit_id', 'hsn_code',
        'purchase_price', 'sale_price', 'mrp', 'gst_rate', 'tax_type',
        'stock_qty', 'low_stock_alert', 'description', 'track_inventory', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'mrp' => 'decimal:2',
            'gst_rate' => 'decimal:2',
            'stock_qty' => 'decimal:3',
            'low_stock_alert' => 'decimal:3',
            'track_inventory' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ItemCategory::class, 'category_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function isLowStock(): bool
    {
        return $this->track_inventory && $this->stock_qty <= $this->low_stock_alert;
    }
}
