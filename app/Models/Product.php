<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'sku', 'stock', 'price', 'category',
        'low_stock_threshold', 'unit_of_measurement', 'supplier_id'
    ];

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Contact::class, 'supplier_id');
    }
}
