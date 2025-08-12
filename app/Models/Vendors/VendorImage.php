<?php

namespace App\Models\Vendors;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorImage extends Model
{
    use HasFactory;

    protected $fillable = ['supplier_id', 'image_path'];

    public function supplier()
    {
        return $this->belongsTo(Vendor::class);
    }
}
