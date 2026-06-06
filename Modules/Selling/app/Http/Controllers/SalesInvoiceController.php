<?php

namespace Modules\Selling\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Selling\Models\SalesInvoice;

class SalesInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('selling::sales-invoices.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($purchase_invoice_id = null)
    {
        if (request()->input('purchase_invoice_id')) {
            $purchase_invoice_id = request()->input('purchase_invoice_id');
        }

        return view('selling::sales-invoices.create', compact('purchase_invoice_id'));
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('selling::sales-invoices.show', compact('id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('selling::sales-invoices.edit', compact('id'));
    }

    public function print($id)
    {
        $salesInvoice = SalesInvoice::with('items')->findOrFail($id);

        $purchaseInvoice = $salesInvoice->purchaseInvoice;

        // If you also need its items
        $purchaseInvoiceItems = $purchaseInvoice?->items ?? collect();

        // Calculate totals from the purchase invoice
        $purchaseTotals = [
            'subtotal' => $purchaseInvoice?->total_price ?? 0,
            'discount' => $purchaseInvoice?->discount ?? 0,
            'amount' => $purchaseInvoice?->subtotal ?? 0,
            'tax' => $purchaseInvoice?->tax ?? 0,
            'total' => $purchaseInvoice?->total ?? 0,
        ];

        $view = 'selling::sales-invoices.print.customer-copy';

        return view($view, compact('salesInvoice', 'purchaseTotals', 'purchaseInvoice', 'purchaseInvoiceItems'));
    }
}
