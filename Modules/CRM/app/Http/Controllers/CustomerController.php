<?php

namespace Modules\CRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\CRM\Models\Customer;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return view('crm::customers.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('crm::customers.create');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {

        return view('crm::customers.show', compact('id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('crm::customers.edit', compact('id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function documents($id)
    {
        $customer = Customer::findOrFail($id);

        return view('crm::customers.documents', compact('customer'));
    }

    public function orders($id)
    {
        $customer = Customer::findOrFail($id);

        return view('crm::customers.orders', compact('customer'));
    }

    public function invoices($id)
    {
        $customer = Customer::findOrFail($id);

        return view('crm::customers.invoices', compact('customer'));
    }

    public function showOrder($id, $orderId)
    {
        $customer = Customer::findOrFail($id);

        return view('crm::customers.show-order', compact('customer', 'orderId'));
    }

    public function showInvoice($id, $invoiceId)
    {
        $customer = Customer::findOrFail($id);

        return view('crm::customers.show-invoice', compact('customer', 'invoiceId'));
    }
}
