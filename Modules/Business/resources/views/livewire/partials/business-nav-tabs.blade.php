<x-admin.subnav :items="[
    [
        'label' => 'Settings',
        'route' => 'admin.business.settings',
        'activeWhen' => ['admin.business.settings'],
    ],
    [
        'label' => 'Companies',
        'route' => 'admin.business.companies.index',
        'activeWhen' => ['admin.business.companies.*'],
    ],
    [
        'label' => 'Locations',
        'route' => 'admin.business.locations.index',
        'activeWhen' => ['admin.business.locations.*'],
    ],
    [
        'label' => 'Partners',
        'route' => 'admin.business.business-partners.index',
        'activeWhen' => ['admin.business.business-partners.*'],
    ],
    [
        'label' => 'Sponsors',
        'route' => 'admin.business.sponsors.index',
        'activeWhen' => ['admin.business.sponsors.*'],
    ],
    [
        'label' => 'Taxes',
        'route' => 'admin.business.taxes.index',
        'activeWhen' => ['admin.business.taxes.*'],
    ],
    [
        'label' => 'Currencies',
        'route' => 'admin.business.currencies.index',
        'activeWhen' => ['admin.business.currencies.*'],
    ],
]" />
