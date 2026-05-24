<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\Unit;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with(['category', 'unit'])->orderBy('name');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('sku', 'like', "%{$s}%")
                ->orWhere('barcode', 'like', "%{$s}%"));
        }

        $items = $query->paginate(20);

        return view('items.index', compact('items'));
    }

    public function create()
    {
        return view('items.form', [
            'item' => new Item,
            'categories' => ItemCategory::orderBy('name')->get(),
            'units' => Unit::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Item::create($this->validated($request));

        return redirect()->route('items.index')->with('success', 'Item created.');
    }

    public function edit(Item $item)
    {
        return view('items.form', [
            'item' => $item,
            'categories' => ItemCategory::orderBy('name')->get(),
            'units' => Unit::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Item $item)
    {
        $item->update($this->validated($request));

        return redirect()->route('items.index')->with('success', 'Item updated.');
    }

    public function show(Item $item)
    {
        return redirect()->route('items.edit', $item);
    }

    public function destroy(Item $item)
    {
        $item->delete();

        return redirect()->route('items.index')->with('success', 'Item deleted.');
    }

    public function byBarcode(Request $request)
    {
        $code = trim((string) $request->get('code', ''));
        if ($code === '') {
            return response()->json(['message' => 'Barcode is required.'], 422);
        }

        $item = Item::where('is_active', true)
            ->where(function ($query) use ($code) {
                $query->where('barcode', $code)
                    ->orWhere('sku', $code);
            })
            ->first();

        if (! $item) {
            return response()->json(['message' => 'No item found for this barcode.'], 404);
        }

        return response()->json($this->itemPayload($item));
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        if ($q === '') {
            return response()->json([]);
        }

        $items = Item::where('is_active', true)
            ->where(fn ($query) => $query->where('name', 'like', "%{$q}%")
                ->orWhere('sku', 'like', "%{$q}%")
                ->orWhere('barcode', 'like', "%{$q}%"))
            ->limit(15)
            ->get();

        $exact = Item::where('is_active', true)
            ->where(fn ($query) => $query->where('barcode', $q)->orWhere('sku', $q))
            ->first();

        if ($exact && ! $items->contains('id', $exact->id)) {
            $items->prepend($exact);
        }

        return response()->json($items->map(fn ($item) => $this->itemPayload($item))->values());
    }

    protected function itemPayload(Item $item): array
    {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'sku' => $item->sku,
            'barcode' => $item->barcode,
            'sale_price' => (float) $item->sale_price,
            'purchase_price' => (float) $item->purchase_price,
            'gst_rate' => (float) $item->gst_rate,
            'hsn_code' => $item->hsn_code,
            'stock_qty' => (float) $item->stock_qty,
        ];
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:50', 'unique:items,sku,'.($request->route('item')?->id ?? 'NULL')],
            'barcode' => ['nullable', 'string', 'max:50', 'unique:items,barcode,'.($request->route('item')?->id ?? 'NULL')],
            'category_id' => ['nullable', 'exists:item_categories,id'],
            'unit_id' => ['nullable', 'exists:units,id'],
            'hsn_code' => ['nullable', 'string', 'max:20'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'mrp' => ['nullable', 'numeric', 'min:0'],
            'gst_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'tax_type' => ['nullable', 'in:inclusive,exclusive'],
            'stock_qty' => ['nullable', 'numeric', 'min:0'],
            'low_stock_alert' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'track_inventory' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['track_inventory'] = $request->boolean('track_inventory', true);
        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
