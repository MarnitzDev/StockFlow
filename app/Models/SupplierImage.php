<?php

namespace App\Models;

use App\Models\Vendors\Supplier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierImage extends Model
{
    use HasFactory;

    protected $fillable = ['supplier_id', 'image_path'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
