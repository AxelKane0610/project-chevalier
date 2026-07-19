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

    'api_service' => [
        'power_automate_api_key' => env('POWER_AUTOMATE_API_KEY'),
        'sw_ticket_url' => env('API_POWER_AUTOMATE_EEG_TICKET'),
        'sw_ticket_complete_url' => env('API_POWER_AUTOMATE_EEG_COMPLETE'),
        'laser_engraving_complete_url' => env('API_POWER_AUTOMATE_LASER_ENGRAVING_COMPLETE'),
        'loan_unit_complete_url' => env('API_POWER_AUTOMATE_LOAN_UNIT_COMPLETE'),
        'out_of_office_request_url' => env('API_POWER_AUTOMATE_OUT_OF_OFFICE_REQUEST'),
        'out_of_office_approve_reject_from_web' => env('API_POWER_AUTOMATE_OUT_OF_OFFICE_APPROVE_REJECT_FROM_WEB'),
        'thermal_event_request_url' => env('API_POWER_AUTOMATE_THERMAL_EVENT_REQUEST'),
        'invoice_exceptional_create_ticket_request_url' => env('API_POWER_AUTOMATE_CREATE_INVOICE_EXCEPTIONAL_REQUEST'),
        'invoice_exceptional_request_sale_support_url' => env('API_POWER_AUTOMATE_INVOICE_EXCEPTIONAL_REQUEST_SALE_SUPPORT'),
    ],

];
