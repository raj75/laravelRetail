<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\BusinessSetting;
use App\Models\ExpenseCategory;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\Party;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        BusinessSetting::create([
            'business_name' => 'LaravelRetail',
            'legal_name' => 'LaravelRetail Pvt Ltd',
            'gstin' => '29AAAAA0000A1Z5',
            'phone' => '9876543210',
            'email' => 'info@laravelretail.local',
            'address' => '123 Business Street',
            'city' => 'Bangalore',
            'state' => 'Karnataka',
            'pincode' => '560001',
            'enable_gst' => true,
            'terms_conditions' => 'Goods once sold will not be taken back.',
        ]);

        User::create([
            'name' => 'Admin',
            'email' => 'admin@laravelretail.com',
            'password' => Hash::make('password'),
        ]);

        Account::create(['name' => 'Cash', 'type' => 'cash', 'opening_balance' => 10000, 'current_balance' => 10000, 'is_default' => true]);
        Account::create(['name' => 'HDFC Bank', 'type' => 'bank', 'bank_name' => 'HDFC', 'opening_balance' => 50000, 'current_balance' => 50000]);

        $cat = ItemCategory::create(['name' => 'General']);
        $pcs = Unit::create(['name' => 'Pieces', 'short_name' => 'Pcs']);
        Unit::create(['name' => 'Kilogram', 'short_name' => 'Kg']);

        Item::create([
            'name' => 'Sample Product A',
            'sku' => 'SKU-001',
            'barcode' => '8901234567890',
            'category_id' => $cat->id,
            'unit_id' => $pcs->id,
            'hsn_code' => '8471',
            'purchase_price' => 500,
            'sale_price' => 750,
            'gst_rate' => 18,
            'stock_qty' => 100,
            'low_stock_alert' => 10,
        ]);

        Item::create([
            'name' => 'Sample Product B',
            'sku' => 'SKU-002',
            'barcode' => '8901234567891',
            'category_id' => $cat->id,
            'unit_id' => $pcs->id,
            'hsn_code' => '6109',
            'purchase_price' => 200,
            'sale_price' => 350,
            'gst_rate' => 12,
            'stock_qty' => 50,
            'low_stock_alert' => 5,
        ]);

        Party::create(['name' => 'Walk-in Customer', 'type' => 'customer', 'phone' => '9000000001']);
        Party::create(['name' => 'ABC Traders', 'type' => 'customer', 'phone' => '9000000002', 'gstin' => '29BBBBB0000B1Z5', 'city' => 'Bangalore', 'state' => 'Karnataka']);
        Party::create(['name' => 'XYZ Suppliers', 'type' => 'supplier', 'phone' => '9000000003', 'gstin' => '29CCCCC0000C1Z5']);

        ExpenseCategory::insert([
            ['name' => 'Rent'],
            ['name' => 'Salary'],
            ['name' => 'Utilities'],
            ['name' => 'Transport'],
        ]);
    }
}
