<?php

namespace TwoBase\SvgUploadSecurity\Support;

use TwoBase\SvgUploadSecurity\Exceptions\UnsafeSvgUploadException;

final class ValidationMessageResolver
{
    public static function resolve(UnsafeSvgUploadException $exception): string
    {
        $override = config('svg-upload-security.validation_message');

        if (is_string($override) && $override !== '') {
            return __($override, $exception->replace);
        }

        return __($exception->translationKey, $exception->replace);
    }
}
