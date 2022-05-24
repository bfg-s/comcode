<?php

namespace Bfg\Comcode;

use PhpParser\Node\Stmt\Class_;
use PhpParser\PrettyPrinter\Standard;

class PrettyPrinter extends Standard
{
    /**
     * @param  Class_  $node
     * @return string
     */
    protected function pStmt_Class(Class_ $node): string
    {
        return "\n"
            . $this->pClassCommon($node, ' ' . $node->name);
    }

    /**
     * @param  AnonymousStmt  $node
     * @return string
     */
    public function panonymous(AnonymousStmt $node): string
    {
        $result = "";

        foreach ($node->nodes as $nodeChild) {

            $result .= $this->{'p' . $nodeChild->getType()}($nodeChild);
        }

        return $result;
    }
}
