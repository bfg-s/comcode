<?php

namespace Bfg\Comcode\Nodes;

use Bfg\Comcode\QueryNode;
use Bfg\Comcode\Traits\CommonWhileExpressions;
use Bfg\Comcode\Traits\FuncCommonTrait;
use PhpParser\Node\Expr\Closure;
use PhpParser\NodeAbstract;

class ClosureNode extends QueryNode
{
    use CommonWhileExpressions;
    use FuncCommonTrait;

    /**
     * @var Closure|null
     */
    public ?NodeAbstract $node = null;

    /**
     * @var callable
     */
    protected $callback;

    /**
     * @param  callable  $callback
     */
    public function __construct(
        callable $callback,
    ) {
        $this->callback = $callback;
    }

    /**
     * Get instance class of node type
     * @return <class-string>
     */
    public function nodeClass(): string
    {
        return Closure::class;
    }

    /**
     * @return void
     */
    public function mounted(): void
    {
        call_user_func($this->callback, $this);
    }
}
