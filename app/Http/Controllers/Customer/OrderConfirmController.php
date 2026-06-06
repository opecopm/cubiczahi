<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Modules\Selling\Models\SalesOrder;

class OrderConfirmController extends Controller
{
    public function show(int $orderId): View
    {
        $customer = auth()->user()->ensureCustomerProfile();

        $order = SalesOrder::with('items')
            ->where('customer_id', $customer->id)
            ->findOrFail($orderId);

        return view(theme_view('pages.order.confirm'), compact('order'));
    }
}
