<?php

namespace Bfg\Comcode\Interfaces;

use PhpParser\Node\Stmt;

interface BirthNodeInterface
{
    /**
     * STMT birth method
     * @return Stmt
     */
    public function birth(): Stmt;
}
