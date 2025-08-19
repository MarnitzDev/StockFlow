<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_id',
        'quantity',
        'stock',
        'type',
        'reason',
        'user_id',
        'unit_price',
        'reference',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    const TYPE_IN = 'in';
    const TYPE_OUT = 'out';

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function recordMovement($inventoryId, $quantity, $type, $reason, $userId, $unitPrice, $reference = null)
    {
        $inventory = Inventory::findOrFail($inventoryId);

        $newStock = $type === 'in' ? $inventory->stock + $quantity : $inventory->stock - $quantity;

        $movement = self::create([
            'inventory_id' => $inventoryId,
            'quantity' => $quantity,
            'stock' => $newStock,
            'type' => $type,
            'reason' => $reason,
            'user_id' => $userId,
            'unit_price' => $unitPrice,
            'reference' => $reference,
        ]);

        $inventory->update(['stock' => $newStock]);

        return $movement;
    }
}
