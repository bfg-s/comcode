<?php

namespace Bfg\Comcode\Nodes;

use Bfg\Comcode\InlineTrap;
use Bfg\Comcode\Interfaces\AlwaysLastNodeInterface;
use Bfg\Comcode\Interfaces\BirthNodeInterface;
use Bfg\Comcode\Interfaces\ReconstructionNodeInterface;
use Bfg\Comcode\Node;
use Bfg\Comcode\QueryNode;
use Bfg\Comcode\Traits\CommonWhileExpressions;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeAbstract;

class ReturnNode extends QueryNode implements
    BirthNodeInterface, AlwaysLastNodeInterface, ReconstructionNodeInterface
{
    use CommonWhileExpressions;

    /**
     * @var Return_|null
     */
    public ?NodeAbstract $node = null;

    /**
     * Get instance class of node type
     * @return <class-string>
     */
    public static function nodeClass(): string
    {
        return Return_::class;
    }

    /**
     * @return InlineTrap
     */
    public function this(): InlineTrap
    {
        $this->node->expr
            = new InlineTrap('this');

        return $this->node->expr
            ->__bindExpression($this, $this->node);
    }

    /**
     * NODE birth method
     * @return NodeAbstract
     */
    public function birth(): NodeAbstract
    {
        return Node::return();
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
