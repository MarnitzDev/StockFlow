<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Services\PriceService;
use Illuminate\Support\Collection;

class PurchaseOrderService
{
    protected $priceService;

    public function __construct(PriceService $priceService)
    {
        $this->priceService = $priceService;
    }

    public function calculateTotals(PurchaseOrder $purchaseOrder): array
    {
        $items = $purchaseOrder->items->map(function ($item) {
            $unitPrice = $this->priceService->calculateVendorPurchasePrice($item->product->price);
            $sellingPrice = $this->priceService->calculateSellingPrice($unitPrice);
            $finalPosPrice = $this->priceService->calculateFinalPosPrice($unitPrice);
            $taxAmount = $this->priceService->calculateTaxAmount($sellingPrice);

            return [
                'id' => $item->id,
                'product_name' => $item->product->name,
                'quantity' => (int) $item->quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $item->quantity * $unitPrice,
                'selling_price' => $sellingPrice,
                'final_pos_price' => $finalPosPrice,
                'tax' => $taxAmount,
                'total' => $item->quantity * $unitPrice,
            ];
        });

        return [
            'items' => $items,
            'subtotal' => $items->sum('subtotal'),
            'tax' => $items->sum('tax'),
            'total' => $items->sum('total'),
        ];
    }

    public function calculateProductTotals(Collection $products): array
    {
        $items = $products->map(function ($product) {
            $unitPrice = $this->priceService->calculateVendorPurchasePrice($product['price'] ?? 0);
            $taxAmount = $this->priceService->calculateTaxAmount($unitPrice);
            $subtotal = $unitPrice * ($product['quantity'] ?? 1);
            $total = $subtotal + $taxAmount;

            return [
                'id' => $product['id'] ?? null,
                'name' => $product['name'] ?? '',
                'sku' => $product['sku'] ?? '',
                'quantity' => $product['quantity'] ?? 1,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
                'tax' => $taxAmount,
                'total' => $total,
                'stock' => $product['stock'] ?? 0,
                'inventory_stock' => $product['inventory_stock'] ?? 0,
                'image_url' => $product['image_url'] ?? null,
            ];
        });

        return [
            'items' => $items,
            'subtotal' => $items->sum('subtotal'),
            'tax' => $items->sum('tax'),
            'total' => $items->sum('total'),
        ];
    }

}
