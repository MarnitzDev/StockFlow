<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SalesOrder extends Model
{
    use HasFactory;

    protected $fillable = ['order_number', 'customer_id', 'total_amount', 'status', 'notes'];

    public function items()
    {
        return $this->hasMany(SalesOrderItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function processSale()
    {
        DB::transaction(function () {
            foreach ($this->items as $item) {
                $inventory = Inventory::findOrFail($item->inventory_id);

                if ($inventory->stock < $item->quantity) {
                    throw new \Exception("Insufficient stock for item: {$inventory->name}");
                }

                StockMovement::recordMovement(
                    $inventory->id,
                    $item->quantity,
                    StockMovement::TYPE_OUT,
                    'Sales Order',
                    auth()->id(),
                    $item->unit_price,
                    "SO-{$this->id}"
                );

                $inventory->stock -= $item->quantity;
                $inventory->save();
            }

            $this->update(['status' => 'completed']);
        });
    }
}
