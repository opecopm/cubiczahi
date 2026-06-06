<?php

namespace App\Livewire\Customer\Catalog;

use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Modules\Inventory\Models\Item;
use Modules\Inventory\Models\ItemCategory;
use Modules\Business\Models\Currency;

class ProductList extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';

    #[Url(history: true)]
    public $activeCategory = '';

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $sort = 'name_asc';

    public function updatedSearch($value)
    {
        $this->resetPage();
    }

    public function updatedActiveCategory($value)
    {
        $this->resetPage();
    }

    public function updatedSort($value)
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Item::where('items.status', 'active')
            ->where('items.type', 'service')
            ->with(['primaryImage', 'category', 'prices', 'activeVariants']);

        $activeCatModel = null;
        if ($this->activeCategory) {
            $activeCatModel = ItemCategory::find($this->activeCategory);
            if ($activeCatModel) {
                if (is_null($activeCatModel->parent_id)) {
                    $childIds = ItemCategory::where('parent_id', $activeCatModel->id)->pluck('id')->toArray();
                    $childIds[] = $activeCatModel->id;
                    $query->whereIn('items.category_id', $childIds);
                } else {
                    $query->where('items.category_id', $this->activeCategory);
                }
            }
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('items.name', 'like', '%' . $this->search . '%')
                  ->orWhere('items.description', 'like', '%' . $this->search . '%');
            });
        }

        switch ($this->sort) {
            case 'price_asc':
                $query->join('item_prices', function ($join) {
                    $join->on('items.id', '=', 'item_prices.item_id')
                         ->where('item_prices.price_type', '=', 'sell');
                })->orderBy('item_prices.price', 'asc')->select('items.*');
                break;
            case 'price_desc':
                $query->join('item_prices', function ($join) {
                    $join->on('items.id', '=', 'item_prices.item_id')
                         ->where('item_prices.price_type', '=', 'sell');
                })->orderBy('item_prices.price', 'desc')->select('items.*');
                break;
            case 'name_desc':
                $query->orderBy('items.name->en', 'desc');
                break;
            case 'name_asc':
            default:
                $query->orderBy('items.name->en', 'asc');
                break;
        }

        $items = $query->paginate(12);

        $categories = ItemCategory::whereNull('parent_id')->with(['children' => function($q) {
            $q->withCount(['items' => function ($q) {
                $q->where('status', 'active')->where('type', 'service');
            }]);
        }])->withCount(['items' => function ($q) {
            $q->where('status', 'active')->where('type', 'service');
        }])->get();

        $activeMainCategoryId = null;
        if ($activeCatModel) {
            $activeMainCategoryId = is_null($activeCatModel->parent_id) ? $activeCatModel->id : $activeCatModel->parent_id;
        }

        $totalCount = Item::where('status', 'active')->where('type', 'service')->count();
        $defaultCurrency = Currency::where('is_default', true)->where('status', 'active')->first();

        return view(theme_view('livewire.customer.catalog.product-list'), compact('items', 'categories', 'activeMainCategoryId', 'totalCount', 'defaultCurrency'));
    }
}
