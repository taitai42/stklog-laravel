<?php

namespace taitai42\Stklog;

use Illuminate\Support\ServiceProvider;
use taitai42\Stklog\Contracts\Driver;

class StklogServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/stklog.php' => config_path('stklog.php'),
        ]);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/stklog.php', 'stklog'
        );

        $driver = config('stklog.transport', 'http');

        $class = $this->getDriverClass($driver);

        $this->app->singleton(Driver::class, function ($app) use ($class) {
            return new $class(config("stklog"), LOG_DEBUG);
        });

        $logger = new Stklog(resolve(Driver::class));

        $this->app->instance('log', $logger);

        if (isset($this->app['log.setup'])) {
            call_user_func($this->app['log.setup'], $logger);
        }

    }

    private function getDriverClass($driver)
    {
        $drivername = ucfirst($driver);

        $classname = __NAMESPACE__ . '\\Driver\\' . $drivername . 'Driver';
        if (!class_exists($classname)) {
            $classname = __NAMESPACE__ . '\\Driver\\HttpDriver';
        }

        return $classname;
    }
}