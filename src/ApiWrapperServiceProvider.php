<?php

namespace NineCloud\CasterSphereWrapper;

use Illuminate\Support\ServiceProvider;

class ApiWrapperServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/cswrapper.php', 'cswrapper');
    }

    public function boot()
    {
        if (function_exists('config_path')) {
            $this->publishes([
                // get the config file
            ], 'config');
        }
    }
}
