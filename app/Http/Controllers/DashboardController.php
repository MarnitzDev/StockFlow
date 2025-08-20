<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use App\Models\Inventory;
use App\Models\Vendor;
use App\Models\PurchaseOrder;
use Carbon\Carbon;
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
}
