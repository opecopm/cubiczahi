<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;

class ShopController extends Controller
{
    public function index()
    {
        return view(theme_view('pages.shop.cart'));
    }

    public function checkout()
    {
        return view(theme_view('pages.shop.checkout'));
    }
}
