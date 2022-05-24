<?php

use Bfg\Comcode\PhpInlineTrap;
use Bfg\Comcode\PhpService;
use PhpParser\Node\Expr;

if (! function_exists('php')) {
    /**
     * @param  string|Expr|null  $expression
     * @return PhpService|PhpInlineTrap
     */
    function php (string|Expr $expression = null): PhpService|PhpInlineTrap
    {
        $service = new PhpService;
        if ($expression) {
            return $service->var($expression);
        }
        return $service;
    }
}

if (! function_exists('base_path')) {

    function base_path ($path = ''): string
    {
        return getcwd().($path != '' ? DIRECTORY_SEPARATOR.$path : '');
    }
}

if (! function_exists('var_export54')) {
    /**
     * @param $var
     * @param  bool|int  $inline
     * @param $indent
     * @return string|null
     */
    function var_export54($var, bool|int $inline = 4, $indent = ""): ?string
    {
        switch (gettype($var)) {
            case "string":
                if (\Bfg\Comcode\Comcode::isCanBeClass($var)) {
                    return $var.'::class';
                }
                return '"'.addcslashes($var, "\\\$\"\r\n\t\v\f").'"';
            case "array":
                $inlineOriginal = $inline;
                $count = count($var);
                $inline = !is_bool($inline) ? $count < $inline : $inline;
                $eol = !$inline ? PHP_EOL : "";
                $indexed = array_keys($var) === range(0, count($var) - 1);
                $r = [];
                foreach ($var as $key => $value) {
                    $r[] = ($inline ? '' : "$indent    ")
                        .($indexed ? "" : var_export54($key, $inlineOriginal)." => ")
                        .var_export54($value, $inlineOriginal, "$indent    ");
                }
                $e = $inline ? ' ' : '';
                return "[$eol".implode(",$eol{$e}", $r).$eol.($inline ? '' : $indent)."]";
            case "boolean":
                return $var ? "true" : "false";
            case "NULL":
                return "null";
            default:
                return var_export($var, true);
        }
    }
}
