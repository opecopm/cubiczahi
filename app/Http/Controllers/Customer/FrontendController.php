<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\CMS\Models\Page;
use Modules\Business\Models\Currency;
use Modules\Inventory\Models\Item;
use Modules\CMS\Models\Banner;
use Modules\CMS\Models\Testimonial;

class FrontendController extends Controller
{
    /**
     * Display a dynamic CMS page.
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function show(string $slug)
    {
        $slug = trim($slug, '/');

        $matchedPage = Page::where('slug', $slug)
            ->where('status', 'published')
            ->with(['sections' => function ($query) {
                $query->where('is_enabled', true)->orderBy('sort_order', 'asc');
            }, 'sections.blocks' => function ($query) {
                $query->where('is_enabled', true)->orderBy('sort_order', 'asc');
            }])
            ->first();

        if (!$matchedPage) {
            abort(404, 'Page not found');
        }

        // Fetch dynamic system contexts if available (e.g. default currency)
        $defaultCurrency = null;
        if (class_exists(\Modules\Business\Models\Currency::class)) {
            $defaultCurrency = Currency::where('is_default', true)->where('status', 'active')->first();
        }

        // Support custom page layouts from themes if specified (e.g. custom home template)
        $viewPath = 'pages.cms-page';
        if ($matchedPage->template_type === 'custom' && !empty($matchedPage->template_name)) {
            $customView = 'pages.' . $matchedPage->template_name;
            if (view()->exists(theme_view($customView))) {
                $viewPath = $customView;
            }
        }

        $data = [
            'page' => $matchedPage,
            'defaultCurrency' => $defaultCurrency,
        ];

        // If rendering the custom home template, load the dynamic widgets expected by the layout
        if ($viewPath === 'pages.home') {
            if (class_exists(Item::class)) {
                $data['featuredServices'] = Item::where('status', 'active')
                    ->where('type', 'service')
                    ->with(['primaryImage', 'prices', 'activeVariants'])
                    ->take(6)
                    ->get();
            } else {
                $data['featuredServices'] = collect();
            }

            if (class_exists(Banner::class)) {
                $data['heroBanner'] = Banner::where('slug', 'home-sliders')
                    ->where('status', true)
                    ->with(['items' => function ($query) {
                        $query->where('status', 1)->orderBy('sort_order');
                    }])
                    ->first();
            } else {
                $data['heroBanner'] = null;
            }

            if (class_exists(Testimonial::class)) {
                $data['testimonials'] = Testimonial::where('status', true)
                    ->orderBy('sort_order', 'asc')
                    ->orderBy('created_at', 'desc')
                    ->get();
            } else {
                $data['testimonials'] = collect();
            }
        }

        return view(theme_view($viewPath), $data);
    }
}
