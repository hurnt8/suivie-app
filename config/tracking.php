<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Tracking Prefix
    |--------------------------------------------------------------------------
    |
    | Used only as a fallback the first time the application boots, before
    | any row exists in the `settings` table. Once seeded, the prefix used
    | by TrackingCodeService is the one configured in /admin/settings.
    |
    */

    'default_prefix' => env('TRACKING_DEFAULT_PREFIX', 'LVR'),

];
