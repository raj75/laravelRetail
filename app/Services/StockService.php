<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Item;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;

class StockService
{
    public function applyInvoice(Invoice $invoice): void
    {
        if (! $invoice->affectsStock()) {
            return;
        }

        $direction = $invoice->stockDirection();

        foreach ($invoice->items as $line) {
            if (! $line->item_id) {
                continue;
            }

            $item = Item::find($line->item_id);
            if (! $item || ! $item->track_inventory) {
                continue;
            }

            $qty = (float) $line->quantity;
            if ($direction === 'out') {
                $this->move($item, 'out', $qty, Invoice::class, $invoice->id);
            } else {
                $this->move($item, 'in', $qty, Invoice::class, $invoice->id);
            }
        }
    }

    public function reverseInvoice(Invoice $invoice): void
    {
        if (! $invoice->affectsStock()) {
            return;
        }

        $direction = $invoice->stockDirection();

        foreach ($invoice->items as $line) {
            if (! $line->item_id) {
                continue;
            }

            $item = Item::find($line->item_id);
            if (! $item || ! $item->track_inventory) {
                continue;
            }

            $qty = (float) $line->quantity;
            if ($direction === 'out') {
                $this->move($item, 'in', $qty, Invoice::class, $invoice->id, 'Reversal');
            } else {
                $this->move($item, 'out', $qty, Invoice::class, $invoice->id, 'Reversal');
            }
        }
    }

    public function adjust(Item $item, string $type, float $qty, ?string $notes = null): void
    {
        $this->move($item, $type === 'in' ? 'in' : 'out', $qty, null, null, $notes);
    }

    protected function move(
        Item $item,
        string $type,
        float $qty,
        ?string $refType,
        ?int $refId,
        ?string $notes = null
    ): void {
        $before = (float) $item->stock_qty;
        $after = $type === 'in' ? $before + $qty : $before - $qty;

        $item->update(['stock_qty' => $after]);

        StockMovement::create([
            'item_id' => $item->id,
            'type' => $type === 'in' ? 'in' : 'out',
            'quantity' => $qty,
            'stock_before' => $before,
            'stock_after' => $after,
            'reference_type' => $refType,
            'reference_id' => $refId,
            'notes' => $notes,
            'user_id' => Auth::id(),
        ]);
    }
}
