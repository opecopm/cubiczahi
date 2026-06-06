@php
    $id = $customer->id;
@endphp

<x-admin.subnav :items="[
    [
        'label' => 'Details',
        'href' => url('admin/crm/customers/' . $id),
        'active' => request()->is('admin/crm/customers/' . $id),
        'navigate' => false,
    ],
    [
        'label' => 'Edit',
        'href' => url('admin/crm/customers/' . $id . '/edit'),
        'active' => request()->is('admin/crm/customers/' . $id . '/edit'),
        'navigate' => false,
    ],
]" />
