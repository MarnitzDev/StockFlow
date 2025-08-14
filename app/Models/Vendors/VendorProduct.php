<?php

namespace App\Models\Vendors;

use App\Services\PriceService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorProduct extends Model
{
    use HasFactory;

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
