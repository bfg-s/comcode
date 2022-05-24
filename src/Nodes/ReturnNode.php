<?php

namespace Bfg\Comcode\Nodes;

use Bfg\Comcode\Comcode;
use Bfg\Comcode\Interfaces\BirthNodeInterface;
use Bfg\Comcode\Node;
use Bfg\Comcode\PhpInlineTrap;
use Bfg\Comcode\QueryNode;
use Bfg\Comcode\Traits\CommonWhileExpressions;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeAbstract;

class ReturnNode extends QueryNode implements
    BirthNodeInterface
{
    use CommonWhileExpressions;

    /**
     * @var Return_|null
     */
    public ?NodeAbstract $node = null;

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

    /**
     * Get instance class of node type
     * @return <class-string>
     */
    public static function nodeClass(): string
    {
        return Return_::class;
    }

    /**
     * Has modifies
     * @return bool
     */
    public static function modified(): bool
    {
        return true;
    }
}
