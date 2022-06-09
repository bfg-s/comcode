<?php

namespace Bfg\Comcode\Nodes;

use Bfg\Comcode\Comcode;
use Bfg\Comcode\Interfaces\AnonymousInterface;
use Bfg\Comcode\Interfaces\BirthNodeInterface;
use Bfg\Comcode\Interfaces\ClarificationNodeInterface;
use Bfg\Comcode\Interfaces\ReconstructionNodeInterface;
use Bfg\Comcode\QueryNode;
use Bfg\Comcode\Traits\CommonWhileExpressions;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeAbstract;

class LineNode extends QueryNode implements
    BirthNodeInterface, ClarificationNodeInterface, AnonymousInterface, ReconstructionNodeInterface
{
    use CommonWhileExpressions;

    /**
     * @var int
     */
    protected static int $counter = -1;

    /**
     * @var Expression|null
     */
    public ?NodeAbstract $node = null;

    public function __construct(
        public ?int $num = null
    ) {
        static::$counter++;
    }

    /**
     * Get instance class of node type
     * @return <class-string>
     */
    public function nodeClass(): string
    {
        return Expression::class;
    }

    /**
     * NODE birth method
     * @return NodeAbstract
     */
    public function birth(): NodeAbstract
    {
        return Comcode::anonymousLine();
    }

    /**
     * @param  Expression|mixed  $stmt
     * @param  string|int  $key
     * @return bool
     */
    public function clarification(mixed $stmt, string|int $key): bool
    {
        return $key
            == (is_null($this->num) ? static::$counter : $this->num);
    }

    /**
     * NODE reconstruction method
     * @return void
     */
    public function reconstruction(): void
    {
        $this->original = clone $this->node;
    }
}
