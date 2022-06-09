<?php

namespace Bfg\Comcode\Nodes;

use Bfg\Comcode\Interfaces\BirthNodeInterface;
use Bfg\Comcode\Interfaces\ReconstructionNodeInterface;
use Bfg\Comcode\QueryNode;
use Bfg\Comcode\Traits\CommonWhileExpressions;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeAbstract;

class ExpressionNode extends QueryNode implements
    BirthNodeInterface, ReconstructionNodeInterface
{
    use CommonWhileExpressions;

    /**
     * @var Return_|null
     */
    public ?NodeAbstract $node = null;

    public function __construct(
        public string $class,
        public string $store = 'stmts',
        public array $args = [],
    ) {
    }

    /**
     * Get instance class of node type
     * @return <class-string>
     */
    public function nodeClass(): string
    {
        return $this->class;
    }

    /**
     * NODE birth method
     * @return NodeAbstract
     */
    public function birth(): NodeAbstract
    {
        return new $this->class(...$this->args);
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
