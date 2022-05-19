<?php

namespace Bfg\Comcode;

use PhpParser\Node\Stmt;
use PhpParser\PrettyPrinter\Standard;

class PrettyPrinter extends Standard
{
    /**
     * @param  Stmt\Class_  $node
     * @return string
     */
    protected function pStmt_Class(Stmt\Class_ $node): string
    {
        return "\n"
            . $this->pClassCommon($node, ' ' . $node->name);
    }
}
