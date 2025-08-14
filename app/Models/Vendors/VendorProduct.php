<?php

namespace App\Models\Vendors;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Casts\PriceCast;

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
        'price' => PriceCast::class.':vendor',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
