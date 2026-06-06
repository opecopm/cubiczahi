<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Attributes\Modelable;
use Livewire\Component;
use Modules\Accounting\Models\Account;
use Modules\Business\Models\Location;
use Modules\Business\Models\Tax;
use Modules\CRM\Models\Customer;
use Modules\Inventory\Models\Brand;
use Modules\Inventory\Models\Item;
use Modules\Inventory\Models\ItemCategory;

class EntitySelect extends Component
{
    public string $entity = '';

    public string $label = '';

    public ?string $icon = null;

    public ?string $addNewUrl = null;

    public array $params = [];

    #[Modelable]
    public $value = null;

    public string $search = '';

    public array $options = [];

    public int $limit = 10;

    public int $minChars = 2;

    private bool $suppressSearchUpdated = false;

    public function mount(string $entity, ?string $label = null, ?string $icon = null, ?string $addNewUrl = null, $value = null, int $limit = 10, int $minChars = 2, array $params = [])
    {
        $this->entity = $entity;
        $this->label = $label ?? $this->defaultLabel($entity);
        $this->icon = $icon;
        $this->addNewUrl = $addNewUrl;
        $this->value = $value;
        $this->limit = $limit;
        $this->minChars = $minChars;
        $this->params = $params;

        $this->syncSearchFromValue();
    }

    public function updatedValue()
    {
        $this->syncSearchFromValue();
    }

    public function updatedSearch()
    {
        if ($this->suppressSearchUpdated) {
            $this->suppressSearchUpdated = false;

            return;
        }

        if ($this->value !== null) {
            $this->value = null;
        }

        $term = trim($this->search);
        if ($term === '') {
            $this->options = $this->defaultOptions();

            return;
        }

        if (mb_strlen($term) < $this->minChars) {
            $this->options = [];

            return;
        }

        $this->options = $this->searchOptions($term);
    }

    public function refreshOptions()
    {
        if ($this->value !== null) {
            $this->options = [];

            return;
        }

        $term = trim($this->search);
        if ($term === '') {
            $this->options = $this->defaultOptions();

            return;
        }

        if (mb_strlen($term) < $this->minChars) {
            $this->options = [];

            return;
        }

        $this->options = $this->searchOptions($term);
    }

    public function selectOption($id): void
    {
        $config = $this->config();
        $record = $this->baseQuery($config)->find($id);
        if (! $record) {
            return;
        }

        $this->value = $record->id;
        $this->options = [];

        $this->suppressSearchUpdated = true;
        $this->search = $this->formatPrimary($record);
    }

    public function clearSelection(): void
    {
        $this->value = null;
        $this->options = [];
        $this->suppressSearchUpdated = true;
        $this->search = '';
    }

    private function syncSearchFromValue(): void
    {
        if (empty($this->value)) {
            $this->options = [];
            $this->suppressSearchUpdated = true;
            $this->search = '';

            return;
        }

        $config = $this->config();
        $record = $this->baseQuery($config)->find($this->value);
        if (! $record) {
            $this->value = null;
            $this->options = [];
            $this->suppressSearchUpdated = true;
            $this->search = '';

            return;
        }

        $this->options = [];
        $this->suppressSearchUpdated = true;
        $this->search = $this->formatPrimary($record);
    }

    private function defaultOptions(): array
    {
        $config = $this->config();
        $query = $this->baseQuery($config);

        if (! empty($config['orderBy'])) {
            $query->orderBy($config['orderBy']);
        }

        $records = $query->limit($this->limit)->get();

        return $records->map(function ($record) {
            return [
                'id' => $record->id,
                'primary' => $this->formatPrimary($record),
                'secondary' => $this->formatSecondary($record),
            ];
        })->toArray();
    }

    private function searchOptions(string $term): array
    {
        $config = $this->config();
        $query = $this->baseQuery($config);
        $locale = app()->getLocale();

        $query->where(function ($q) use ($term, $config, $locale) {
            foreach ($config['search'] as $col) {
                $column = str_replace('{locale}', $locale, $col);
                $q->orWhere($column, 'like', '%'.$term.'%');
            }
        });

        if (! empty($config['orderBy'])) {
            $query->orderBy($config['orderBy']);
        }

        $records = $query->limit($this->limit)->get();

        return $records->map(function ($record) {
            return [
                'id' => $record->id,
                'primary' => $this->formatPrimary($record),
                'secondary' => $this->formatSecondary($record),
            ];
        })->toArray();
    }

    private function baseQuery(array $config)
    {
        $modelClass = $config['model'];
        $query = $modelClass::query();

        if (! empty($config['query']) && is_callable($config['query'])) {
            ($config['query'])($query);
        }

        return $query;
    }

    private function config(): array
    {
        return match ($this->entity) {
            'expense_account' => [
                'model' => Account::class,
                'search' => ['code', 'name'],
                'orderBy' => 'name',
                'query' => fn ($q) => $q->where('active', true)->where('type', 'expense'),
            ],
            'cash_account' => [
                'model' => Account::class,
                'search' => ['code', 'name'],
                'orderBy' => 'name',
                'query' => fn ($q) => $q->where('active', true)->where('type', 'cash'),
            ],
            'item' => [
                'model' => Item::class,
                'search' => ['reference', 'name', 'model_number'],
                'orderBy' => 'reference',
                'query' => function ($q) {
                    $categoryId = $this->params['category_id'] ?? null;
                    $trackInventory = $this->params['track_inventory'] ?? null;
                    $type = $this->params['type'] ?? null;
                    if (! empty($categoryId)) {
                        $categoryIds = $this->itemCategoryAndDescendantIds((int) $categoryId);
                        $q->whereIn('category_id', $categoryIds);
                    }

                    if ($type !== null && $type !== '') {
                        $q->where('type', $type);
                    }
                    if ($trackInventory === true || $trackInventory === 1 || $trackInventory === 'true') {
                        $q->where('track_inventory', true);
                    }
                },
            ],
            'inventory_item' => [
                'model' => Item::class,
                'search' => ['reference', 'name', 'model_number'],
                'orderBy' => 'reference',
                'query' => function ($q) {
                    $trackInventory = $this->params['track_inventory'] ?? null;
                    if ($trackInventory === null || $trackInventory === '' || $trackInventory === true || $trackInventory === 1 || $trackInventory === 'true') {
                        $q->where('track_inventory', true);
                    }

                    $type = $this->params['type'] ?? null;
                    if ($type !== null && $type !== '') {
                        $q->where('type', $type);
                    }
                },
            ],
            'item_category' => [
                'model' => ItemCategory::class,
                'search' => ['name', 'name->{locale}'],
                'orderBy' => 'name',
                'query' => fn ($q) => $q->with('parent.parent.parent.parent.parent'),
            ],
            'brand' => [
                'model' => Brand::class,
                'search' => ['name'],
                'orderBy' => 'name',
            ],
            'tax' => [
                'model' => Tax::class,
                'search' => ['name', 'rate'],
                'orderBy' => 'name',
            ],
            'location' => [
                'model' => Location::class,
                'search' => ['name', 'code', 'type'],
                'orderBy' => 'name',
                'query' => function ($q) {
                    $id = $this->params['id'] ?? null;
                    if (! empty($id)) {
                        $q->where('id', (int) $id);
                    }
                },
            ],
            'project' => [
                'model' => User::class,
                'search' => ['email'],
                'query' => fn ($q) => $q->whereRaw('1=0'),
            ],
            'customer' => [
                'model' => Customer::class,
                'search' => ['reference', 'name', 'email', 'phone', 'company->{locale}'],
                'orderBy' => 'reference',
            ],
            'user' => [
                'model' => User::class,
                'search' => ['first_name', 'last_name', 'email'],
                'orderBy' => 'first_name',
                'query' => function ($q) {
                    $companyId = $this->params['company_id'] ?? null;
                    if (! empty($companyId)) {
                        $q->whereHas('companies', function ($cq) use ($companyId) {
                            $cq->where('companies.id', (int) $companyId);
                        });
                    }

                    $locationId = $this->params['location_id'] ?? null;
                    if (! empty($locationId)) {
                        $q->where('location_id', (int) $locationId);
                    }
                },
            ],
            default => [
                'model' => User::class,
                'search' => ['email'],
                'orderBy' => 'email',
            ],
        };
    }

    private function defaultLabel(string $entity): string
    {
        return match ($entity) {
            'expense_account' => 'Account',
            'cash_account' => 'Paid Account',
            'item' => 'Item',
            'inventory_item' => 'Item',
            'item_category' => 'Category',
            'brand' => 'Brand',
            'tax' => 'Tax',
            'location' => 'Location',
            'project' => 'Project',
            'customer' => 'Customer',
            'user' => 'User',
            default => 'Select',
        };
    }

    private function formatPrimary($record): string
    {
        return match ($this->entity) {
            'expense_account', 'cash_account' => trim((string) ($record->code ?? '').' - '.(string) ($record->name ?? '')),
            'item', 'inventory_item' => trim(
                (string) ($record->reference ?? '')
                .(! empty($record->model_number) ? ' - '.(string) $record->model_number : '')
            ),
            'item_category' => $this->itemCategoryBreadcrumb($record),
            'brand' => (string) ($record->name ?? ''),
            'tax' => trim((string) ($record->name ?? '').' - '.(string) ($record->rate ?? '').'%'),
            'location' => trim(
                (string) ($record->name ?? '')
                .(! empty($record->type) ? ' ('.(string) $record->type.')' : '')
            ),
            'project' => (string) ($record->id ?? ''),
            'customer' => trim((string) ($record->reference ?? '').' - '.(string) ($record->name ?? '')),
            'user' => trim((string) ($record->first_name ?? '').' '.(string) ($record->last_name ?? '')),
            default => (string) ($record->id ?? ''),
        };
    }

    private function formatSecondary($record): string
    {
        return match ($this->entity) {
            'item', 'inventory_item' => (string) ($record->name ?? ''),
            'item_category' => $this->itemCategoryParentBreadcrumb($record),
            'customer' => (string) ($record->email ?? ''),
            'user' => (string) ($record->email ?? ''),
            'expense_account', 'cash_account' => (string) ($record->type ?? ''),
            'location' => (string) ($record->code ?? ''),
            default => '',
        };
    }

    private function itemCategoryBreadcrumb($category): string
    {
        $parts = [];
        $current = $category;
        $guard = 0;

        while ($current && $guard < 20) {
            $name = (string) ($current->name ?? '');
            if ($name !== '') {
                $parts[] = $name;
            }

            $current = $current->parent;
            $guard++;
        }

        return implode(' > ', array_reverse($parts));
    }

    private function itemCategoryParentBreadcrumb($category): string
    {
        if (empty($category?->parent)) {
            return '';
        }

        $parts = [];
        $current = $category->parent;
        $guard = 0;

        while ($current && $guard < 20) {
            $name = (string) ($current->name ?? '');
            if ($name !== '') {
                $parts[] = $name;
            }

            $current = $current->parent;
            $guard++;
        }

        return implode(' > ', array_reverse($parts));
    }

    private function itemCategoryAndDescendantIds(int $rootId): array
    {
        if ($rootId <= 0) {
            return [];
        }

        $ids = [$rootId];
        $frontier = [$rootId];
        $guard = 0;

        while (! empty($frontier) && $guard < 50) {
            $children = ItemCategory::query()
                ->whereIn('parent_id', $frontier)
                ->pluck('id')
                ->all();

            $children = array_values(array_diff($children, $ids));
            if ($children === []) {
                break;
            }

            $ids = array_merge($ids, $children);
            $frontier = $children;
            $guard++;
        }

        return $ids;
    }

    public function render()
    {
        return view('admin.livewire.entity-select');
    }
}
