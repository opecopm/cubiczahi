<?php

namespace App\Livewire\Admin\Layout;

use App\Livewire\Actions\Logout;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Navigation extends Component
{
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect(route('admin.login'), navigate: true);
    }

    public function markAllRead(): void
    {
        dd('mark all read'); // TODO: implement mark all read functionality
        Auth::user()?->unreadNotifications->markAsRead();
    }

    public function render()
    {
        $menu = [
            [
                'title' => __('Dashboard'),
                'href' => route('admin.dashboard'),
                'active' => request()->routeIs('admin.dashboard'),
                'icon' => 'ti-home',
                'children' => null,
            ],
            ['type' => 'section', 'title' => 'CRM'],
            [
                'title' => 'Customers',
                'href' => '#',
                'active' => request()->routeIs('admin.crm.*'),
                'icon' => 'ti-users',
                'children' => [
                    [
                        'title' => 'Customers',
                        'href' => route('admin.crm.customers.index'),
                        'active' => request()->routeIs('admin.crm.customers.*'),
                        'icon' => 'ti-users',
                    ],
                    [
                        'title' => 'Customer Groups',
                        'href' => route('admin.crm.customer-groups.index'),
                        'active' => request()->routeIs('admin.crm.customer-groups.*'),
                        'icon' => 'ti-users-group',
                    ],
                ],
            ],
            ['type' => 'section', 'title' => 'Selling'],
            [
                'title' => 'Orders & Invoices',
                'href' => '#',
                'active' => request()->routeIs('admin.selling.*'),
                'icon' => 'ti-shopping-cart',
                'children' => [
                    [
                        'title' => 'Sales Orders',
                        'href' => route('admin.selling.sales-orders.index'),
                        'active' => request()->routeIs('admin.selling.sales-orders.*'),
                        'icon' => 'ti-file-invoice',
                    ],
                    [
                        'title' => 'Sales Invoices',
                        'href' => route('admin.selling.sales-invoices.index'),
                        'active' => request()->routeIs('admin.selling.sales-invoices.*'),
                        'icon' => 'ti-receipt',
                    ],
                ],
            ],
            ['type' => 'section', 'title' => 'Inventory'],
            [
                'title' => 'Items',
                'href' => route('admin.inventory.items.index'),
                'active' => request()->routeIs('admin.inventory.items.*'),
                'icon' => 'ti-package',
                'children' => null,
            ],
            ['type' => 'section', 'title' => 'Content'],
            [
                'title' => 'CMS',
                'href' => '#',
                'active' => request()->routeIs('admin.cms.*'),
                'icon' => 'ti-layout-dashboard',
                'children' => [
                    [
                        'title' => 'Pages',
                        'href' => route('admin.cms.pages.index'),
                        'active' => request()->routeIs('admin.cms.pages.*'),
                        'icon' => 'ti-file-description',
                    ],
                    [
                        'title' => 'Blog Posts',
                        'href' => route('admin.cms.blogs.index'),
                        'active' => request()->routeIs('admin.cms.blogs.*'),
                        'icon' => 'ti-pencil',
                    ],
                    [
                        'title' => 'Categories',
                        'href' => route('admin.cms.blog-categories.index'),
                        'active' => request()->routeIs('admin.cms.blog-categories.*'),
                        'icon' => 'ti-tags',
                    ],
                    [
                        'title' => 'Banners',
                        'href' => route('admin.cms.banners.index'),
                        'active' => request()->routeIs('admin.cms.banners.*'),
                        'icon' => 'ti-picture-in-picture',
                    ],
                    [
                        'title' => 'Testimonials',
                        'href' => route('admin.cms.testimonials.index'),
                        'active' => request()->routeIs('admin.cms.testimonials.*'),
                        'icon' => 'ti-message-share',
                    ],
                ],
            ],
            [
                'title' => 'Media Gallery',
                'href' => route('admin.mediagallery.media-assets.index'),
                'active' => request()->routeIs('admin.mediagallery.*'),
                'icon' => 'ti-photo',
                'children' => null,
            ],
            ['type' => 'section', 'title' => 'IAM'],
            [
                'title' => 'Users & Roles',
                'href' => '#',
                'active' => request()->routeIs('admin.iam.*'),
                'icon' => 'ti-shield-lock',
                'children' => [
                    [
                        'title' => 'Users',
                        'href' => route('admin.iam.users.index'),
                        'active' => request()->routeIs('admin.iam.users.*'),
                        'icon' => 'ti-users',
                    ],
                    [
                        'title' => 'Roles',
                        'href' => route('admin.iam.roles.index'),
                        'active' => request()->routeIs('admin.iam.roles.*'),
                        'icon' => 'ti-shield',
                    ],
                    [
                        'title' => 'Teams',
                        'href' => route('admin.iam.teams.index'),
                        'active' => request()->routeIs('admin.iam.teams.*'),
                        'icon' => 'ti-users-group',
                    ],
                    [
                        'title' => 'Permissions',
                        'href' => route('admin.iam.permissions.index'),
                        'active' => request()->routeIs('admin.iam.permissions.*'),
                        'icon' => 'ti-lock',
                    ],
                    [
                        'title' => 'Permission Groups',
                        'href' => route('admin.iam.permission-groups.index'),
                        'active' => request()->routeIs('admin.iam.permission-groups.*'),
                        'icon' => 'ti-lock-cog',
                    ],
                ],
            ],
            ['type' => 'section', 'title' => 'Business'],
            [
                'title' => 'Business',
                'href' => '#',
                'active' => request()->routeIs('admin.business.*'),
                'icon' => 'ti-building',
                'children' => [
                    [
                        'title' => 'Companies',
                        'href' => route('admin.business.companies.index'),
                        'active' => request()->routeIs('admin.business.companies.*'),
                        'icon' => 'ti-building-store',
                    ],
                    [
                        'title' => 'Locations',
                        'href' => route('admin.business.locations.index'),
                        'active' => request()->routeIs('admin.business.locations.*'),
                        'icon' => 'ti-map-pin',
                    ],
                    [
                        'title' => 'Currencies',
                        'href' => route('admin.business.currencies.index'),
                        'active' => request()->routeIs('admin.business.currencies.*'),
                        'icon' => 'ti-coin',
                    ],
                    [
                        'title' => 'Taxes',
                        'href' => route('admin.business.taxes.index'),
                        'active' => request()->routeIs('admin.business.taxes.*'),
                        'icon' => 'ti-receipt',
                    ],
                    [
                        'title' => 'Partners',
                        'href' => route('admin.business.business-partners.index'),
                        'active' => request()->routeIs('admin.business.business-partners.*'),
                        'icon' => 'ti-handshake',
                    ],
                    [
                        'title' => 'Sponsors',
                        'href' => route('admin.business.sponsors.index'),
                        'active' => request()->routeIs('admin.business.sponsors.*'),
                        'icon' => 'ti-heart',
                    ],
                    [
                        'title' => 'Settings',
                        'href' => route('admin.business.settings'),
                        'active' => request()->routeIs('admin.business.settings'),
                        'icon' => 'ti-adjustments-horizontal',
                    ],
                ],
            ],
            ['type' => 'section', 'title' => 'Global'],
            [
                'title' => 'Global',
                'href' => '#',
                'active' => request()->routeIs('admin.global.*'),
                'icon' => 'ti-world',
                'children' => [
                    [
                        'title' => 'Languages',
                        'href' => route('admin.global.languages.index'),
                        'active' => request()->routeIs('admin.global.languages.*'),
                        'icon' => 'ti-language',
                    ],
                    [
                        'title' => 'Custom Fields',
                        'href' => route('admin.global.custom-fields.index'),
                        'active' => request()->routeIs('admin.global.custom-fields.*'),
                        'icon' => 'ti-forms',
                    ],
                    [
                        'title' => 'Reference Schemas',
                        'href' => route('admin.global.reference-schemas.index'),
                        'active' => request()->routeIs('admin.global.reference-schemas.*'),
                        'icon' => 'ti-schema',
                    ],
                    [
                        'title' => 'Document Types',
                        'href' => route('admin.global.document-types.index'),
                        'active' => request()->routeIs('admin.global.document-types.*'),
                        'icon' => 'ti-file-description',
                    ],
                ],
            ],
            ['type' => 'section', 'title' => 'System'],
            [
                'title' => 'System',
                'href' => '#',
                'active' => request()->routeIs('admin.system.*'),
                'icon' => 'ti-settings',
                'children' => [
                    [
                        'title' => 'Menus',
                        'href' => route('admin.system.menus.index'),
                        'active' => request()->routeIs('admin.system.menus.*'),
                        'icon' => 'ti-layout-sidebar',
                    ],
                    [
                        'title' => 'Menu Items',
                        'href' => route('admin.system.menu-items.index'),
                        'active' => request()->routeIs('admin.system.menu-items.*'),
                        'icon' => 'ti-list',
                    ],
                    [
                        'title' => 'Workflows',
                        'href' => route('admin.system.workflows.index'),
                        'active' => request()->routeIs('admin.system.workflows.*'),
                        'icon' => 'ti-git-branch',
                    ],
                ],
            ],
        ];

        return view('admin.livewire.layout.navigation', [
            'menu' => $menu,
        ]);
    }
}
