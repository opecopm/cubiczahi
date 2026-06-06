<x-admin.subnav :items="[
    [
        'label' => 'Users',
        'route' => 'admin.iam.users.index',
        'activeWhen' => ['admin.iam.users.*'],
    ],
    [
        'label' => 'Roles',
        'route' => 'admin.iam.roles.index',
        'activeWhen' => ['admin.iam.roles.*'],
    ],
    [
        'label' => 'Teams',
        'route' => 'admin.iam.teams.index',
        'activeWhen' => ['admin.iam.teams.*'],
    ],
    [
        'label' => 'Permissions',
        'route' => 'admin.iam.permissions.index',
        'activeWhen' => ['admin.iam.permissions.*'],
    ],
    [
        'label' => 'Permission Groups',
        'route' => 'admin.iam.permission-groups.index',
        'activeWhen' => ['admin.iam.permission-groups.*'],
    ],
]" />
