<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Modules\Business\Models\Currency;
use Modules\Inventory\Models\Item;
use Modules\Inventory\Models\ItemCategory;

class CatalogController extends Controller
{
    public function index(): View
    {
        $totalCount = Item::where('status', 'active')->where('type', 'service')->count();

        return view(theme_view('pages.catalog.index'), compact('totalCount'));
    }

    public function deals(): View
    {
        return view(theme_view('pages.catalog.deals'));
    }

    public function show(string $slug): View
    {
        $item = Item::where(function($q) use ($slug) {
                $q->where('slug', $slug)->orWhere('id', $slug);
            })
            ->where('status', 'active')
            ->with(['primaryImage', 'images', 'category', 'prices'])
            ->firstOrFail();

        $related = Item::where('status', 'active')
            ->where('type', 'service')
            ->where('id', '!=', $item->id)
            ->with(['primaryImage', 'prices', 'activeVariants'])
            ->take(3)
            ->get();

        return view(theme_view('pages.catalog.show'), compact('item', 'related'));
    }
}
