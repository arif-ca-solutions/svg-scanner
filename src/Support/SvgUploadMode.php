<?php

namespace TwoBase\SvgUploadSecurity\Support;

final class SvgUploadMode
{
    public const SANITIZE = 'sanitize';

    public const BLOCK = 'block';

    /**
     * @return self::SANITIZE|self::BLOCK
     */
    public static function normalize(string $mode): string
    {
        return match (strtolower($mode)) {
            self::SANITIZE => self::SANITIZE,
            self::BLOCK, 'reject' => self::BLOCK,
            default => self::BLOCK,
        };
    }
}
