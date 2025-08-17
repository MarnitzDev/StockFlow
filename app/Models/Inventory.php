<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventory';

    protected $fillable = [
        'name',
        'sku',
        'description',
        'stock',
        'price',
        'category_id',
        'low_stock_threshold',
        'vendor_id',
        'image_url',
        'available_on_pos',
    ];

    protected $casts = [
        'stock' => 'integer',
        'price' => 'decimal:2',
        'low_stock_threshold' => 'integer',
        'available_on_pos' => 'boolean',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
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
        return $query->whereRaw('stock <= low_stock_threshold');
    }

    // Methods
    public function updateStock($stock, $type = 'in')
    {
        $this->stock += ($type === 'in' ? $stock : -$stock);
        $this->save();

        // Create a stock movement record
        $this->stockMovements()->create([
            'stock' => $stock,
            'type' => $type,
        ]);
    }

    public function isLowStock()
    {
        return $this->stock <= $this->low_stock_threshold;
    }

    public function getTotalValue()
    {
        return $this->stock * $this->price;
    }

    public function togglePOSAvailability()
    {
        $this->available_on_pos = !$this->available_on_pos;
        $this->save();
    }
}
