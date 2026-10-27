<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'eps' => [
        'base_url' => env('EPS_BASE_URL', env('EPSBaseURL', 'https://pgapi.eps.com.bd/v1')),
        'merchant_id' => env('EPS_MERCHANT_ID', env('EPSMerchentID', env('EPSMerchantID'))),
        'store_id' => env('EPS_STORE_ID', env('EPSStoreID')),
        'username' => env('EPS_USERNAME', env('EPSUserName')),
        'password' => env('EPS_PASSWORD', env('EPSPassword')),
        'hash_key' => env('EPS_HASH_KEY', env('EPSHashkey', env('EPSHashKey'))),
        'device_type_id' => env('EPS_DEVICE_TYPE_ID', env('EPSDeviceTypeID', 1)),
    ],

];
