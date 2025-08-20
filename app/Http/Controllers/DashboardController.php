<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Vendor;
use App\Models\PurchaseOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('App/Dashboard');
    }

    public function getKpis(): JsonResponse
    {
        try {
            $totalProducts = Inventory::count();
            $totalStock = Inventory::sum('stock');
            $totalStockValue = Inventory::sum(DB::raw('stock * price'));

            // Assuming PurchaseOrder is used for sales
            $salesThisMonth = PurchaseOrder::whereMonth('created_at', now()->month)->sum('total_amount');
            $ordersThisMonth = PurchaseOrder::whereMonth('created_at', now()->month)->count();

            $lowStockAlerts = Inventory::where('stock', '<=', DB::raw('low_stock_threshold'))->count();

            return response()->json([
                'totalProducts' => $totalProducts,
                'totalStock' => $totalStock,
                'totalStockValue' => $totalStockValue,
                'salesThisMonth' => $salesThisMonth,
                'ordersThisMonth' => $ordersThisMonth,
                'lowStockAlerts' => $lowStockAlerts,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getKpis: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching KPIs'], 500);
        }
    }

    public function inventoryByCategory()
    {
        try {
            $inventoryByCategory = Category::select('categories.id', 'categories.name as category', DB::raw('COUNT(inventory.id) as count'))
                ->leftJoin('categories as child_categories', 'categories.id', '=', 'child_categories.parent_id')
                ->leftJoin('inventory', function ($join) {
                    $join->on('categories.id', '=', 'inventory.category_id')
                        ->orOn('child_categories.id', '=', 'inventory.category_id');
                })
                ->whereNull('categories.parent_id')
                ->groupBy('categories.id', 'categories.name')
                ->with(['children' => function ($query) {
                    $query->withCount('inventoryItems');
                }])
                ->get();

            return response()->json($inventoryByCategory);
        } catch (\Exception $e) {
            \Log::error('Error in inventoryByCategory: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching inventory data: ' . $e->getMessage()], 500);
        }
    }
}
