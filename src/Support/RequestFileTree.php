<?php

namespace TwoBase\SvgUploadSecurity\Support;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

/**
 * Walk and replace nested multipart file bags on the request.
 */
final class RequestFileTree
{
    /**
     * @param  callable(UploadedFile, string): void  $visitor  Receives file and dot-path key
     */
    public static function walk(Request $request, callable $visitor): void
    {
        $files = $request->files->all();
        self::walkArray($files, '', $visitor);
    }

    /**
     * @param  array<string, mixed>  $files
     */
    protected static function walkArray(array $files, string $prefix, callable $visitor): void
    {
        foreach ($files as $key => $value) {
            $path = $prefix === '' ? (string) $key : $prefix.'.'.$key;

            if ($value instanceof UploadedFile) {
                $visitor($value, $path);

                continue;
            }

            if (is_array($value)) {
                self::walkArray($value, $path, $visitor);
            }
        }
    }

    public static function setAtPath(Request $request, string $dotPath, UploadedFile $file): void
    {
        $root = $request->files->all();
        self::setNested($root, explode('.', $dotPath), $file);
        $request->files->replace($root);
    }

    /**
     * @param  array<string, mixed>  $array
     * @param  list<string>  $segments
     */
    protected static function setNested(array &$array, array $segments, UploadedFile $file): void
    {
        $key = array_shift($segments);

        if ($key === null) {
            return;
        }

        if ($segments === []) {
            $array[$key] = $file;

            return;
        }

        if (! isset($array[$key]) || ! is_array($array[$key])) {
            $array[$key] = [];
        }

        self::setNested($array[$key], $segments, $file);
    }
}
