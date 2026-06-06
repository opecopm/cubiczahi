@php
    $isCatalogNav =
        request()->is('admin/inventory/items') ||
        request()->is('admin/inventory/items/*') ||
        request()->is('admin/inventory/services') ||
        request()->is('admin/inventory/services/*') ||
        request()->is('admin/inventory/item-categories') ||
        request()->is('admin/inventory/item-categories/*') ||
        request()->is('admin/inventory/brands') ||
        request()->is('admin/inventory/brands/*');
@endphp

<x-admin.subnav :items="[
    [
        'label' => 'Products',
        'href' => url('admin/inventory/items'),
        'active' => request()->is('admin/inventory/items') || request()->is('admin/inventory/items/*'),
        'navigate' => false,
    ],
    [
        'label' => 'Services',
        'href' => url('admin/inventory/services'),
        'active' => request()->is('admin/inventory/services') || request()->is('admin/inventory/services/*'),
        'navigate' => false,
    ],
    [
        'label' => 'Categories',
        'href' => url('admin/inventory/item-categories'),
        'active' => request()->is('admin/inventory/item-categories') || request()->is('admin/inventory/item-categories/*'),
        'navigate' => false,
    ],
    [
        'label' => 'Brands',
        'href' => url('admin/inventory/brands'),
        'active' => request()->is('admin/inventory/brands') || request()->is('admin/inventory/brands/*'),
        'navigate' => false,
    ]
]" />
