<?php

namespace Bfg\Comcode\Traits;

use Bfg\Comcode\Comcode;
use Bfg\Comcode\Node;
use Bfg\Comcode\PhpInlineTrap;
use PhpParser\Node\Expr;

trait CommonWhileExpressions
{
    /**
     * @return PhpInlineTrap
     */
    public function this(): PhpInlineTrap
    {
        return $this->var('this');
    }

    /**
     * @param  string|Expr  $name
     * @return PhpInlineTrap
     */
    public function var(
        string|Expr $name
    ): PhpInlineTrap {
        $this->node->expr
            = new PhpInlineTrap($name);
        return $this->node->expr
            ->__bindExpression($this->node);
    }

    /**
     * @param  string  $function
     * @param ...$arguments
     * @return PhpInlineTrap
     */
    public function func(
        string $function,
        ...$arguments
    ): PhpInlineTrap {
        return $this->var(
            Node::callFunction($function, ...$arguments)
        );
    }

    public function real(
        mixed $value = null
    ): PhpInlineTrap {
        return $this->var(
            Comcode::defineValueNode($value)
        );
    }
}
