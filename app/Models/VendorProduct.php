<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorProduct extends Model
{
    use HasFactory;

    protected $table = 'vendor_products';

    protected $fillable = [
        'vendor_id',
        'name',
        'sku',
        'description',
        'price',
        'stock',
        'category_id',
        'image_url'
    ];
    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
