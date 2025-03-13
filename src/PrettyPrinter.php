<?php

namespace Bfg\Comcode;

use PhpParser\Node\Expr;
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
            $comments = $nodeChild->getComments();
            if ($comments) {
                $result .= $this->nl . $this->pComments($comments);
            }
            $result .= $this->{'p'.$nodeChild->getType()}($nodeChild, 0, 161);
        }

        return $result ? rtrim($result, ';').";" : $result;
    }

    /**
     * @param  AnonymousExpr  $node
     * @return string
     */
    public function pexanonymous(AnonymousExpr $node): string
    {
        $result = "";

        if ($node instanceof InlineTrap) {
            foreach ($node->nodes as $nodeChild) {
                $comments = $nodeChild->getComments();
                if ($comments) {
                    $result .= $this->nl . $this->pComments($comments);
                }
                $result .= $this->{'p'.$nodeChild->getType()}($nodeChild, 0, 161);
            }
            return $result ? rtrim($result, ';').";" : $result;
        } else {
            if ($node->expr instanceof Expr) {
                $result .= $this->{'p'.$node->expr->getType()}($node->expr, 0, 161);
            } else {
                if (!$node->expr instanceof AnonymousStmt) {
                    $result .= $node->expr;
                }
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
            $result .= $this->{'p'.$node->expr->getType()}($node->expr, 0, 161);
        } else {
            if (!$node->expr instanceof AnonymousStmt) {
                $result .= $node->expr;
            }
        }
        return $result ? rtrim($result, ';').";" : $result;
    }
}
