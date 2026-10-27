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
    | Mode: block | sanitize
    |--------------------------------------------------------------------------
    |
    | block    — reject upload with a validation error when security scanning
    |            would change the file or cannot produce safe content (default).
    | sanitize — replace upload with library-cleaned SVG.
    |
    | Legacy: "reject" is treated as "block".
    |
    */
    'mode' => env('SVG_UPLOAD_SECURITY_MODE', 'block'),

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

    /*
    |--------------------------------------------------------------------------
    | Validation message (localization)
    |--------------------------------------------------------------------------
    |
    | Leave null to use reason-specific keys from lang/{locale}/svg-upload-security.php
    | (namespace svg-upload-security::validation.*). Set a string to override all
    | failures with one message or translation key, e.g. "custom.unsafe_svg".
    |
    */
    'validation_message' => env('SVG_UPLOAD_SECURITY_VALIDATION_MESSAGE'),

];
