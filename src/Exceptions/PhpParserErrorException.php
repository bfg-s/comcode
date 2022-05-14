<?php

namespace Bfg\Comcode\Exceptions;

use Throwable;

class PhpParserErrorException extends \Exception
{
    public function __construct(
        string $message,
        int $code = 2,
        ?Throwable $previous = null
    ) {
        parent::__construct(
            "PHP parser error exception: " . $message,
            $code,
            $previous
        );
    }
}
