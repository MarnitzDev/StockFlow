<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;

class PricingService
{
    public function calculateVendorPrice($value)
    {
        $markup = 1 + Config::get('pricing.vendor_markup');
        return round($value * $markup, 2);
    }

    public function calculatePosPrice($value)
    {
        $withTax = $value * (1 + Config::get('pricing.tax_rate'));
        $withDiscount = $withTax * (1 - Config::get('pricing.discount_rate'));
        return round($withDiscount, 2);
    }

    public function calculateInventoryPrice($value)
    {
        // Implement inventory valuation logic based on your method (FIFO, LIFO, etc.)
        return round($value, 2);
    }
}
