<?php

use Bfg\Comcode\InlineTrap;
use Bfg\Comcode\PhpService;
use PhpParser\Node\Expr;

if (!function_exists('php')) {
    /**
     * @param  string|Expr|null  $expression
     * @return PhpService|InlineTrap
     */
    function php(string|Expr $expression = null): PhpService|InlineTrap
    {
        $service = new PhpService;
        if ($expression) {
            return $service->var($expression);
        }
        return $service;
    }
}

if (!function_exists('base_path')) {
    /**
     * @param  string  $path
     * @return string
     */
    function base_path(string $path = ''): string
    {
        return getcwd().($path != '' ? DIRECTORY_SEPARATOR.$path : '');
    }
}
