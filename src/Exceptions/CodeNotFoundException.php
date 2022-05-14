<?php

namespace Bfg\Comcode\Exceptions;

use Throwable;

class CodeNotFoundException extends \Exception
{
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
