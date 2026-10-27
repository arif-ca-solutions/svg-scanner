<?php

namespace TwoBase\SvgUploadSecurity\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use TwoBase\SvgUploadSecurity\Exceptions\UnsafeSvgUploadException;
use TwoBase\SvgUploadSecurity\Services\SvgUploadProcessor;
use TwoBase\SvgUploadSecurity\Support\RequestFileTree;
use TwoBase\SvgUploadSecurity\Support\ValidationMessageResolver;

class SanitizeSvgUploads
{
    public function __construct(
        protected SvgUploadProcessor $processor,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (! config('svg-upload-security.enabled', true)) {
            return $next($request);
        }

        if ($request->files->count() === 0) {
            return $next($request);
        }

        try {
            RequestFileTree::walk($request, function ($file, $path) use ($request): void {
                try {
                    $replacement = $this->processor->processUploadedFile($file);
                    if ($replacement !== null) {
                        RequestFileTree::setAtPath($request, $path, $replacement);
                    }
                } catch (UnsafeSvgUploadException $e) {
                    $this->logRejection($request, $e->getMessage());

                    throw ValidationException::withMessages([
                        $path => [
                            ValidationMessageResolver::resolve($e),
                        ],
                    ]);
                }
            });
        } catch (ValidationException $e) {
            throw $e;
        }

        return $next($request);
    }

    protected function logRejection(Request $request, string $reason): void
    {
        if (! config('svg-upload-security.logging.enabled', false)) {
            return;
        }

        $user = $request->user();

        Log::channel(config('svg-upload-security.logging.channel', 'stack'))->warning('svg_upload_rejected', [
            'ip' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 512),
            'user_id' => $user?->getAuthIdentifier(),
            'path' => $request->path(),
            'method' => $request->method(),
            'reason' => $reason,
        ]);
    }
}
