<?php

namespace App\Services;

class PriceService
{
    /**
     * Calculate the price at which we purchase from vendors.
     *
     * @param float $basePrice
     * @return float
     */
    public function calculateVendorPurchasePrice($basePrice)
    {
        $vendorDiscount = config('pricing.vendor_discount', 0);
        return $basePrice * (1 - $vendorDiscount);
    }

    /**
     * Calculate the selling price for POS (and inventory).
     *
     * @param float $basePrice
     * @return float
     */
    public function calculateSellingPrice($basePrice)
    {
        $markup = config('pricing.markup', 0.3); // 30% markup by default
        return $basePrice * (1 + $markup);
    }

    /**
     * Apply tax to a given price.
     *
     * @param float $price
     * @return float
     */
    public function applyTax($price)
    {
        $taxRate = config('pricing.tax_rate', 0.1);
        return $price * (1 + $taxRate);
    }

    /**
     * Calculate the final POS price including tax.
     *
     * @param float $basePrice
     * @return float
     */
    public function calculateFinalPosPrice($basePrice)
    {
        $sellingPrice = $this->calculateSellingPrice($basePrice);
        return $this->applyTax($sellingPrice);
    }

    /**
     * Calculate the tax amount for a given price.
     *
     * @param float $price
     * @return float
     */
    public function calculateTaxAmount($price)
    {
        return $this->applyTax($price) - $price;
    }
}
