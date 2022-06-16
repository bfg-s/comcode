<?php

namespace Bfg\Comcode\Nodes;

use Bfg\Comcode\Node;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\NodeAbstract;

class InterfaceNode extends ClassNode
{
    /**
     * NODE birth method
     * @return NodeAbstract
     */
    public function birth(): NodeAbstract
    {
        return Node::interfaceStmt($this->name);
    }

    /**
     * Get instance class of node type
     * @return <class-string>
     */
    public function nodeClass(): string
    {
        return Interface_::class;
    }
}
