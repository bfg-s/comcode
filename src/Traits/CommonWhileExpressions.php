<?php

namespace Bfg\Comcode\Traits;

use Bfg\Comcode\Comcode;
use Bfg\Comcode\InlineTrap;
use Bfg\Comcode\Node;
use PhpParser\Node\Expr;

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
        $this->node->expr
            = new InlineTrap($name);
        return $this->node->expr
            ->__bindExpression($this, $this->node);
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
        $this->node->expr = new InlineTrap(
            $name
        );
        return $this->node->expr
            ->__bindExpression($this, $this->node)
            ->staticCall($class, ...$arguments);
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
