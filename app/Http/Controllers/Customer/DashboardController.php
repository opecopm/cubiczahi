<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Modules\Selling\Models\SalesOrder;

class DashboardController extends Controller
{
    public function index(): View
    {
        $customer = auth()->user()->ensureCustomerProfile();

        $ordersCount = SalesOrder::where('customer_id', $customer->id)->count();
        $recentOrders = SalesOrder::where('customer_id', $customer->id)
            ->latest()
            ->take(5)
            ->get();
        $addressesCount = $customer->addresses()->count();

        return view(theme_view('pages.dashboard'), compact(
            'addressesCount',
            'ordersCount',
            'recentOrders'
        ));
    }
}
