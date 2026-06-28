<?php

namespace LaravelSwal;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use LaravelSwal\Http\Middleware\InjectSwal;

class LaravelSwalServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/laravel-swal.php', 'laravel-swal'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(Router $router): void
    {
        // Publish configuration
        $this->publishes([
            __DIR__.'/../config/laravel-swal.php' => config_path('laravel-swal.php'),
        ], 'laravel-swal-config');

        // Publish JS assets
        $this->publishes([
            __DIR__.'/../resources/js/swal.js' => resource_path('js/vendor/laravel-swal/swal.js'),
        ], 'laravel-swal-assets');

        // Automatically inject SweetAlert2 and script wrapper on web routes if enabled
        if (config('laravel-swal.auto_inject', true)) {
            $router->pushMiddlewareToGroup('web', InjectSwal::class);
        }
    }
}
