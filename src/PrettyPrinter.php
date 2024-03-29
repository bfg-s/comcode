<?php

namespace Bfg\Comcode;

use PhpParser\Node\Expr;
use PhpParser\Node\Stmt\Class_;
use PhpParser\PrettyPrinter\Standard;

class PrettyPrinter extends Standard
{
    /**
     * @param  AnonymousStmt  $node
     * @return string
     */
    public function panonymous(AnonymousStmt $node): string
    {
        $result = "";

        foreach ($node->nodes as $nodeChild) {
            $result .= $this->{'p'.$nodeChild->getType()}($nodeChild);
        }

        return $result;
    }

    /**
     * @param  AnonymousExpr  $node
     * @return string
     */
    public function pexanonymous(AnonymousExpr $node): string
    {
        $result = "";

        if ($node->expr instanceof Expr) {
            $result .= $this->{'p'.$node->expr->getType()}($node->expr);
        } else {
            if (!$node->expr instanceof AnonymousStmt) {
                $result .= $node->expr;
            }
        }

        return $result;
    }

    /**
     * @param  AnonymousLine  $node
     * @return string
     */
    public function plineanonymous(AnonymousLine $node): string
    {
        $result = "";

        if ($node->expr instanceof Expr) {
            $result .= $this->{'p'.$node->expr->getType()}($node->expr);
        } else {
            if (!$node->expr instanceof AnonymousStmt) {
                $result .= $node->expr;
            }
        }

        return $result ? $result.";" : $result;
    }
}
