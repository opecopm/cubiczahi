<?php

namespace Modules\Selling\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Selling\Models\SalesOrder;

class SalesOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('selling::sales-orders.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('selling::sales-orders.create');
    }

    /**
     * Show the form for edit the resource.
     */
    public function edit($id)
    {
        return view('selling::sales-orders.edit', compact('id'));
    }

    public function show($id)
    {
        return view('selling::sales-orders.show', compact('id'));
    }

    public function print($id)
    {
        $salesOrder = SalesOrder::with(['items', 'customer.addresses', 'company'])->findOrFail($id);

        return view('selling::sales-orders.print', compact('salesOrder'));
    }
}
