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
use Bfg\Comcode\Nodes\NamespaceNode;
use Bfg\Comcode\Nodes\NamespaceUseNode;
use Bfg\Comcode\Nodes\TraitNode;
use Bfg\Comcode\QueryNode;

class TraitSubject extends ClassSubject
{
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
            new TraitNode($this->class)
        );
    }
}
