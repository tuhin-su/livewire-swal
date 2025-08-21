<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Timer
    |--------------------------------------------------------------------------
    |
    | Default timer for success and info alerts (in milliseconds).
    | Set to null to disable auto-close.
    |
    */
    'default_timer' => 3000,

    /*
    |--------------------------------------------------------------------------
    | Toast Timer
    |--------------------------------------------------------------------------
    |
    | Default timer for toast notifications (in milliseconds).
    |
    */
    'toast_timer' => 3000,

    /*
    |--------------------------------------------------------------------------
    | Default Toast Position
    |--------------------------------------------------------------------------
    |
    | Default position for toast notifications.
    | Options: 'top', 'top-start', 'top-end', 'center', 'center-start', 
    | 'center-end', 'bottom', 'bottom-start', 'bottom-end'
    |
    */
    'toast_position' => 'top-end',

    /*
    |--------------------------------------------------------------------------
    | Auto Include SweetAlert2
    |--------------------------------------------------------------------------
    |
    | Whether to automatically include SweetAlert2 CDN in the blade directive.
    | Set to false if you're bundling SweetAlert2 yourself.
    |
    */
    'auto_include_sweetalert' => true,

    /*
    |--------------------------------------------------------------------------
    | SweetAlert2 CDN URL
    |--------------------------------------------------------------------------
    |
    | CDN URL for SweetAlert2. Only used if auto_include_sweetalert is true.
    |
    */
    'cdn_url' => 'https://cdn.jsdelivr.net/npm/sweetalert2@11',

    /*
    |--------------------------------------------------------------------------
    | Default Button Colors
    |--------------------------------------------------------------------------
    |
    | Default colors for confirm and cancel buttons.
    |
    */
    'button_colors' => [
        'confirm' => '#3085d6',
        'cancel' => '#d33',
        'success' => '#28a745',
        'error' => '#dc3545',
        'warning' => '#ffc107',
        'info' => '#17a2b8',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Alert Options
    |--------------------------------------------------------------------------
    |
    | Default options that will be merged with all alerts.
    |
    */
    'default_options' => [
        'heightAuto' => false,
        'allowOutsideClick' => true,
        'allowEscapeKey' => true,
    ],
];
