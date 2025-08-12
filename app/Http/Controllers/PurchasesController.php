<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\PurchaseOrder;
use App\Models\Bill;
use App\Models\Payment;

class PurchasesController extends Controller
{
    public function orders(Request $request)
    {
        $perPage = $request->input('rows', 10);
        $sortField = $request->input('sortField', 'created_at');
        $sortOrder = $request->input('sortOrder', 'desc');
        $page = $request->input('page', 1);

        $orders = PurchaseOrder::with('vendor')
            ->when($sortField, function ($query, $sortField) use ($sortOrder) {
                $query->orderBy($sortField, $sortOrder === '1' ? 'asc' : 'desc');
            })
            ->paginate($perPage, ['*'], 'page', $page);

        return Inertia::render('App/Purchases/Orders', [
            'orders' => $orders
        ]);
    }

    public function bills()
    {
        $bills = Bill::with('purchaseOrder', 'vendor')->orderBy('created_at', 'desc')->get();

        return Inertia::render('App/Purchases/Bills', [
            'bills' => $bills
        ]);
    }

    public function payments()
    {
        $payments = Payment::with('bill', 'vendor')->orderBy('created_at', 'desc')->get();

        return Inertia::render('App/Purchases/Payments', [
            'payments' => $payments
        ]);
    }
}
