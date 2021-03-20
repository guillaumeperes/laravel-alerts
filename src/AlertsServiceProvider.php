<?php

namespace GuillaumePeres\Alerts;

use Illuminate\Support\ServiceProvider;

class AlertsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes(array(__DIR__.'/config/config.php' => config_path('guillaumeperes/alerts.php')));
    }

    public function register()
    {
        $this->mergeConfig();
        $this->registerAlertsClass();
    }

    public function mergeConfig()
    {
        $this->mergeConfigFrom(__DIR__.'/config/config.php', 'guillaumeperes.alerts');
    }

    private function registerAlertsClass()
    {
        $this->app->singleton('alerts', function($app) {
            return new Alerts($app['session.store'], $app['config']);
        });
    }

    public function provides()
    {
        return array(
            'alerts'
        );
    }
}
