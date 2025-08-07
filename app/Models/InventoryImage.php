<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryImage extends Model
{
    use HasFactory;

    protected $fillable = ['inventory_id', 'image_path', 'is_primary', 'order'];

    protected $casts = [
        'is_primary' => 'boolean',
        'order' => 'integer',
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}
