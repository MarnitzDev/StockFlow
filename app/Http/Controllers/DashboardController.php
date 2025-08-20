<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Vendor;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
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
            $now = now();
            $lastMonth = $now->copy()->subMonth()->endOfMonth();
            $twoMonthsAgo = $now->copy()->subMonths(2)->endOfMonth();

            // Last month data (most recent complete month)
            $totalProducts = Inventory::count();
            $totalStock = Inventory::sum('stock');
            $totalStockValue = Inventory::sum(DB::raw('stock * price'));
            $salesLastMonth = SalesOrder::whereBetween('created_at', [
                $lastMonth->copy()->startOfMonth(),
                $lastMonth
            ])->sum('total_amount');
            $ordersLastMonth = SalesOrder::whereBetween('created_at', [
                $lastMonth->copy()->startOfMonth(),
                $lastMonth
            ])->count();
            $lowStockAlerts = Inventory::where('stock', '<=', DB::raw('low_stock_threshold'))->count();

            // Two months ago data (for comparison)
            $previousTotalProducts = Inventory::whereDate('created_at', '<=', $twoMonthsAgo)->count();
            $previousTotalStock = Inventory::whereDate('updated_at', '<=', $twoMonthsAgo)->sum('stock');
            $previousTotalStockValue = Inventory::whereDate('updated_at', '<=', $twoMonthsAgo)->sum(DB::raw('stock * price'));
            $salesTwoMonthsAgo = SalesOrder::whereBetween('created_at', [
                $twoMonthsAgo->copy()->startOfMonth(),
                $twoMonthsAgo
            ])->sum('total_amount');
            $ordersTwoMonthsAgo = SalesOrder::whereBetween('created_at', [
                $twoMonthsAgo->copy()->startOfMonth(),
                $twoMonthsAgo
            ])->count();
            $previousLowStockAlerts = Inventory::whereDate('updated_at', '<=', $twoMonthsAgo)
                ->where('stock', '<=', DB::raw('low_stock_threshold'))
                ->count();

            // Calculate percentage changes
            $changes = [
                'totalProducts' => $this->calculatePercentageChange($previousTotalProducts, $totalProducts),
                'totalStock' => $this->calculatePercentageChange($previousTotalStock, $totalStock),
                'totalStockValue' => $this->calculatePercentageChange($previousTotalStockValue, $totalStockValue),
                'salesLastMonth' => $this->calculatePercentageChange($salesTwoMonthsAgo, $salesLastMonth),
                'ordersLastMonth' => $this->calculatePercentageChange($ordersTwoMonthsAgo, $ordersLastMonth),
                'lowStockAlerts' => $this->calculatePercentageChange($previousLowStockAlerts, $lowStockAlerts),
            ];

            return response()->json([
                'currentData' => [
                    'totalProducts' => $totalProducts,
                    'totalStock' => $totalStock,
                    'totalStockValue' => $totalStockValue,
                    'salesLastMonth' => $salesLastMonth,
                    'ordersLastMonth' => $ordersLastMonth,
                    'lowStockAlerts' => $lowStockAlerts,
                ],
                'changes' => $changes,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getKpis: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching KPIs'], 500);
        }
    }

    private function calculatePercentageChange($oldValue, $newValue)
    {
        if ($oldValue == 0 && $newValue == 0) {
            return 0;
        }
        if ($oldValue == 0) {
            return $newValue > 0 ? 100 : 0;
        }
        return round((($newValue - $oldValue) / $oldValue) * 100, 1);
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

    public function salesOverTime()
    {
        try {
            $salesData = SalesOrder::select(
                DB::raw('TO_CHAR(created_at, \'YYYY-MM\') as month'),
                DB::raw('SUM(total_amount) as total')
            )
                ->where('created_at', '>=', Carbon::now()->subMonths(12))
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->map(function ($item) {
                    return [
                        'month' => $item->month,
                        'total' => (float) $item->total
                    ];
                });

            return response()->json($salesData);
        } catch (\Exception $e) {
            \Log::error('Error in salesOverTime: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching sales data: ' . $e->getMessage()], 500);
        }
    }

    public function purchasesOverTime()
    {
        try {
            $purchaseData = PurchaseOrder::select(
                DB::raw('TO_CHAR(created_at, \'YYYY-MM\') as month'),
                DB::raw('SUM(total_amount) as total')
            )
                ->where('created_at', '>=', Carbon::now()->subMonths(12))
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->map(function ($item) {
                    return [
                        'month' => $item->month,
                        'total' => (float) $item->total
                    ];
                });

            return response()->json($purchaseData);
        } catch (\Exception $e) {
            \Log::error('Error in purchasesOverTime: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching purchase data: ' . $e->getMessage()], 500);
        }
    }

    public function topSellingProducts()
    {
        $topProducts = SalesOrderItem::select('inventory_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(subtotal) as total_sales'))
            ->with('inventory:id,name')
            ->groupBy('inventory_id')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->inventory->name,
                    'total_quantity' => $item->total_quantity,
                    'total_sales' => $item->total_sales,
                ];
            });

        return response()->json($topProducts);
    }

    public function getLowStockItems()
    {
        $lowStockItems = Inventory::where('stock', '<=', DB::raw('low_stock_threshold'))
            ->select('name', 'sku', 'stock', 'low_stock_threshold')
            ->get();

        return response()->json($lowStockItems);
    }
}
