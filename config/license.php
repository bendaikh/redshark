<?php

return [

    /*
    |--------------------------------------------------------------------------
    | License Server URL
    |--------------------------------------------------------------------------
    |
    | This is the URL of your licensing server that validates licenses.
    | You should host this on your own server.
    |
    */
    'server_url' => env('LICENSE_SERVER_URL', 'https://your-license-server.com'),

    /*
    |--------------------------------------------------------------------------
    | License Key
    |--------------------------------------------------------------------------
    |
    | The license key provided to the client. This is set during installation
    | or can be configured in the .env file.
    |
    */
    'key' => env('LICENSE_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Licensed Domain
    |--------------------------------------------------------------------------
    |
    | The domain this license is valid for. If empty, it will be set
    | during the first activation.
    |
    */
    'domain' => env('LICENSE_DOMAIN', ''),

    /*
    |--------------------------------------------------------------------------
    | Grace Period (in days)
    |--------------------------------------------------------------------------
    |
    | If the license server is unreachable, the application will continue
    | to work for this many days before requiring online validation.
    |
    */
    'grace_period' => env('LICENSE_GRACE_PERIOD', 7),

    /*
    |--------------------------------------------------------------------------
    | Validation Interval (in hours)
    |--------------------------------------------------------------------------
    |
    | How often the license should be validated online.
    | Set to 0 to validate on every request (not recommended).
    |
    */
    'validation_interval' => env('LICENSE_VALIDATION_INTERVAL', 24),

    /*
    |--------------------------------------------------------------------------
    | Secret Key for License Generation
    |--------------------------------------------------------------------------
    |
    | This secret is used to sign and verify license keys.
    | KEEP THIS SECRET! Only you should know this.
    |
    */
    'secret' => env('LICENSE_SECRET', 'your-super-secret-key-change-this'),

    /*
    |--------------------------------------------------------------------------
    | Product ID
    |--------------------------------------------------------------------------
    |
    | A unique identifier for your product. Used in license validation.
    |
    */
    'product_id' => env('LICENSE_PRODUCT_ID', 'redshark'),

    /*
    |--------------------------------------------------------------------------
    | Routes to Exclude from License Check
    |--------------------------------------------------------------------------
    |
    | These routes will not require license validation.
    |
    */
    'excluded_routes' => [
        'license.activate',
        'license.store',
        'license.check',
    ],

    /*
    |--------------------------------------------------------------------------
    | License File Path
    |--------------------------------------------------------------------------
    |
    | Where to store the license cache file.
    |
    */
    'cache_file' => storage_path('app/license.json'),

];

