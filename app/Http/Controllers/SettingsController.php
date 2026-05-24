<?php

namespace App\Http\Controllers;

use App\Models\BusinessSetting;
use App\Models\ExpenseCategory;
use App\Models\ItemCategory;
use App\Models\Unit;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function edit()
    {
        $settings = BusinessSetting::current();
        $categories = ItemCategory::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        $expenseCategories = ExpenseCategory::orderBy('name')->get();

        return view('settings.edit', compact('settings', 'categories', 'units', 'expenseCategories'));
    }

    public function update(Request $request)
    {
        $settings = BusinessSetting::current();

        $settings->update($request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string'],
            'gstin' => ['nullable', 'string', 'max:15'],
            'pan' => ['nullable', 'string', 'max:10'],
            'phone' => ['nullable', 'string'],
            'email' => ['nullable', 'email'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string'],
            'state' => ['nullable', 'string'],
            'pincode' => ['nullable', 'string'],
            'invoice_prefix' => ['nullable', 'string'],
            'enable_gst' => ['nullable', 'boolean'],
            'terms_conditions' => ['nullable', 'string'],
        ]));

        if ($request->filled('new_category')) {
            ItemCategory::create(['name' => $request->new_category]);
        }

        if ($request->filled('new_unit_name')) {
            Unit::create([
                'name' => $request->new_unit_name,
                'short_name' => $request->new_unit_short ?? substr($request->new_unit_name, 0, 3),
            ]);
        }

        if ($request->filled('new_expense_category')) {
            ExpenseCategory::create(['name' => $request->new_expense_category]);
        }

        return back()->with('success', 'Settings saved.');
    }
}
