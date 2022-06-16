<?php

namespace Bfg\Comcode\Nodes;

use Bfg\Comcode\Node;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\NodeAbstract;

class TraitNode extends ClassNode
{
    /**
     * NODE birth method
     * @return NodeAbstract
     */
    public function birth(): NodeAbstract
    {
        return Node::traitStmt($this->name);
    }

    /**
     * Get instance class of node type
     * @return <class-string>
     */
    public function nodeClass(): string
    {
        return Trait_::class;
    }
}
