<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Selling\Models\SalesOrder;

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $customer = $user->ensureCustomerProfile();

        $orders = SalesOrder::where('customer_id', $customer->id)
            ->with('items')
            ->get();

        return view('themes.laundry-one.pages.orders', compact('orders'));
    }

    public function show(int $orderId)
    {
        $customer = auth()->user()->ensureCustomerProfile();

        $order = SalesOrder::with(['items', 'deliveryMethod'])
            ->where('customer_id', $customer->id)
            ->findOrFail($orderId);

        return view('themes.laundry-one.pages.order.show', compact('order'));
    }
}
