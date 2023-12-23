<?php

namespace Bfg\Comcode\Subjects;

use Bfg\Comcode\Exceptions\QueryNodeError;
use Bfg\Comcode\Node;
use Bfg\Comcode\Nodes\ClassConstNode;
use Bfg\Comcode\Nodes\ClassExtendsNode;
use Bfg\Comcode\Nodes\ClassImplementNode;
use Bfg\Comcode\Nodes\ClassMethodNode;
use Bfg\Comcode\Nodes\ClassNode;
use Bfg\Comcode\Nodes\ClassPropertyNode;
use Bfg\Comcode\Nodes\ClassTraitNode;
use Bfg\Comcode\Nodes\EnumCaseNode;
use Bfg\Comcode\Nodes\EnumNode;
use Bfg\Comcode\Nodes\NamespaceNode;
use Bfg\Comcode\Nodes\NamespaceUseNode;
use Bfg\Comcode\Nodes\TraitNode;
use Bfg\Comcode\QueryNode;

class EnumSubject extends ClassSubject
{
    /**
     * @param  string  $name
     * @param  mixed|null  $value
     * @return EnumCaseNode
     */
    public function case(
        string $name, mixed $value = null
    ): EnumCaseNode {
        return $this->classNode->apply(
            new EnumCaseNode($name, $value)
        );
    }

    /**
     * @param  string  $type
     * @return $this
     */
    public function scalarType(string $type): static
    {
        $this->classNode->node->scalarType = Node::identifier($type);

        return $this;
    }

    /**
     * Discover individual environment
     * @return void
     */
    protected function discoverStmtEnvironment(): void
    {
        $this->namespaceNode = $this->apply(
            new NamespaceNode($this->class)
        );

        $this->classNode = $this->namespaceNode->apply(
            new EnumNode($this->class)
        );
    }
}
