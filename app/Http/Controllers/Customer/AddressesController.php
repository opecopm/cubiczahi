<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AddressesController extends Controller
{
    public function index(Request $request)
    {
        auth()->user()->ensureCustomerProfile();

        return view('themes.laundry-one.pages.addresses');
    }
}
