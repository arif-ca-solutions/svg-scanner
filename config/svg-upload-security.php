<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable SVG upload middleware
    |--------------------------------------------------------------------------
    */
    'enabled' => env('SVG_UPLOAD_SECURITY_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Mode: sanitize | reject
    |--------------------------------------------------------------------------
    |
    | sanitize — replace upload with library-cleaned SVG (default).
    | reject   — fail if cleaned output differs from input (audit doc approach).
    |
    */
    'mode' => env('SVG_UPLOAD_SECURITY_MODE', 'sanitize'),

    /*
    |--------------------------------------------------------------------------
    | MIME types and extensions treated as SVG candidates
    |--------------------------------------------------------------------------
    */
    'mime_types' => [
        'image/svg+xml',
        'image/svg',
    ],

    'extensions' => [
        'svg',
        'svgz',
    ],

    /*
    |--------------------------------------------------------------------------
    | Content sniffing (disguised SVG uploads)
    |--------------------------------------------------------------------------
    */
    'sniff_content' => env('SVG_UPLOAD_SECURITY_SNIFF_CONTENT', true),

    /*
    |--------------------------------------------------------------------------
    | Security logging (optional database table — publish migration)
    |--------------------------------------------------------------------------
    */
    'logging' => [
        'enabled' => env('SVG_UPLOAD_SECURITY_LOG', false),
        'channel' => env('SVG_UPLOAD_SECURITY_LOG_CHANNEL', 'stack'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Register middleware on the global HTTP stack automatically
    |--------------------------------------------------------------------------
    |
    | When false, add alias "sanitize.svg" to routes or middleware groups yourself.
    |
    */
    'register_global_middleware' => env('SVG_UPLOAD_SECURITY_GLOBAL_MIDDLEWARE', true),

    'validation_message' => 'The uploaded SVG file is not allowed.',

];
