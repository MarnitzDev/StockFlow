<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'vendor' => $this->vendor->name,
            'order_date' => $this->order_date->format('Y-m-d'),
            'status' => $this->status,
            'items' => $this->when($this->items->isNotEmpty(), function () {
                return $this->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_name' => $item->product->name,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'subtotal' => $item->quantity * $item->unit_price,
                        'selling_price' => $item->selling_price ?? null,
                        'tax' => $item->tax ?? null,
                        'final_pos_price' => $item->final_pos_price ?? null,
                    ];
                });
            }, []),
            'subtotal' => $this->items->sum(function ($item) {
                return $item->quantity * $item->unit_price;
            }),
            'tax' => $this->when(isset($this->calculatedTotals), function () {
                return $this->calculatedTotals['tax'] ?? 0;
            }, 0),
            'total' => $this->total_amount,
        ];
    }
}
