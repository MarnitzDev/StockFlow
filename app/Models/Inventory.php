<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'description',
        'quantity',
        'price',
        'category_id',
        'low_stock_threshold',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'low_stock_threshold' => 'integer',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function stockAdjustments()
    {
        return $this->hasMany(StockAdjustment::class);
    }

    // Scopes
    public function scopeLowStock($query)
    {
        return $query->whereRaw('quantity <= low_stock_threshold');
    }

    // Methods
    public function updateStock($quantity, $type = 'in')
    {
        $this->quantity += ($type === 'in' ? $quantity : -$quantity);
        $this->save();

        // Create a stock movement record
        $this->stockMovements()->create([
            'quantity' => $quantity,
            'type' => $type,
        ]);
    }

    public function isLowStock()
    {
        return $this->quantity <= $this->low_stock_threshold;
    }

    public function getTotalValue()
    {
        return $this->quantity * $this->price;
    }

    public function images()
    {
        return $this->hasMany(InventoryImage::class)->orderBy('order');
    }

    public function primaryImage()
    {
        return $this->hasOne(InventoryImage::class)->where('is_primary', true);
    }
}
