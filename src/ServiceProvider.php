<?php

namespace Bfg\Comcode;

use Bfg\Installer\Providers\InstalledProvider;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

/**
 * Class ServiceProvider
 * @package Bfg\Comcode
 */
class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Register route settings.
     * @return void
     */
    public function register()
    {
        ini_set('xdebug.max_nesting_level', 3000);
    }

    /**
     * Bootstrap services.
     * @return void
     */
    public function boot()
    {
        //
    }
}

