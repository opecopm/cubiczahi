<x-admin.subnav :items="[
    [
        'label' => 'Pages',
        'route' => 'admin.cms.pages.index',
        'activeWhen' => ['admin.cms.pages.*'],
    ],
    [
        'label' => 'Page Builder',
        'route' => 'admin.cms.page-builder.index',
        'activeWhen' => ['admin.cms.page-builder.*'],
    ],
    [
        'label' => 'Blogs',
        'route' => 'admin.cms.blogs.index',
        'activeWhen' => ['admin.cms.blogs.*'],
    ],
    [
        'label' => 'Blog Categories',
        'route' => 'admin.cms.blog-categories.index',
        'activeWhen' => ['admin.cms.blog-categories.*'],
    ],
    [
        'label' => 'Banners',
        'route' => 'admin.cms.banners.index',
        'activeWhen' => ['admin.cms.banners.*'],
    ],
    [
        'label' => 'Teams',
        'route' => 'admin.cms.teams.index',
        'activeWhen' => ['admin.cms.teams.*'],
    ],
    [
        'label' => 'Testimonials',
        'route' => 'admin.cms.testimonials.index',
        'activeWhen' => ['admin.cms.testimonials.*'],
    ],
    [
        'label' => 'Projects',
        'route' => 'admin.cms.projects.index',
        'activeWhen' => ['admin.cms.projects.*'],
    ],
    [
        'label' => 'Forms',
        'route' => 'admin.cms.forms.index',
        'activeWhen' => ['admin.cms.forms.*'],
    ],
    [
        'label' => 'Menus',
        'route' => 'admin.cms.menus.index',
        'activeWhen' => ['admin.cms.menus.*', 'admin.cms.menu-items.*'],
    ],
    [
        'label' => 'Web Settings',
        'route' => 'admin.cms.web-settings.index',
        'activeWhen' => ['admin.cms.web-settings.*'],
    ],
]" />
