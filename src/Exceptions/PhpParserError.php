<?php

namespace Bfg\Comcode\Exceptions;

use Exception;
use Throwable;

class PhpParserError extends Exception
{
    /**
     * @param  string  $message
     * @param  int  $code
     * @param  Throwable|null  $previous
     */
    public function __construct(
        string $message,
        int $code = 2,
        ?Throwable $previous = null
    ) {
        parent::__construct(
            "PHP parser error exception: ".$message,
            $code,
            $previous
        );
    }
}
