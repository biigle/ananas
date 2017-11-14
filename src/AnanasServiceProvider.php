<?php

namespace Biigle\Modules\Ananas;

use Biigle\Services\Modules;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class AnanasServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @param  \Biigle\Services\Modules  $modules
     * @param  \Illuminate\Routing\Router  $router
     *
     * @return void
     */
    public function boot(Modules $modules, Router $router)
    {
        // $this->loadViewsFrom(__DIR__.'/resources/views', 'ananas');

        // $this->publishes([
        //     __DIR__.'/public/assets' => public_path('vendor/ananas'),
        // ], 'public');

        $router->group([
            'namespace' => 'Biigle\Modules\Ananas\Http\Controllers',
            'middleware' => 'web',
        ], function ($router) {
            require __DIR__.'/Http/routes.php';
        });

        // $modules->register('ananas', [
        //     'viewMixins' => [
        //         'manualTutorial',
        //     ],
        // ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('command.ananas.publish', function ($app) {
            return new \Biigle\Modules\Ananas\Console\Commands\Publish();
        });
        $this->commands('command.ananas.publish');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'command.ananas.publish',
        ];
    }
}
