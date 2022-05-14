<?php

namespace Bfg\Comcode\Exceptions;

use Throwable;

class SubjectNotFoundException extends \Exception
{
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
