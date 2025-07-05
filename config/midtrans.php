<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Midtrans Server & Client Keys
    |--------------------------------------------------------------------------
    |
    | These values are pulled from your .env file. Do NOT commit your actual
    | keys to Git.
    |
    */

    'server_key'    => env('MIDTRANS_SERVER_KEY'),
    'client_key'    => env('MIDTRANS_CLIENT_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Environment
    |--------------------------------------------------------------------------
    |
    | true = production mode, false = sandbox mode
    |
    */

    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

    /*
    |--------------------------------------------------------------------------
    | Additional Settings
    |--------------------------------------------------------------------------
    |
    | Whether to sanitize inputs & enable 3DSecure
    |
    */

    'is_sanitized'  => env('MIDTRANS_IS_SANITIZED', true),
    'is_3ds'        => env('MIDTRANS_IS_3DS', true),

];
