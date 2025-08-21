<?php

namespace LivewireSwal;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class LivewireSwalServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind the SwalService to the container
        $this->app->singleton('livewire-swal', function () {
            return new SwalService();
        });

        // Register the facade alias
        $this->app->alias('livewire-swal', SwalService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register the Blade directive
        Blade::directive('swal-script', function () {
            return "<?php echo view('livewire-swal::swal-script')->render(); ?>";
        });

        // Load package views
        $this->loadViewsFrom(__DIR__.'/resources/views', 'livewire-swal');

        // Publish configuration
        $this->publishes([
            __DIR__.'/../config/livewire-swal.php' => config_path('livewire-swal.php'),
        ], 'livewire-swal-config');

        // Publish views
        $this->publishes([
            __DIR__.'/resources/views' => resource_path('views/vendor/livewire-swal'),
        ], 'livewire-swal-views');

        // Merge default configuration
        $this->mergeConfigFrom(__DIR__.'/../config/livewire-swal.php', 'livewire-swal');
    }
}
