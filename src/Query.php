<?php

namespace Bfg\Comcode;

use PhpParser\Node\Stmt;

class Query extends QuerySearchEngine
{
    /**
     * @param  Stmt  $stmt
     * @return static
     */
    public static function new(Stmt $stmt): static
    {
        return new static($stmt);
    }
}
