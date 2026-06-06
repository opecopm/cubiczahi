<x-admin.subnav :items="[
    [
        'label' => 'Menus',
        'route' => 'admin.system.menus.index',
        'activeWhen' => ['admin.system.menus.*'],
    ],
    [
        'label' => 'Menu Items',
        'route' => 'admin.system.menu-items.index',
        'activeWhen' => ['admin.system.menu-items.*'],
    ],
    [
        'label' => 'Workflows',
        'route' => 'admin.system.workflows.index',
        'activeWhen' => ['admin.system.workflows.*'],
    ],
]" />
