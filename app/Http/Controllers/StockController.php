<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $items = Item::with(['category', 'unit'])
            ->when($request->low_stock, fn ($q) => $q->whereColumn('stock_qty', '<=', 'low_stock_alert'))
            ->orderBy('name')
            ->paginate(25);

        $movements = StockMovement::with('item')->latest()->limit(20)->get();

        return view('stock.index', compact('items', 'movements'));
    }

    public function adjust(Request $request, StockService $stockService)
    {
        $data = $request->validate([
            'item_id' => ['required', 'exists:items,id'],
            'type' => ['required', 'in:in,out'],
            'quantity' => ['required', 'numeric', 'min:0.001'],
            'notes' => ['nullable', 'string'],
        ]);

        $item = Item::findOrFail($data['item_id']);
        $stockService->adjust($item, $data['type'], (float) $data['quantity'], $data['notes'] ?? 'Manual adjustment');

        return back()->with('success', 'Stock adjusted.');
    }
}
