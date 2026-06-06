<x-admin.subnav :items="[
    [
        'label' => 'Sales Orders',
        'href' => url('admin/selling/sales-orders'),
        'active' => request()->is('admin/selling/sales-orders') || request()->is('admin/selling/sales-orders/*'),
        'navigate' => false,
    ],
    [
        'label' => 'Sales Invoices',
        'href' => url('admin/selling/sales-invoices'),
        'active' => request()->is('admin/selling/sales-invoices') || request()->is('admin/selling/sales-invoices/*'),
        'navigate' => false,
    ],
]" />

