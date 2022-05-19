<?php

use Bfg\Comcode\PhpService;

if (! function_exists('php')) {

    function php (): PhpService
    {
        return app(PhpService::class);
    }
}
