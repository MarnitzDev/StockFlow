<?php

namespace App\Models;

use App\Models\Vendors\Vendor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'order_number',
        'order_date',
        'total_amount',
        'status',
        'payment_date',
    ];

    protected $casts = [
        'order_date' => 'datetime',
    ];

    public function supplier()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
}
