<?php

namespace TwoBase\SvgUploadSecurity\Exceptions;

use Exception;
use Throwable;

class UnsafeSvgUploadException extends Exception
{
    public function __construct(
        string $message,
        public readonly string $translationKey = 'svg-upload-security::validation.failed',
        public readonly array $replace = [],
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }
}
