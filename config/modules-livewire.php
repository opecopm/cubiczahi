<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Class Namespace
    |--------------------------------------------------------------------------
    |
    */

    'namespace' => 'Livewire',

    /*
    |--------------------------------------------------------------------------
    | View Path
    |--------------------------------------------------------------------------
    |
    */

    'view' => 'Resources/views/livewire',

    /*
    |--------------------------------------------------------------------------
    | Custom modules setup
    |--------------------------------------------------------------------------
    |
    | Our modules use nwidart/laravel-modules v13 which places classes under
    | the module's `app/` folder. The default namespace lookup builds the path
    | as `{module_path}/Livewire`, but the actual path is `{module_path}/app/Livewire`.
    | We use custom_modules to point the package at the correct directory while
    | keeping the PHP namespace correct (Modules\MODULE\Livewire).
    |
    */

    'custom_modules' => [
        'IAM' => [
            'path' => base_path('Modules/IAM/app'),
            'module_namespace' => 'Modules\\IAM',
            'namespace' => 'Livewire',
            'name_lower' => 'iam',
        ],
        'Business' => [
            'path' => base_path('Modules/Business/app'),
            'module_namespace' => 'Modules\\Business',
            'namespace' => 'Livewire',
            'name_lower' => 'business',
        ],
        'Global' => [
            'path' => base_path('Modules/Global/app'),
            'module_namespace' => 'Modules\\Global',
            'namespace' => 'Livewire',
            'name_lower' => 'global',
        ],
        'System' => [
            'path' => base_path('Modules/System/app'),
            'module_namespace' => 'Modules\\System',
            'namespace' => 'Livewire',
            'name_lower' => 'system',
        ],
        'Inventory' => [
            'path' => base_path('Modules/Inventory/app'),
            'module_namespace' => 'Modules\\Inventory',
            'namespace' => 'Livewire',
            'name_lower' => 'inventory',
        ],
        'CRM' => [
            'path' => base_path('Modules/CRM/app'),
            'module_namespace' => 'Modules\\CRM',
            'namespace' => 'Livewire',
            'name_lower' => 'crm',
        ],
        'Selling' => [
            'path' => base_path('Modules/Selling/app'),
            'module_namespace' => 'Modules\\Selling',
            'namespace' => 'Livewire',
            'name_lower' => 'selling',
        ],
        'MediaGallery' => [
            'path' => base_path('Modules/MediaGallery/app'),
            'module_namespace' => 'Modules\\MediaGallery',
            'namespace' => 'Livewire',
            'name_lower' => 'mediagallery',
        ],
        'CMS' => [
            'path' => base_path('Modules/CMS/app'),
            'module_namespace' => 'Modules\\CMS',
            'namespace' => 'Livewire',
            'name_lower' => 'cms',
        ],
    ],

];
