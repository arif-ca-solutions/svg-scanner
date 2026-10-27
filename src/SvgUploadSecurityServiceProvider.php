<?php

namespace TwoBase\SvgUploadSecurity;

use enshrined\svgSanitize\Sanitizer;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use RuntimeException;
use TwoBase\SvgUploadSecurity\Http\Middleware\SanitizeSvgUploads;
use TwoBase\SvgUploadSecurity\Services\SvgUploadProcessor;

class SvgUploadSecurityServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! class_exists(Sanitizer::class)) {
            throw new RuntimeException(
                'Missing dependency [enshrined/svg-sanitize]. '
                .'It is required by [2base/svg-upload-security] and should install automatically. '
                .'Run: composer require 2base/svg-upload-security (or composer install).'
            );
        }

        $this->mergeConfigFrom(
            __DIR__.'/../config/svg-upload-security.php',
            'svg-upload-security'
        );

        $this->app->singleton(SvgUploadProcessor::class, fn () => SvgUploadProcessor::fromConfig());
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/svg-upload-security.php' => config_path('svg-upload-security.php'),
            ], 'svg-upload-security-config');
        }

        /** @var Router $router */
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('sanitize.svg', SanitizeSvgUploads::class);

        if (config('svg-upload-security.register_global_middleware', true)) {
            /** @var Kernel $kernel */
            $kernel = $this->app->make(Kernel::class);
            $kernel->prependMiddleware(SanitizeSvgUploads::class);
        }
    }
}
