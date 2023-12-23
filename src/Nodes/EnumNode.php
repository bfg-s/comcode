<?php

namespace Bfg\Comcode\Nodes;

use Bfg\Comcode\Node;
use PhpParser\Node\Stmt\Enum_;
use PhpParser\NodeAbstract;

class EnumNode extends ClassNode
{
    /**
     * @param  string  $name
     * @param  mixed|null  $value
     * @return $this
     */
    public function case(
        string $name, mixed $value = null
    ): static {
        return $this->apply(
            new EnumCaseNode($name, $value)
        );
    }

    /**
     * NODE birth method
     * @return NodeAbstract
     */
    public function birth(): NodeAbstract
    {
        return Node::enumStmt($this->name, $this->type);
    }

    /**
     * Get instance class of node type
     * @return <class-string>
     */
    public function nodeClass(): string
    {
        return Enum_::class;
    }
}
