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
        'created_at',
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

    public static function recordMovement($inventoryId, $quantity, $type, $reason, $userId, $unitPrice, $reference = null, $createdAt = null)
    {
        $inventory = Inventory::findOrFail($inventoryId);

        $newStock = $type === self::TYPE_IN ? $inventory->stock + $quantity : $inventory->stock - $quantity;

        $movementData = [
            'inventory_id' => $inventoryId,
            'quantity' => $quantity,
            'stock' => $newStock,
            'type' => $type,
            'reason' => $reason,
            'user_id' => $userId,
            'unit_price' => $unitPrice,
            'reference' => $reference,
        ];

        if ($createdAt) {
            $movementData['created_at'] = $createdAt;
        }

        $movement = self::create($movementData);

        if (!$createdAt || $createdAt->greaterThan(now())) {
            $inventory->update(['stock' => $newStock]);
        }

        return $movement;
    }

    public static function reconcileInventory($inventoryId)
    {
        $inventory = Inventory::findOrFail($inventoryId);
        $movements = self::where('inventory_id', $inventoryId)->orderBy('created_at')->get();

        $currentStock = 0;
        foreach ($movements as $movement) {
            $currentStock = $movement->type === self::TYPE_IN ? $currentStock + $movement->quantity : $currentStock - $movement->quantity;
            $movement->update(['stock' => $currentStock]);
        }

        $inventory->update(['stock' => $currentStock]);
    }
}
