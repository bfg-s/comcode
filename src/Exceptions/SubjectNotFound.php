<?php

namespace Bfg\Comcode\Exceptions;

use Exception;
use Throwable;

class SubjectNotFound extends Exception
{
    /**
     * @param  string  $subject
     * @param  int  $code
     * @param  Throwable|null  $previous
     */
    public function __construct(
        string $subject,
        int $code = 1,
        ?Throwable $previous = null
    ) {
        parent::__construct(
            "Subject [{$subject}] not found!",
            $code,
            $previous
        );
    }
}
