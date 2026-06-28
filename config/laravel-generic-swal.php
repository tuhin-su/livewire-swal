<?php

return [
    /*
     * Enable or disable automatic injection of the JS assets and handlers.
     * When true, the middleware automatically injects scripts on all web routes.
     */
    'auto_inject' => true,

    /*
     * The source URL for the SweetAlert2 library.
     * By default, it loads SweetAlert2 from the jsDelivr CDN.
     * If you want to host it yourself, set this to your local asset path (e.g. '/js/sweetalert2.all.min.js').
     * If you already load SweetAlert2 separately in your layouts or build, set this to false or null.
     */
    'swal_cdn' => 'https://cdn.jsdelivr.net/npm/sweetalert2@11',
];
