<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use App\Services\PricingService;
use Illuminate\Support\Facades\App;

class PriceCast implements CastsAttributes
{
    protected $context;

    public function __construct($context = 'default')
    {
        $this->context = $context;
    }

    public function get($model, string $key, $value, array $attributes)
    {
        $pricingService = App::make(PricingService::class);
        return $this->applyPriceLogic($pricingService, $value);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        return $value;
    }

    private function applyPriceLogic(PricingService $pricingService, $value)
    {
        switch ($this->context) {
            case 'vendor':
                return $pricingService->calculateVendorPrice($value);
            case 'pos':
                return $pricingService->calculatePosPrice($value);
            case 'inventory':
                return $pricingService->calculateInventoryPrice($value);
            default:
                return $value;
        }
    }
}
