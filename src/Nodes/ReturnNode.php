<?php

namespace Bfg\Comcode\Nodes;

use Bfg\Comcode\Interfaces\AlwaysLastNodeInterface;
use Bfg\Comcode\Interfaces\BirthNodeInterface;
use Bfg\Comcode\Node;
use Bfg\Comcode\PhpInlineTrap;
use Bfg\Comcode\QueryNode;
use Bfg\Comcode\Traits\CommonWhileExpressions;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeAbstract;

class ReturnNode extends QueryNode implements
    BirthNodeInterface, AlwaysLastNodeInterface
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
     * @return PhpInlineTrap
     */
    public function this(): PhpInlineTrap
    {
        $this->node->expr
            = new PhpInlineTrap('this');

        return $this->node->expr
            ->__bindExpression($this->node);
    }

    /**
     * NODE birth method
     * @return NodeAbstract
     */
    public function birth(): NodeAbstract
    {
        return Node::return();
    }
}
