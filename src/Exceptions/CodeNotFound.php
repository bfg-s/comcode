<?php

namespace Bfg\Comcode\Exceptions;

use Exception;
use Throwable;

class CodeNotFound extends Exception
{
    /**
     * @param  int  $code
     * @param  Throwable|null  $previous
     */
    public function __construct(
        int $code = 3,
        ?Throwable $previous = null
    ) {
        parent::__construct(
            "Code not found!",
            $code,
            $previous
        );
    }
}
