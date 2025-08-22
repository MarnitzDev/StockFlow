<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'order_number',
        'order_date',
        'total_amount',
        'status',
        'payment_date',
    ];

    protected $casts = [
        'order_date' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function processOrder()
    {
        DB::transaction(function () {
            foreach ($this->items as $item) {
                $inventory = Inventory::findOrFail($item->inventory_id);

                StockMovement::recordMovement(
                    $inventory->id,
                    $item->quantity,
                    StockMovement::TYPE_IN,
                    'Purchase Order',
                    auth()->id(),
                    $item->unit_price,
                    "PO-{$this->id}",
                    $this->order_date
                );

                $item->update(['received_quantity' => $item->quantity]);
            }

            $this->update(['status' => 'received']);

            Bill::create([
                'purchase_order_id' => $this->id,
                'vendor_id' => $this->vendor_id,
                'amount' => $this->total_amount,
                'due_date' => $this->order_date->addDays(30),
                'status' => 'pending'
            ]);

            // Reconcile inventory after processing the order
            $inventoryIds = $this->items->pluck('inventory_id')->unique();
            foreach ($inventoryIds as $inventoryId) {
                StockMovement::reconcileInventory($inventoryId);
            }
        });
    }
}
