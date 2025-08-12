<?php

namespace App\Models\Vendors;

use App\Models\Inventory;
use App\Models\SupplierImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'phone', 'address'];
    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function image()
    {
        return $this->hasOne(SupplierImage::class);
    }

}
