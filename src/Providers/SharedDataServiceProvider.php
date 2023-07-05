<?php

namespace Microoculus\SharedData\Providers;

use Microoculus\SharedData\SharedData;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use File;

class SharedDataServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Bootstrap shared data service.
     *
     * @return void
     */
    public function boot()
    {
      
        $this->publishes([
            __DIR__.'/../../config/shared-data.php' => config_path('shared-data.php'),
        ], 'shared-data-config');

      
        if (File::exists(__DIR__ . '/../helpers.php')) {
            require __DIR__ . '/../helpers.php';
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/shared-data.php',
            'shared-data'
        );

        $this->app->singleton(SharedData::class, function () {
            return new SharedData($this->app['config']['shared-data']);
        });

        $this->app->extend('blade.compiler', function (BladeCompiler $bladeCompiler) {
            $bladeCompiler->directive($this->app['config']['shared-data.blade_directive.name'] ?? 'shared', function () {
                return '<?php echo app(\\'.SharedData::class.'::class)->render(); ?>';
            });

            return $bladeCompiler;
        });

       
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            SharedData::class,
            'blade.compiler'
        ];
    }
}
