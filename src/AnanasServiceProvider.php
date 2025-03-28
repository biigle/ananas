<?php

namespace Biigle\Modules\Ananas;

use Biigle\Http\Requests\UpdateUserSettings;
use Biigle\Modules\Ananas\Observers\AnnotationAssistanceRequestObserver;
use Biigle\Services\Modules;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Gate;
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
        $this->loadViewsFrom(__DIR__.'/resources/views', 'ananas');
        $this->loadMigrationsFrom(__DIR__.'/Database/migrations');

        $this->publishes([
            __DIR__.'/public' => public_path('vendor/ananas'),
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
            'apidoc' => [__DIR__.'/Http/Controllers/Api/'],
        ]);

        if (config('ananas.notifications.allow_user_settings')) {
            $modules->registerViewMixin('ananas', 'settings.notifications');
            UpdateUserSettings::addRule('ananas_notifications', 'filled|in:email,web');
        }

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
        $this->mergeConfigFrom(__DIR__.'/config/ananas.php', 'ananas');

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
