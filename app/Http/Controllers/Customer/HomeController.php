<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Modules\Business\Models\Currency;
use Modules\Inventory\Models\Item;
use Modules\CMS\Models\Banner;
use Modules\CMS\Models\Testimonial;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredServices = Item::where('status', 'active')
            ->where('type', 'service')
            ->with(['primaryImage', 'prices', 'activeVariants'])
            ->take(6)
            ->get();

        $defaultCurrency = Currency::where('is_default', true)->where('status', 'active')->first();

        $heroBanner = Banner::where('slug', 'home-sliders')
            ->where('status', true)
            ->with(['items' => function ($query) {
                $query->where('status', 1)->orderBy('sort_order');
            }])
            ->first();

        $testimonials = Testimonial::where('status', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view(theme_view('pages.home'), compact('featuredServices', 'defaultCurrency', 'heroBanner', 'testimonials'));
    }
}
