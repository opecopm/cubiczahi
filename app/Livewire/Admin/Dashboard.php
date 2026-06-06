<?php

namespace App\Livewire\Admin;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Modules\CRM\Models\Customer;
use Modules\Inventory\Models\Item;
use Modules\Selling\Models\SalesInvoice;
use Modules\Selling\Models\SalesOrder;

#[Layout('admin.layouts.app')]
class Dashboard extends Component
{
    public string $period = 'month'; // today | week | month | year

    public function render()
    {
        $now = Carbon::now();

        $periodStart = match ($this->period) {
            'today' => $now->copy()->startOfDay(),
            'week'  => $now->copy()->startOfWeek(),
            'year'  => $now->copy()->startOfYear(),
            default => $now->copy()->startOfMonth(),
        };

        // ── KPI cards ──────────────────────────────────────────────────
        $totalOrders = SalesOrder::whereBetween('order_date', [$periodStart->toDateString(), $now->toDateString()])->count();
        $totalRevenue = SalesOrder::whereBetween('order_date', [$periodStart->toDateString(), $now->toDateString()])
            ->whereNotIn('status', ['canceled', 'draft'])
            ->sum('total');

        $pendingOrders = SalesOrder::whereIn('status', ['new', 'confirmed', 'processing'])->count();
        $readyOrders   = SalesOrder::where('status', 'ready')->count();

        $totalCustomers  = Customer::count();
        $newCustomers    = Customer::whereBetween('created_at', [$periodStart, $now])->count();

        $unpaidInvoices = SalesInvoice::where('status', 'final')->where('due_amount', '>', 0)->count();
        $overdueAmount  = SalesInvoice::where('status', 'final')->where('due_amount', '>', 0)->sum('due_amount');

        $activeServices = Item::where('status', 'active')->where('type', 'service')->count();

        // ── Order status breakdown ─────────────────────────────────────
        $statusCounts = SalesOrder::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // ── Monthly revenue for the last 6 months ─────────────────────
        $monthlyRevenue = SalesOrder::select(
                DB::raw("DATE_FORMAT(order_date, '%Y-%m') as month"),
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->whereNotIn('status', ['canceled', 'draft'])
            ->where('order_date', '>=', $now->copy()->subMonths(5)->startOfMonth()->toDateString())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Fill in missing months with zeros
        $chartMonths   = [];
        $chartRevenue  = [];
        $chartOrders   = [];
        for ($i = 5; $i >= 0; $i--) {
            $key = $now->copy()->subMonths($i)->format('Y-m');
            $chartMonths[]  = $now->copy()->subMonths($i)->format('M Y');
            $row = $monthlyRevenue->firstWhere('month', $key);
            $chartRevenue[] = $row ? (float) $row->revenue : 0;
            $chartOrders[]  = $row ? (int) $row->orders : 0;
        }

        // ── Recent orders ──────────────────────────────────────────────
        $recentOrders = SalesOrder::with('customer')
            ->latest('order_date')
            ->limit(8)
            ->get();

        // ── Orders by delivery method ──────────────────────────────────
        $deliveryBreakdown = SalesOrder::select('delivery_method_id', DB::raw('count(*) as count'))
            ->with('deliveryMethod')
            ->groupBy('delivery_method_id')
            ->get();

        return view('admin.livewire.dashboard', compact(
            'totalOrders', 'totalRevenue', 'pendingOrders', 'readyOrders',
            'totalCustomers', 'newCustomers', 'unpaidInvoices', 'overdueAmount',
            'activeServices', 'statusCounts', 'recentOrders',
            'chartMonths', 'chartRevenue', 'chartOrders',
            'deliveryBreakdown',
        ));
    }
}
