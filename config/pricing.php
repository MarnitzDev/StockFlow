<?php

return [
    'tax_rate' => env('TAX_RATE', 0.10),
    'discount_rate' => env('DISCOUNT_RATE', 0.05),
    'vendor_markup' => env('VENDOR_MARKUP', 0.00),
    'inventory_valuation_method' => env('INVENTORY_VALUATION_METHOD', 'fifo'),
];
