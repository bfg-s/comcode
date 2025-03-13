<?php

namespace Bfg\Comcode\Traits;

use Bfg\Comcode\AnonymousExpr;
use Bfg\Comcode\AnonymousLine;
use Bfg\Comcode\Comcode;
use Bfg\Comcode\InlineTrap;
use Bfg\Comcode\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt\Expression;

trait CommonWhileExpressions
{
    /**
     * @return InlineTrap
     */
    public function this(): InlineTrap
    {
        return $this->var('this');
    }

    /**
     * @param  string|Expr  $name
     * @return InlineTrap
     */
    public function var(
        string|Expr $name
    ): InlineTrap {
        $trap = new InlineTrap($name);
        $isExpr = $this->node instanceof Expression || property_exists($this->node, 'expr');
        if ($isExpr) {
            $this->node->expr = $trap->node;
        } else {
            $this->node->nodes[] = $trap;
        }
        $trap->__bindExpression($this, $this->node, $isExpr ? 'expr' : 'nodes');
        return $trap;
    }

    /**
     * @param  string  $function
     * @param ...$arguments
     * @return InlineTrap
     */
    public function func(
        string $function,
        ...$arguments
    ): InlineTrap {
        return $this->var(
            Node::callFunction($function, ...$arguments)
        );
    }

    /**
     * @param  string  $class
     * @param  string  $name
     * @param  mixed  ...$arguments
     * @return InlineTrap
     */
    public function staticCall(
        string $class,
        string $name,
        ...$arguments
    ): InlineTrap {
        $class = Comcode::useIfClass(
            $class,
            $this->subject
        );
        $trap = new InlineTrap($name);
        $isExpr = $this->node instanceof Expression && property_exists($this->node, 'expr');
        if ($isExpr) {
            $this->node->expr = $trap->node;
        } else {
            $this->node->nodes[] = $trap;
        }
        $trap
            ->__bindExpression($this, $this->node, $isExpr ? 'expr' : 'nodes')
            ->staticCall($class, ...$arguments);
        return $trap;
    }

    /**
     * @param  mixed|null  $value
     * @return InlineTrap
     */
    public function real(
        mixed $value = null
    ): InlineTrap {
        return $this->var(
            Comcode::defineValueNode($value)
        );
    }
}
