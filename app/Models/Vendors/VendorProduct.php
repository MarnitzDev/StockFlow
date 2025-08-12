<?php

namespace App\Models\Vendors;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorProduct extends Model
{
    use HasFactory;

    protected $fillable = ['vendor_id', 'name', 'sku', 'price', 'stock', 'description'];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
