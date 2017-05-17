<?php

namespace taitai42\Stklog;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Monolog\Logger;
use taitai42\Stklog\Contracts\Driver;
use taitai42\Stklog\Middleware\StklogMiddleware;
use taitai42\Stklog\Model\StackRepository;

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
        if ($this->app->environment() != 'testing') {

            $this->mergeConfigFrom(
                __DIR__ . '/config/stklog.php', 'stklog'
            );

            $driver = config('stklog.transport', 'http');
            $class = $this->getDriverClass($driver);

            $monolog = Log::getMonolog()->pushHandler(new $class(config('stklog.project_key'),
                config('stklog.level', Logger::INFO)));

            /**
             * now we replace the laravel log instance with our that contains the new handler
             */
            $this->app->instance('log', $monolog);

            if (isset($this->app['log.setup'])) {
                call_user_func($this->app['log.setup'], $monolog);
            }
        }
    }

    /**
     * build the driver classname with the selected transport
     * @param $driver
     *
     * @return string
     */
    private function getDriverClass($driver)
    {
        $drivername = ucfirst($driver);

        $classname = __NAMESPACE__ . '\\Handler\\Stklog' . $drivername . 'Handler';
        if (!class_exists($classname)) {
            $classname = __NAMESPACE__ . '\\Handler\\StklogHttpHandler';
        }

        return $classname;
    }
}