<?php

namespace App\Models\Vendors;

use App\Models\Vendors\VendorProduct;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'phone', 'address'];

    public function products()
    {
        return $this->hasMany(VendorProduct::class);
    }

    public function image()
    {
        return $this->hasOne(VendorImage::class);
    }
}
