<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For detailed instructions you can look the title section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'title' => 'AHQAMS',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For detailed instructions you can look the favicon section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_ico_only' => false,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Google Fonts
    |--------------------------------------------------------------------------
    |
    | Here you can allow or not the use of external google fonts. Disabling the
    | google fonts may be useful if your admin panel internet access is
    | restricted somehow.
    |
    | For detailed instructions you can look the google fonts section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'google_fonts' => [
        'allowed' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For detailed instructions you can look the logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'logo' => '<b style="color: white;">AHQAMS</b>',
    'logo_img' => 'login_assets/img/Army_Logo_my.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'Admin Logo',

    /*
    |--------------------------------------------------------------------------
    | Authentication Logo
    |--------------------------------------------------------------------------
    |
    | Here you can setup an alternative logo to use on your login and register
    | screens. When disabled, the admin panel logo will be used instead.
    |
    | For detailed instructions you can look the auth logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'auth_logo' => [
        'enabled' => false,
        'img' => [
            'path' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
            'alt' => 'Auth Logo',
            'class' => '',
            'width' => 50,
            'height' => 50,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Preloader Animation
    |--------------------------------------------------------------------------
    |
    | Here you can change the preloader animation configuration. Currently, two
    | modes are supported: 'fullscreen' for a fullscreen preloader animation
    | and 'cwrapper' to attach the preloader animation into the content-wrapper
    | element and avoid overlapping it with the sidebars and the top navbar.
    |
    | For detailed instructions you can look the preloader section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'preloader' => [
        'enabled' => false,
        'mode' => 'fullscreen',
        'img' => [
            'path' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
            'alt' => 'AdminLTE Preloader Image',
            'effect' => 'animation__shake',
            'width' => 60,
            'height' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For detailed instructions you can look the user menu section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'usermenu_enabled' => true,
    'usermenu_header' => false,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => false,
    'usermenu_desc' => false,
    'usermenu_profile_url' => 'profile',

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look the layout section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For detailed instructions you can look the auth classes section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions you can look the admin panel classes here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For detailed instructions you can look the sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For detailed instructions you can look the right sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions you can look the urls section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_route_url' => false,
    'dashboard_url' => 'dashboard',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => 'profile',
    'disable_darkmode_routes' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Asset Bundling
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Asset Bundling option for the admin panel.
    | Currently, the next modes are supported: 'mix', 'vite' and 'vite_js_only'.
    | When using 'vite_js_only', it's expected that your CSS is imported using
    | JavaScript. Typically, in your application's 'resources/js/app.js' file.
    | If you are not using any of these, leave it as 'false'.
    |
    | For detailed instructions you can look the asset bundling section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'laravel_asset_bundling' => false,
    'laravel_css_path' => 'css/app.css',
    'laravel_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'menu' => [
        // Navbar items:

        [
            'text' => 'Dashboard',
            'url' => 'dashboard',
            'icon' => 'fas fa-fw fa-tachometer-alt',
        ],

        [
            'text' => 'Master Data',
            'icon' => 'fa fa fa- fa-database text-secondary',
            'can' => 'system_admin_access',
            'submenu' => [
                [
                    'text' => 'Buses',
                    'url' => 'buses',
                    'icon' => 'fas fa-fw fa-bus',
                    'active' => [
                        'buses*',
                    ],
                ],
                [
                    'text' => 'Living Out Bus Routes',
                    'url' => 'bus-routes',
                    'icon' => 'fas fa-fw fa-road',
                    'active' => [
                        'bus-routes*',
                    ],
                ],

                [
                    'text' => 'Drivers',
                    'url' => 'drivers',
                    'icon' => 'fas fa-fw fa-user',
                    'active' => [
                        'drivers*',
                    ],
                ],
                [
                    'text' => 'Escorts',
                    'url' => 'escorts',
                    'icon' => 'fas fa-fw fa-user',
                    'active' => [
                        'escorts*',
                    ],
                ],
                [
                    'text' => 'SLCMP In Charges',
                    'url' => 'slcmp-incharges',
                    'icon' => 'fas fa-fw fa-user-shield',
                    'active' => [
                        'slcmp-incharges*',
                    ],
                ],
                [
                    'text' => 'Filling Stations',
                    'url' => 'filling-stations',
                    'icon' => 'fas fa-fw fa-gas-pump',
                    'active' => [
                        'filling-stations*',
                    ],
                ],
                [
                    'text' => 'Establishments',
                    'url' => 'establishment',
                    'icon' => 'fas fa-building',
                    'active' => [
                        'establishment*',
                    ],
                ],
                [
                    'text' => 'Persons',
                    'url' => 'persons',
                    'icon' => 'fas fa-fw fa-users',
                    'active' => [
                        'persons*',
                    ],
                ],
                [
                    'text' => 'Living In Bus Routes',
                    'url' => 'living-in-buses',
                    'icon' => 'fas fa-fw fa-bus',
                    'active' => [
                        'living-in-buses*',
                    ],
                ],
                // [
                //     'text' => 'Bus Pass Statuses',
                //     'url' => 'bus-pass-statuses',
                //     'icon' => 'fas fa-fw fa-tags',
                //     'active' => [
                //         'bus-pass-statuses*',
                //     ],
                // ],

                [
                    'text' => 'Living In Destination Locations',
                    'url' => 'destination-locations',
                    'icon' => 'fas fa-fw fa-map-marker-alt',
                    'active' => [
                        'destination-locations*',
                    ],
                ],

                [
                    'text' => 'Provinces',
                    'url' => 'province',
                    'icon' => 'fas fa-fw fa-map',
                    'active' => [
                        'province*',
                    ],
                ],

                [
                    'text' => 'District',
                    'url' => 'district',
                    'icon' => 'fas fa-fw fa-map-pin',
                    'active' => [
                        'district*',
                    ],
                ],

                [
                    'text' => 'GS Division',
                    'url' => 'gs-devision',
                    'icon' => 'fas fa-fw fa-landmark',
                    'active' => [
                        'gs-devision*',
                    ],
                ],

                [
                    'text' => 'Police Station',
                    'url' => 'police-station',
                    'icon' => 'fas fa-shield-alt',
                    'active' => [
                        'police-station*',
                    ],
                ],
            ],
        ],
        [
            'text' => 'Assignments',
            'icon' => 'fa fa fa- fa-database text-secondary',
            'can' => 'system_admin_access',
            'submenu' => [
                [
                    'text' => 'Bus Assignments',
                    'url' => 'bus-assignments',
                    'icon' => 'fas fa-fw fa-link',
                    'active' => [
                        'bus-assignments*',
                    ],
                ],
                [
                    'text' => 'Driver Assignments',
                    'url' => 'bus-driver-assignments',
                    'icon' => 'fas fa-fw fa-user-tie',
                    'active' => [
                        'bus-driver-assignments*',
                    ],
                ],
                [
                    'text' => 'Escort Assignments',
                    'url' => 'bus-escort-assignments',
                    'icon' => 'fas fa-fw fa-user-shield',
                    'active' => [
                        'bus-escort-assignments*',
                    ],
                ],
                [
                    'text' => 'SLCMP In-charge Assignments',
                    'url' => 'slcmp-incharge-assignments',
                    'icon' => 'fas fa-fw fa-shield-alt',
                    'active' => [
                        'slcmp-incharge-assignments*',
                    ],
                ],
                [
                    'text' => 'Filling Station Assignments',
                    'url' => 'bus-filling-station-assignments',
                    'icon' => 'fas fa-fw fa-gas-pump',
                    'active' => [
                        'bus-filling-station-assignments*',
                    ],
                ],
            ],
        ],

        [
            'text' => 'Bus Pass Applications',
            'url' => 'bus-pass-applications',
            'icon' => 'fas fa-fw fa-id-card',
            'can' => 'operational_user_access',
            'active' => [
                'bus-pass-applications*',
            ],
        ],

        [
            'text' => 'Bus Pass Approvals',
            'url' => 'bus-pass-approvals',
            'icon' => 'fas fa-fw fa-check-circle',
            'can' => 'access_bus_pass_approvals',
            'active' => [
                'bus-pass-approvals*',
            ],
            'classes' => 'bus-pass-approvals-menu',
        ],

        [
            'text' => 'Bus Pass Integration',
            'url' => 'bus-pass-integration',
            'icon' => 'fas fa-fw fa-chart-bar',
            'can' => 'access_bus_pass_integration',
            'active' => [
                'bus-pass-integration*',
            ],
            'classes' => 'bus-pass-integration-menu',
        ],

        [
            'text' => 'Integrated Applications',
            'url' => 'integrated-applications',
            'icon' => 'fas fa-fw fa-check',
            'can' => 'branch_user_access',
            'active' => [
                'integrated-applications*',
            ],
        ],



        [
            'text' => 'Rejected Applications',
            'url' => 'rejected-applications',
            'icon' => 'fas fa-fw fa-times-circle',
            'active' => [
                'rejected-applications*',
            ],
        ],

        [
            'text' => 'QR Download',
            'url' => 'qr-download',
            'icon' => 'fas fa-fw fa-qrcode',
            'can' => 'access_qr_download',
            'active' => [
                'qr-download*',
            ],
        ],

        // System Administration (System Administrator only)
        [
            'text' => 'System Administration',
            'icon' => 'fas fa-fw fa-cogs',
            'can' => 'system_admin_access',
            'submenu' => [
                [
                    'text' => 'User Management',
                    'url' => 'users',
                    'icon' => 'fas fa-fw fa-users',
                    'can' => 'manage_user_accounts',
                    'active' => [
                        'users*',
                    ],
                ],
                [
                    'text' => 'Role Management',
                    'url' => 'roles',
                    'icon' => 'fas fa-fw fa-user-shield',
                    'can' => 'system_admin_access',
                    'active' => [
                        'roles*',
                    ],
                ],
                // [
                //     'text' => 'Role Hierarchy',
                //     'url' => 'roles-hierarchy',
                //     'icon' => 'fas fa-fw fa-sitemap',
                //     'can' => 'system_admin_access',
                //     'active' => [
                //         'roles-hierarchy*',
                //     ],
                // ],
            ],

        ],
        [
            'text' => 'Reports',
            'icon' => 'fas fa-fw fa-cogs',
            'can' => 'access_reports',
            'submenu' => [
                // [
                //     'text' => 'Rejected Applications',
                //     'url' => 'rejected-applications',
                //     'icon' => 'fas fa-fw fa-times-circle',
                //     'active' => [
                //         'rejected-applications*',
                //     ],
                // ],
                // [
                //     'text' => 'Temporary Card Printed',
                //     'url' => 'temporary-card-printed',
                //     'icon' => 'fas fa-fw fa-id-card',
                //     // 'can' => 'manage_user_accounts',
                //     'active' => [
                //         'temporary-card-printed*',
                //     ],
                // ],
                // [
                //     'text' => 'Handed Over Applications',
                //     'url' => 'handed-over-applications',
                //     'icon' => 'fas fa-fw fa-check',
                //     // 'can' => 'manage_user_accounts',
                //     'active' => [
                //         'handed-over-applications*',
                //     ],
                // ],
                // [
                //     'text' => 'Not Yet Handed Over Applications',
                //     'url' => 'not-yet-handed-over-applications',
                //     'icon' => 'fas fa-fw fa-times',
                //     // 'can' => 'manage_user_accounts',
                //     'active' => [
                //         'not-yet-handed-over-applications*',
                //     ],
                // ],

                [
                    'text' => 'Passenger Counts',
                    'icon' => 'fas fa-fw fa-chart-bar',
                    'submenu' => [
                        // [
                        //     'text' => 'All Routes',
                        //     'url' => 'passenger-counts',
                        //     'icon' => 'fas fa-fw fa-list',
                        //     'active' => [
                        //         'passenger-counts*',
                        //     ],
                        // ],
                        [
                            'text' => 'Living Out',
                            'url' => 'living-out-passenger-counts',
                            'icon' => 'fas fa-fw fa-bus',
                            'active' => [
                                'living-out-passenger-counts*',
                            ],
                        ],
                        [
                            'text' => 'Living In',
                            'url' => 'living-in-passenger-counts',
                            'icon' => 'fas fa-fw fa-home',
                            'active' => [
                                'living-in-passenger-counts*',
                            ],
                        ],
                    ],
                ],
                [
                    'text' => 'Establishment wise Integrated Applications',
                    'url' => 'establishment-wise-applications',
                    'icon' => 'fas fa-fw fa-building',
                    // 'can' => 'manage_user_accounts',
                    'active' => [
                        'establishment-wise-applications*',
                    ],
                ],
                [
                    'text' => 'Route Establishment Report',
                    'url' => 'route-establishment-report',
                    'icon' => 'fas fa-fw fa-route',
                    // 'can' => 'manage_user_accounts',
                    'active' => [
                        'route-establishment-report*',
                    ],
                ],
                [
                    'text' => 'Onboarded Passengers',
                    'url' => 'onboarded-passengers',
                    'icon' => 'fas fa-fw fa-users',
                    // 'can' => 'manage_user_accounts',
                    'active' => [
                        'onboarded-passengers*',
                    ],
                ],

            ],

        ],




    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For detailed instructions you can look the plugins section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Plugins-Configuration
    |
    */

    'plugins' => [
        'Datatables' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'css/select2.min.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/chart.umd.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/sweetalert2.min.js',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'css/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/pace.min.js',
                ],
            ],
        ],
        'PendingApprovalsBadge' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/pending-approvals-badge.js',
                ],
            ],
        ],
        'CustomCSS' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'css/custom-menu.css',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame
    |--------------------------------------------------------------------------
    |
    | Here we change the IFrame mode configuration. Note these changes will
    | only apply to the view that extends and enable the IFrame mode.
    |
    | For detailed instructions you can look the iframe mode section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/IFrame-Mode-Configuration
    |
    */

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'livewire' => false,
];
