<?php

namespace Biigle\Modules\Ananas;

use Biigle\Services\Modules;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory as EloquentFactory;
use Biigle\Modules\Ananas\Observers\AnnotationAssistanceRequestObserver;

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
        $this->loadViewsFrom(__DIR__.'/resources/views', 'ananas');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        $this->publishes([
            __DIR__.'/public/assets' => public_path('vendor/ananas'),
        ], 'public');

        $router->group([
            'namespace' => 'Biigle\Modules\Ananas\Http\Controllers',
            'middleware' => 'web',
        ], function ($router) {
            require __DIR__.'/Http/routes.php';
        });

        $modules->register('ananas', [
            'viewMixins' => [
                'manualTutorial',
                'annotationsScripts',
                'annotationsStyles',
                'annotationsAnnotationsTab',
                'notificationTabs',
            ],
        ]);

        AnnotationAssistanceRequest::observe(new AnnotationAssistanceRequestObserver);

        Gate::policy(\Biigle\Modules\Ananas\AnnotationAssistanceRequest::class, \Biigle\Modules\Ananas\Policies\AnnotationAssistanceRequestPolicy::class);
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

        if (config('app.env') === 'testing') {
            $this->registerEloquentFactoriesFrom(__DIR__.'/database/factories');
        }
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

    /**
     * Register factories.
     *
     * @param  string  $path
     * @return void
     */
    protected function registerEloquentFactoriesFrom($path)
    {
        $this->app->make(EloquentFactory::class)->load($path);
    }
}
