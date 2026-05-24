<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessSetting extends Model
{
    protected $fillable = [
        'business_name', 'legal_name', 'gstin', 'pan', 'phone', 'email',
        'address', 'city', 'state', 'pincode', 'invoice_prefix', 'currency',
        'financial_year_start', 'enable_gst', 'terms_conditions', 'logo_path',
    ];

    protected function casts(): array
    {
        return ['enable_gst' => 'boolean'];
    }

    public static function current(): self
    {
        return static::firstOrCreate([], ['business_name' => 'LaravelRetail']);
    }
}
