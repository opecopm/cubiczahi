<x-admin.subnav :items="[
    [
        'label' => 'Customers',
        'href' => url('admin/crm/customers'),
        'active' => request()->is('admin/crm/customers') || request()->is('admin/crm/customers/*'),
        'navigate' => false,
    ],
    [
        'label' => 'Customer Groups',
        'href' => url('admin/crm/customer-groups'),
        'active' => request()->is('admin/crm/customer-groups') || request()->is('admin/crm/customer-groups/*'),
        'navigate' => false,
    ],
]" />
