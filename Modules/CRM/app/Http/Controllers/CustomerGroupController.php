<?php

namespace Modules\CRM\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CRM\Models\CustomerGroup;

class CustomerGroupController extends Controller
{
    public function index()
    {
        return view('crm::customergroups.index');
    }

    public function create()
    {
        return view('crm::customergroups.index');
    }

    public function store(Request $request)
    {
        // Delegated to Livewire
        return redirect()->route('admin.crm.customer-groups.index');
    }

    public function show(CustomerGroup $customerGroup)
    {
        return view('crm::customergroups.index');
    }

    public function edit(CustomerGroup $customerGroup)
    {
        return view('crm::customergroups.index');
    }

    public function update(Request $request, CustomerGroup $customerGroup)
    {
        // Delegated to Livewire
        return redirect()->route('admin.crm.customer-groups.index');
    }

    public function destroy(CustomerGroup $customerGroup)
    {
        // Delegated to Livewire
        return redirect()->route('admin.crm.customer-groups.index');
    }
}
